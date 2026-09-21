<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectUserAssignment;
use App\Models\User;
use App\Services\HumanResourceAllocationService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class HumanResourceAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_collaborator_cannot_exceed_one_hundred_percent_on_overlapping_projects(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $collaborator = User::factory()->create(['role' => 'collaborateur']);
        $firstProject = $this->project($owner);
        $secondProject = $this->project($owner);
        $service = app(HumanResourceAllocationService::class);
        $start = CarbonImmutable::parse('2026-09-21');
        $end = CarbonImmutable::parse('2026-10-21');

        $service->assign($firstProject, $collaborator, 80, $start, $end, 'Développeur Laravel');

        try {
            $service->assign($secondProject, $collaborator, 30, $start, $end, 'Architecte');
            $this->fail('Une surcharge RH doit être refusée.');
        } catch (ValidationException) {
            // Le dépassement est correctement bloqué par le service.
        }

        $this->assertSame(80, $service->totalLoad($collaborator, $start));
        $this->assertCount(1, ProjectUserAssignment::all());
    }

    public function test_non_overlapping_assignments_are_allowed(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $collaborator = User::factory()->create(['role' => 'collaborateur']);
        $firstProject = $this->project($owner);
        $secondProject = $this->project($owner);
        $service = app(HumanResourceAllocationService::class);

        $service->assign(
            $firstProject,
            $collaborator,
            100,
            CarbonImmutable::parse('2026-09-21'),
            CarbonImmutable::parse('2026-09-30'),
        );
        $service->assign(
            $secondProject,
            $collaborator,
            100,
            CarbonImmutable::parse('2026-10-01'),
            null,
        );

        $this->assertCount(2, ProjectUserAssignment::all());
        $this->assertSame(100, $service->totalLoad($collaborator, CarbonImmutable::parse('2026-10-15')));
    }

    private function project(User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet de charge '.fake()->unique()->word(),
            'description' => 'Projet de test.',
            'categorie' => 'Recherche',
            'statut' => 'en_etude',
            'besoin_financier_target' => 100000,
            'besoin_financier_actuel' => 0,
        ]);
    }
}
