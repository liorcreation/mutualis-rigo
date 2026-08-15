<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Enums\ReservationStatus;
use App\Models\MaterialReservation;
use App\Models\MutualizationContribution;
use App\Notifications\NewReservationRequested;
use App\Notifications\ReservationStatusUpdated;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialReservationCenter extends Component
{
    use WithPagination;

    public string $activeTab = 'catalogue';

    // Formulaire de demande.
    public bool $showRequestForm = false;

    public ?int $selectedContributionId = null;

    public string $dateDebut = '';

    public string $dateFin = '';

    public string $quantite = '1';

    public string $commentaire = '';

    // Panneau de décision (validation/refus par le contributeur).
    public bool $showReview = false;

    public ?int $selectedReservationId = null;

    public string $decision = '';

    public string $commentaireValidation = '';

    protected $queryString = [
        'activeTab' => ['except' => 'catalogue'],
    ];

    public function selectTab(string $tab): void
    {
        if (in_array($tab, ['catalogue', 'mes-demandes', 'demandes-recues'], true)) {
            $this->activeTab = $tab;
            $this->resetPage();
        }
    }

    public function openRequestForm(int $contributionId): void
    {
        $contribution = MutualizationContribution::findOrFail($contributionId);

        abort_unless(Gate::allows('create', [MaterialReservation::class, $contribution]), 403);

        $this->resetValidation();
        $this->reset(['dateDebut', 'dateFin', 'commentaire']);
        $this->selectedContributionId = $contributionId;
        $this->quantite = '1';
        $this->showRequestForm = true;
    }

    public function closeRequestForm(): void
    {
        $this->showRequestForm = false;
        $this->resetValidation();
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['selectedContributionId', 'dateDebut', 'dateFin', 'quantite', 'commentaire'], true)) {
            $this->validateOnly($property, $this->requestRules());
        }
    }

    #[Computed]
    public function availability(): ?bool
    {
        if ($this->selectedContributionId === null || $this->dateDebut === '' || $this->dateFin === '') {
            return null;
        }

        if (strtotime($this->dateFin) < strtotime($this->dateDebut)) {
            return null;
        }

        return MaterialReservation::isAvailable($this->selectedContributionId, $this->dateDebut, $this->dateFin);
    }

    public function submitRequest(): void
    {
        $validated = $this->validate($this->requestRules());

        $contribution = MutualizationContribution::findOrFail($validated['selectedContributionId']);

        abort_unless(Gate::allows('create', [MaterialReservation::class, $contribution]), 403);

        if (! MaterialReservation::isAvailable($contribution->id, $validated['dateDebut'], $validated['dateFin'])) {
            $this->addError('dateFin', 'Ce matériel est déjà réservé sur une partie de cette période.');

            return;
        }

        $reservation = MaterialReservation::create([
            'contribution_id' => $contribution->id,
            'requested_by' => auth()->id(),
            'date_debut' => $validated['dateDebut'],
            'date_fin' => $validated['dateFin'],
            'quantite' => $validated['quantite'],
            'commentaire' => $validated['commentaire'] ?: null,
            'statut' => ReservationStatus::EN_ATTENTE->value,
        ]);

        $contribution->user?->notify(new NewReservationRequested($reservation->load(['contribution.project', 'requester'])));

        $this->closeRequestForm();
        $this->reset(['selectedContributionId', 'dateDebut', 'dateFin', 'quantite', 'commentaire']);
        $this->activeTab = 'mes-demandes';
        session()->flash('reservation-message', 'Votre demande de réservation a été envoyée au contributeur.');
    }

    public function cancel(int $reservationId): void
    {
        $reservation = MaterialReservation::findOrFail($reservationId);

        abort_unless(Gate::allows('cancel', $reservation), 403);
        abort_unless($reservation->statut === ReservationStatus::EN_ATTENTE, 422);

        $reservation->update(['statut' => ReservationStatus::ANNULEE->value]);

        session()->flash('reservation-message', 'Votre demande de réservation a été annulée.');
    }

    public function review(int $reservationId, string $decision): void
    {
        abort_unless(in_array($decision, ['validee', 'refusee'], true), 422);

        $reservation = MaterialReservation::with('contribution')->findOrFail($reservationId);
        abort_unless(Gate::allows('validate', $reservation), 403);

        $this->selectedReservationId = $reservation->id;
        $this->decision = $decision;
        $this->commentaireValidation = '';
        $this->resetValidation();
        $this->showReview = true;
    }

    public function closeReview(): void
    {
        $this->showReview = false;
        $this->resetValidation();
    }

    public function saveDecision(): void
    {
        $validated = $this->validate($this->decisionRules());

        $reservation = MaterialReservation::with(['contribution', 'requester'])->findOrFail($validated['selectedReservationId']);

        abort_unless(Gate::allows('validate', $reservation), 403);
        abort_unless($reservation->statut === ReservationStatus::EN_ATTENTE, 422);

        $reservation->update([
            'statut' => $validated['decision'],
            'commentaire_validation' => $validated['commentaireValidation'] ?: null,
        ]);

        $reservation->requester?->notify(new ReservationStatusUpdated($reservation));

        $this->closeReview();
        $this->reset(['selectedReservationId', 'decision', 'commentaireValidation']);
        session()->flash('reservation-message', $validated['decision'] === 'validee'
            ? 'Réservation validée. Le demandeur a été informé.'
            : 'Réservation refusée avec son motif.');
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function requestRules(): array
    {
        return [
            'selectedContributionId' => ['required', 'integer', 'exists:mutualization_contributions,id'],
            'dateDebut' => ['required', 'date', 'after_or_equal:today'],
            'dateFin' => ['required', 'date', 'after_or_equal:dateDebut'],
            'quantite' => ['required', 'integer', 'min:1'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, list<mixed>>
     */
    protected function decisionRules(): array
    {
        return [
            'selectedReservationId' => ['required', 'integer', 'exists:material_reservations,id'],
            'decision' => ['required', Rule::in(['validee', 'refusee'])],
            'commentaireValidation' => [
                Rule::requiredIf(fn (): bool => $this->decision === 'refusee'),
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function render(): View
    {
        $availableMaterials = MutualizationContribution::query()
            ->where('type_apport', ContributionType::MATERIEL->value)
            ->where('statut', ContributionStatus::VALIDE->value)
            ->where('user_id', '!=', auth()->id())
            ->with(['project', 'user'])
            ->latest()
            ->paginate(9, pageName: 'cataloguePage');

        $myRequests = MaterialReservation::query()
            ->where('requested_by', auth()->id())
            ->with(['contribution.project', 'contribution.user'])
            ->latest()
            ->paginate(10, pageName: 'mesDemandesPage');

        $receivedRequests = MaterialReservation::query()
            ->whereHas('contribution', fn ($query) => $query->where('user_id', auth()->id()))
            ->with(['contribution.project', 'requester'])
            ->latest()
            ->paginate(10, pageName: 'demandesRecuesPage');

        return view('livewire.material-reservation-center', [
            'availableMaterials' => $availableMaterials,
            'myRequests' => $myRequests,
            'receivedRequests' => $receivedRequests,
        ])->layout('layouts.app');
    }
}
