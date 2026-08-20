<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Admin\ProjectReview;
use App\Livewire\NotificationCenter;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(User $owner, string $statut = 'en_etude'): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet de test',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => $statut,
            'besoin_financier_target' => 100000,
            'besoin_financier_actuel' => 0,
        ]);
    }

    public function test_the_bell_shows_the_number_of_unread_notifications(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $projectA = $this->makeProject($owner);
        $projectB = $this->makeProject($owner);
        $owner->notify(new ProjectStatusUpdated($projectA->fresh()));
        $owner->notify(new ProjectStatusUpdated($projectB->fresh()));

        Livewire::actingAs($owner)
            ->test(NotificationCenter::class)
            ->assertSee('2');
    }

    public function test_toggling_opens_and_closes_the_panel(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);

        Livewire::actingAs($owner)
            ->test(NotificationCenter::class)
            ->assertSet('open', false)
            ->call('toggle')
            ->assertSet('open', true)
            ->call('close')
            ->assertSet('open', false);
    }

    public function test_filtering_by_unread_hides_already_read_notifications(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = $this->makeProject($owner);

        $owner->notify(new ProjectStatusUpdated($project->fresh()));
        $readNotification = $owner->notifications()->latest()->first();
        $readNotification->markAsRead();

        $project->update(['statut' => 'en_cours_de_mutualisation']);
        $owner->notify(new ProjectStatusUpdated($project->fresh()));

        $component = Livewire::actingAs($owner)->test(NotificationCenter::class)->call('toggle');

        $component->assertSee('En etude')->assertSee('En cours de mutualisation');

        $component->call('setFilter', 'unread')
            ->assertDontSee('En etude')
            ->assertSee('En cours de mutualisation');
    }

    public function test_mark_all_as_read_clears_the_unread_badge(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = $this->makeProject($owner);
        $owner->notify(new ProjectStatusUpdated($project->fresh()));

        $this->assertSame(1, $owner->unreadNotifications()->count());

        Livewire::actingAs($owner)
            ->test(NotificationCenter::class)
            ->call('markAllAsRead');

        $this->assertSame(0, $owner->unreadNotifications()->count());
    }

    public function test_opening_a_notification_marks_it_read_and_redirects_to_its_target(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = $this->makeProject($owner);
        $owner->notify(new ProjectStatusUpdated($project->fresh()));
        $notification = $owner->notifications()->firstOrFail();

        Livewire::actingAs($owner)
            ->test(NotificationCenter::class)
            ->call('open', $notification->id)
            ->assertRedirect(route('projects.show', $project));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_changing_a_project_status_notifies_the_owner_and_is_visible_in_their_notification_center(): void
    {
        $admin = User::factory()->create(['role' => 'admin_systeme']);
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = $this->makeProject($owner);

        Livewire::actingAs($admin)
            ->test(ProjectReview::class)
            ->call('openTransition', $project->id)
            ->set('targetStatus', 'en_cours_de_mutualisation')
            ->call('updateStatus');

        $notification = $owner->notifications()->firstOrFail();
        $this->assertSame('Statut du projet mis à jour', $notification->data['title']);
        $this->assertSame(route('projects.show', $project), $notification->data['url']);

        Livewire::actingAs($owner)
            ->test(NotificationCenter::class)
            ->call('toggle')
            ->assertSee('Statut du projet mis à jour');
    }

    public function test_a_guest_sees_no_notifications(): void
    {
        Livewire::test(NotificationCenter::class)
            ->call('toggle')
            ->assertSee('Aucune notification');
    }
}
