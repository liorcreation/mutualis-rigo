<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ContributionApproval;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ContributionStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ContributionApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(): Project
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);

        return Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet de test',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_cours_de_mutualisation',
            'besoin_financier_target' => 500000,
            'besoin_financier_actuel' => 0,
        ]);
    }

    private function makeContribution(
        Project $project,
        User $contributor,
        string $type,
        string $statut = 'en_attente',
        ?float $montant = null,
    ): MutualizationContribution {
        return MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'type_apport' => $type,
            'montant' => $montant,
            'description_apport' => $type === 'competence' ? 'Développeur Laravel' : null,
            'statut' => $statut,
        ]);
    }

    public function test_financier_reviewer_only_sees_financial_contributions(): void
    {
        $financier = User::factory()->create(['role' => 'responsable_financier']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject();

        $this->makeContribution($project, $contributor, 'financier', montant: 50000);
        $this->makeContribution($project, $contributor, 'competence');

        Livewire::actingAs($financier)
            ->test(ContributionApproval::class)
            ->assertSee('50 000 FCFA')
            ->assertDontSee('Développeur Laravel');
    }

    public function test_rh_reviewer_only_sees_skill_contributions(): void
    {
        $rh = User::factory()->create(['role' => 'responsable_rh']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject();

        $this->makeContribution($project, $contributor, 'financier', montant: 50000);
        $this->makeContribution($project, $contributor, 'competence');

        Livewire::actingAs($rh)
            ->test(ContributionApproval::class)
            ->assertSee('Développeur Laravel')
            ->assertDontSee('50 000 FCFA');
    }

    public function test_roles_outside_any_validation_perimeter_are_denied(): void
    {
        $stranger = User::factory()->create(['role' => 'personne_physique']);

        Livewire::actingAs($stranger)
            ->test(ContributionApproval::class)
            ->assertForbidden();
    }

    public function test_a_reviewer_cannot_filter_outside_their_perimeter(): void
    {
        $financier = User::factory()->create(['role' => 'responsable_financier']);

        Livewire::actingAs($financier)
            ->test(ContributionApproval::class)
            ->set('typeFilter', 'competence')
            ->assertForbidden();
    }

    public function test_a_reviewer_cannot_open_a_contribution_outside_their_perimeter(): void
    {
        $financier = User::factory()->create(['role' => 'responsable_financier']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject();
        $competence = $this->makeContribution($project, $contributor, 'competence');

        Livewire::actingAs($financier)
            ->test(ContributionApproval::class)
            ->call('review', $competence->id, 'valide')
            ->assertForbidden();
    }

    public function test_validating_a_contribution_notifies_the_contributor_and_recalculates_progress(): void
    {
        Notification::fake();

        $financier = User::factory()->create(['role' => 'responsable_financier']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject();
        $contribution = $this->makeContribution($project, $contributor, 'financier', montant: 50000);

        Livewire::actingAs($financier)
            ->test(ContributionApproval::class)
            ->call('review', $contribution->id, 'valide')
            ->assertSet('showReview', true)
            ->call('saveDecision')
            ->assertHasNoErrors()
            ->assertSet('showReview', false)
            ->assertSee('Contribution validée et progression du projet recalculée.');

        $this->assertSame('valide', $contribution->fresh()->statut->value);
        Notification::assertSentTo($contributor, ContributionStatusUpdated::class);
    }

    public function test_refusing_a_contribution_requires_a_reason(): void
    {
        $financier = User::factory()->create(['role' => 'responsable_financier']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject();
        $contribution = $this->makeContribution($project, $contributor, 'financier', montant: 50000);

        Livewire::actingAs($financier)
            ->test(ContributionApproval::class)
            ->call('review', $contribution->id, 'refuse')
            ->call('saveDecision')
            ->assertHasErrors(['commentaire']);

        $this->assertSame('en_attente', $contribution->fresh()->statut->value);
    }

    public function test_refusing_a_contribution_with_a_reason_stores_the_comment(): void
    {
        Notification::fake();

        $financier = User::factory()->create(['role' => 'responsable_financier']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject();
        $contribution = $this->makeContribution($project, $contributor, 'financier', montant: 50000);

        Livewire::actingAs($financier)
            ->test(ContributionApproval::class)
            ->call('review', $contribution->id, 'refuse')
            ->set('commentaire', 'Le budget du projet est déjà couvert.')
            ->call('saveDecision')
            ->assertHasNoErrors();

        $fresh = $contribution->fresh();
        $this->assertSame('refuse', $fresh->statut->value);
        $this->assertSame('Le budget du projet est déjà couvert.', $fresh->commentaire_validation);
    }

    public function test_a_contribution_already_reviewed_cannot_be_reviewed_again(): void
    {
        $financier = User::factory()->create(['role' => 'responsable_financier']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject();
        $contribution = $this->makeContribution($project, $contributor, 'financier', 'valide', 50000);

        Livewire::actingAs($financier)
            ->test(ContributionApproval::class)
            ->call('review', $contribution->id, 'valide')
            ->call('saveDecision')
            ->assertStatus(422);
    }
}
