<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    protected $table = 'medical_incident_type_lookup';

    protected $fillable = ['description'];
}
