<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class medical_referrals extends Model
{
    protected $table = 'medical_referrals';

    protected $fillable = [
        'referrals',
        'medical_visit_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\medical_visits::class, 'medical_visit_id');
    }
}
