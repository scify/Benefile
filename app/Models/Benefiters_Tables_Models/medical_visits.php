<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class medical_visits extends Model
{
    protected $table = 'medical_visits';
    protected $fillable = ['medical_visit_date'];


    public function benefiter(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\Benefiter::class,'id', 'benefiter_id');
    }

    public function doctor(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\User::class, 'id', 'doctor_id');
    }

    public function medicalLocation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\medical_location_lookup::class, 'id', 'medical_location_id');
    }
    public function medicalIncidentType(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Benefiters_Tables_Models\medical_incident_type_lookup::class, 'id', 'medical_incident_id');
    }

}
