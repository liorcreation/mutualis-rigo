<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\CreateProject;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_step_one_blocks_progress_when_the_project_sheet_is_invalid(): void
    {
        $user = User::factory()->create(['role' => 'chef_projet']);

        Livewire::actingAs($user)
            ->test(CreateProject::class)
            ->set('titre', 'Abc')
            ->call('nextStep')
            ->assertHasErrors(['titre'])
            ->assertSet('step', 1);
    }

    public function test_wizard_advances_through_the_three_steps_and_creates_the_project(): void
    {
        $user = User::factory()->create(['role' => 'chef_projet']);

        Livewire::actingAs($user)
            ->test(CreateProject::class)
            ->set('titre', 'Ferme solaire communautaire')
            ->set('categorie', 'Énergie')
            ->set('description', 'Un projet solaire pour desservir le quartier en électricité propre.')
            ->set('besoinFinancierTarget', '2500000')
            ->call('nextStep')
            ->assertSet('step', 2)
            ->set('competences.0.role', 'Ingénieur solaire')
            ->set('competences.0.niveau', 'senior')
            ->call('nextStep')
            ->assertSet('step', 3)
            ->set('materiels.0.nom', 'Panneaux solaires')
            ->set('materiels.0.quantite', '10')
            ->set('materiels.0.date_souhaitee', now()->addWeek()->toDateString())
            ->call('save')
            ->assertRedirect(route('projects.index'));

        $project = Project::firstOrFail();
        $this->assertSame('Ferme solaire communautaire', $project->titre);
        $this->assertSame('en_etude', $project->statut->value);
        $this->assertCount(1, $project->besoins_competences);
        $this->assertSame('Ingénieur solaire', $project->besoins_competences[0]['role']);
        $this->assertCount(1, $project->besoins_materiels);
        $this->assertSame('Panneaux solaires', $project->besoins_materiels[0]['label']);
        $this->assertSame(10, $project->besoins_materiels[0]['quantite']);
        $this->assertSame('Votre projet est maintenant soumis à l’étude. Retrouvez-le dans le catalogue.', session('project-message'));
    }

    public function test_rh_and_material_steps_are_optional(): void
    {
        $user = User::factory()->create(['role' => 'chef_projet']);

        Livewire::actingAs($user)
            ->test(CreateProject::class)
            ->set('titre', 'Projet minimal sans ressources')
            ->set('categorie', 'Numérique')
            ->set('description', 'Un projet qui ne nécessite ni compétence ni matériel particulier.')
            ->call('nextStep')
            ->call('nextStep')
            ->call('save')
            ->assertHasNoErrors();

        $project = Project::firstOrFail();
        $this->assertSame([], $project->besoins_competences);
        $this->assertSame([], $project->besoins_materiels);
    }
}
