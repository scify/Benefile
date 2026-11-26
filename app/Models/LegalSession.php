<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalSession extends Model
{
    protected $table = 'legal_sessions';

    protected $fillable = ['user_id', 'legal_folder_id', 'medical_location_id', 'legal_date', 'legal_comments'];

    public function lawyerActions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Folders\LegalLawyerAction::class, 'legal_session_id');
    }
}
