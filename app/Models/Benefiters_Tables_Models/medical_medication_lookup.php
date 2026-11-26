<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class medical_medication_lookup extends Model
{
    protected $table = 'medical_medication_lookup';

    protected $fillable = [
        'description'];

    public function medical_medication(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Benefiters_Tables_Models\medical_visits::class, 'medical_visit_id');
    }

}
