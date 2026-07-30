<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\ContractStatus;
use App\Enums\PaymentStatus;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly ContractService $contracts,
    ) {}

    public function charge(Contract $contract, User $user, string $method): Payment
    {
        abort_unless($contract->user_id === $user->id, 403);
        abort_unless($contract->signed_at !== null, 422);
        abort_if($contract->status === ContractStatus::ACTIVE || $contract->status === ContractStatus::COMPLETED, 422);

        return DB::transaction(function () use ($contract, $user, $method): Payment {
            $payment = Payment::create([
                'contract_id' => $contract->id,
                'user_id' => $user->id,
                'status' => PaymentStatus::PENDING,
                'amount' => $contract->amount,
                'currency' => $contract->currency,
                'method' => $method,
            ]);

            $result = $this->gateway->charge($payment, ['contract' => $contract]);

            $payment->update([
                'status' => $result->successful ? PaymentStatus::SUCCESSFUL : PaymentStatus::FAILED,
                'gateway_reference' => $result->gatewayReference,
                'gateway_response' => $result->raw,
                'paid_at' => $result->successful ? now() : null,
                'failed_reason' => $result->failureReason,
            ]);

            if ($result->successful) {
                $this->contracts->activate($contract);
            }

            return $payment->fresh();
        });
    }
}
