<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Message;
use App\Models\MutualizationContribution;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProjectChat extends Component
{
    use WithFileUploads;

    public Project $project;

    public User $participant;

    public ?int $contributionId = null;

    public string $content = '';

    public $attachment;

    public function mount(Project $project, User $participant, ?int $contributionId = null): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->canAccessConversation($project, $participant, $contributionId), 403);

        $this->project = $project;
        $this->participant = $participant;
        $this->contributionId = $contributionId;
        $this->markMessagesAsRead();
    }

    public function send(): void
    {
        $validated = $this->validate();
        $attachmentPath = $this->attachment?->store('project-messages', 'public');

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->participant->id,
            'project_id' => $this->project->id,
            'contribution_id' => $this->contributionId,
            'content' => trim((string) ($validated['content'] ?? '')),
            'attachment_path' => $attachmentPath,
        ]);

        $this->reset(['content', 'attachment']);
        $this->resetValidation();
        $this->dispatch('message-sent');
    }

    public function refreshMessages(): void
    {
        $this->markMessagesAsRead();
    }

    protected function rules(): array
    {
        return [
            'content' => ['nullable', 'string', 'max:5000', 'required_without:attachment'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,csv,txt,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function render(): View
    {
        $messages = Message::query()
            ->with('sender')
            ->where('project_id', $this->project->id)
            ->where(function ($query): void {
                $query->where(function ($query): void {
                    $query->where('sender_id', auth()->id())
                        ->where('receiver_id', $this->participant->id);
                })->orWhere(function ($query): void {
                    $query->where('sender_id', $this->participant->id())
                        ->where('receiver_id', auth()->id());
                });
            })
            ->oldest()
            ->get();

        return view('livewire.project-chat', [
            'messages' => $messages,
        ])->layout('layouts.app');
    }

    private function markMessagesAsRead(): void
    {
        Message::query()
            ->where('project_id', $this->project->id)
            ->where('sender_id', $this->participant->id)
            ->where('receiver_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function canAccessConversation(Project $project, User $participant, ?int $contributionId): bool
    {
        $currentUserId = auth()->id();

        if ($participant->id === $currentUserId) {
            return false;
        }

        if ($project->user_id === $currentUserId) {
            return $contributionId === null || MutualizationContribution::query()
                ->whereKey($contributionId)
                ->where('project_id', $project->id)
                ->where('user_id', $participant->id)
                ->exists();
        }

        return $participant->id === $project->user_id
            && MutualizationContribution::query()
                ->where('project_id', $project->id)
                ->where('user_id', $currentUserId)
                ->exists();
    }
}
