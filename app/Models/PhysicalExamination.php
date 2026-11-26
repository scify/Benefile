<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhysicalExamination extends Model
{
    protected $table = 'medical_examinations';

    protected $fillable = [
        'height',
        'weight',
        'skull_perimeter',
        'temperature',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'description',
        'medical_visit_id',
        'examination_date'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\MedicalVisit::class, 'medical_visit_id');
    }
}
