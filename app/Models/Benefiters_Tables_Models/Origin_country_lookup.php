<?php

namespace App\Models\Benefiters_Tables_Models;

use Illuminate\Database\Eloquent\Model;

class Origin_country_lookup extends Model
{
    protected $table = 'countries_lookup';
    protected $primaryKey = 'id';
    protected $fillable = ['name'];
}
