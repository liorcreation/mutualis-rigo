<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\ProjectFaq;
use App\Models\Project;
use App\Models\ProjectQuestion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectFaqTest extends TestCase
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

    public function test_a_guest_can_view_the_faq_but_not_ask_a_question(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = $this->makeProject($owner);

        Livewire::test(ProjectFaq::class, ['project' => $project])
            ->assertSee($project->titre)
            ->assertDontSee('wire:submit="ask"', false);
    }

    public function test_an_authenticated_user_can_ask_a_public_question(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $visitor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);

        Livewire::actingAs($visitor)
            ->test(ProjectFaq::class, ['project' => $project])
            ->set('question', 'Quel est le budget total nécessaire ?')
            ->call('ask')
            ->assertHasNoErrors()
            ->assertSet('question', '')
            ->assertSee('Votre question a été publiée.');

        $question = ProjectQuestion::firstOrFail();
        $this->assertSame($visitor->id, $question->user_id);
        $this->assertSame('Quel est le budget total nécessaire ?', $question->question);
        $this->assertNull($question->answer);
    }

    public function test_a_question_must_be_at_least_five_characters(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $visitor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);

        Livewire::actingAs($visitor)
            ->test(ProjectFaq::class, ['project' => $project])
            ->set('question', 'Ok ?')
            ->call('ask')
            ->assertHasErrors(['question']);

        $this->assertSame(0, ProjectQuestion::count());
    }

    public function test_a_guest_cannot_submit_a_question_directly(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $project = $this->makeProject($owner);

        Livewire::test(ProjectFaq::class, ['project' => $project])
            ->set('question', 'Une question envoyée sans compte connecté ?')
            ->call('ask')
            ->assertForbidden();
    }

    public function test_the_project_owner_can_answer_a_question(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $visitor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $question = ProjectQuestion::create([
            'project_id' => $project->id,
            'user_id' => $visitor->id,
            'question' => 'Le matériel est-il fourni ?',
        ]);

        Livewire::actingAs($owner)
            ->test(ProjectFaq::class, ['project' => $project])
            ->call('startAnswer', $question->id)
            ->assertSet('answeringQuestionId', $question->id)
            ->set('answer', 'Oui, le matériel de base est fourni par le projet.')
            ->call('saveAnswer')
            ->assertHasNoErrors()
            ->assertSet('answeringQuestionId', null)
            ->assertSee('Réponse publiée.');

        $fresh = $question->fresh();
        $this->assertSame('Oui, le matériel de base est fourni par le projet.', $fresh->answer);
        $this->assertSame($owner->id, $fresh->answered_by);
        $this->assertNotNull($fresh->answered_at);
    }

    public function test_a_visitor_cannot_answer_someone_elses_question(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $visitor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $question = ProjectQuestion::create([
            'project_id' => $project->id,
            'user_id' => $visitor->id,
            'question' => 'Le matériel est-il fourni ?',
        ]);

        Livewire::actingAs($visitor)
            ->test(ProjectFaq::class, ['project' => $project])
            ->call('startAnswer', $question->id)
            ->assertForbidden();

        $this->assertNull($question->fresh()->answer);
    }

    public function test_the_owner_can_cancel_an_answer_in_progress(): void
    {
        $owner = User::factory()->create(['role' => 'chef_projet']);
        $visitor = User::factory()->create(['role' => 'personne_physique']);
        $project = $this->makeProject($owner);
        $question = ProjectQuestion::create([
            'project_id' => $project->id,
            'user_id' => $visitor->id,
            'question' => 'Le matériel est-il fourni ?',
        ]);

        Livewire::actingAs($owner)
            ->test(ProjectFaq::class, ['project' => $project])
            ->call('startAnswer', $question->id)
            ->set('answer', 'Brouillon de réponse')
            ->call('cancelAnswer')
            ->assertSet('answeringQuestionId', null)
            ->assertSet('answer', '');

        $this->assertNull($question->fresh()->answer);
    }
}
