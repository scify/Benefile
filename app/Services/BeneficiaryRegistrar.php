<?php

namespace App\Services;

use App\Models\Beneficiary;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * Creates a beneficiary: from the basic information form, or from the CSV import.
 *
 * The beneficiary and its other rows (languages, legal statuses, history) are
 * saved in one transaction: all or nothing. The model creates the psychosocial
 * folder (Beneficiary::booted()).
 */
class BeneficiaryRegistrar
{
    /**
     * @param  array<string, mixed>  $attributes  The columns of the benefiters table.
     * @param  Closure(Beneficiary): void|null  $withRows  Saves the other rows of the new beneficiary.
     */
    public function register(array $attributes, ?Closure $withRows = null): Beneficiary
    {
        return DB::transaction(function () use ($attributes, $withRows): Beneficiary {
            $benefiter = Beneficiary::forceCreate($attributes);
            if ($withRows !== null) {
                $withRows($benefiter);
            }

            return $benefiter;
        });
    }
}
