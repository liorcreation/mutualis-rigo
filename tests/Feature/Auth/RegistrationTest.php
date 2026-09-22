<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('projects.index', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_a_company_can_register_with_a_business_profile(): void
    {
        Volt::test('pages.auth.register')
            ->set('accountType', 'personne_morale')
            ->set('name', 'Contact RIGO Conseil')
            ->set('email', 'entreprise@example.com')
            ->set('nomEntreprise', 'RIGO Conseil SARL')
            ->set('rneSiret', 'BF-RCCM-2026-A')
            ->set('secteurActivite', 'Conseil numérique')
            ->set('representantLegal', 'Awa Kaboré')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register');

        $user = User::query()->where('email', 'entreprise@example.com')->firstOrFail();

        $this->assertSame('personne_morale', $user->role->value);
        $this->assertSame('RIGO Conseil SARL', $user->profile->nom_entreprise);
        $this->assertSame('BF-RCCM-2026-A', $user->profile->rne_siret);
    }
}
