<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public bool $confirmingDeletion = false;

    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-4">
    <header>
        <h2 class="text-lg font-black text-rose-300">
            {{ __('Supprimer le compte') }}
        </h2>

        <p class="mt-1 text-sm text-slate-400">
            {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Téléchargez au préalable toute information que vous souhaitez conserver.') }}
        </p>
    </header>

    <button
        type="button"
        wire:click="$set('confirmingDeletion', true)"
        class="rounded-2xl bg-rose-500/15 px-6 py-3 text-xs font-black uppercase tracking-widest text-rose-300 transition hover:bg-rose-500 hover:text-white"
    >{{ __('Supprimer le compte') }}</button>

    <x-ui.modal wireProperty="confirmingDeletion" onClose="$set('confirmingDeletion', false)" title="Êtes-vous sûr de vouloir supprimer votre compte ?">
        <form wire:submit="deleteUser" class="space-y-5">
            <p class="text-sm text-slate-400">
                {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Saisissez votre mot de passe pour confirmer la suppression définitive de votre compte.') }}
            </p>

            <label class="block">
                <span class="sr-only">{{ __('Mot de passe') }}</span>
                <x-ui.password-input id="delete-password" wireModel="password" placeholder="{{ __('Mot de passe') }}" :required="false" />
                @error('password')<span class="mt-2 block text-xs font-medium text-rose-400">{{ $message }}</span>@enderror
            </label>

            <div class="flex justify-end gap-3">
                <button type="button" wire:click="$set('confirmingDeletion', false)" class="rounded-2xl border border-white/10 px-5 py-3 text-xs font-bold text-slate-300 transition hover:bg-white/10 hover:text-white">
                    {{ __('Annuler') }}
                </button>
                <button type="submit" class="rounded-2xl bg-rose-500 px-5 py-3 text-xs font-black uppercase tracking-widest text-white transition hover:bg-rose-400">
                    {{ __('Supprimer le compte') }}
                </button>
            </div>
        </form>
    </x-ui.modal>
</section>
