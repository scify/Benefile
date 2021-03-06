<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class medical_medication extends Model
{
    protected $table = 'medical_medication';

    protected $fillable = [
        'medical_visit_id',
        'dosage',
        'duration',
        'supply_from_praksis',
        'medication_lookup_id'];

    public function medical_visits()
    {
        return $this->hasOne('App\Models\Benefiters_Tables_Models\medical_visits', 'medical_visit_id');
    }

    public function medical_medication_lookup()
    {
        return $this->hasOne('App\Models\Benefiters_Tables_Models\medical_medication_lookup', 'id', 'medication_lookup_id');
    }
}
