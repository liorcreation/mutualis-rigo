<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class HomepageNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_cta_points_to_project_catalog(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('projects.index'), false);
    }

    public function test_projects_index_route_is_named_correctly(): void
    {
        $this->assertSame('/projects', route('projects.index', [], false));

        $this->get(route('projects.index'))->assertOk();
    }

    public function test_legacy_demo_registry_route_no_longer_exists(): void
    {
        $this->assertFalse(Route::has('registry'));

        $this->get('/registre')->assertNotFound();
    }

    public function test_sidebar_logout_form_is_present_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('action="'.route('logout').'"', false);
    }

    public function test_profile_page_has_logout_button(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Se déconnecter');
    }
}
