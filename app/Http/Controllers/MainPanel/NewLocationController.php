<?php

namespace App\Http\Controllers\MainPanel;

use App\Services\AddNewLocationService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class NewLocationController extends Controller
{
    public function __construct(){
        // only the administrator should add a new location
        $this->middleware('admin');
    }

    public function getNewLocationView(){
        return view('location.new_location');
    }

    public function postSaveNewLocation(Request $request){
        $addNewLocationService = new AddNewLocationService();
        $validator = $addNewLocationService->newLocationValidation($request->all());
        if($validator->fails()){
            return redirect('new-location')
                ->withInput($request->all())
                ->withErrors($validator->errors()->all());
        } else {
            $addNewLocationService->saveNewLocationToDB($request->all());
            return view('location.new_location')
                ->with('success', \Lang::get('new_location_controller_messages.success_msg'));
        }
    }
}
