<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->id === $payment->user_id || match ($user->role) {
            UserRole::RESPONSABLE_FINANCIER,
            UserRole::TOP_MANAGEMENT,
            UserRole::ADMIN_SYSTEME => true,
            default => false,
        };
    }
}
