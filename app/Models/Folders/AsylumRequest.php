<?php

namespace App\Models\Folders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The asylum request in the legal folder of a beneficiary.
 */
class AsylumRequest extends Model
{
    protected $table = 'asylum_request';

    /** @var list<string> */
    protected $fillable = ['request_date', 'procedure_id', 'request_status_id', 'request_progress', 'legal_folder_id'];

    /** @return BelongsTo<LegalFolder, $this> */
    public function legalFolder(): BelongsTo
    {
        return $this->belongsTo(LegalFolder::class, 'legal_folder_id');
    }
}
