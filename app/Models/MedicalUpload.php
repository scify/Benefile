<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalUpload extends Model
{
    protected $table = 'medical_uploads';

    protected $fillable = [
        'title',
        'description',
        'path',
        'medical_visit_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\MedicalVisit::class, 'medical_visit_id');
    }
}
