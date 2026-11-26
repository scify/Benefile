<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class medical_hospitalizations extends Model
{
    protected $table = 'medical_hospitalizations';

    protected $fillable = [
        'hospitalizations',
        'hospitalization_date',
        'medical_visit_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Benefiters_Tables_Models\medical_visits::class, 'medical_visit_id');
    }
}
