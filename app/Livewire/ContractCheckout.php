<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ContractStatus;
use App\Enums\PaymentStatus;
use App\Models\Contract;
use App\Models\MutualizationContribution;
use App\Services\ContractService;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ContractCheckout extends Component
{
    public Contract $contract;

    public bool $accepted = false;

    public string $paymentMethod = 'mobile_money';

    public function mount(MutualizationContribution $contribution, ContractService $contracts): void
    {
        $this->contract = $contracts->createFromContribution($contribution, auth()->user());
    }

    public function signContract(ContractService $contracts): void
    {
        Gate::authorize('sign', $this->contract);

        $this->validate(['accepted' => ['accepted']]);

        $this->contract = $contracts->sign($this->contract, auth()->user(), request()->ip() ?? '0.0.0.0');
    }

    public function pay(PaymentService $payments, string $method): void
    {
        Gate::authorize('pay', $this->contract);

        $this->paymentMethod = $method;

        $validated = $this->validate([
            'paymentMethod' => ['required', Rule::in(['mobile_money', 'carte'])],
        ]);

        $payments->charge($this->contract, auth()->user(), $validated['paymentMethod']);

        $this->contract = $this->contract->fresh();
    }

    public function render(): View
    {
        return view('livewire.contract-checkout', [
            'contractStatus' => ContractStatus::class,
            'latestPayment' => $this->contract->payments()->latest()->first(),
            'paymentFailed' => $this->contract->payments()->latest()->first()?->status === PaymentStatus::FAILED,
        ])->layout('layouts.app');
    }
}
