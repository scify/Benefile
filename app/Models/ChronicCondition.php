<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChronicCondition extends Model
{
    protected $table = 'medical_chronic_conditions_lookup';
    protected $fillable = ['description'];
}
