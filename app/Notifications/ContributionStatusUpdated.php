<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\MutualizationContribution;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionStatusUpdated extends Notification
{
    public function __construct(public MutualizationContribution $contribution) {}

    /**
     * Les canaux sont configurables via RIGO_NOTIFICATION_CHANNELS.
     * Exemple : database,mail ou database uniquement.
     *
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return config('rigo.notification_channels', ['database']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isApproved = $this->contribution->statut->value === 'valide';

        return (new MailMessage)
            ->subject($isApproved ? 'Votre contribution a été validée' : 'Votre contribution a été refusée')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($isApproved
                ? 'Votre contribution au projet « '.$this->contribution->project?->titre.' » a été validée.'
                : 'Votre contribution au projet « '.$this->contribution->project?->titre.' » a été refusée.')
            ->when($this->contribution->commentaire_validation, function (MailMessage $mail): void {
                $mail->line('Commentaire : '.$this->contribution->commentaire_validation);
            })
            ->action('Voir mon espace', route('dashboard'))
            ->line('Merci de participer à la mutualisation RIGO.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isApproved = $this->contribution->statut->value === 'valide';

        return [
            'title' => $isApproved ? 'Contribution validée' : 'Contribution refusée',
            'message' => $isApproved
                ? 'Votre apport a été validé pour « '.$this->contribution->project?->titre.' ». '
                : 'Votre apport a été refusé pour « '.$this->contribution->project?->titre.' ». ',
            'comment' => $this->contribution->commentaire_validation,
            'status' => $this->contribution->statut->value,
            'url' => route('dashboard'),
        ];
    }
}
