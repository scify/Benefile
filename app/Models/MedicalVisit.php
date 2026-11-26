<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalVisit extends Model
{
    protected $table = 'medical_visits';
    protected $fillable = ['medical_visit_date'];

    public function benefiter(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Beneficiary::class,'id', 'benefiter_id');
    }

    public function doctor(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\User::class, 'id', 'doctor_id');
    }

    public function medicalLocation(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\Location::class, 'id', 'medical_location_id');
    }
    public function medicalIncidentType(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\IncidentType::class, 'id', 'medical_incident_id');
    }

}
