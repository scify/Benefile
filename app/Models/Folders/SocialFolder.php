<?php

namespace App\Models\Folders;

use App\Models\Beneficiary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The psychosocial folder of a beneficiary: one for each beneficiary.
 */
class SocialFolder extends Model
{
    protected $table = 'social_folder';

    /** @var list<string> */
    protected $fillable = ['benefiter_id', 'comments'];

    /** @return BelongsTo<Beneficiary, $this> */
    public function benefiter(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'benefiter_id');
    }
}
