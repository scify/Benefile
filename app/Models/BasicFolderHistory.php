<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicFolderHistory extends Model
{
    protected $table = 'basic_folder_update_history';
    protected $fillable = ['user_id', 'benefiter_id', 'medical_location_id', 'comments', 'update_date'];

    public function updatedBy(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public function benefiterId(): \Illuminate\Database\Eloquent\Relations\BelongsTo{
        return $this->belongsTo(\App\Models\Beneficiary::class, 'id', 'benefiter_id');
    }

    public function locationId(): \Illuminate\Database\Eloquent\Relations\HasOne{
        return $this->hasOne(\App\Models\Location::class, 'id', 'medical_location_id');
    }
}
