<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentHistory extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';

    public string $search = '';

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $payments = Payment::query()
            ->with(['contract.project'])
            ->when(! $this->isFinanceOrManagement(), fn ($query) => $query->where('user_id', auth()->id()))
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('reference', 'like', "%{$this->search}%")
                        ->orWhereHas('contract', fn ($query) => $query->where('contract_number', 'like', "%{$this->search}%"));
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.payment-history', [
            'payments' => $payments,
            'statuses' => PaymentStatus::cases(),
        ])->layout('layouts.app');
    }

    private function isFinanceOrManagement(): bool
    {
        return match (auth()->user()?->role) {
            UserRole::RESPONSABLE_FINANCIER,
            UserRole::TOP_MANAGEMENT,
            UserRole::ADMIN_SYSTEME => true,
            default => false,
        };
    }
}
