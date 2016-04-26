<?php

namespace App\Http\Controllers\MainPanel;

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
}
