<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasicFolderHistory extends Model
{
    protected $table = 'basic_folder_update_history';
    protected $fillable = ['user_id', 'benefiter_id', 'medical_location_id', 'comments', 'update_date'];

    public function updatedBy(){
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function benefiterId(){
        return $this->belongsTo('App\Models\Benefiters_Table_Models\Benefiter', 'id', 'benefiter_id');
    }

    public function locationId(){
        return $this->hasOne('App/Models/Benefiters_Tables_Models/medical_location_lookup.php', 'id', 'medical_location_id');
    }
}
