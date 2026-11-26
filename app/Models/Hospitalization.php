<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hospitalization extends Model
{
    protected $table = 'medical_hospitalizations';

    protected $fillable = [
        'hospitalizations',
        'hospitalization_date',
        'medical_visit_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\MedicalVisit::class, 'medical_visit_id');
    }
}
