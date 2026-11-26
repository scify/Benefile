<?php

namespace App\Http\Controllers\Folders;

use App\Http\Controllers\Controller;
use App\Models\Benefiters_Tables_Models\ICD10;
use App\Models\Benefiters_Tables_Models\medical_examination_results_lookup;
use App\Models\Benefiters_Tables_Models\medical_incident_type_lookup;
use App\Models\Benefiters_Tables_Models\medical_location_lookup;
use App\Models\Benefiters_Tables_Models\medical_visits;
use App\Services\BasicInfoService;
use App\Services\BenefiterMedicalFolderService;
use App\Services\DatesHelper;
use Illuminate\Http\Request;

/**
 * The medical folder: the medical visits, and the lists of medicines and ICD-10 codes.
 */
class MedicalFolderController extends Controller
{
    public function __construct(
        private readonly BasicInfoService $basicInfoService,
        private readonly BenefiterMedicalFolderService $medicalVisit,
        private readonly DatesHelper $datesHelper,
    ) {
    }

    //------------ GET MEDICAL FOLDER FOR BENEFITER -------------------------------//
    public function getMedicalFolder($id){
        // POST result message
        $selected_medical_visit_id = session()->get('selected_medical_visit_id', function() { return 0; });
        $visit_submited_succesfully = session()->get('visit_submited_succesfully', function() { return 0; });
        session()->forget('visit_submited_succesfully'); // 0:initial value, 1:Success, 2:Unsuccess

        // ------ VALIDATION FAILURE SAVE TYPED DATA ------------------ //
            // chronic conditions
        $chronic_conditions_sesssion = session()->get('chronic_conditions_session');
        session()->forget('chronic_conditions_session');
            // lab results
        $lab_results_session = session()->get('lab_results_session');
        session()->forget('lab_results_session');
            // diagnosis results
        $diagnosis_results_session = session()->get('diagnosis_results_session');
        session()->forget('diagnosis_results_session');
            // hospitalizations
        $hospitalization_session = session()->get('hospitalization_session');
        session()->forget('hospitalization_session');
            // hospitalization dates
        $hospitalization_date_session = session()->get('hospitalization_date_session');
        session()->forget('hospitalization_date_session');
            // referrals
        $referrals_session = session()->get('referrals_session');
        session()->forget('referrals_session');
            // referrals is done
        $referrals_is_done_session = session()->get('referrals_is_done_session');
        session()->forget('referrals_is_done_session');
            //Examination results (consists of selected conditions & descriptions)
        $examResultDescription_session = session()->get('examResultDescription_session');
        session()->forget('examResultDescription_session');
        $examResultLoukup_session = session()->get('examResultLoukup_session');
        session()->forget('examResultLoukup_session');
            // transform the above session ICD10 ids into respective description
        $examResultLoukup_session_description =[[]];
        for($i=0 ; $i<legacy_count($examResultLoukup_session) ; $i++){
            for ($j=0 ; $j<legacy_count($examResultLoukup_session[$i]) ; $j++){
                if(!empty($examResultLoukup_session[$i][$j])){
                    $examResultLoukup_session_description[$i][$j] = $this->medicalVisit->getICD10By_id($examResultLoukup_session[$i][$j]);
                }
            }
        }
            // medication (consists of lookup select options or typed name, dosage, duration, supplied from PRAKSIS checkbox )
        $medication_name_from_lookup_session = session()->get('medication_name_from_lookup_session');
        session()->forget('medication_name_from_lookup_session');
        $medication_name_from_lookup_session_description = [];
        for($i=0; $i<legacy_count($medication_name_from_lookup_session) ; $i++){
            $medication_name_from_lookup_session_description[$i] = $this->medicalVisit->getMedicationLookupBy_id($medication_name_from_lookup_session[$i]);
        }
        $medication_new_name_session = session()->get('medication_new_name_session');
        session()->forget('medication_new_name_session');
        $medication_dosage_session = session()->get('medication_dosage_session');
        session()->forget('medication_dosage_session');
        $medication_duration_session = session()->get('medication_duration_session');
        session()->forget('medication_duration_session');
        $supply_from_praksis_hidden_session = session()->get('supply_from_praksis_hidden_session');
        session()->forget('supply_from_praksis_hidden_session');
        $upload_file_description_session = session()->get('upload_file_description_session');
        session()->forget('upload_file_description_session');
        $upload_file_title_session = session()->get('upload_file_title_session');
        session()->forget('upload_file_title_session');

        // ------ END VALIDATION FAILURE SAVE TYPED DATA ------------------ //


        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        $medical_visits_number = $this->medicalVisit->benefiter_medical_visits_number($id) ; //medical_visits::where('benefiter_id', $id)->count();
        $benefiter_medical_visits_list = $this->medicalVisit->findMedicalVisitsForBenefiter($id); // medical_visits::where('benefiter_id', $id)->with('doctor', 'medicalLocation', 'medicalIncidentType')->get();
        $referrals = $this->medicalVisit->findAllMedicalVisitsReferralsForBenefiter($benefiter_medical_visits_list);
        if ($benefiter == null) {
            return view('errors.404');
        } else {
            $ExamResultsLookup = $this->medicalVisit->examinationsResultsLookup(); //medical_examination_results_lookup::get()->all();
            // brings the medical location array from db
            $medical_locations = $this->medicalVisit->medicalLocationsLookup();  //medical_location_lookup::get();
            $medical_incident_type = $this->medicalVisit->medicalIncidentTypeLookup();  //medical_incident_type_lookup::get();
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
                        ->with('visit_submited_succesfully', $visit_submited_succesfully);
        }
    }

