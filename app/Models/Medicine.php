<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    protected $table = 'medical_medication_lookup';

    protected $fillable = [
        'description'];

    public function medical_medication(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\MedicalVisit::class, 'medical_visit_id');
    }

}
