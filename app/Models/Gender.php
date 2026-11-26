<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'genders_lookup';
    protected $primaryKey = 'id';
    protected $fillable = ['gender'];

    public function benefiters(): \Illuminate\Database\Eloquent\Relations\HasMany {
        return $this->hasMany(\App\Models\Beneficiary::class, 'gender_id');
    }
}
