<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalReferral extends Model
{
    protected $table = 'medical_referrals';

    protected $fillable = [
        'referrals',
        'medical_visit_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\MedicalVisit::class, 'medical_visit_id');
    }
}
