<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Livewire\Admin\ProjectReview;
use App\Models\AuditLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectReviewTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(string $statut, string $titre = 'Projet de test'): Project
    {
        $owner = User::factory()->create(['role' => 'personne_physique']);

        return Project::create([
            'user_id' => $owner->id,
            'titre' => $titre,
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => $statut,
            'besoin_financier_target' => 100000,
            'besoin_financier_actuel' => 0,
        ]);
    }

    public function test_pilotage_roles_can_access_the_review_screen(): void
    {
        $chefProjet = User::factory()->create(['role' => 'chef_projet']);
        $this->makeProject('en_etude', 'Projet en étude');

        Livewire::actingAs($chefProjet)
            ->test(ProjectReview::class)
            ->assertSee('Revue des projets')
            ->assertSee('Projet en étude');
    }

    public function test_roles_outside_the_pilotage_committee_are_denied(): void
    {
        $rh = User::factory()->create(['role' => 'responsable_rh']);

        Livewire::actingAs($rh)
            ->test(ProjectReview::class)
            ->assertForbidden();
    }

    public function test_draft_projects_are_not_listed_for_review(): void
    {
        $admin = User::factory()->create(['role' => 'admin_systeme']);
        $this->makeProject('brouillon', 'Brouillon invisible');
        $this->makeProject('en_etude', 'Projet visible');

        Livewire::actingAs($admin)
            ->test(ProjectReview::class)
            ->assertDontSee('Brouillon invisible')
            ->assertSee('Projet visible');
    }

    public function test_opening_the_transition_on_a_studied_project_defaults_to_mutualization(): void
    {
        $admin = User::factory()->create(['role' => 'admin_systeme']);
        $project = $this->makeProject('en_etude');

        Livewire::actingAs($admin)
            ->test(ProjectReview::class)
            ->call('openTransition', $project->id)
            ->assertSet('showTransition', true)
            ->assertSet('targetStatus', 'en_cours_de_mutualisation');
    }

    public function test_opening_the_transition_on_a_project_under_mutualization_defaults_to_closure(): void
    {
        $admin = User::factory()->create(['role' => 'admin_systeme']);
        $project = $this->makeProject('en_cours_de_mutualisation');

        Livewire::actingAs($admin)
            ->test(ProjectReview::class)
            ->call('openTransition', $project->id)
            ->assertSet('targetStatus', 'cloture');
    }

    public function test_a_closed_project_can_no_longer_be_transitioned(): void
    {
        $admin = User::factory()->create(['role' => 'admin_systeme']);
        $project = $this->makeProject('cloture');

        Livewire::actingAs($admin)
            ->test(ProjectReview::class)
            ->call('openTransition', $project->id)
            ->assertForbidden();
    }

    public function test_updating_the_status_persists_the_transition_and_seals_an_audit_entry(): void
    {
        $admin = User::factory()->create(['role' => 'admin_systeme']);
        $project = $this->makeProject('en_etude');
        $auditCountBefore = AuditLog::count();

        Livewire::actingAs($admin)
            ->test(ProjectReview::class)
            ->call('openTransition', $project->id)
            ->set('targetStatus', 'en_cours_de_mutualisation')
            ->call('updateStatus')
            ->assertHasNoErrors()
            ->assertSet('showTransition', false)
            ->assertSee('Le statut du projet a été mis à jour.');

        $this->assertSame('en_cours_de_mutualisation', $project->fresh()->statut->value);
        $this->assertSame($auditCountBefore + 1, AuditLog::count());
        $this->assertSame('Changement de statut du projet', AuditLog::latest('id')->first()->action);
    }

    public function test_an_inconsistent_status_transition_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin_systeme']);
        $project = $this->makeProject('en_etude');

        Livewire::actingAs($admin)
            ->test(ProjectReview::class)
            ->set('selectedProjectId', $project->id)
            ->set('targetStatus', 'brouillon')
            ->call('updateStatus')
            ->assertStatus(422);

        $this->assertSame('en_etude', $project->fresh()->statut->value);
    }
}
