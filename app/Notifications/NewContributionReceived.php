<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\MutualizationContribution;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContributionReceived extends Notification
{
    public function __construct(public MutualizationContribution $contribution) {}

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
            ->subject('Un nouvel apport est proposé pour votre projet')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Un utilisateur propose un apport pour votre projet « '.$this->contribution->project?->titre.' ».')
            ->line('Type : '.$this->contribution->type_apport->value)
            ->action('Ouvrir mon espace', route('dashboard'))
            ->line('Vous serez informé après la validation de cet apport.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouvel apport reçu',
            'message' => 'Un utilisateur propose un apport pour « '.$this->contribution->project?->titre.' ».',
            'type' => $this->contribution->type_apport->value,
            'url' => route('dashboard'),
        ];
    }
}
