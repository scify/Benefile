<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExaminationResult extends Model
{
    protected $table = 'medical_examination_results';
    protected $primaryKey = 'id';
    protected $fillable = ['icd10_id', 'description', 'results_lookup_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\MedicalVisit::class,'id', 'medical_visit_id');
    }

    public function medical_examination_results_lookup(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\ExaminationResultType::class, 'id', 'results_lookup_id');
    }

    public function icd10(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Icd10Code::class, 'id', 'icd10_id');
    }
}
