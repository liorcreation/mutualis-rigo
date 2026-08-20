<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ProjectCatalog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectCatalogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeProject(string $statut, array $overrides = []): Project
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);

        return Project::create(array_merge([
            'user_id' => $owner->id,
            'titre' => 'Projet de test',
            'description' => 'Description de test',
            'categorie' => 'Numérique',
            'statut' => $statut,
            'besoin_financier_target' => 100000,
            'besoin_financier_actuel' => 0,
        ], $overrides));
    }

    public function test_the_catalog_only_lists_publicly_visible_projects(): void
    {
        $this->makeProject('brouillon', ['titre' => 'Brouillon caché']);
        $this->makeProject('en_etude', ['titre' => 'En attente de validation']);
        $this->makeProject('en_cours_de_mutualisation', ['titre' => 'Projet en cours visible']);
        $this->makeProject('cloture', ['titre' => 'Projet clôturé visible']);

        Livewire::test(ProjectCatalog::class)
            ->assertDontSee('Brouillon caché')
            ->assertDontSee('En attente de validation')
            ->assertSee('Projet en cours visible')
            ->assertSee('Projet clôturé visible');
    }

    public function test_a_guest_can_browse_the_catalog_without_a_session(): void
    {
        $this->makeProject('en_cours_de_mutualisation', ['titre' => 'Projet ouvert aux visiteurs']);

        Livewire::test(ProjectCatalog::class)
            ->assertSee('Projet ouvert aux visiteurs')
            ->assertSee('Se connecter');
    }

    public function test_searching_by_keyword_filters_the_results(): void
    {
        $this->makeProject('en_cours_de_mutualisation', ['titre' => 'Ferme solaire communautaire']);
        $this->makeProject('en_cours_de_mutualisation', ['titre' => 'Atelier de couture partagé']);

        Livewire::test(ProjectCatalog::class)
            ->set('search', 'solaire')
            ->assertSee('Ferme solaire communautaire')
            ->assertDontSee('Atelier de couture partagé');
    }

    public function test_searching_matches_the_project_category(): void
    {
        $this->makeProject('en_cours_de_mutualisation', ['titre' => 'Projet A', 'categorie' => 'Agriculture']);
        $this->makeProject('en_cours_de_mutualisation', ['titre' => 'Projet B', 'categorie' => 'Éducation']);

        Livewire::test(ProjectCatalog::class)
            ->set('search', 'Agriculture')
            ->assertSee('Projet A')
            ->assertDontSee('Projet B');
    }

    public function test_filtering_by_status_narrows_results_to_that_status(): void
    {
        $this->makeProject('en_cours_de_mutualisation', ['titre' => 'Projet en cours']);
        $this->makeProject('cloture', ['titre' => 'Projet clôturé']);

        Livewire::test(ProjectCatalog::class)
            ->set('status', 'cloture')
            ->assertDontSee('Projet en cours')
            ->assertSee('Projet clôturé');
    }

    public function test_a_non_public_status_cannot_be_forced_through_the_filter(): void
    {
        $this->makeProject('brouillon', ['titre' => 'Brouillon jamais visible']);
        $this->makeProject('en_cours_de_mutualisation', ['titre' => 'Projet visible']);

        Livewire::test(ProjectCatalog::class)
            ->set('status', 'brouillon')
            ->assertDontSee('Brouillon jamais visible')
            ->assertSee('Projet visible');
    }

    public function test_filtering_by_need_type_financier_only_shows_projects_seeking_funding(): void
    {
        $this->makeProject('en_cours_de_mutualisation', [
            'titre' => 'Projet financier',
            'besoin_financier_target' => 500000,
        ]);
        $this->makeProject('en_cours_de_mutualisation', [
            'titre' => 'Projet sans financement',
            'besoin_financier_target' => null,
        ]);

        Livewire::test(ProjectCatalog::class)
            ->set('needType', 'financier')
            ->assertSee('Projet financier')
            ->assertDontSee('Projet sans financement');
    }

    public function test_filtering_by_need_type_competence_only_shows_projects_seeking_skills(): void
    {
        $this->makeProject('en_cours_de_mutualisation', [
            'titre' => 'Projet avec compétences',
            'besoins_competences' => [['role' => 'Développeur Laravel', 'niveau' => 'senior']],
        ]);
        $this->makeProject('en_cours_de_mutualisation', [
            'titre' => 'Projet sans compétence',
            'besoins_competences' => [],
        ]);

        Livewire::test(ProjectCatalog::class)
            ->set('needType', 'competence')
            ->assertSee('Projet avec compétences')
            ->assertDontSee('Projet sans compétence');
    }

    public function test_filtering_by_need_type_materiel_only_shows_projects_seeking_equipment(): void
    {
        $this->makeProject('en_cours_de_mutualisation', [
            'titre' => 'Projet avec matériel',
            'besoins_materiels' => [['label' => 'Vidéoprojecteur', 'quantite' => 1, 'date_souhaitee' => null]],
        ]);
        $this->makeProject('en_cours_de_mutualisation', [
            'titre' => 'Projet sans matériel',
            'besoins_materiels' => [],
        ]);

        Livewire::test(ProjectCatalog::class)
            ->set('needType', 'materiel')
            ->assertSee('Projet avec matériel')
            ->assertDontSee('Projet sans matériel');
    }

    public function test_clear_filters_resets_search_status_and_page(): void
    {
        Livewire::test(ProjectCatalog::class)
            ->set('search', 'solaire')
            ->set('status', 'cloture')
            ->set('needType', 'financier')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('status', 'all')
            ->assertSet('needType', 'all');
    }

    public function test_results_are_paginated_across_multiple_pages(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->makeProject('en_cours_de_mutualisation', ['titre' => "Projet numéro {$i}"]);
        }

        $component = Livewire::test(ProjectCatalog::class);
        $this->assertSame(9, substr_count($component->html(), 'wire:key="project-'));

        $component->call('nextPage');
        $this->assertSame(1, substr_count($component->html(), 'wire:key="project-'));
    }

    public function test_financial_and_competence_progress_are_displayed_on_the_card(): void
    {
        $this->makeProject('en_cours_de_mutualisation', [
            'titre' => 'Projet avec indicateurs',
            'besoin_financier_target' => 200000,
            'besoin_financier_actuel' => 0,
            'besoins_competences' => [['role' => 'Chef de chantier', 'niveau' => 'expert']],
        ]);

        Livewire::test(ProjectCatalog::class)
            ->assertSee('Progression financière')
            ->assertSee('Compétences mutualisées')
            ->assertSee('0%');
    }

    public function test_requested_materials_are_displayed_as_chips_on_the_card(): void
    {
        $this->makeProject('en_cours_de_mutualisation', [
            'titre' => 'Projet matériel',
            'besoins_materiels' => [['label' => 'Tables pliantes', 'quantite' => 5, 'date_souhaitee' => null]],
        ]);

        Livewire::test(ProjectCatalog::class)
            ->assertSee('Tables pliantes');
    }
}
