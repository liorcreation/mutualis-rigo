<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\UserRole;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

/**
 * Données métier utilisées lors du contrôle d'un profil.
 */
class UpdateProfileDetails extends Component
{
    public string $prenom = '';

    public string $nom = '';

    public string $titreProfessionnel = '';

    public string $competencesText = '';

    public string $biographie = '';

    public string $nomEntreprise = '';

    public string $rneSiret = '';

    public string $secteurActivite = '';

    public string $representantLegal = '';

    public string $siteWeb = '';

    public bool $isVerified = false;

    public function mount(): void
    {
        $profile = Auth::user()->profile;

        if ($profile === null) {
            $profile = Auth::user()->profile()->create();
        }

        $this->fill([
            'prenom' => (string) ($profile->prenom ?? ''),
            'nom' => (string) ($profile->nom ?? ''),
            'titreProfessionnel' => (string) ($profile->titre_professionnel ?? ''),
            'competencesText' => implode(', ', $profile->competences ?? []),
            'biographie' => (string) ($profile->biographie ?? ''),
            'nomEntreprise' => (string) ($profile->nom_entreprise ?? ''),
            'rneSiret' => (string) ($profile->rne_siret ?? ''),
            'secteurActivite' => (string) ($profile->secteur_activite ?? ''),
            'representantLegal' => (string) ($profile->representant_legal ?? ''),
            'siteWeb' => (string) ($profile->site_web ?? ''),
            'isVerified' => (bool) $profile->is_verified,
        ]);
    }

    public function save(): void
    {
        $validated = $this->validate();
        $profile = Auth::user()->profile()->firstOrCreate();

        $competences = collect(explode(',', $validated['competencesText']))
            ->map(fn (string $competence): string => trim($competence))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $profile->fill([
            'prenom' => $validated['prenom'] ?: null,
            'nom' => $validated['nom'] ?: null,
            'titre_professionnel' => $validated['titreProfessionnel'] ?: null,
            'competences' => $competences,
            'biographie' => $validated['biographie'] ?: null,
            'nom_entreprise' => $validated['nomEntreprise'] ?: null,
            'rne_siret' => $validated['rneSiret'] ?: null,
            'secteur_activite' => $validated['secteurActivite'] ?: null,
            'representant_legal' => $validated['representantLegal'] ?: null,
            'site_web' => $validated['siteWeb'] ?: null,
        ]);

        if ($profile->isDirty()) {
            $profile->is_verified = false;
        }

        $profile->save();
        $this->isVerified = (bool) $profile->is_verified;
        $this->dispatch('profile-details-updated');
    }

    protected function rules(): array
    {
        $isCompany = Auth::user()->role === UserRole::PERSONNE_MORALE;

        return [
            'prenom' => [Rule::requiredIf(! $isCompany), 'nullable', 'string', 'max:100'],
            'nom' => [Rule::requiredIf(! $isCompany), 'nullable', 'string', 'max:100'],
            'titreProfessionnel' => ['nullable', 'string', 'max:150'],
            'competencesText' => ['nullable', 'string', 'max:1000'],
            'biographie' => ['nullable', 'string', 'max:3000'],
            'nomEntreprise' => [Rule::requiredIf($isCompany), 'nullable', 'string', 'max:255'],
            'rneSiret' => ['nullable', 'string', 'max:80'],
            'secteurActivite' => ['nullable', 'string', 'max:150'],
            'representantLegal' => ['nullable', 'string', 'max:150'],
            'siteWeb' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function render(): View
    {
        return view('livewire.profile.update-profile-details', [
            'isCompany' => Auth::user()->role === UserRole::PERSONNE_MORALE,
        ]);
    }
}
