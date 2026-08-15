<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\ReservationStatus;
use App\Models\MaterialReservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationStatusUpdated extends Notification
{
    public function __construct(public MaterialReservation $reservation) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return config('rigo.notification_channels', ['database']);
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isApproved = $this->reservation->statut === ReservationStatus::VALIDEE;

        return (new MailMessage)
            ->subject($isApproved ? 'Votre réservation a été validée' : 'Votre réservation a été refusée')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($isApproved
                ? 'Votre demande de réservation de matériel pour « '.$this->reservation->contribution?->project?->titre.' » a été validée.'
                : 'Votre demande de réservation de matériel pour « '.$this->reservation->contribution?->project?->titre.' » a été refusée.')
            ->when($this->reservation->commentaire_validation, function (MailMessage $mail): void {
                $mail->line('Commentaire : '.$this->reservation->commentaire_validation);
            })
            ->action('Voir mes réservations', route('material.reservations'))
            ->line('Merci de participer à la mutualisation RIGO.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isApproved = $this->reservation->statut === ReservationStatus::VALIDEE;

        return [
            'title' => $isApproved ? 'Réservation validée' : 'Réservation refusée',
            'message' => $isApproved
                ? 'Votre demande de réservation a été validée pour « '.$this->reservation->contribution?->project?->titre.' ».'
                : 'Votre demande de réservation a été refusée pour « '.$this->reservation->contribution?->project?->titre.' ».',
            'comment' => $this->reservation->commentaire_validation,
            'status' => $this->reservation->statut->value,
            'url' => route('material.reservations'),
        ];
    }
}
