<?php

namespace App\Http\Controllers\Folders;

use App\Http\Controllers\Controller;
use App\Models\Icd10Code;
use App\Models\ExaminationResultType;
use App\Models\IncidentType;
use App\Models\Location;
use App\Models\MedicalVisit;
use App\Services\BasicInfoService;
use App\Services\BenefiterMedicalFolderService;
use App\Enums\VisitResult;
use Illuminate\Http\Request;

/**
 * The medical folder: the medical visits, and the lists of medicines and ICD-10 codes.
 */
class MedicalFolderController extends Controller
{
    public function __construct(
        private readonly BasicInfoService $basicInfoService,
        private readonly BenefiterMedicalFolderService $medicalVisit,
    ) {
    }

    //------------ GET MEDICAL FOLDER FOR BENEFITER -------------------------------//
    public function getMedicalFolder($id){
        // POST result message
        $selected_medical_visit_id = session()->get('selected_medical_visit_id', function() { return 0; });
        $visit_result = VisitResult::fromSession();

        // ------ VALIDATION FAILURE SAVE TYPED DATA ------------------ //
            // chronic conditions
        $chronic_conditions_sesssion = session()->get('chronic_conditions_session');
            // lab results
        $lab_results_session = session()->get('lab_results_session');
            // diagnosis results
        $diagnosis_results_session = session()->get('diagnosis_results_session');
            // hospitalizations
        $hospitalization_session = session()->get('hospitalization_session');
            // hospitalization dates
        $hospitalization_date_session = session()->get('hospitalization_date_session');
            // referrals
        $referrals_session = session()->get('referrals_session');
            // referrals is done
        $referrals_is_done_session = session()->get('referrals_is_done_session');
            //Examination results (consists of selected conditions & descriptions)
        $examResultDescription_session = session()->get('examResultDescription_session');
        $examResultLoukup_session = session()->get('examResultLoukup_session');
            // transform the above session ICD10 ids into respective description
        $examResultLoukup_session_description =[[]];
        for($i=0 ; $i<legacy_count($examResultLoukup_session) ; $i++){
            for ($j=0 ; $j<legacy_count($examResultLoukup_session[$i]) ; $j++){
                if(!empty($examResultLoukup_session[$i][$j])){
                    $examResultLoukup_session_description[$i][$j] = $this->medicalVisit->getICD10By_id($examResultLoukup_session[$i][$j]);
                }
            }
        }
            // medication (consists of lookup select options or typed name, dosage, duration, supplied by the organisation checkbox )
        $medication_name_from_lookup_session = session()->get('medication_name_from_lookup_session');
        $medication_name_from_lookup_session_description = [];
        for($i=0; $i<legacy_count($medication_name_from_lookup_session) ; $i++){
            $medication_name_from_lookup_session_description[$i] = $this->medicalVisit->getMedicationLookupBy_id($medication_name_from_lookup_session[$i]);
        }
        $medication_new_name_session = session()->get('medication_new_name_session');
        $medication_dosage_session = session()->get('medication_dosage_session');
        $medication_duration_session = session()->get('medication_duration_session');
        $supply_from_praksis_hidden_session = session()->get('supply_from_praksis_hidden_session');
        $upload_file_description_session = session()->get('upload_file_description_session');
        $upload_file_title_session = session()->get('upload_file_title_session');

        // ------ END VALIDATION FAILURE SAVE TYPED DATA ------------------ //

        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        $medical_visits_number = $this->medicalVisit->benefiter_medical_visits_number($id) ; //MedicalVisit::where('benefiter_id', $id)->count();
        $benefiter_medical_visits_list = $this->medicalVisit->findMedicalVisitsForBenefiter($id); // MedicalVisit::where('benefiter_id', $id)->with('doctor', 'medicalLocation', 'medicalIncidentType')->get();
        $referrals = $this->medicalVisit->findAllMedicalVisitsReferralsForBenefiter($benefiter_medical_visits_list);
        if ($benefiter == null) {
            return view('errors.404');
        } else {
            $ExamResultsLookup = $this->medicalVisit->examinationsResultsLookup(); //ExaminationResultType::get()->all();
            // brings the medical location array from db
            $medical_locations = $this->medicalVisit->medicalLocationsLookup();  //Location::get();
            $medical_incident_type = $this->medicalVisit->medicalIncidentTypeLookup();  //IncidentType::get();
            $medical_locations_array = $this->medicalVisit->reindex_array($medical_locations);
            $medical_incident_type_array = $this->medicalVisit->reindex_array($medical_incident_type);
            $doctor_id = $this->medicalVisit->findDoctorId();  //auth()->id();
            $benefiter_id = $benefiter->id;
            return view('benefiter.medical-folder')
                        ->with('selected_medical_visit_id', $selected_medical_visit_id)
                        ->with('ExamResultsLookup', $ExamResultsLookup)
                        ->with('medical_locations_array', $medical_locations_array)
                        ->with('medical_incident_type_array', $medical_incident_type_array)
                        ->with('benefiter_id', $benefiter_id)
                        ->with('doctor_id', $doctor_id)
                        ->with('benefiter', $benefiter)
                        ->with('medical_visits_number', $medical_visits_number)
                        ->with('chronic_conditions_sesssion', $chronic_conditions_sesssion)
                        ->with('lab_results_session', $lab_results_session)
                        ->with('diagnosis_results_session', $diagnosis_results_session)
                        ->with('hospitalization_session', $hospitalization_session)
                        ->with('hospitalization_date_session', $hospitalization_date_session)
                        ->with('referrals_session', $referrals_session)
                        ->with('referrals_is_done_session', $referrals_is_done_session)
                        ->with('examResultDescription_session', $examResultDescription_session)
                        ->with('examResultLoukup_session', $examResultLoukup_session)
                        ->with('examResultLoukup_session_description', $examResultLoukup_session_description)
                        ->with('medication_name_from_lookup_session', $medication_name_from_lookup_session)
                        ->with('medication_name_from_lookup_session_description', $medication_name_from_lookup_session_description)
                        ->with('medication_new_name_session', $medication_new_name_session)
                        ->with('medication_dosage_session', $medication_dosage_session)
                        ->with('medication_duration_session', $medication_duration_session)
                        ->with('supply_from_praksis_hidden_session', $supply_from_praksis_hidden_session)
                        ->with('benefiter_medical_visits_list', $benefiter_medical_visits_list)
                        ->with('referrals', $referrals)
                        ->with('upload_file_description_session', $upload_file_description_session)
                        ->with('upload_file_title_session', $upload_file_title_session)
                        ->with('visit_result', $visit_result);
        }
    }

