<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Payment;
use App\Services\Payments\PaymentGatewayResult;

interface PaymentGatewayInterface
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function charge(Payment $payment, array $context): PaymentGatewayResult;
}
