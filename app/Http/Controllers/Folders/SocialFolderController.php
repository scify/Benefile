<?php

namespace App\Http\Controllers\Folders;

use App\Http\Controllers\Controller;
use App\Services\BasicInfoService;
use App\Services\BenefiterMedicalFolderService;
use App\Services\SocialFolderService;
use Illuminate\Http\Request;

/**
 * The psychosocial folder: the social history and the sessions of the psychologists.
 */
class SocialFolderController extends Controller
{
    public function __construct(
        private readonly BasicInfoService $basicInfoService,
        private readonly SocialFolderService $socialFolderService,
        private readonly BenefiterMedicalFolderService $medicalVisit,
    ) {
    }

    // get social folder view
    public function getSocialFolder($id){
        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        $psychologist_id = auth()->id();
        // brings the medical location array from db
        $medical_locations = $this->medicalVisit->medicalLocationsLookup();
        $medical_locations_array = $this->medicalVisit->reindex_array($medical_locations);
        // get psychosocial theme from session, else get null and afterwards forget session value
        $session_theme = session()->get('psychosocialTheme', function() { return null; });
        session()->forget('psychosocialTheme');
        $successMsg = session()->get('success', function() { return null; });
        session()->forget('success');
        if($benefiter == null) {
            return view('errors.404');
        } else {
            $socialFolder = $this->socialFolderService->getSocialFolderFromBenefiterId($id);
            $psychosocialSubjects = $this->socialFolderService->getAllPsychosocialSupportSubjects();
                $benefiter_sessions = $this->socialFolderService->getAllSessionsFromBenefiterId($id);
                $psychosocialSupport = $this->socialFolderService->getBenefiterPsychosocialSupport($id);
                return view('benefiter.social_folder')
                    ->with("tab", "social")
                    ->with("psychosocialSubjects", $psychosocialSubjects)
                    ->with("benefiter", $benefiter)
                    ->with("social_folder", $socialFolder)
                    ->with("psychosocial_support", $psychosocialSupport)
                    ->with("psychologist_id", $psychologist_id)
                    ->with("session_theme", $session_theme)
                    ->with('benefiter_sessions', $benefiter_sessions)
                    ->with('medical_locations_array', $medical_locations_array)
                    ->with('success', $successMsg);
        }
    }

    // post from social folder form
    public function postSocialFolder(Request $request, $id){
        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        $psychosocialSubjects = $this->socialFolderService->getAllPsychosocialSupportSubjects();
        $socialFolder = null;
        $psychosocialSupport = null;
        $validator = $this->socialFolderService->socialFolderValidation($request->all());
        if($validator->fails()){
            return redirect('benefiter/' . $id . '/social-folder')->with("tab", "social")->with("psychosocialSubjects", $psychosocialSubjects)->with("benefiter", $benefiter)->with("social_folder", $socialFolder)->with("psychosocial_support", $psychosocialSupport)->withErrors($validator->errors()->all());
        } else {
            $this->socialFolderService->saveSocialFolderToDB($request->all(), $id);
            $socialFolder = $this->socialFolderService->getSocialFolderFromBenefiterId($id);
            $psychosocialSupport = $this->socialFolderService->getBenefiterPsychosocialSupport($id);
            return redirect('benefiter/' . $id . '/social-folder')->with("tab", "social")->with("psychosocialSubjects", $psychosocialSubjects)->with("benefiter", $benefiter)->with("social_folder", $socialFolder)->with("psychosocial_support", $psychosocialSupport)->with('success', \Lang::get('records_controller_messages.social_folder_create_success'));
        }
    }

    // save a new session from social folder view
    public function postSessionSave(Request $request, $id){
        $validator = $this->socialFolderService->sessionValidation(array(
                                                                        'session_date' => $request->session_date,
                                                                        'session_comments' => $request->session_comments
                                                                     ));
        if($validator->fails()){
            return redirect('benefiter/'.$id.'/social-folder')
                ->withInput(array(
                                    'session_comments' => $request->session_comments,
                                    'session_date' => $request->session_date,
                                 ))
                ->with('psychosocialTheme', $request->psychosocial_theme)
                ->withErrors($validator->errors()->all());
        } else {
            $this->socialFolderService->saveNewSessionToDB($request->all(), $id);
            return redirect('benefiter/'.$id.'/social-folder')->with('success', \Lang::get('records_controller_messages.session_create_success'));
        }
    }

    // update an edited session
    public function postSessionEdit(Request $request, $id, $session_id){
        $validator = $this->socialFolderService->sessionValidation(array(
                                                                    'session_date' => $request->session_date,
                                                                    'session_comments' => $request->session_comments
                                                                  ));
        if($validator->fails()){
            return redirect('benefiter/'.$id.'/social-folder')
                ->withInput(array(
                    'session_comments' => $request->session_comments,
                    'session_date' => $request->session_date,
                ))
                ->with('psychosocialTheme', $request->psychosocial_theme)
                ->withErrors($validator->errors()->all());
        } else {
            $this->socialFolderService->saveEditedSessionToDB($request->all(), $session_id, $id);
            return redirect('benefiter/'.$id.'/social-folder')->with('success', \Lang::get('records_controller_messages.session_edit_success'));
        }
    }

    // delete a session
    public function getSessionDelete($id, $session_id){
        $this->socialFolderService->deleteSessionById($session_id, $id);
        return redirect("benefiter/" . $id . "/social-folder")->with('success', \Lang::get('records_controller_messages.session_delete_success'));
    }
}
