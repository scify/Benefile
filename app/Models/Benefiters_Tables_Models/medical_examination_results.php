<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class medical_examination_results extends Model
{
    protected $table = 'medical_examination_results';
    protected $primaryKey = 'id';
    protected $fillable = ['icd10_id', 'description', 'results_lookup_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\medical_visits::class,'id', 'medical_visit_id');
    }

    public function medical_examination_results_lookup(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\medical_examination_results_lookup::class, 'id', 'results_lookup_id');
    }

    public function icd10(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\ICD10::class, 'id', 'icd10_id');
    }
}
