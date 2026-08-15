<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\QuickContributionModal;
use App\Livewire\UserDashboard;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\User;
use App\Services\ContractService;
use App\Services\PaymentService;
use App\Services\ProjectMutualizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ContributionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(User $owner, float $target = 1000000): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet de test',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_cours_de_mutualisation',
            'besoin_financier_target' => $target,
            'besoin_financier_actuel' => 0,
        ]);
    }

    public function test_submitting_a_financial_contribution_notifies_user_instead_of_redirecting(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $contributor->profile()->create(['is_verified' => true]);
        $project = $this->makeProject($owner);

        Livewire::actingAs($contributor)
            ->test(QuickContributionModal::class)
            ->call('openContributionModal', $project->id)
            ->set('typeApport', 'financier')
            ->set('montant', '50000')
            ->call('save')
            ->assertDispatched('notify')
            ->assertDispatched('contribution-created');

        $contribution = MutualizationContribution::firstOrFail();
        $this->assertSame('en_attente', $contribution->statut->value);
    }

    public function test_dashboard_lists_all_contribution_statuses_with_distinct_badges(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);

        MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'type_apport' => 'financier',
            'montant' => 50000,
            'statut' => 'en_attente',
        ]);

        $validated = MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'type_apport' => 'financier',
            'montant' => 75000,
            'statut' => 'valide',
        ]);

        Livewire::actingAs($contributor)
            ->test(UserDashboard::class)
            ->call('selectTab', 'contributions')
            ->assertSee('En attente de validation')
            ->assertSee('En attente de signature/paiement')
            ->assertSee('Contractualiser & payer')
            ->assertDontSee('Contrat actif');

        $contract = app(ContractService::class)->createFromContribution($validated, $contributor);
        $contract = app(ContractService::class)->sign($contract, $contributor, '127.0.0.1');
        app(PaymentService::class)->charge($contract, $contributor, 'mobile_money');

        Livewire::actingAs($contributor)
            ->test(UserDashboard::class)
            ->call('selectTab', 'contributions')
            ->assertSee('Contrat actif')
            ->assertSee('Voir le contrat');
    }

    public function test_project_progress_only_counts_paid_contracts_not_just_validated_contributions(): void
    {
        Storage::fake('local');

        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner, target: 100000);

        $contribution = MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'type_apport' => 'financier',
            'montant' => 100000,
            'statut' => 'valide',
        ]);

        $service = app(ProjectMutualizationService::class);

        // Validée par l'admin mais pas encore signée/payée : ne doit pas compter.
        $this->assertSame(0.0, $service->financialProgress($project->fresh()));

        $contract = app(ContractService::class)->createFromContribution($contribution, $contributor);
        $contract = app(ContractService::class)->sign($contract, $contributor, '127.0.0.1');

        // Signé mais pas encore payé : toujours 0.
        $this->assertSame(0.0, $service->financialProgress($project->fresh()));

        app(PaymentService::class)->charge($contract, $contributor, 'mobile_money');

        // Payé : le contrat est actif, la progression doit refléter le montant.
        $this->assertSame(100.0, $service->financialProgress($project->fresh()));
        $this->assertSame(100.0, $service->recalculate($project->fresh())['financial']);
        $this->assertSame(100000.0, (float) $project->fresh()->besoin_financier_actuel);
    }

    public function test_deleting_a_contribution_soft_deletes_it_instead_of_removing_the_row(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);

        $contribution = MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'type_apport' => 'financier',
            'montant' => 50000,
            'statut' => 'en_attente',
        ]);

        $contribution->delete();

        $this->assertSoftDeleted('mutualization_contributions', ['id' => $contribution->id]);
        $this->assertTrue($contribution->fresh()->trashed());
        $this->assertNull(MutualizationContribution::find($contribution->id));
        $this->assertNotNull(MutualizationContribution::withTrashed()->find($contribution->id));

        $contribution->restore();

        $this->assertFalse($contribution->fresh()->trashed());
        $this->assertNotNull(MutualizationContribution::find($contribution->id));
    }
}
