<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Profile\UpdateProfileDetails;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_personne_physique_can_complete_their_business_profile(): void
    {
        $user = User::factory()->create(['role' => 'personne_physique']);

        Livewire::actingAs($user)
            ->test(UpdateProfileDetails::class)
            ->set('prenom', 'Awa')
            ->set('nom', 'Kaboré')
            ->set('titreProfessionnel', 'Développeuse Laravel')
            ->set('competencesText', 'Laravel, Livewire, PostgreSQL')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('profile-details-updated');

        $this->assertSame(['Laravel', 'Livewire', 'PostgreSQL'], $user->fresh()->profile->competences);
        $this->assertFalse($user->fresh()->profile->is_verified);
    }

    public function test_editing_a_verified_profile_requires_a_new_verification(): void
    {
        $user = User::factory()->create(['role' => 'personne_morale']);
        $user->profile()->create(['nom_entreprise' => 'RIGO Conseil', 'is_verified' => true]);

        Livewire::actingAs($user)
            ->test(UpdateProfileDetails::class)
            ->set('nomEntreprise', 'RIGO Conseil Afrique')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse($user->fresh()->profile->is_verified);
        $this->assertSame('RIGO Conseil Afrique', $user->fresh()->profile->nom_entreprise);
    }
}
