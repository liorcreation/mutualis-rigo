<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class NotificationCenter extends Component
{
    public bool $open = false;

    public string $filter = 'all';

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function setFilter(string $filter): void
    {
        $this->filter = in_array($filter, ['all', 'unread'], true) ? $filter : 'all';
    }

    /**
     * Marque la notification comme lue puis redirige vers l'élément concerné.
     */
    public function open(string $notificationId): void
    {
        $notification = auth()->user()?->notifications()->whereKey($notificationId)->first();

        if ($notification === null) {
            return;
        }

        $notification->markAsRead();
        $this->close();

        $url = $notification->data['url'] ?? null;

        if (is_string($url) && $url !== '') {
            $this->redirect($url, navigate: true);
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()?->unreadNotifications->markAsRead();
    }

    public function render(): View
    {
        $user = auth()->user();

        $notifications = $user
            ?->notifications()
            ->when($this->filter === 'unread', fn ($query) => $query->whereNull('read_at'))
            ->latest()
            ->limit(8)
            ->get() ?? collect();

        return view('livewire.notification-center', [
            'notifications' => $notifications,
            'unreadCount' => $user?->unreadNotifications()->count() ?? 0,
        ]);
    }
}
