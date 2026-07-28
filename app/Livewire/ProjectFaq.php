<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProjectQuestion;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ProjectFaq extends Component
{
    public Project $project;

    public string $question = '';

    public ?int $answeringQuestionId = null;

    public string $answer = '';

    public function mount(Project $project): void
    {
        $this->project = $project->load('user.profile');
    }

    public function ask(): void
    {
        abort_unless(auth()->check(), 403);

        $validated = $this->validateOnly('question');

        ProjectQuestion::create([
            'project_id' => $this->project->id,
            'user_id' => auth()->id(),
            'question' => $validated['question'],
        ]);

        $this->reset('question');
        session()->flash('faq-message', 'Votre question a été publiée.');
    }

    public function startAnswer(int $questionId): void
    {
        abort_unless(auth()->id() === $this->project->user_id, 403);

        $this->answeringQuestionId = $questionId;
        $this->answer = '';
        $this->resetValidation();
    }

    public function cancelAnswer(): void
    {
        $this->answeringQuestionId = null;
        $this->answer = '';
        $this->resetValidation();
    }

    public function saveAnswer(): void
    {
        abort_unless(auth()->id() === $this->project->user_id, 403);

        $validated = $this->validateOnly('answer');
        $question = ProjectQuestion::where('project_id', $this->project->id)->findOrFail($this->answeringQuestionId);

        $question->update([
            'answer' => $validated['answer'],
            'answered_by' => auth()->id(),
            'answered_at' => now(),
        ]);

        $this->cancelAnswer();
        session()->flash('faq-message', 'Réponse publiée.');
    }

    public function render(): View
    {
        return view('livewire.project-faq', [
            'questions' => ProjectQuestion::query()
                ->with(['user', 'answerer'])
                ->where('project_id', $this->project->id)
                ->latest()
                ->get(),
        ])->layout('layouts.app');
    }

    protected function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:5', 'max:1000'],
            'answer' => ['required', 'string', 'min:2', 'max:2000'],
        ];
    }
}
