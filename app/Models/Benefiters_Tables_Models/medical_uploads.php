<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class medical_uploads extends Model
{
    protected $table = 'medical_uploads';

    protected $fillable = [
        'title',
        'description',
        'path',
        'medical_visit_id'];

    public function medical_visits(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\App\Models\Benefiters_Tables_Models\medical_visits::class, 'medical_visit_id');
    }
}
