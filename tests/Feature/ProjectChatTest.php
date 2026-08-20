<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ProjectChat;
use App\Models\Message;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectChatTest extends TestCase
{
    use RefreshDatabase;

    private function makeProject(User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'titre' => 'Projet de test',
            'description' => 'Description de test',
            'categorie' => 'test',
            'statut' => 'en_cours_de_mutualisation',
            'besoin_financier_target' => 100000,
            'besoin_financier_actuel' => 0,
        ]);
    }

    private function makeContribution(Project $project, User $contributor): MutualizationContribution
    {
        return MutualizationContribution::create([
            'project_id' => $project->id,
            'user_id' => $contributor->id,
            'type_apport' => 'competence',
            'description_apport' => 'Développeur Laravel',
            'statut' => 'valide',
        ]);
    }

    public function test_owner_can_open_a_conversation_with_a_contributor_and_send_a_message(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $contribution = $this->makeContribution($project, $contributor);

        Livewire::actingAs($owner)
            ->test(ProjectChat::class, [
                'project' => $project,
                'participant' => $contributor,
                'contributionId' => $contribution->id,
            ])
            ->set('content', 'Bonjour, merci pour votre contribution.')
            ->call('send')
            ->assertHasNoErrors()
            ->assertDispatched('message-sent');

        $message = Message::firstOrFail();
        $this->assertSame($owner->id, $message->sender_id);
        $this->assertSame($contributor->id, $message->receiver_id);
        $this->assertSame('Bonjour, merci pour votre contribution.', $message->content);
    }

    public function test_contributor_can_open_a_conversation_with_the_project_owner(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $this->makeContribution($project, $contributor);

        Livewire::actingAs($contributor)
            ->test(ProjectChat::class, ['project' => $project, 'participant' => $owner])
            ->set('content', 'Bonjour, j’ai une question.')
            ->call('send')
            ->assertHasNoErrors();

        $message = Message::firstOrFail();
        $this->assertSame($contributor->id, $message->sender_id);
        $this->assertSame($owner->id, $message->receiver_id);
    }

    public function test_a_user_without_a_contribution_cannot_message_the_owner(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $stranger = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);

        Livewire::actingAs($stranger)
            ->test(ProjectChat::class, ['project' => $project, 'participant' => $owner])
            ->assertForbidden();
    }

    public function test_a_user_cannot_open_a_conversation_with_themselves(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = $this->makeProject($owner);

        Livewire::actingAs($owner)
            ->test(ProjectChat::class, ['project' => $project, 'participant' => $owner])
            ->assertForbidden();
    }

    public function test_owner_cannot_use_a_contribution_id_belonging_to_someone_else(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributorA = User::factory()->create(['role' => 'personne_physique']);
        $contributorB = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $contributionOfA = $this->makeContribution($project, $contributorA);

        Livewire::actingAs($owner)
            ->test(ProjectChat::class, [
                'project' => $project,
                'participant' => $contributorB,
                'contributionId' => $contributionOfA->id,
            ])
            ->assertForbidden();
    }

    public function test_a_message_requires_content_or_an_attachment(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $this->makeContribution($project, $contributor);

        Livewire::actingAs($owner)
            ->test(ProjectChat::class, ['project' => $project, 'participant' => $contributor])
            ->set('content', '')
            ->call('send')
            ->assertHasErrors(['content']);

        $this->assertSame(0, Message::count());
    }

    public function test_sending_a_message_with_an_attachment_stores_the_file(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $this->makeContribution($project, $contributor);

        Livewire::actingAs($owner)
            ->test(ProjectChat::class, ['project' => $project, 'participant' => $contributor])
            ->set('attachment', UploadedFile::fake()->create('devis.pdf', 100, 'application/pdf'))
            ->call('send')
            ->assertHasNoErrors();

        $message = Message::firstOrFail();
        $this->assertNotNull($message->attachment_path);
        Storage::disk('public')->assertExists($message->attachment_path);
    }

    public function test_opening_the_conversation_marks_incoming_messages_as_read(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $contributor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $this->makeContribution($project, $contributor);

        $incoming = Message::create([
            'sender_id' => $contributor->id,
            'receiver_id' => $owner->id,
            'project_id' => $project->id,
            'content' => 'Message non lu',
        ]);

        $this->assertNull($incoming->read_at);

        Livewire::actingAs($owner)
            ->test(ProjectChat::class, ['project' => $project, 'participant' => $contributor])
            ->assertSee('Message non lu');

        $this->assertNotNull($incoming->fresh()->read_at);
    }
}
