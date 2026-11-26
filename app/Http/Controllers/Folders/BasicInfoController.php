<?php

namespace App\Http\Controllers\Folders;

use App\Http\Controllers\Controller;
use App\Models\Benefiters_Tables_Models\Benefiter;
use App\Services\BasicInfoService;
use App\Services\OccurrenceService;
use App\Services\ReferralService;
use App\Services\BenefiterMedicalFolderService;
use Illuminate\Http\Request;

/**
 * The basic information of a beneficiary: the form, the duplicate check, incidents and referrals.
 */
class BasicInfoController extends Controller
{
    public function __construct(
        private readonly BasicInfoService $basicInfoService,
        private readonly OccurrenceService $occurrences,
        private readonly ReferralService $referrals,
        private readonly BenefiterMedicalFolderService $medicalVisit,
    ) {
    }

    // get basic info view
    public function getBasicInfo($id){
        $allFoldersHistory = null;
        // get all occurences from DB
        $occurrences = $this->occurrences->forBeneficiary((int) $id);
        // brings the referrals options array from db to view

        $basic_info_referral = $this->referrals->types();
        $countryAbandonReasons = $this->basicInfoService->getAllCountryAbandonReasons();
        $basic_info_referral_array = $this->medicalVisit->reindex_array($basic_info_referral);
        // brings all referrals saved to db for this benefiter id
        $benefiter_referrals_list = $this->referrals->forBeneficiary((int) $id);
        $workTitle = null;
        $languages = $this->basicInfoService->getAllLanguages();
        $languageLevels = $this->basicInfoService->getAllLanguageLevels();
        // brings the medical location array from db
        $medical_locations = $this->medicalVisit->medicalLocationsLookup();
        $medical_locations_array = $this->medicalVisit->reindex_array($medical_locations);
        // get legal statuses from session, else get null and afterwards forget session value
        // If validation fails, get back all previously written info
        $country_abandon_reason = session()->get('country_abandon_reason', function() { return 1; });
        session()->forget('country_abandon_reason');
        $legal_statuses = session()->get('legalStatuses', function() { return null; });
        session()->forget('legalStatuses');
        $benefiterLanguagesAndLevels = session()->get('benefiter_languages', function() { return null; });
        session()->forget('benefiter_languages');
        // The session is stored as JSON: the objects of these lists come back as arrays.
        $toObjects = fn ($list) => is_iterable($list) ? array_map(fn ($item) => (object) $item, is_array($list) ? $list : iterator_to_array($list)) : $list;
        $legal_statuses = $toObjects($legal_statuses);
        $benefiterLanguagesAndLevels = $toObjects($benefiterLanguagesAndLevels);
        $successMsg = session()->get('success', function() { return null; });
        session()->forget('success');
        $errors = session()->get('errors' , function() { return null; });
        session()->forget('errors');
        $medical_location_id = session()->get('medical_location_id' , function() { return null; });
        session()->forget('medical_location_id');
        // checks if id is correct, so it could find the existent benefiter with that id
        if($id > 0){
            $benefiter = $this->basicInfoService->findExistentBenefiter($id);
            if($benefiter == null) {
                return view('errors.404');
            } else {
                if ($errors == null) {
                    $legal_statuses = $this->basicInfoService->getLegalStatusesByBenefiterId($id);
                }
                $benefiterLanguagesAndLevels = $this->basicInfoService->getLanguagesAndLanguagesLevelsByBenefiterId($id);
                $workTitle = $this->basicInfoService->getWorkTitleNameFromBenefiterId($id);
            }
            $allFoldersHistory = app(\App\Services\FolderHistoryService::class)->forBeneficiary((int) $id, auth()->user());
        } else {
            $benefiter = new Benefiter();
            $benefiter->country_abandon_reason_id = $country_abandon_reason;
        }
        return view('benefiter.basic_info')->with("languages", $languages)
                                           ->with("languageLevels", $languageLevels)
                                           ->with("occurrences", $occurrences)
                                           ->with("benefiter", $benefiter)
                                           ->with("legalStatuses", $legal_statuses)
                                           ->with("benefiter_languages", $benefiterLanguagesAndLevels)
                                           ->with('basic_info_referral_array', $basic_info_referral_array)
                                           ->with('benefiter_referrals_list', $benefiter_referrals_list)
                                           ->with('workTitle', $workTitle)
                                           ->with('success', $successMsg)
                                           ->with('countryAbandonReasons', $countryAbandonReasons)
                                           ->with('medical_locations_array', $medical_locations_array)
                                           ->with('medical_location_id', $medical_location_id)
                                           ->with('allFoldersHistory', $allFoldersHistory);
    }

