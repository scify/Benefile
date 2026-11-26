<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'medical_location_lookup';

    protected $fillable = ['description'];
}
