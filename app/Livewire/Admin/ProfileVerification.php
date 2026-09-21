<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Profile;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * File de contrôle des profils avant accès aux apports sensibles.
 */
class ProfileVerification extends Component
{
    use WithPagination;

    public string $filter = 'pending';

    public function mount(): void
    {
        abort_unless(auth()->user()?->role?->canVerifyProfiles(), 403);
    }

    public function updatedFilter(): void
    {
        abort_unless(in_array($this->filter, ['pending', 'verified', 'all'], true), 422);
        $this->resetPage();
    }

    public function setVerification(int $profileId, bool $verified): void
    {
        abort_unless(auth()->user()?->role?->canVerifyProfiles(), 403);

        $profile = Profile::query()->findOrFail($profileId);
        $profile->update(['is_verified' => $verified]);

        session()->flash(
            'verification-message',
            $verified ? 'Profil vérifié avec succès.' : 'Profil retiré de la liste des profils vérifiés.',
        );
    }

    public function render(): View
    {
        abort_unless(auth()->user()?->role?->canVerifyProfiles(), 403);

        $profiles = Profile::query()
            ->with('user')
            ->when($this->filter === 'pending', fn ($query) => $query->where('is_verified', false))
            ->when($this->filter === 'verified', fn ($query) => $query->where('is_verified', true))
            ->latest()
            ->paginate(12);

        return view('livewire.admin.profile-verification', compact('profiles'))
            ->layout('layouts.app');
    }
}
