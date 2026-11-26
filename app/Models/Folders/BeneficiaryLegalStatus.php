<?php

namespace App\Models\Folders;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A legal status of a beneficiary, with its description and expiry date.
 */
class BeneficiaryLegalStatus extends Model
{
    protected $table = 'benefiters_legal_status';

    /** @var list<string> */
    protected $fillable = ['description', 'exp_date', 'benefiter_id', 'legal_lookup_id'];

    /** @return BelongsTo<Beneficiary, $this> */
    public function benefiter(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'benefiter_id');
    }
}
