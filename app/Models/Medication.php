<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    protected $table = 'medical_medication';

    protected $fillable = [
        'medical_visit_id',
        'dosage',
        'duration',
        'supply_from_praksis',
        'medication_lookup_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\MedicalVisit::class, 'medical_visit_id');
    }

    public function medical_medication_lookup(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Medicine::class, 'id', 'medication_lookup_id');
    }
}
