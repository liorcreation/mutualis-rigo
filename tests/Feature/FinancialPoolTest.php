<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Models\Contract;
use App\Models\FinancialLedgerEntry;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\User;
use App\Services\FinancialPoolService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FinancialPoolTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_contributions_are_registered_and_the_chain_is_valid(): void
    {
        [$project, $contributor] = $this->projectAndContributor();
        $contract = $this->activeContract($project, $contributor, 150000);
        $service = app(FinancialPoolService::class);

        $entry = $service->recordPaidContribution($contract);

        $this->assertSame(150000.0, $service->balance($project));
        $this->assertSame(FinancialPoolService::CONTRIBUTION, $entry->entry_type);
        $this->assertTrue($service->verifyIntegrity()['valid']);
    }

    public function test_transfer_is_atomic_and_cannot_exceed_the_source_balance(): void
    {
        [$source, $contributor] = $this->projectAndContributor();
        $destination = Project::create([
            'user_id' => $source->user_id,
            'titre' => 'Projet destinataire',
            'description' => 'Projet de test.',
            'categorie' => 'Recherche',
            'statut' => 'en_etude',
            'besoin_financier_target' => 100000,
            'besoin_financier_actuel' => 0,
        ]);
        $contract = $this->activeContract($source, $contributor, 200000);
        $service = app(FinancialPoolService::class);
        $service->recordPaidContribution($contract);
        $actor = User::factory()->create(['role' => 'responsable_financier']);

        $service->transfer($source, $destination, 75000, $actor, 'TRANSFER:TEST-001');

        $this->assertSame(125000.0, $service->balance($source));
        $this->assertSame(75000.0, $service->balance($destination));
        $this->assertSame(2, FinancialLedgerEntry::count());

        try {
            $service->transfer($source, $destination, 200000, $actor, 'TRANSFER:TEST-002');
            $this->fail('Un transfert supérieur au solde doit être refusé.');
        } catch (ValidationException) {
            // Le solde disponible est correctement contrôlé.
        }
        $this->assertSame(2, FinancialLedgerEntry::count());
    }

    public function test_a_tampered_ledger_entry_is_detected(): void
    {
        [$project, $contributor] = $this->projectAndContributor();
        $contract = $this->activeContract($project, $contributor, 50000);
        $service = app(FinancialPoolService::class);
        $service->recordPaidContribution($contract);

        DB::table('financial_ledger_entries')->update(['amount' => 999999]);

        $result = $service->verifyIntegrity();

        $this->assertFalse($result['valid']);
        $this->assertNotNull($result['invalid_id']);
    }

    /**
     * @return array{0: Project, 1: User}
     */
    private function projectAndContributor(): array
    {
        $owner = User::factory()->create(['role' => 'personne_physique']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $contributor->profile()->create(['is_verified' => true]);
        $project = Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet financier',
            'description' => 'Projet de test.',
            'categorie' => 'Recherche',
            'statut' => 'en_etude',
            'besoin_financier_target' => 100000,
            'besoin_financier_actuel' => 0,
        ]);

        return [$project, $contributor];
    }

    private function activeContract(Project $project, User $contributor, int $amount): Contract
    {
        $contribution = MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'type_apport' => ContributionType::FINANCIER,
            'montant' => $amount,
            'statut' => ContributionStatus::VALIDE,
        ]);

        return Contract::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'contribution_id' => $contribution->id,
            'status' => ContractStatus::ACTIVE,
            'amount' => $amount,
            'currency' => 'XOF',
        ]);
    }
}
