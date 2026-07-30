<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Génère des numéros de référence uniques du type PREFIX-2026-XXXXXX.
 */
class ReferenceNumberGenerator
{
    private const MAX_ATTEMPTS = 10;

    public static function generate(string $table, string $column, string $prefix): string
    {
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $candidate = sprintf('%s-%s-%06d', $prefix, date('Y'), random_int(0, 999999));

            if (! DB::table($table)->where($column, $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new RuntimeException("Impossible de générer un numéro unique pour {$table}.{$column} après ".self::MAX_ATTEMPTS.' tentatives.');
    }
}
