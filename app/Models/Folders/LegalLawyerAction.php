<?php

namespace App\Models\Folders;

use App\Models\LegalSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An action of the lawyer in a legal session.
 */
class LegalLawyerAction extends Model
{
    protected $table = 'legal_lawyer_action';

    /** @var list<string> */
    protected $fillable = ['lawyer_action_id', 'legal_session_id'];

    /** @return BelongsTo<LegalSession, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(LegalSession::class, 'legal_session_id');
    }
}
