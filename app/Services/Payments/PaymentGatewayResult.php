<?php

declare(strict_types=1);

namespace App\Services\Payments;

final readonly class PaymentGatewayResult
{
    /**
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public bool $successful,
        public ?string $gatewayReference,
        public array $raw,
        public ?string $failureReason = null,
    ) {}
}
