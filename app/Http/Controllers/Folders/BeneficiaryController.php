<?php

namespace App\Http\Controllers\Folders;

use App\Http\Controllers\Controller;
use App\Services\BasicInfoService;
use App\Services\BenefitersService;

/**
 * The list of the beneficiaries, and the deletion of a file.
 */
class BeneficiaryController extends Controller
{
    public function __construct(
        private readonly BasicInfoService $basicInfoService,
        private readonly BenefitersService $benefiterList,
    ) {
    }

    //------------ GET BENEFITERS LIST -------------------------------//
    public function getBenefitersList(){
        $benefiters =  $this->benefiterList->getAllBenefiters();
        return view('benefiter.benefiters_list', compact('benefiters'));
    }

    // delete a benefiter from id
    public function getDeleteBenefiter($id){
        $this->basicInfoService->deleteBenefiter($id);
        return redirect('benefiters-list');
    }
}
