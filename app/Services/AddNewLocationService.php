<?php

namespace App\Services;

use App\Models\Benefiters_Tables_Models\medical_location_lookup;
use Illuminate\Support\Facades\Validator;

class AddNewLocationService{

    public function newLocationValidation($request){
        $rules = array(
            'new_location' => 'required|max:255',
        );
        return Validator::make($request, $rules);
    }

    public function saveNewLocationToDB($request){
        $location = new medical_location_lookup();
        $location->description = $request['new_location'];
        $location->save();
    }
}
