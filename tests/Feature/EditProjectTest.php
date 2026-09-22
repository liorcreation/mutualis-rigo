<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\EditProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_owner_can_edit_the_project_sheet_and_its_structured_needs(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = Project::create([
            'user_id' => $owner->id,
            'titre' => 'Ancien titre du projet',
            'description' => 'Une description initiale suffisamment longue pour le formulaire.',
            'categorie' => 'Éducation',
            'statut' => 'en_etude',
            'besoin_financier_target' => 100000,
            'besoins_competences' => [['role' => 'Formateur', 'niveau' => 'junior']],
            'besoins_materiels' => [['label' => 'Salle', 'quantite' => 1, 'date_souhaitee' => null]],
        ]);

        Livewire::actingAs($owner)
            ->test(EditProject::class, ['project' => $project])
            ->set('titre', 'Nouveau titre du projet')
            ->set('description', 'Une nouvelle description détaillée et suffisamment longue pour être enregistrée.')
            ->set('besoinFinancierTarget', '250000')
            ->set('competences.0.role', 'Chef de projet')
            ->set('competences.0.niveau', 'senior')
            ->set('materiels.0.nom', 'Vidéoprojecteur')
            ->set('materiels.0.quantite', '2')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $project = $project->fresh();

        $this->assertSame('Nouveau titre du projet', $project->titre);
        $this->assertSame('250000.00', $project->besoin_financier_target);
        $this->assertSame('Chef de projet', $project->besoins_competences[0]['role']);
        $this->assertSame(2, $project->besoins_materiels[0]['quantite']);
    }

    public function test_a_closed_project_cannot_be_edited(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet clôturé',
            'description' => 'Une description de projet clôturé.',
            'categorie' => 'test',
            'statut' => 'cloture',
        ]);

        Livewire::actingAs($owner)
            ->test(EditProject::class, ['project' => $project])
            ->assertForbidden();
    }
}
