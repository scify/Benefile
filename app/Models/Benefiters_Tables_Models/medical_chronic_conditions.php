<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class medical_chronic_conditions extends Model
{
    protected $table = 'medical_chronic_conditions';

    protected $fillable = [
        'description'];

    public function chronic_conditions_lookup(): \Illuminate\Database\Eloquent\Relations\HasMany{
        return $this->hasMany(\App\Models\Benefiters_Tables_Models\medical_visits::class, 'id', 'chronic_condition_id');
    }

    public function benefiter(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\Benefiter::class, 'id', 'benefiters_id');
    }

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\medical_visits::class,'id', 'medical_visit_id');
    }
}
