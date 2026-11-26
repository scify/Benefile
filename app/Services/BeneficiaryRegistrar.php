<?php

namespace App\Services;

use App\Models\Benefiters_Tables_Models\Benefiter;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Creates a beneficiary: from the basic information form, or from the CSV import.
 *
 * The beneficiary and its other rows (languages, legal statuses, history) are
 * saved in one transaction: all or nothing. The model creates the psychosocial
 * folder (Benefiter::booted()).
 */
class BeneficiaryRegistrar
{
    /**
     * @param  array<string, mixed>  $attributes  The columns of the benefiters table.
     * @param  Closure(Benefiter): void|null  $withRows  Saves the other rows of the new beneficiary.
     */
    public function register(array $attributes, ?Closure $withRows = null): Benefiter
    {
        return DB::transaction(function () use ($attributes, $withRows): Benefiter {
            $benefiter = Benefiter::forceCreate($attributes);
            if ($withRows !== null) {
                $withRows($benefiter);
            }

            return $benefiter;
        });
    }
}
