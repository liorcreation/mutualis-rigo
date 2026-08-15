<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\MaterialReservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReservationRequested extends Notification
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
        $reservation = $this->reservation;

        return (new MailMessage)
            ->subject('Une demande de réservation pour votre matériel')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($reservation->requester?->name.' souhaite réserver votre matériel du projet « '.$reservation->contribution?->project?->titre.' ».')
            ->line('Période demandée : du '.$reservation->date_debut->format('d/m/Y').' au '.$reservation->date_fin->format('d/m/Y').'.')
            ->action('Traiter la demande', route('material.reservations'))
            ->line('Vous pouvez valider ou refuser cette demande depuis votre espace.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $reservation = $this->reservation;

        return [
            'title' => 'Nouvelle demande de réservation',
            'message' => $reservation->requester?->name.' souhaite réserver votre matériel pour « '.$reservation->contribution?->project?->titre.' ».',
            'url' => route('material.reservations'),
        ];
    }
}
