<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ContractStatus;
use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Models\Contract;
use App\Models\MutualizationContribution;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Formalise une contribution financière validée en contrat signé.
 */
class ContractService
{
    public function createFromContribution(MutualizationContribution $contribution, User $user): Contract
    {
        abort_unless($contribution->user_id === $user->id, 403);
        abort_unless($contribution->type_apport === ContributionType::FINANCIER, 422);
        abort_unless($contribution->statut === ContributionStatus::VALIDE, 422);

        return DB::transaction(fn (): Contract => Contract::query()->firstOrCreate(
            ['contribution_id' => $contribution->id],
            [
                'project_id' => $contribution->project_id,
                'user_id' => $contribution->user_id,
                'status' => ContractStatus::PENDING_SIGNATURE,
                'amount' => $contribution->montant,
            ],
        ));
    }

    public function sign(Contract $contract, User $user, string $ip): Contract
    {
        abort_unless($contract->user_id === $user->id, 403);
        abort_unless($contract->status === ContractStatus::PENDING_SIGNATURE, 422);
        abort_if($contract->terms_accepted_at !== null, 422);

        return DB::transaction(function () use ($contract, $user, $ip): Contract {
            $signedAt = now();
            $documentHash = hash('sha256', implode('|', [
                $contract->contract_number,
                $user->id,
                (string) $contract->amount,
                $signedAt->toIso8601String(),
                $ip,
            ]));

            $contract->update([
                'terms_accepted_at' => $signedAt,
                'signature_ip' => $ip,
                'document_hash' => $documentHash,
                'signed_at' => $signedAt,
            ]);

            return $contract->fresh();
        });
    }

    public function activate(Contract $contract): Contract
    {
        return DB::transaction(function () use ($contract): Contract {
            $contract->update(['status' => ContractStatus::ACTIVE]);

            app(ContractPdfService::class)->generate($contract);
            app(ProjectMutualizationService::class)->recalculate($contract->project);

            return $contract->fresh();
        });
    }
}