    //------------ POST MEDICAL VISIT DATA ----------------------------------------//
    public function postMedicalFolder(Request $request, $id){
        // The files of a visit: common document and image types, 10 MB at most each.
        $request->validate(['upload_file_title.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,odt,xls,xlsx,ods,txt,rtf']]);
        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        abort_if($benefiter === null, 404);
        $folder = redirect('benefiter/'.$benefiter->id.'/medical-folder');

        $validator = $this->medicalVisit->medicalValidation($request->all());
        if ($validator->fails()) {
            // The form shows the typed rows again. A browser cannot get a chosen file back, so only the file names return.
            $fileNames = array_values(array_map(
                fn ($file) => $file->getClientOriginalName(),
                array_filter((array) $request->file('upload_file_title', []))
            ));

            return $folder
                ->withInput($request->only(['examination_date', 'medical_location_id', 'incident_type', 'height', 'weight',
                    'temperature', 'blood_pressure_systolic', 'blood_pressure_diastolic', 'skull_perimeter']))
                ->with([
                    'visit_result' => VisitResult::Failed->value,
                    'chronic_conditions_session' => $request['chronic_conditions'],
                    'lab_results_session' => $request['lab_results'],
                    'diagnosis_results_session' => $request['diagnosis_results'],
                    'hospitalization_session' => $request['hospitalization'],
                    'hospitalization_date_session' => $request['hospitalization_date'],
                    'referrals_session' => $request['referrals'],
                    'referrals_is_done_session' => $request['is_done_id'],
                    'examResultDescription_session' => $request['examResultDescription'],
                    'examResultLoukup_session' => $request['examResultLoukup'],
                    'medication_name_from_lookup_session' => $request['medication_name_from_lookup'],
                    'medication_new_name_session' => $request['medication_new_name'],
                    'medication_dosage_session' => $request['medication_dosage'],
                    'medication_duration_session' => $request['medication_duration'],
                    'supply_from_praksis_hidden_session' => $request['supply_from_praksis_hidden'],
                    'upload_file_description_session' => $request['upload_file_description'],
                    'upload_file_title_session' => $fileNames,
                ])
                ->withErrors($validator->errors()->all());
        }

