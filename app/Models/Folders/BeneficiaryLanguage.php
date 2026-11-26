<?php

namespace App\Models\Folders;

use App\Models\Benefiters_Tables_Models\Benefiter;
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

    /** @return BelongsTo<Benefiter, $this> */
    public function benefiter(): BelongsTo
    {
        return $this->belongsTo(Benefiter::class, 'benefiter_id');
    }
}
