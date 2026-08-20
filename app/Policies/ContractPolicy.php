<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\ContractStatus;
use App\Models\Contract;
use App\Models\User;

class ContractPolicy
{
    public function view(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id
            || $user->id === $contract->project->user_id
            || $user->isFinanceOrManagement();
    }

    public function sign(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id
            && $contract->status === ContractStatus::PENDING_SIGNATURE
            && $contract->terms_accepted_at === null;
    }

    public function pay(User $user, Contract $contract): bool
    {
        return $user->id === $contract->user_id
            && $contract->signed_at !== null
            && ! in_array($contract->status, [ContractStatus::ACTIVE, ContractStatus::COMPLETED], true);
    }

    public function download(User $user, Contract $contract): bool
    {
        return $this->view($user, $contract) && $contract->pdf_path !== null;
    }
}
