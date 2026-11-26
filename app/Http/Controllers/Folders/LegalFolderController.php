<?php

namespace App\Http\Controllers\Folders;

use App\Http\Controllers\Controller;
use App\Services\BasicInfoService;
use App\Services\BenefiterMedicalFolderService;
use App\Services\LegalFolderService;
use Illuminate\Http\Request;

/**
 * The legal folder and the legal sessions.
 */
class LegalFolderController extends Controller
{
    public function __construct(
        private readonly BasicInfoService $basicInfoService,
        private readonly BenefiterMedicalFolderService $medicalVisit,
        private readonly LegalFolderService $legalFolderService,
    ) {
    }

    // returns view of legal folder
    public function getLegalFolder($id){
        // brings the medical location array from db
        $medical_locations = $this->medicalVisit->medicalLocationsLookup();
        $medical_locations_array = $this->medicalVisit->reindex_array($medical_locations);
        $legalFolder = $this->legalFolderService->findLegalFolderFromBenefiterId($id);
        $asylumRequest = null;
        $legalStatus = null;
        $lawyerActions = null;
        $successMsg = session()->get('success', function() { return null; });
        session()->forget('success');
        // if the legal folder exists return all things connected with it
        if($legalFolder != null){
            $asylumRequest = $this->legalFolderService->findAsylumRequestFromLegalFolderId($legalFolder->id);
            $legalStatus = $this->legalFolderService->findLegalSectionStatusFromLegalFolderId($legalFolder->id);
        }
        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        if($benefiter == null){
            return view('errors.404');
        }
        $legalSessionsHistory = $this->legalFolderService->getLegalSessionsHistoryForSingleBenefiter($id);
        return view('benefiter.legal_folder')
            ->with('legal_folder', $legalFolder)
            ->with('benefiter', $benefiter)
            ->with('asylum_request', $asylumRequest)
            ->with('legal_status', $legalStatus)
            ->with('lawyer_action', $lawyerActions)
            ->with('tab', 'legal')
            ->with('medical_locations_array', $medical_locations_array)
            ->with('legalSessionsHistory', $legalSessionsHistory)
            ->with('success', $successMsg);
    }

    // gets data from legal folder form
    public function postLegalFolder(Request $request, $id){
        $validator = $this->legalFolderService->legalFolderValidator($request->all());
        if ($validator->fails()){
            return redirect('benefiter/'.$id.'/legal-folder')
                ->withInput($request->all())
                ->withErrors($validator->errors()->all());
        } else {
            $this->legalFolderService->saveLegalFolderToDB($request->all(), $id);
            return redirect('benefiter/'.$id.'/legal-folder')->with('success', \Lang::get('records_controller_messages.legal_folder_create_success'));
        }
    }

    // gets data from legal session form
    public function postLegalSession(Request $request, $id){
        $validator = $this->legalFolderService->legalSessionValidator($request->all());
        if($validator->fails()){
            return redirect('benefiter/'.$id.'/legal-folder')
                ->withInput($request->all())
                ->withErrors($validator->errors()->all());
        } else {
            $this->legalFolderService->saveLegalSessionToDB($request->all(), $id);
            return redirect('benefiter/'.$id.'/legal-folder')->with('success', \Lang::get('records_controller_messages.legal_session_create_success'));
        }
    }
}