    //------------ POST MEDICAL VISIT DATA ----------------------------------------//
    public function postMedicalFolder(Request $request, $id){
        // The files of a visit: common document and image types, 10 MB at most each.
        $request->validate(['upload_file_title.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,odt,xls,xlsx,ods,txt,rtf']]);
        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        abort_if($benefiter === null, 404);
        $benefiter_folder_number = $this->medicalVisit->find_benefiter_folder_number($id);
        $benefiter_medical_history_list = $this->medicalVisit->get_all_medical_visits_for_benefiter($id);
        $doctor_id = $this->medicalVisit->get_logged_in_user_id();

        $benefiter_id = $benefiter->id;
        $medical_visits_number = $this->medicalVisit->count_medical_visits_for_a_benefiter($id);
        // brings the medical location array from db
        $medical_locations = $this->medicalVisit->getAllMedicalLocations();
        $medical_locations_array = $this->medicalVisit->reindex_array($medical_locations);
        $ExamResultsLookup = $this->medicalVisit->examinationsResultsLookup();

        // Post Validation
        $validator = $this->medicalVisit->medicalValidation($request->all());
        if($validator->fails()){
            //Fetch all array posts (if validation fails)
            $new_medical_location = $request['new_medical_location'];
            $chronic_conditions_session = $request['chronic_conditions'];
            $lab_results_session = $request['lab_results'];
            $diagnosis_results_session = $request['diagnosis_results'];
            $hospitalization_session = $request['hospitalization'];
            $hospitalization_date_session = $request['hospitalization_date'];
            $referrals_session = $request['referrals'];
            $referrals_is_done_session = $request['is_done_id'];
            $examResultDescription_session = $request['examResultDescription'];
            $examResultLoukup_session = $request['examResultLoukup'];
            $medication_name_from_lookup_session = $request['medication_name_from_lookup'];
            $medication_new_name_session = $request['medication_new_name'];
            $medication_dosage_session = $request['medication_dosage'];
            $medication_duration_session = $request['medication_duration'];
            $supply_from_praksis_hidden_session = $request['supply_from_praksis_hidden'];

            $upload_file_description_session = $request['upload_file_description'];

            $upload_file_title_session = array();
            $upload_file_title_session_files = $request['upload_file_title'];
            if(!empty($upload_file_title_session_files)) {
                foreach ($upload_file_title_session_files as $uft) {
                    if(!empty($uft)) {
                        array_push($upload_file_title_session, $uft->getClientOriginalName());
                    }
                }
            }
            $visit_submited_succesfully = 2; // 0:initial value, 1:Success, 2:Unsuccess
            return redirect('benefiter/'.$benefiter_id.'/medical-folder')
                ->withInput(array(
                    'examination_date' => $request['examination_date'],
                    'medical_location_id' => $request['medical_location_id'],
                    'incident_type' => $request['incident_type'],
                    'height' => $request['height'],
                    'weight' => $request['weight'],
                    'temperature' => $request['temperature'],
                    'blood_pressure_systolic' => $request['blood_pressure_systolic'],
                    'blood_pressure_diastolic' => $request['blood_pressure_diastolic'],
                    'skull_perimeter' => $request['skull_perimeter'],
                ))
                // ALL THE BELLOW ARE SEND BACK TO THE FORM IF THE POST FAIL
                ->with('benefiter', $benefiter)
                ->with('benefiter_folder_number', $benefiter_folder_number)
                ->with('benefiter_medical_history_list', $benefiter_medical_history_list)
                ->with('doctor_id', $doctor_id)
                ->with('benefiter_id', $benefiter_id)
                ->with('medical_locations_array', $medical_locations_array)
                ->with('new_medical_location', $new_medical_location)
                ->with('ExamResultsLookup', $ExamResultsLookup)
                ->with('medical_visits_number', $medical_visits_number)
                ->with('visit_submited_succesfully', $visit_submited_succesfully)
                ->with('chronic_conditions_session', $chronic_conditions_session)
                ->with('lab_results_session', $lab_results_session)
                ->with('diagnosis_results_session', $diagnosis_results_session)
                ->with('hospitalization_session', $hospitalization_session)
                ->with('hospitalization_date_session', $hospitalization_date_session)
                ->with('referrals_session', $referrals_session)
                ->with('referrals_is_done_session', $referrals_is_done_session)
                ->with('examResultDescription_session', $examResultDescription_session)
                ->with('examResultLoukup_session', $examResultLoukup_session)
                ->with('medication_name_from_lookup_session', $medication_name_from_lookup_session)
                ->with('medication_new_name_session', $medication_new_name_session)
                ->with('medication_dosage_session', $medication_dosage_session)
                ->with('medication_duration_session', $medication_duration_session)
                ->with('supply_from_praksis_hidden_session', $supply_from_praksis_hidden_session)
                ->with('upload_file_description_session', $upload_file_description_session)
                ->with('upload_file_title_session', $upload_file_title_session)
                ->withErrors($validator->errors()->all());
        } else {
            // The visit belongs to the beneficiary of the address and to the logged-in doctor,
            // whatever the form sends.
            $saved = $this->medicalVisit->save_new_medical_visit_tables(array_merge($request->all(), [
                'benefiter_id' => $benefiter->id,
                'doctor_id' => auth()->id(),
            ]));
            $visit_submited_succesfully = $saved ? 1 : 2; // 0:initial value, 1:Success, 2:Unsuccess
            return redirect('benefiter/'.$benefiter_id.'/medical-folder')
                ->with('benefiter', $benefiter)
                ->with('benefiter_folder_number', $benefiter_folder_number)
                ->with('benefiter_medical_history_list', $benefiter_medical_history_list)
                ->with('doctor_id', $doctor_id)
                ->with('benefiter_id', $benefiter_id)
                ->with('medical_locations_array', $medical_locations_array)
                ->with('ExamResultsLookup', $ExamResultsLookup)
                ->with('medical_visits_number', $medical_visits_number)
//                ->with('icd10', $icd10)
                ->with('visit_submited_succesfully', $visit_submited_succesfully);
        }

    }

    //------------ GET MEDICAL VISIT MODAL DATA FOR BENEFITER FOR EACH VISIT ------//
    public function getMedicalVisitModal(Request $request, $id){
        // ------ MODAL: MEDICAL HISTORY DATA FOR EACH MEDICAL VISIT ------ //
        // initialize
        $med_visit_doctor = '';
        $med_visit_date = '';
        $med_visit_location = '';
        $med_visit_incident_type = '';
        $med_visit_chronic_conditions = '';
        $med_visit_height = '';
        $med_visit_weight = '';
        $med_visit_temperature = '';
        $med_visit_blood_pressure_systolic = '';
        $med_visit_blood_pressure_diastolic = '';
        $med_visit_skull_perimeter = '';
        $med_visit_exam_results = '';
        $med_visit_lab_results = '';
        $med_visit_diagnosis_results = '';
        $med_visit_medication = '';
        $med_visit_hospitalizations = '';
        $med_visit_referrals = '';
        $med_visit_uploads = '';
        // TODO CREATE A SERVICE THAT RETURNS A JSON WITH ALL INFO FOR EVERY VISIT
        $current_benefiter_medical_visit_id = $request['current_medical_visit'];
        $benefiter_medical_visits_list = $this->medicalVisit->findMedicalVisitsForBenefiter($id);
        $benefiter_folder_number = $this->medicalVisit->find_benefiter_folder_number($id);
        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        $ExamResultsLookup = $this->medicalVisit->get_medical_examination_results_from_lookup();
        // for every medical visit of the benefiter fetch the corresponding medical data from DB
        foreach($benefiter_medical_visits_list as $med_visit) {
            if ($med_visit['id'] == $current_benefiter_medical_visit_id) {
                //Doctor Name
                $med_visit_doctor = $med_visit['doctor']['name'] . ' ' . $med_visit['doctor']['lastname'];
                // Examination date
                if ($med_visit['medical_visit_date'] == null) {
                    $med_visit_date = $this->datesHelper->getFinelyFormattedStringDateFromDBDate($med_visit['created_at']);
                } else {
                    $med_visit_date = $this->datesHelper->getFinelyFormattedStringDateFromDBDate($med_visit['medical_visit_date']);
                }
                // Visit location
                $med_visit_location = $med_visit['medicalLocation']['description'];
                // Visit incident type
                $med_visit_incident_type = $med_visit['medicalIncidentType']['description'];
                // Chronic Conditions
                $med_visit_chronic_conditions = $this->medicalVisit->findMedicalChronicConditionsForBenefiter($id, $med_visit['id']);
                // physical examinations
                $med_visit_examination = $this->medicalVisit->findMedicalVisitExamination($med_visit['id']);
                // height
                $med_visit_height = $med_visit_examination['height'];
                // weight
                $med_visit_weight = $med_visit_examination['weight'];
                // temperature
                $med_visit_temperature = $med_visit_examination['temperature'];
                // blood pressure
                $med_visit_blood_pressure_systolic = $med_visit_examination['blood_pressure_systolic'];
                $med_visit_blood_pressure_diastolic = $med_visit_examination['blood_pressure_diastolic'];
                // skull_perimeter
                $med_visit_skull_perimeter = $med_visit_examination['skull_perimeter'];
                // Examination results
                $med_visit_exam_results = $this->medicalVisit->findMedicalVisitExaminationResults($med_visit['id']);
                // Lab results
                $med_visit_lab_results = $this->medicalVisit->findMedicalVisitLabResults($med_visit['id']);
                // Diagnosis results
                $med_visit_diagnosis_results = $this->medicalVisit->findMedicalVisitDiagnosisResults($med_visit['id']);
                // Medication
                $med_visit_medication = $this->medicalVisit->findMedicalVisitMedication($med_visit['id']);
                // Hospitalizations
                $med_visit_hospitalizations = $this->medicalVisit->findMedicalVisitHospitalizations($med_visit['id']);
                // Referrals
                $med_visit_referrals = $this->medicalVisit->findMedicalVisitReferrals($med_visit['id']);
                // Uploads
                $med_visit_uploads = $this->medicalVisit->findMedicalVisitUploads($med_visit['id']);
            }
        }
        // ------ END MODAL: MEDICAL HISTORY DATA FOR EACH MEDICAL VISIT ------ //
        return  view('partials.modals.medical_visit_info')
                                                    ->with('benefiter_medical_visits_list', $benefiter_medical_visits_list)
                                                    ->with('med_visit_doctor', $med_visit_doctor)
                                                    ->with('med_visit_location', $med_visit_location)
                                                    ->with('med_visit_incident_type', $med_visit_incident_type)
                                                    ->with('med_visit_chronic_conditions', $med_visit_chronic_conditions)
                                                    ->with('med_visit_height', $med_visit_height)
                                                    ->with('med_visit_weight', $med_visit_weight)
                                                    ->with('med_visit_temperature', $med_visit_temperature)
                                                    ->with('med_visit_blood_pressure_systolic', $med_visit_blood_pressure_systolic)
                                                    ->with('med_visit_blood_pressure_diastolic', $med_visit_blood_pressure_diastolic)
                                                    ->with('med_visit_skull_perimeter', $med_visit_skull_perimeter)
                                                    ->with('med_visit_exam_results', $med_visit_exam_results)
                                                    ->with('med_visit_lab_results', $med_visit_lab_results)
                                                    ->with('med_visit_diagnosis_results', $med_visit_diagnosis_results)
                                                    ->with('med_visit_medication', $med_visit_medication)
                                                    ->with('med_visit_hospitalizations', $med_visit_hospitalizations)
                                                    ->with('med_visit_referrals', $med_visit_referrals)
                                                    ->with('med_visit_uploads', $med_visit_uploads)
                                                    ->with('benefiter_folder_number', $benefiter_folder_number)
                                                    ->with('benefiter', $benefiter)
                                                    ->with('ExamResultsLookup', $ExamResultsLookup)
                                                    ->with('med_visit_date', $med_visit_date);
    }

    //------------ GET: EDIT MEDICAL VISIT ----------------------------------------//
    public function getMedicalVisitForEditing(Request $request, $id){
        $selected_medical_visit_id = $request['medical_visit_id'];
        $benefiter = $this->basicInfoService->findExistentBenefiter($id);
        $benefiter_medical_visits_list = $this->medicalVisit->findMedicalVisitsForBenefiter($id);
//        $doctor_id = $this->medicalVisit->findDoctorId();
        $medical_locations = $this->medicalVisit->medicalLocationsLookup();  //medical_location_lookup::get();
        $medical_incident_type = $this->medicalVisit->medicalIncidentTypeLookup();  //medical_incident_type_lookup::get();
        $medical_locations_array = $this->medicalVisit->reindex_array($medical_locations);
        $medical_incident_type_array = $this->medicalVisit->reindex_array($medical_incident_type);
        $ExamResultsLookup = $this->medicalVisit->examinationsResultsLookup();

        // initialize variables
        $med_visit_doctor = '';
        $med_visit_date = '';
        $med_visit_location_id = '';
        $med_visit_incident_type_id = '';
        $med_visit_chronic_conditions = '';
        $med_visit_height = '';
        $med_visit_weight = '';
        $med_visit_temperature = '';
        $med_visit_blood_pressure_systolic = '';
        $med_visit_blood_pressure_diastolic = '';
        $med_visit_skull_perimeter = '';
        $med_visit_exam_results = '';
        $med_visit_lab_results = '';
        $med_visit_diagnosis_results = '';
        $med_visit_medication = '';
        $med_visit_hospitalizations = '';
        $med_visit_referrals = '';
        $med_visit_uploads = '';
        $doctor_id = null;

        // for every medical visit of the benefiter fetch the corresponding medical data from DB
        foreach($benefiter_medical_visits_list as $med_visit) {
            if ($med_visit['id'] == $selected_medical_visit_id) {
                $doctor_id = $med_visit['doctor']['id'];
                //Doctor Name
                $med_visit_doctor = $med_visit['doctor']['name'] . ' ' . $med_visit['doctor']['lastname'];
                // Examination date
                if ($med_visit['medical_visit_date'] == null) {
                    $med_visit_date = $this->datesHelper->getFinelyFormattedStringDateFromDBDate($med_visit['created_at']);
                } else {
                    $med_visit_date = $this->datesHelper->getFinelyFormattedStringDateFromDBDate($med_visit['medical_visit_date']);
                }
                // Visit location
                $med_visit_location_id = $med_visit['medical_location_id'];
                // Visit incident type
                $med_visit_incident_type_id = $med_visit['medical_incident_id'];
                // Chronic Conditions
                $med_visit_chronic_conditions = $this->medicalVisit->findMedicalChronicConditionsForBenefiter($id, $med_visit['id']);
                // physical examinations
                $med_visit_examination = $this->medicalVisit->findMedicalVisitExamination($med_visit['id']);
                // height
                $med_visit_height = $med_visit_examination['height'];
                // weight
                $med_visit_weight = $med_visit_examination['weight'];
                // temperature
                $med_visit_temperature = $med_visit_examination['temperature'];
                // blood pressure
                $med_visit_blood_pressure_systolic = $med_visit_examination['blood_pressure_systolic'];
                $med_visit_blood_pressure_diastolic = $med_visit_examination['blood_pressure_diastolic'];
                // skull_perimeter
                $med_visit_skull_perimeter = $med_visit_examination['skull_perimeter'];
                // Examination results
                $med_visit_exam_results = $this->medicalVisit->findMedicalVisitExaminationResults($med_visit['id']);
                // Lab results
                $med_visit_lab_results = $this->medicalVisit->findMedicalVisitLabResults($med_visit['id']);
                // Diagnosis results
                $med_visit_diagnosis_results = $this->medicalVisit->findMedicalVisitDiagnosisResults($med_visit['id']);
                // Medication
                $med_visit_medication = $this->medicalVisit->findMedicalVisitMedication($med_visit['id']);
                // Hospitalizations
                $med_visit_hospitalizations = $this->medicalVisit->findMedicalVisitHospitalizations($med_visit['id']);
                // Referrals
                $med_visit_referrals = $this->medicalVisit->findMedicalVisitReferrals($med_visit['id']);
                // Uploads
                $med_visit_uploads = $this->medicalVisit->findMedicalVisitUploads($med_visit['id']);

            }
        }
        // The visit is not a visit of this beneficiary.
        abort_if($doctor_id === null, 404);
        return view('partials.forms.medical-visit.medical_visit_edit')
            ->with('selected_medical_visit_id',$selected_medical_visit_id)
            ->with('benefiter',$benefiter)
            ->with('doctor_id',$doctor_id)
            ->with('med_visit_date',$med_visit_date)
            ->with('med_visit_location_id',$med_visit_location_id)
            ->with('med_visit_incident_type_id',$med_visit_incident_type_id)
            ->with('med_visit_chronic_conditions',$med_visit_chronic_conditions)
            ->with('med_visit_height',$med_visit_height)
            ->with('med_visit_weight',$med_visit_weight)
            ->with('med_visit_temperature',$med_visit_temperature)
            ->with('med_visit_blood_pressure_systolic',$med_visit_blood_pressure_systolic)
            ->with('med_visit_blood_pressure_diastolic',$med_visit_blood_pressure_diastolic)
            ->with('med_visit_skull_perimeter',$med_visit_skull_perimeter)
            ->with('med_visit_exam_results',$med_visit_exam_results)
            ->with('med_visit_lab_results',$med_visit_lab_results)
            ->with('med_visit_diagnosis_results',$med_visit_diagnosis_results)
            ->with('med_visit_medication',$med_visit_medication)
            ->with('med_visit_hospitalizations', $med_visit_hospitalizations)
            ->with('med_visit_referrals',$med_visit_referrals)
            ->with('med_visit_uploads',$med_visit_uploads)
            ->with('medical_locations_array',$medical_locations_array)
            ->with('medical_incident_type_array',$medical_incident_type_array)
            ->with('ExamResultsLookup',$ExamResultsLookup)
            ->with('med_visit_doctor',$med_visit_doctor)
            ->with('$benefiter_medical_visits_list', $benefiter_medical_visits_list);
    }

    //------------ POST: EDIT MEDICAL VISIT ---------------------------------------//
    public function postMedicalVisitFromEditing(Request $request, $id){
        // The files of a visit: common document and image types, 10 MB at most each.
        $request->validate(['upload_file_title.*' => ['nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif,webp,doc,docx,odt,xls,xlsx,ods,txt,rtf']]);
        // Only a visit of the beneficiary of the address.
        $visit = \App\Models\Benefiters_Tables_Models\medical_visits::where('id', (int) $request['selected_medical_visit_id'])
            ->where('benefiter_id', (int) $id)
            ->firstOrFail();
        $selected_medical_visit_id = $visit->id;
        $benefiter_id = $visit->benefiter_id;
//        dd($request->all());

        // Post Validation
        $validator = $this->medicalVisit->medicalValidation($request->all());
        if($validator->fails()){
            $visit_submited_succesfully = 2; // 0:initial value, 1:Success, 2:Unsuccess
            return redirect('benefiter/'.$benefiter_id.'/editMedicalVisit?medical_visit_id='.$selected_medical_visit_id)
                ->with('visit_submited_succesfully', $visit_submited_succesfully)
                ->withErrors($validator->errors()->all());
        } else {
            $saved = $this->medicalVisit->update_medical_visit_tables($request->all(), $selected_medical_visit_id);
            $visit_submited_succesfully = $saved ? 3 : 2; // 0:initial value, 1:Success, 2:Unsuccess, 3: Success update
            return redirect('benefiter/'.$benefiter_id.'/medical-folder')
                ->with('selected_medical_visit_id', $selected_medical_visit_id)
                ->with('visit_submited_succesfully', $visit_submited_succesfully);
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
