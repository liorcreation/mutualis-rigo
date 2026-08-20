<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectStatusUpdated extends Notification
{
    public function __construct(public Project $project) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return config('rigo.notification_channels', ['database']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Le statut de votre projet a changé')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Votre projet « '.$this->project->titre.' » est maintenant « '.$this->statusLabel().' ».')
            ->action('Voir le projet', route('projects.show', $this->project))
            ->line('Merci de participer à la mutualisation RIGO.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Statut du projet mis à jour',
            'message' => 'Votre projet « '.$this->project->titre.' » est maintenant « '.$this->statusLabel().' ».',
            'status' => $this->project->statut->value,
            'url' => route('projects.show', $this->project),
        ];
    }

    private function statusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->project->statut->value));
    }
}
