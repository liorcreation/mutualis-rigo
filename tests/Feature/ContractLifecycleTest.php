<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Livewire\ContractCheckout;
use App\Livewire\PaymentHistory;
use App\Models\Contract;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ContractLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function makeValidatedContribution(): MutualizationContribution
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);

        $project = Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet de test',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_cours_de_mutualisation',
            'besoin_financier_target' => 1000000,
            'besoin_financier_actuel' => 0,
        ]);

        return MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => User::factory()->create(['role' => 'personne_physique'])->id,
            'type_apport' => 'financier',
            'montant' => 250000,
            'statut' => 'valide',
        ]);
    }

    public function test_full_contract_and_payment_flow_generates_pdf(): void
    {
        Storage::fake('local');

        $contribution = $this->makeValidatedContribution();
        $contributor = User::find($contribution->user_id);

        // Étape 1 : la page de checkout affiche l'aperçu et la case de signature.
        $this->actingAs($contributor)
            ->get(route('contracts.checkout', $contribution))
            ->assertOk()
            ->assertSee('Contrat de mutualisation')
            ->assertSee('Signer le contrat')
            ->assertSee('250 000');

        $contract = Contract::where('contribution_id', $contribution->id)->firstOrFail();
        $this->assertSame(ContractStatus::PENDING_SIGNATURE, $contract->status);

        // Étape 2 : signature via le composant Livewire (pas d'appel direct au service).
        $component = Livewire::actingAs($contributor)
            ->test(ContractCheckout::class, ['contribution' => $contribution])
            ->set('accepted', true)
            ->call('signContract')
            ->assertHasNoErrors()
            ->assertSee('Mobile Money');

        $contract = $contract->fresh();
        $this->assertNotNull($contract->signed_at);
        $this->assertNotNull($contract->document_hash);

        // Étape 3 : paiement via le composant Livewire.
        $component->set('paymentMethod', 'mobile_money')
            ->call('pay', 'mobile_money')
            ->assertHasNoErrors()
            ->assertSee('Contrat actif et payé')
            ->assertSee('Télécharger le contrat');

        $contract = $contract->fresh();
        $this->assertSame(ContractStatus::ACTIVE, $contract->status);
        $this->assertNotNull($contract->pdf_path);
        Storage::disk('local')->assertExists($contract->pdf_path);

        // Étape 4 : téléchargement, accès refusé à un tiers, vérification publique.
        $this->actingAs($contributor)
            ->get(route('contracts.download', $contract))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $stranger = User::factory()->create(['role' => 'personne_physique']);
        $this->actingAs($stranger)
            ->get(route('contracts.download', $contract))
            ->assertForbidden();

        $hash = substr($contract->document_hash, 0, 12);
        $this->get(route('contracts.verify', ['contract' => $contract->contract_number, 'h' => $hash]))
            ->assertOk()
            ->assertSee($contract->contract_number);

        $this->get(route('contracts.verify', ['contract' => $contract->contract_number, 'h' => 'wronghash']))
            ->assertNotFound();

        // Étape 5 : le paiement apparaît dans l'historique du contributeur.
        Livewire::actingAs($contributor)
            ->test(PaymentHistory::class)
            ->assertSee($contract->contract_number)
            ->assertSee('successful');
    }

    public function test_checkout_page_rejects_non_owner(): void
    {
        $contribution = $this->makeValidatedContribution();
        $stranger = User::factory()->create(['role' => 'personne_physique']);

        $this->actingAs($stranger)
            ->get(route('contracts.checkout', $contribution))
            ->assertForbidden();
    }

    public function test_cannot_sign_twice(): void
    {
        $contribution = $this->makeValidatedContribution();
        $contributor = User::find($contribution->user_id);

        Livewire::actingAs($contributor)
            ->test(ContractCheckout::class, ['contribution' => $contribution])
            ->set('accepted', true)
            ->call('signContract')
            ->set('accepted', true)
            ->call('signContract')
            ->assertForbidden();
    }
}