        // The visit belongs to the beneficiary of the address and to the logged-in doctor,
        // whatever the form sends.
        $saved = $this->medicalVisit->save_new_medical_visit_tables(array_merge($request->all(), [
            'benefiter_id' => $benefiter->id,
            'doctor_id' => auth()->id(),
        ]));

        return $folder->with('visit_result', ($saved ? VisitResult::Saved : VisitResult::Failed)->value);
    }

    // The window with a past visit, from the medical history.
    public function getMedicalVisitModal(Request $request, $id){
        $details = $this->medicalVisit->visitDetails((int) $id, (int) $request['current_medical_visit']);
        abort_if($details === null, 404);

        return view('partials.modals.medical_visit_info', $details + [
            'benefiter_folder_number' => $this->medicalVisit->find_benefiter_folder_number($id),
            'benefiter' => $this->basicInfoService->findExistentBenefiter($id),
            'ExamResultsLookup' => $this->medicalVisit->get_medical_examination_results_from_lookup(),
        ]);
    }

    // The edit form of a past visit.
    public function getMedicalVisitForEditing(Request $request, $id){
        $selected_medical_visit_id = (int) $request['medical_visit_id'];
        $details = $this->medicalVisit->visitDetails((int) $id, $selected_medical_visit_id);
        // The visit is not a visit of this beneficiary.
        abort_if($details === null, 404);

        return view('partials.forms.medical-visit.medical_visit_edit', $details + [
            'selected_medical_visit_id' => $selected_medical_visit_id,
            'benefiter' => $this->basicInfoService->findExistentBenefiter($id),
            'medical_locations_array' => $this->medicalVisit->reindex_array($this->medicalVisit->medicalLocationsLookup()),
            'medical_incident_type_array' => $this->medicalVisit->reindex_array($this->medicalVisit->medicalIncidentTypeLookup()),
            'ExamResultsLookup' => $this->medicalVisit->examinationsResultsLookup(),
        ]);
    }

    //------------ POST: EDIT MEDICAL VISIT ---------------------------------------//
    public function postMedicalVisitFromEditing(Request $request, $id){
        // The files of a visit: common document and image types, 10 MB at most each.
        $request->validate(['upload_file_title.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,odt,xls,xlsx,ods,txt,rtf']]);
        // Only a visit of the beneficiary of the address.
        $visit = \App\Models\MedicalVisit::where('id', (int) $request['selected_medical_visit_id'])
            ->where('benefiter_id', (int) $id)
            ->firstOrFail();
        $selected_medical_visit_id = $visit->id;
        $benefiter_id = $visit->benefiter_id;

        // Post Validation
        $validator = $this->medicalVisit->medicalValidation($request->all());
        if($validator->fails()){
            return redirect('benefiter/'.$benefiter_id.'/editMedicalVisit?medical_visit_id='.$selected_medical_visit_id)
                ->with('visit_result', VisitResult::Failed->value)
                ->withErrors($validator->errors()->all());
        } else {
            $saved = $this->medicalVisit->update_medical_visit_tables($request->all(), $selected_medical_visit_id);
            return redirect('benefiter/'.$benefiter_id.'/medical-folder')
                ->with('selected_medical_visit_id', $selected_medical_visit_id)
                ->with('visit_result', ($saved ? VisitResult::Updated : VisitResult::Failed)->value);
        }

    }

    //------ MEDICATION LIST FETCH "LIKE" OBJECTS ---------------------------------//
    public function getMedicationList(Request $request){
        $full_medication_name = $this->medicalVisit->get_full_medication_name($request['q']);
        return $full_medication_name;
    }

    //------ ICD10 SELECT LIST FETCH "LIKE" OBJECTS -------------------------------//
    public function getICD10List(Request $request){
        $full_icd10_description = $this->medicalVisit->get_full_icd10_description($request['q']);
        return $full_icd10_description;
    }
}
