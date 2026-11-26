<?php

namespace App\Models\Folders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The action and the result of one section of the legal folder.
 */
class LegalSectionStatus extends Model
{
    protected $table = 'legal_section_status';

    /** @var list<string> */
    protected $fillable = ['legal_option_id', 'action_id', 'result_id', 'legal_folder_id'];

    /** @return BelongsTo<LegalFolder, $this> */
    public function legalFolder(): BelongsTo
    {
        return $this->belongsTo(LegalFolder::class, 'legal_folder_id');
    }
}
