<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\FinancialLedgerEntry;
use App\Models\Project;
use App\Services\FinancialPoolService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Vue finance du registre de mutualisation.
 */
class FinancialPool extends Component
{
    use WithPagination;

    public ?int $sourceProjectId = null;

    public ?int $destinationProjectId = null;

    public string $amount = '';

    public string $reference = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->role?->canManageFinancialPool(), 403);
    }

    public function transfer(FinancialPoolService $pool): void
    {
        $validated = $this->validate();

        $pool->transfer(
            Project::query()->findOrFail($validated['sourceProjectId']),
            Project::query()->findOrFail($validated['destinationProjectId']),
            (float) $validated['amount'],
            auth()->user(),
            $validated['reference'] !== '' ? $validated['reference'] : null,
        );

        $this->reset(['sourceProjectId', 'destinationProjectId', 'amount', 'reference']);
        session()->flash('finance-message', 'Transfert enregistré et scellé dans le registre financier.');
    }

    public function verifyLedger(FinancialPoolService $pool): void
    {
        abort_unless(auth()->user()?->role?->canManageFinancialPool(), 403);
        $result = $pool->verifyIntegrity();

        session()->flash(
            'finance-message',
            $result['valid']
                ? sprintf('Intégrité confirmée : %d écriture(s) vérifiée(s).', $result['entries'])
                : sprintf('Alerte : l’écriture #%d ne correspond plus à sa signature.', $result['invalid_id']),
        );
    }

    protected function rules(): array
    {
        return [
            'sourceProjectId' => ['required', 'integer', 'exists:projets,id', 'different:destinationProjectId'],
            'destinationProjectId' => ['required', 'integer', 'exists:projets,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'reference' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function render(FinancialPoolService $pool): View
    {
        abort_unless(auth()->user()?->role?->canManageFinancialPool(), 403);

        $projects = Project::query()->orderBy('titre')->get();
        $projectBalances = $projects->mapWithKeys(fn (Project $project): array => [
            $project->id => $pool->balance($project),
        ]);

        return view('livewire.admin.financial-pool', [
            'projects' => $projects,
            'projectBalances' => $projectBalances,
            'summary' => $pool->summary(),
            'ledger' => FinancialLedgerEntry::query()
                ->with(['sourceProject', 'destinationProject', 'creator'])
                ->latest('id')
                ->paginate(12),
        ])->layout('layouts.app');
    }
}
