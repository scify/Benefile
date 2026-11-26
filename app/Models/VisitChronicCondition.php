<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitChronicCondition extends Model
{
    protected $table = 'medical_chronic_conditions';

    protected $fillable = [
        'description'];

    public function chronic_conditions_lookup(): \Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(\App\Models\MedicalVisit::class, 'id', 'chronic_condition_id');
    }

    public function benefiter(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Beneficiary::class, 'id', 'benefiters_id');
    }

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\MedicalVisit::class,'id', 'medical_visit_id');
    }
}
