<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\MaterialReservationCenter;
use App\Models\MaterialReservation;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\User;
use App\Notifications\NewReservationRequested;
use App\Notifications\ReservationStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class MaterialReservationTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet matériel',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_cours_de_mutualisation',
            'besoin_financier_target' => 100000,
            'besoin_financier_actuel' => 0,
        ]);
    }

    private function makeValidatedMaterial(User $contributor, Project $project): MutualizationContribution
    {
        return MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'type_apport' => 'materiel',
            'description_apport' => '3 tables pliantes',
            'statut' => 'valide',
        ]);
    }

    public function test_verified_user_can_request_a_reservation_and_owner_is_notified(): void
    {
        Notification::fake();

        $contributor = User::factory()->create(['role' => 'chef_projet']);
        $requester = User::factory()->create(['role' => 'personne_physique']);
        $requester->profile()->create(['is_verified' => true]);
        $project = $this->makeProject($contributor);
        $material = $this->makeValidatedMaterial($contributor, $project);

        Livewire::actingAs($requester)
            ->test(MaterialReservationCenter::class)
            ->call('openRequestForm', $material->id)
            ->set('dateDebut', now()->addDays(2)->toDateString())
            ->set('dateFin', now()->addDays(4)->toDateString())
            ->set('quantite', '2')
            ->set('commentaire', 'Pour un événement associatif')
            ->call('submitRequest')
            ->assertHasNoErrors();

        $reservation = MaterialReservation::firstOrFail();
        $this->assertSame($material->id, $reservation->contribution_id);
        $this->assertSame($requester->id, $reservation->requested_by);
        $this->assertSame('en_attente', $reservation->statut->value);

        Notification::assertSentTo($contributor, NewReservationRequested::class);
    }

    public function test_contributor_cannot_request_a_reservation_of_their_own_material(): void
    {
        $contributor = User::factory()->create(['role' => 'chef_projet']);
        $contributor->profile()->create(['is_verified' => true]);
        $project = $this->makeProject($contributor);
        $material = $this->makeValidatedMaterial($contributor, $project);

        Livewire::actingAs($contributor)
            ->test(MaterialReservationCenter::class)
            ->call('openRequestForm', $material->id)
            ->assertForbidden();
    }

    public function test_overlapping_reservation_request_is_rejected_with_an_availability_error(): void
    {
        $contributor = User::factory()->create(['role' => 'chef_projet']);
        $requesterA = User::factory()->create(['role' => 'personne_physique']);
        $requesterA->profile()->create(['is_verified' => true]);
        $requesterB = User::factory()->create(['role' => 'personne_physique']);
        $requesterB->profile()->create(['is_verified' => true]);
        $project = $this->makeProject($contributor);
        $material = $this->makeValidatedMaterial($contributor, $project);

        MaterialReservation::create([
            'contribution_id' => $material->id,
            'requested_by' => $requesterA->id,
            'date_debut' => now()->addDays(5)->toDateString(),
            'date_fin' => now()->addDays(10)->toDateString(),
            'quantite' => 1,
            'statut' => 'en_attente',
        ]);

        Livewire::actingAs($requesterB)
            ->test(MaterialReservationCenter::class)
            ->call('openRequestForm', $material->id)
            ->set('dateDebut', now()->addDays(7)->toDateString())
            ->set('dateFin', now()->addDays(8)->toDateString())
            ->set('quantite', '1')
            ->call('submitRequest')
            ->assertHasErrors(['dateFin']);

        $this->assertSame(1, MaterialReservation::count());
    }

    public function test_contribution_owner_can_validate_a_reservation_and_requester_is_notified(): void
    {
        Notification::fake();

        $contributor = User::factory()->create(['role' => 'chef_projet']);
        $requester = User::factory()->create(['role' => 'personne_physique']);
        $requester->profile()->create(['is_verified' => true]);
        $project = $this->makeProject($contributor);
        $material = $this->makeValidatedMaterial($contributor, $project);

        $reservation = MaterialReservation::create([
            'contribution_id' => $material->id,
            'requested_by' => $requester->id,
            'date_debut' => now()->addDays(2)->toDateString(),
            'date_fin' => now()->addDays(4)->toDateString(),
            'quantite' => 1,
            'statut' => 'en_attente',
        ]);

        Livewire::actingAs($contributor)
            ->test(MaterialReservationCenter::class)
            ->call('review', $reservation->id, 'validee')
            ->call('saveDecision')
            ->assertHasNoErrors();

        $this->assertSame('validee', $reservation->fresh()->statut->value);
        Notification::assertSentTo($requester, ReservationStatusUpdated::class);
    }

    public function test_a_third_party_cannot_validate_someone_elses_reservation(): void
    {
        $contributor = User::factory()->create(['role' => 'chef_projet']);
        $requester = User::factory()->create(['role' => 'personne_physique']);
        $requester->profile()->create(['is_verified' => true]);
        $stranger = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($contributor);
        $material = $this->makeValidatedMaterial($contributor, $project);

        $reservation = MaterialReservation::create([
            'contribution_id' => $material->id,
            'requested_by' => $requester->id,
            'date_debut' => now()->addDays(2)->toDateString(),
            'date_fin' => now()->addDays(4)->toDateString(),
            'quantite' => 1,
            'statut' => 'en_attente',
        ]);

        Livewire::actingAs($stranger)
            ->test(MaterialReservationCenter::class)
            ->call('review', $reservation->id, 'validee')
            ->assertForbidden();

        $this->assertSame('en_attente', $reservation->fresh()->statut->value);
    }

    public function test_requester_can_cancel_their_own_pending_reservation(): void
    {
        $contributor = User::factory()->create(['role' => 'chef_projet']);
        $requester = User::factory()->create(['role' => 'personne_physique']);
        $requester->profile()->create(['is_verified' => true]);
        $project = $this->makeProject($contributor);
        $material = $this->makeValidatedMaterial($contributor, $project);

        $reservation = MaterialReservation::create([
            'contribution_id' => $material->id,
            'requested_by' => $requester->id,
            'date_debut' => now()->addDays(2)->toDateString(),
            'date_fin' => now()->addDays(4)->toDateString(),
            'quantite' => 1,
            'statut' => 'en_attente',
        ]);

        Livewire::actingAs($requester)
            ->test(MaterialReservationCenter::class)
            ->call('cancel', $reservation->id);

        $this->assertSame('annulee', $reservation->fresh()->statut->value);
    }
}
