<?php

declare(strict_types=1);

namespace App\Services\Payments;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Payment;

/**
 * Simule un prestataire de paiement (Mobile Money / carte) en attendant une
 * intégration réelle (CinetPay, PayDunya...). Toujours remplaçable via le
 * binding de PaymentGatewayInterface dans AppServiceProvider.
 */
class MockPaymentGateway implements PaymentGatewayInterface
{
    public function charge(Payment $payment, array $context): PaymentGatewayResult
    {
        return new PaymentGatewayResult(
            successful: true,
            gatewayReference: 'MOCK-'.strtoupper(uniqid()),
            raw: [
                'simulated' => true,
                'method' => $payment->method,
                'amount' => (string) $payment->amount,
                'currency' => $payment->currency,
                'processed_at' => now()->toIso8601String(),
            ],
        );
    }
}
