<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ContractStatus;
use App\Enums\ContributionStatus;
use App\Enums\ContributionType;
use App\Models\Contract;
use App\Models\FinancialLedgerEntry;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Registre financier de Mutualis.
 *
 * Chaque écriture est ajoutée dans une chaîne SHA-256. Les transferts sont
 * exécutés dans une transaction et les projets concernés sont verrouillés
 * avant le contrôle du solde, ce qui évite le double emploi concurrent.
 */
class FinancialPoolService
{
    public const CONTRIBUTION = 'contribution';

    public const TRANSFER = 'transfer';

    public const GENESIS_HASH = 'INITIAL_HASH_FINANCIAL_POOL_MUTUALIS_2026';

    public function recordPaidContribution(Contract $contract): FinancialLedgerEntry
    {
        $contract->loadMissing('project', 'user', 'contribution');

        return DB::transaction(function () use ($contract): FinancialLedgerEntry {
            $reference = 'CONTRACT:'.$contract->contract_number;
            $existing = FinancialLedgerEntry::query()->where('reference', $reference)->first();

            if ($existing !== null) {
                return $existing;
            }

            return $this->append([
                'created_by' => $contract->user_id,
                'destination_project_id' => $contract->project_id,
                'entry_type' => self::CONTRIBUTION,
                'amount' => (float) $contract->amount,
                'reference' => $reference,
                'metadata' => [
                    'contract_number' => $contract->contract_number,
                    'contribution_id' => $contract->contribution_id,
                    'payment_source' => 'contract_payment',
                ],
            ]);
        });
    }

    public function transfer(
        Project $source,
        Project $destination,
        float $amount,
        User $actor,
        ?string $reference = null,
    ): FinancialLedgerEntry {
        if ($source->is($destination)) {
            throw ValidationException::withMessages([
                'destination_project_id' => 'Le projet source et le projet destinataire doivent être différents.',
            ]);
        }

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Le montant du transfert doit être supérieur à zéro.',
            ]);
        }

        return DB::transaction(function () use ($source, $destination, $amount, $actor, $reference): FinancialLedgerEntry {
            $ids = [$source->id, $destination->id];
            sort($ids);
            Project::query()->whereKey($ids[0])->lockForUpdate()->firstOrFail();
            Project::query()->whereKey($ids[1])->lockForUpdate()->firstOrFail();

            $available = $this->balance($source);

            if ($amount > $available) {
                throw ValidationException::withMessages([
                    'amount' => sprintf(
                        'Solde insuffisant. Le solde disponible du projet source est de %.2f.',
                        $available,
                    ),
                ]);
            }

            $entry = $this->append([
                'created_by' => $actor->id,
                'source_project_id' => $source->id,
                'destination_project_id' => $destination->id,
                'entry_type' => self::TRANSFER,
                'amount' => round($amount, 2),
                'reference' => $reference ?: 'TRANSFER:'.str()->uuid(),
                'metadata' => [
                    'source_title' => $source->titre,
                    'destination_title' => $destination->titre,
                ],
            ]);

            $mutualization = app(ProjectMutualizationService::class);
            $mutualization->recalculate($source->fresh());
            $mutualization->recalculate($destination->fresh());

            return $entry;
        });
    }

    public function balance(Project $project): float
    {
        $paidContributions = (float) $project->contributions()
            ->where('type_apport', ContributionType::FINANCIER->value)
            ->where('statut', ContributionStatus::VALIDE->value)
            ->whereHas('contract', fn ($query) => $query->where('status', ContractStatus::ACTIVE->value))
            ->sum('montant');

        $incomingTransfers = (float) FinancialLedgerEntry::query()
            ->where('entry_type', self::TRANSFER)
            ->where('destination_project_id', $project->id)
            ->sum('amount');

        $outgoingTransfers = (float) FinancialLedgerEntry::query()
            ->where('entry_type', self::TRANSFER)
            ->where('source_project_id', $project->id)
            ->sum('amount');

        return round($paidContributions + $incomingTransfers - $outgoingTransfers, 2);
    }

    /**
     * Contrôle toute la chaîne et retourne le premier index invalide éventuel.
     *
     * @return array{valid: bool, invalid_id: int|null, entries: int}
     */
    public function verifyIntegrity(): array
    {
        $previousHash = self::GENESIS_HASH;
        $entries = FinancialLedgerEntry::query()->oldest('id')->get();

        foreach ($entries as $entry) {
            $payload = $this->payloadForEntry($entry);
            $expectedHash = hash('sha256', $previousHash.$payload);

            if ($entry->hash_parent !== $previousHash || ! hash_equals($expectedHash, $entry->hash_actuel)) {
                return [
                    'valid' => false,
                    'invalid_id' => $entry->id,
                    'entries' => $entries->count(),
                ];
            }

            $previousHash = $entry->hash_actuel;
        }

        return [
            'valid' => true,
            'invalid_id' => null,
            'entries' => $entries->count(),
        ];
    }

    /**
     * @return array{funded: float, transferred: float, entries: int}
     */
    public function summary(): array
    {
        return [
            'funded' => (float) FinancialLedgerEntry::query()
                ->where('entry_type', self::CONTRIBUTION)
                ->sum('amount'),
            'transferred' => (float) FinancialLedgerEntry::query()
                ->where('entry_type', self::TRANSFER)
                ->sum('amount'),
            'entries' => FinancialLedgerEntry::count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function append(array $attributes): FinancialLedgerEntry
    {
        $lastEntry = FinancialLedgerEntry::query()->latest('id')->lockForUpdate()->first();
        $parentHash = $lastEntry?->hash_actuel ?? self::GENESIS_HASH;
        $timestamp = now();

        $entry = FinancialLedgerEntry::create([
            ...$attributes,
            'hash_parent' => $parentHash,
            'hash_actuel' => 'PENDING',
            'enregistre_le' => $timestamp,
        ]);

        // L'identifiant fait partie de la signature : il est connu après
        // l'insertion. La mise à jour interne reste dans la même transaction
        // et n'est jamais exposée comme une modification métier.
        $hash = hash('sha256', $parentHash.$this->payloadForEntry($entry));
        DB::table('financial_ledger_entries')->where('id', $entry->id)->update([
            'hash_actuel' => $hash,
        ]);
        $entry->hash_actuel = $hash;

        return $entry;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function payload(array $payload): string
    {
        return json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    private function payloadForEntry(FinancialLedgerEntry $entry): string
    {
        return $this->payload([
            'id' => $entry->id,
            'created_by' => $entry->created_by,
            'source_project_id' => $entry->source_project_id,
            'destination_project_id' => $entry->destination_project_id,
            'entry_type' => $entry->entry_type,
            'amount' => number_format((float) $entry->amount, 2, '.', ''),
            'reference' => $entry->reference,
            'metadata' => $entry->metadata,
            'enregistre_le' => $entry->enregistre_le?->toIso8601String(),
        ]);
    }
}
