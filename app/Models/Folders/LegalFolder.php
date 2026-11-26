<?php

namespace App\Models\Folders;

use App\Models\Beneficiary;
use App\Models\LegalSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * The legal folder of a beneficiary.
 */
class LegalFolder extends Model
{
    protected $table = 'legal_folder';

    /** @var list<string> */
    protected $fillable = ['legal_folder_status_id', 'penalty_id', 'penalty_text', 'benefiter_id'];

    /** @return BelongsTo<Beneficiary, $this> */
    public function benefiter(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class, 'benefiter_id');
    }

    /** @return HasOne<AsylumRequest, $this> */
    public function asylumRequest(): HasOne
    {
        return $this->hasOne(AsylumRequest::class, 'legal_folder_id');
    }

    /** @return HasMany<LegalSectionStatus, $this> */
    public function sectionStatuses(): HasMany
    {
        return $this->hasMany(LegalSectionStatus::class, 'legal_folder_id');
    }

    /** @return HasMany<LegalSession, $this> */
    public function sessions(): HasMany
    {
        return $this->hasMany(LegalSession::class, 'legal_folder_id');
    }
}