    //------ post from basic info form -------------------------------//
    public function postBasicInfo(Request $request, $id){
        $validator = $this->basicInfoService->basicInfoValidation($request->all(), $id);
        if($validator->fails()){
            $legal_statuses = $this->basicInfoService->getLegalStatusesArrayFromRequest($request->legal_status, $request->legal_status_text, $request->legal_status_exp_date);
            $benefiterLanguagesAndLevels = $this->basicInfoService->getLanguagesAndLanguagesLevelsFromRequest($request->all());
            return redirect('benefiter/'.$id.'/basic-info')
                        ->withInput(array(
                            'folder_number' => $request->folder_number,
                            'lastname' => $request->lastname,
                            'name' => $request->name,
                            'gender' => $request->gender,
                            'birth_date' => $request->birth_date,
                            'fathers_name' => $request->fathers_name,
                            'mothers_name' => $request->mothers_name,
                            'nationality_country' => $request->nationality_country,
                            'origin_country' => $request->origin_country,
                            'arrival_date' => $request->arrival_date,
                            'ethnic_group' => $request->ethnic_group,
                            'telephone' => $request->telephone,
                            'address' => $request->address,
                            'marital_status' => $request->marital_status,
                            'number_of_children' => $request->number_of_children,
                            'relatives_residence' => $request->relatives_residence,
                            'children_names' => $request->children_names,
                            'education_status' => $request->education_status,
                            'education_specialization' => $request->education_specialization,
                            'interpreter' => $request->interpreter,
                            'working' => $request->working,
                            'working_title' => $request->working_title,
                            'working_legally' => $request->working_legally,
                            'travel_route' => $request->travel_route,
                            'travel_duration' => $request->travel_duration,
                            'detention_duration' => $request->detention_duration,
                            'social_history' => $request->social_history,
                            'updated_by_date' => $request->updated_by_date,
                            'updated_by_comments' => $request->updated_by_comments,
                        ))
                        ->with("medical_location_id", $request->medical_location_id)
                        ->with("country_abandon_reason", $request->country_abandon_reason)
                        ->with("legalStatuses", $legal_statuses)
                        ->with("benefiter_languages", $benefiterLanguagesAndLevels)
                        ->withErrors($validator->errors()->all());
        } else {
            if($id > 0){
                $this->basicInfoService->editBasicInfo($request->all(), $id);
                $benefiter = new Benefiter();
                $benefiter->id = $id;
                $successMsg = \Lang::get('records_controller_messages.basic_info_edit_success');
            } else {
                $benefiter = $this->basicInfoService->saveBasicInfoToDB($request->all());
                $successMsg = \Lang::get('records_controller_messages.basic_info_create_success');
            }
            $legal_statuses = $this->basicInfoService->getLegalStatusesByBenefiterId($benefiter->id);
            $benefiterLanguagesAndLevels = $this->basicInfoService->getLanguagesAndLanguagesLevelsByBenefiterId($benefiter->id);
            return redirect('benefiter/'.$benefiter->id.'/basic-info')
                    ->with("legalStatuses", $legal_statuses)
                    ->with("benefiter_languages", $benefiterLanguagesAndLevels)
                    ->with("success", $successMsg);
        }
    }

    // check for duplicated folders
    public function postCheckForDuplicatedFolders(Request $request){
        return $this->basicInfoService->checkForDuplicates($request->all());
    }

    // An incident, from the AJAX form of the basic information.
    public function saveOccurrencesBasicInfo(Request $request, $id){
        $this->occurrences->add((int) $id, $request);
        return redirect('benefiter/'.$id.'/basic-info');
    }

    public function editOccurrencesBasicInfo(Request $request, $id, $occurrence_id){
        $this->occurrences->update((int) $id, (int) $occurrence_id, $request);
        return redirect('benefiter/'.$id.'/basic-info');
    }

    public function deleteOccurrencesBasicInfo($id, $occurrence_id){
        $this->occurrences->delete((int) $id, (int) $occurrence_id);
        return redirect('benefiter/'.$id.'/basic-info');
    }

    public function postBasicInfoReferrals(Request $request, $id){
        $this->referrals->add((int) $id, $request);
        return redirect('benefiter/'.$id.'/basic-info')->with("success", \Lang::get('records_controller_messages.referrals_create_success'));
    }

    public function deleteBasicInfoReferral($id, $referral_id){
        $this->referrals->delete((int) $id, (int) $referral_id);
        return redirect('benefiter/'.$id.'/basic-info')->with("success", \Lang::get('records_controller_messages.referrals_delete_success'));
    }
}
