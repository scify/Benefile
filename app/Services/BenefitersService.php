<?php namespace App\Services;

use App\Models\Beneficiary;
use Validator;

class BenefitersService{

    public function getAllBenefiters(){
        $benefitersList = Beneficiary::with('educationLookup', 'medical_visits')->get();
        return $benefitersList;
    }
}