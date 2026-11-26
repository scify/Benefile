<?php

namespace App\Models\Folders;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A language that a beneficiary speaks, with the level.
 */
class BeneficiaryLanguage extends Model
{
    protected $table = 'benefiters_languages';

    /** @var list<string> */
    protected $fillable = ['benefiter_id', 'language_id', 'language_level_id'];

    /** @return BelongsTo<Beneficiary, $this> */
    public function benefiter(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'benefiter_id');
    }
}
