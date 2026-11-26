<?php namespace App\Services;

use App\Models\Benefiters_Tables_Models\Benefiter;
use App\Models\Benefiters_Tables_Models\medical_chronic_conditions;
use App\Models\Benefiters_Tables_Models\medical_chronic_conditions_lookup;
use App\Models\Benefiters_Tables_Models\medical_diagnosis_results;
use App\Models\Benefiters_Tables_Models\medical_examination_results;
use App\Models\Benefiters_Tables_Models\medical_examinations;
use App\Models\Benefiters_Tables_Models\medical_hospitalizations;
use App\Models\Benefiters_Tables_Models\medical_visits;
use App\Models\Benefiters_Tables_Models\medical_laboratory_results;
use App\Models\Benefiters_Tables_Models\medical_examination_results_lookup;
use App\Models\Benefiters_Tables_Models\medical_medication;
use App\Models\Benefiters_Tables_Models\medical_medication_lookup;
use App\Models\Benefiters_Tables_Models\medical_referrals;
use App\Models\Benefiters_Tables_Models\medical_uploads;
use App\Models\Benefiters_Tables_Models\medical_incident_type_lookup;
use App\Models\Benefiters_Tables_Models\ICD10;
use App\Models\Benefiters_Tables_Models\medical_location_lookup;
use App\Services\DatesHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class BenefiterMedicalFolderService
{

    private $datesHelper;

    public function __construct(DatesHelper $datesHelper, private readonly \Illuminate\Contracts\Auth\Guard $auth)
    {
        $this->datesHelper = $datesHelper;
    }

    public function medicalValidation($request){
        $rules = array(
            'examination_date'         => 'required|date',
            'medical_location_id'      => 'max:255',
            'medical_incident_id'      => 'integer',
            'new_medical_location'     => 'max:255',
            'height'                   => 'numeric',
            'weight'                   => 'numeric',
            'temperature'              => 'numeric',
            'blood_pressure_systolic'  => 'numeric',
            'blood_pressure_diastolic' => 'numeric',
            'skull_perimeter'          => 'numeric',
        );
        // VALIDATE - chronic conditions
        $chronic_conditions = $request['chronic_conditions'];
        foreach ($chronic_conditions as $i=>$cc){
            $rules['chronic_conditions.'.$i] = 'max:255';
        }

        // VALIDATE - clinical results
        if(!empty($request['examResultLoukup'])){
            $examResultsDescription = $request['examResultDescription'];
            $examResults = $request['examResultLoukup'];
            for($i=0; $i<legacy_count($examResults) ; $i++) {
                if(!empty($examResults[$i]) && !empty($examResultsDescription[$i])){
                    for ($j = 0; $j < legacy_count($examResults[$i]); $j++) {
                        if(!empty($examResults[$i][$j])){
                            $rules['examResultDescription.'.$i] = 'max:255';
                            $rules['examResultLoukup.'.$i.'.'.$j] = 'max:255';
                        }
                    }
                }
            }
        }

        // VALIDATE - Lab results
        $lab_results = $request['lab_results'];
        foreach ($lab_results as $i=>$lr){
            $rules['lab_results.'.$i] = 'max:2000';
        }

        // VALIDATE - Diagnosis results
        $diagnosis_results = $request['diagnosis_results'];
        foreach ($diagnosis_results as $i=>$dr){
            $rules['diagnosis_results.'.$i] = 'max:2000';
        }

        // VALIDATE - Hospitalizations
        $hospitalizations = $request['hospitalization'];
        foreach ($hospitalizations as $i=>$h){
            $rules['hospitalization.'.$i] = 'max:2000';
            $rules['haspitalization_date'.$i] = 'date';
        }

        // VALIDATE - Medication
        $count = 0;
        if(!empty($request['medication_name_from_lookup'])){
            $count = sizeof($request['medication_name_from_lookup']);
        }elseif(!empty($request['medication_dosage'])){
            $count = sizeof($request['medication_dosage']);
        }
        elseif(!empty($request['medication_duration'])){
            $count = sizeof($request['medication_duration']);
        }
        elseif(!empty($request['supply_from_praksis_hidden'])){
            $count = sizeof($request['supply_from_praksis_hidden']);
        }
        if($count>0){
            for($i=0 ; $i<$count ; $i++){
                if(!empty($request['medication_name_from_lookup'])){
                    $rules['medication_name_from_lookup.'.$i] = 'max:255';
                    $rules['medication_new_name.'.$i] = 'max:255';
                }else{
                    $rules['medication_new_name.'.$i] = 'max:255';
                    $rules['medication_name_from_lookup.'.$i] = 'max:255';
                }
                    $rules['medication_dosage.'.$i] = 'max:255';
                    $rules['medication_duration.'.$i] = 'max:255';
            }
        }

        // VALIDATE - Referrals
        $referrals = $request['referrals'];
        foreach ($referrals as $i=>$ref){
            $rules['referrals.'.$i] = 'max:255';
        }

        // VALIDATE - Uploaded files
        $upload_file_description = $request['upload_file_description'];
        foreach ($upload_file_description as $i=>$fd){
            $rules['upload_file_description.'.$i] = 'max:255';
        }
        $validator = Validator::make($request, $rules);
        $validator->sometimes('new_medical_location', 'required', function($request){
            return $request['medical_location_id'] == "new_location";
        });
        return $validator;
    }

    /**
     * Saves a new visit with all its sections, in one transaction: all or nothing.
     *
     * @return bool False when the visit could not be saved; nothing is saved then.
     */
    public function save_new_medical_visit_tables($request){
        try {
            \DB::transaction(fn () => $this->save_new_medical_visit_rows($request));
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('The medical visit was not saved.', ['exception' => $e]);
            return false;
        }
    }

    private function save_new_medical_visit_rows($request){
        // medical visit table
        $medicalVisit_id = $this->create_medical_visit($request);
        // chronic conditions table
        $this->save_medical_chronic_conditions($request, $medicalVisit_id);
        //medical_examination_results table
        $this->save_medical_examination_results($request, $medicalVisit_id);
        //medical_examinations table
        $this->save_medical_examinations($request, $medicalVisit_id);
        // laboratory results
        $this->save_medical_laboratory_results($request, $medicalVisit_id);
        // diagnosis results
        $this->save_medical_diagnosis_results($request, $medicalVisit_id);
        // hospitalizations
        $this->save_medical_hospitalizations($request, $medicalVisit_id);
        // medication table
        $this->save_medical_medication($request, $medicalVisit_id);
        // medical referrals
        $this->save_medical_referrals($request, $medicalVisit_id);
        // medical file uploads
        $this->save_medical_uploads($request, $medicalVisit_id);
    }

    //----------- medical_visit table ------------------------------------DONE//
    private function create_medical_visit($request){
        $newMedicalVisit = new medical_visits();
        $newMedicalVisit->benefiter_id = $request['benefiter_id'];
        $newMedicalVisit->doctor_id = $request['doctor_id'];

        $newMedicalVisit->medical_location_id = $this->add_dynamically_medical_locations_to_lookup($request);
        $newMedicalVisit->medical_incident_id = $request['medical_incident_id'];
        $newMedicalVisit->medical_visit_date = $this->datesHelper->makeDBFriendlyDate($request['examination_date']);
        $newMedicalVisit->save();
        return $newMedicalVisit->id;
    }
    private function add_dynamically_medical_locations_to_lookup($request){
        if(!is_int($request['medical_location_id']) && !empty($request['new_medical_location'])){
            // add the input value to medical locations table and return the table id
            $new_medical_location = new medical_location_lookup();
            $new_medical_location->description = $request['new_medical_location'];
            $new_medical_location->save();

            return $new_medical_location->id;
        }
        // else just return the $request['medical_location_id']
        else{
            return $request['medical_location_id'];
        }
    }

    //----------- medical_chronic_conditions table -----------------------DONE//
    // DB save
    private function save_medical_chronic_conditions($request, $id){
        $request_medical_chronic_conditions = $this->get_medical_chronic_conditions($request);
        foreach($request_medical_chronic_conditions as $cc){
            if(!empty($cc)){
                $medical_chronic_conditions = new medical_chronic_conditions();
                // save to lookup first
                $medical_chronic_conditions_lookup = new medical_chronic_conditions_lookup();
                $medical_chronic_conditions_lookup->description = $cc;
                $medical_chronic_conditions_lookup->save();
                // then to chronic conditions table
                $medical_chronic_conditions->benefiters_id = $request['benefiter_id'];
                $medical_chronic_conditions->medical_visit_id = $id;
                $medical_chronic_conditions->description = $cc;
                $medical_chronic_conditions->chronic_condition_id = $medical_chronic_conditions_lookup->id;
                $medical_chronic_conditions->save();
            }
        }
    }
    // post request
    private function get_medical_chronic_conditions($request){
        $chronic_conditions = $request['chronic_conditions'];
        $chronic_conditions_array = [];
        foreach ($chronic_conditions as $cc){
            array_push($chronic_conditions_array, $cc);
        }
        return $chronic_conditions_array;
    }

    //----------- clinical_examination_results table ----------------------DONE//
    // DB save
    private function save_medical_examination_results($request, $id){
        if(!empty($request['examResultLoukup'])){
            $request_med_exams_results = $request['examResultLoukup'];
            $request_med_exams_description = $request['examResultDescription'];
            for($i=0; $i<legacy_count($request_med_exams_results) ; $i++){
                if(!empty($request_med_exams_results[$i])) {
                    for ($j = 0; $j < legacy_count($request_med_exams_results[$i]); $j++) {
                        if (!empty($request_med_exams_results[$i][$j])) {
                            $medical_examination_results = new medical_examination_results();
                            $medical_examination_results->description = $request_med_exams_description[$i];
                            $medical_examination_results->icd10_id = $request_med_exams_results[$i][$j];
                            $medical_examination_results->medical_visit_id = $id;
                            // get medical examinations list from the lookup table
                            $med_exams_lookup_item = medical_examination_results_lookup::where('id', '=', $i + 1)->value('id');
                            $medical_examination_results->results_lookup_id = $med_exams_lookup_item;

                            $medical_examination_results->save();
                        }
                    }
                }
            }
        }
    }

    //----------- medical_examinations table ----------------------------DONE//
    // DB save
    private function save_medical_examinations($request, $id){
        $medical_examination = new medical_examinations();

        $medical_examination->height = $request['height'];
        $medical_examination->weight = $request['weight'];
        $medical_examination->temperature = $request['temperature'];
        $medical_examination->blood_pressure_systolic = $request['blood_pressure_systolic'];
        $medical_examination->blood_pressure_diastolic = $request['blood_pressure_diastolic'];
        $medical_examination->skull_perimeter = $request['skull_perimeter'];
        $medical_examination->examination_date = $this->datesHelper->makeDBFriendlyDate($request['examination_date']);
        $medical_examination->medical_visit_id = $id;

        $medical_examination->save();
    }

    //----------- medical_laboratory_results table ----------------------DONE//
    // DB save
    private function save_medical_laboratory_results($request, $id){
        $request_lab_results = $this->medical_laboratory_results($request);
        foreach($request_lab_results as $rlr){
            if(!empty($rlr)){
                $lab_results = new medical_laboratory_results();

                $lab_results->laboratory_results = $rlr;
                $lab_results->medical_visit_id = $id;

                $lab_results->save();
            }
        }
    }
    // post request
    private function medical_laboratory_results($request){
        $lab_results = $request['lab_results'];
        $lab_results_array = [];
        foreach ($lab_results as $lr){
            array_push($lab_results_array, $lr);
        }
        return $lab_results_array;
    }

    //----------- medical_diagnosis_results table ----------------------DONE//
    // DB save
    private function save_medical_diagnosis_results($request, $id){
        $request_diagnosis_results = $this->medical_diagnosis_results($request);
        foreach($request_diagnosis_results as $rdr){
            if(!empty($rdr)){
                $diagnosis_results = new medical_diagnosis_results();

                $diagnosis_results->diagnosis_results = $rdr;
                $diagnosis_results->medical_visit_id = $id;

                $diagnosis_results->save();
            }
        }
    }
    // post request
    private function medical_diagnosis_results($request){
        $diagnosis_results = $request['diagnosis_results'];
        $diagnosis_results_array = [];
        foreach ($diagnosis_results as $dr){
            array_push($diagnosis_results_array, $dr);
        }
        return $diagnosis_results_array;
    }

    //----------- medical_hospitalizations table ----------------------DONE//
    // DB save
    private function save_medical_hospitalizations($request, $id){
        $request_hospitalizations = $this->medical_hospitalizations($request);
        $request_hospitalization_dates = $this->medical_hospitalization_dates($request);
        foreach($request_hospitalizations as $i=>$rh){
            if(!empty($rh)){
                $hospitalization = new medical_hospitalizations();

                $hospitalization->hospitalizations = $rh;
                $hospitalization->hospitalization_date = $this->datesHelper->makeDBFriendlyDate($request_hospitalization_dates[$i]);
                $hospitalization->medical_visit_id = $id;

                $hospitalization->save();
            }
        }
    }
    // post request
    private function medical_hospitalizations($request){
        $hospitalizations = $request['hospitalization'];
        $hospitalizations_array = [];
        foreach ($hospitalizations as $h){
            array_push($hospitalizations_array, $h);
        }
        return $hospitalizations_array;
    }
    // post request
    private function medical_hospitalization_dates($request){
        $hospitalization_dates = $request['hospitalization_date'];
        $hospitalization_dates_array = [];
        foreach ($hospitalization_dates as $hd){
            array_push($hospitalization_dates_array, $hd);
        }
        return $hospitalization_dates_array;
    }

    //----------- medical_medication table ----------------------------DONE//
    // DB save
    private function save_medical_medication($request, $id){
        if(!empty($request['medication_name_from_lookup'])){
            $request_medication_name_from_lookup = $request['medication_name_from_lookup'];
        }

        if(!empty($request['medication_new_name'])){
            $request_medication_new_name = $request['medication_new_name'];
        }

        $request_medication_name = [];

        $request_medication_dosage = $request['medication_dosage'];
        $request_medication_duration = $request['medication_duration'];
        $request_supply_from_praksis = null;
        if(!empty($request['supply_from_praksis_hidden'])){
            $request_supply_from_praksis = $request['supply_from_praksis_hidden'];
        }

        $request_medication_Number = legacy_count($request_medication_dosage);

        for($i=0; $i<$request_medication_Number ; $i++){
            // Medicinal condition DB entry (save to DB only if all fields are filled)
            if(!empty($request_medication_dosage[$i]) && !empty($request_medication_duration[$i])) {
                $med_medication = new medical_medication();

                // check if the request comes from the auto complete select (from lookup)
                if (empty($request_medication_new_name[$i]) && !empty($request_medication_name_from_lookup[$i])) {
                    $request_medication_name[$i] = $request_medication_name_from_lookup[$i];

                    $med_medication->dosage = $request_medication_dosage[$i];
                    $med_medication->duration = $request_medication_duration[$i];
                    if(!empty($request_supply_from_praksis)){
                        $med_medication->supply_from_praksis = (bool) $request_supply_from_praksis[$i];
                    }else{
                        $med_medication->supply_from_praksis = false;
                    }
                    $med_medication->medical_visit_id = $id;
                    $med_medication->medication_lookup_id = $request_medication_name[$i];

                    $med_medication->save();
                }
                // check if the request comes from the input field.
                else if(!empty($request_medication_new_name[$i])){
                    $request_medication_name[$i] = $request_medication_new_name[$i];

                    $med_medication_lookup = new medical_medication_lookup();
                    $med_medication_lookup->description = $request_medication_name[$i];
                    $med_medication_lookup->save();

                    // then continue to medication table
                    $med_medication->dosage = $request_medication_dosage[$i];
                    $med_medication->duration = $request_medication_duration[$i];
                    if(!empty($request_supply_from_praksis)){
                        $med_medication->supply_from_praksis = (bool) $request_supply_from_praksis[$i];
                    }else{
                        $med_medication->supply_from_praksis = false;
                    }
                    $med_medication->medical_visit_id = $id;
                    $med_medication->medication_lookup_id = $med_medication_lookup->id;

                    $med_medication->save();
                }
            }
        }
    }

    //----------- medical_referrals table ---------------------------DONE//
    // DB save
    private function save_medical_referrals($request, $id){
        $request_medical_referrals = $this->medical_referrals($request);
        $request_medical_referrals_is_done = $this->medical_referrals_is_done($request);
        foreach($request_medical_referrals as $i => $rmr){
            if(!empty($rmr)){
                $med_referral = new medical_referrals();
                $med_referral->referrals = $rmr;
                $med_referral->medical_visit_id = $id;
                $med_referral->is_done_id = $request_medical_referrals_is_done[$i];
                $med_referral->save();
            }
        }
    }
    // post request
    private function medical_referrals($request){
        $referrals = $request['referrals'];
        $referrals_array = [];
        foreach ($referrals as $ref){
            array_push($referrals_array, $ref);
        }
        return $referrals_array;
    }
    // post request
    private function medical_referrals_is_done($request){
        $referrals_is_done = $request['is_done_id'];
        $referrals_is_done_array = [];
        foreach ($referrals_is_done as $rid){
            array_push($referrals_is_done_array, $rid);
        }
        return $referrals_is_done_array;
    }

    //----------- medical_uploads table ----------------------------//
    // DB save
    private function save_medical_uploads($request, $id){
        $request_upload_file_description = $request['upload_file_description'];
        $request_upload_file_title = $request['upload_file_title'];

        $file = request()->file('upload_file_title');

        $files_numbers = legacy_count($request_upload_file_title);
        for ($i = 0; $i < $files_numbers; $i++) {
            if(!empty($file[$i])){

                [$fileName, $storedPath] = $this->storeMedicalUpload($file[$i], (int) $id);
                $medical_upload = new medical_uploads();
                $medical_upload->title = $fileName;
                $medical_upload->description = $request_upload_file_description[$i];
                $medical_upload->path = $storedPath;
                $medical_upload->medical_visit_id = $id;

                $medical_upload->save();
            }
        }
    }

    // ------------------------------------------------------------------ //
    // PART 3 : EDIT SAVED MEDICAL VISIT
    //--------------------------------------------------------------------//

    /**
     * Saves the changes of a visit with all its sections, in one transaction: all or nothing.
     *
     * @return bool False when the changes could not be saved; nothing changes then.
     */
    public function update_medical_visit_tables($request, $selected_medical_visit_id){
        try {
            \DB::transaction(fn () => $this->update_medical_visit_rows($request, $selected_medical_visit_id));
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('The medical visit was not updated.', ['exception' => $e]);
            return false;
        }
    }

    /**
     * Saves the changes of a visit. The rows of each section are deleted and saved
     * again with the methods of a new visit: one algorithm for both, and a row that
     * the user empties is removed. The files keep their own method: a saved file is
     * not sent again.
     */
    private function update_medical_visit_rows($request, $selected_medical_visit_id){
        $visitId = $this->update_medical_visit($request, $selected_medical_visit_id);
        // The rows belong to the beneficiary of the visit, whatever the form sends.
        $request = array_merge($request, ['benefiter_id' => medical_visits::whereKey($visitId)->value('benefiter_id')]);

        foreach ([medical_chronic_conditions::class, medical_examination_results::class, medical_examinations::class,
                  medical_laboratory_results::class, medical_diagnosis_results::class, medical_hospitalizations::class,
                  medical_medication::class, medical_referrals::class] as $section) {
            $section::where('medical_visit_id', $visitId)->delete();
        }

        $this->save_medical_chronic_conditions($request, $visitId);
        $this->save_medical_examination_results($request, $visitId);
        $this->save_medical_examinations($request, $visitId);
        $this->save_medical_laboratory_results($request, $visitId);
        $this->save_medical_diagnosis_results($request, $visitId);
        $this->save_medical_hospitalizations($request, $visitId);
        $this->save_medical_medication($request, $visitId);
        $this->save_medical_referrals($request, $visitId);
        $this->update_medical_uploads($request, $visitId);
    }

    //----------- medical_visit table ------------------------------------DONE//
    private function update_medical_visit($request, $selected_medical_visit_id){
        $updateddMedicalVisit = medical_visits::findOrFail($selected_medical_visit_id);

        // The beneficiary and the doctor of a visit do not change.
        $updateddMedicalVisit->medical_location_id = $this->add_dynamically_medical_locations_to_lookup($request);
        $updateddMedicalVisit->medical_incident_id = $request['medical_incident_id'];
        $updateddMedicalVisit->medical_visit_date = $this->datesHelper->makeDBFriendlyDate($request['examination_date']);
        $updateddMedicalVisit->save();
        return $updateddMedicalVisit->id;
    }

    //----------- medical_uploads table ----------------------------//
    // DB save
    private function update_medical_uploads($request, $selected_medical_visit_id){
        $request_upload_file_description = $request['upload_file_description'];
        $request_upload_file_title = $request['upload_file_title'];
        $file = request()->file('upload_file_title');
        $requests_count = legacy_count($request_upload_file_title);

        $saved_files = medical_uploads::where("medical_visit_id", $selected_medical_visit_id)->get();
        $saved_files_count = legacy_count($saved_files);
        $counter = 0;
        // if the request array is bigger than the saved then update what is saved and then add new rows for the new requests
        if($requests_count > $saved_files_count){
            for($i=0; $i<$requests_count ; $i++) {
                if ($counter < $saved_files_count) {
                    if(!empty($file[$i])){
                        [$fileName, $storedPath] = $this->storeMedicalUpload($file[$i], (int) $selected_medical_visit_id);
                        $medical_upload = medical_uploads::find($saved_files[$counter]['id']);
                        $medical_upload->title = $fileName;
                        $medical_upload->description = $request_upload_file_description[$i];
                        $medical_upload->path = $storedPath;
                        $medical_upload->medical_visit_id = $selected_medical_visit_id;
                        $medical_upload->save();
                    }
                } else {
                    if(!empty($file[$i])){
                        [$fileName, $storedPath] = $this->storeMedicalUpload($file[$i], (int) $selected_medical_visit_id);
                        $medical_upload = new medical_uploads();
                        $medical_upload->title = $fileName;
                        $medical_upload->description = $request_upload_file_description[$i];
                        $medical_upload->path = $storedPath;
                        $medical_upload->medical_visit_id = $selected_medical_visit_id;
                        $medical_upload->save();
                    }
                }
                $counter++;
            }
            // if the request array is smaller then update some rows and delete the rest
        }else{
            for($j=0; $j<$saved_files_count; $j++){
                if ($counter < $requests_count) {
                    $medical_upload = medical_uploads::find($saved_files[$counter]['id']);
                    $medical_upload->description = $request_upload_file_description[$j];
                    if(!empty($file[$j])){
                        [$fileName, $storedPath] = $this->storeMedicalUpload($file[$j], (int) $selected_medical_visit_id);
                        $medical_upload->title = $fileName;
                        $medical_upload->path = $storedPath;
                        $medical_upload->medical_visit_id = $selected_medical_visit_id;
                    }
                    $medical_upload->save();
                } else {
                    $medical_upload = medical_uploads::find($saved_files[$counter]['id']);
                    $medical_upload->delete();
                }
                $counter++;
            }
        }

    }

    // ------------------------------------------------------------------ //
    // PART 3 : END
    //--------------------------------------------------------------------//

    // FUNCTIONS USED BY MANY

    /**
     * The rows of a lookup table as id => description, for the drop-down lists.
     *
     * @param  iterable<object>  $rows
     * @return array<int, string>
     */
    public function reindex_array($rows){
        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row->id] = (string) $row->description;
        }
        return $byId;
    }

    // get logged in user id
    public function get_logged_in_user_id(){
        $user_id = $this->auth->id();
        return $user_id;
    }
    // medical visits for each benefiter
    public function findMedicalVisitsForBenefiter($id){
        return medical_visits::where('benefiter_id', $id)->with('doctor', 'medicalLocation', 'medicalIncidentType')->orderBy('medical_visit_date', 'desc')->get();
    }
    // chronic conditions for each benefiter
    public function findMedicalChronicConditionsForBenefiter($benefiter_id, $medical_visit_id){
        return medical_chronic_conditions::where('benefiters_id', $benefiter_id)->where('medical_visit_id', $medical_visit_id)->with('chronic_conditions_lookup')->get();;
    }
    // number of medical visits foreach benefiter
    public function benefiter_medical_visits_number($id){
        $medical_visits_number = medical_visits::where('benefiter_id', $id)->count();
        return $medical_visits_number;
    }
    // find benefiter folder number
    public function find_benefiter_folder_number($id){
        $benefiter_folder_number = Benefiter::where('id', '=', $id)->first()->folder_number;
        return $benefiter_folder_number;
    }
    // examination results
    public function examinationsResultsLookup(){
        $examResultsLookup = medical_examination_results_lookup::get()->all();
        return $examResultsLookup;
    }
    // get all medical examinaiton results from lookup
    public function get_medical_examination_results_from_lookup(){
        $ExamResultsLookup = medical_examination_results_lookup::get()->all();
        return $ExamResultsLookup;
    }
    // Medical Visit location
    public function medicalLocationsLookup(){
        $medical_locations = medical_location_lookup::get();
        return $medical_locations;
    }
    // Incident type
    public function medicalIncidentTypeLookup(){
        $medical_incident_type = medical_incident_type_lookup::get();
        return $medical_incident_type;
    }
    // doctor id
    public function findDoctorId(){
        $doctor_id = $this->auth->id();
        return $doctor_id;
    }
    // physical examinations
    public function findMedicalVisitExamination($med_visit_id){
        $med_visit_examination = medical_examinations::where('medical_visit_id', $med_visit_id)->first();
        return $med_visit_examination;
    }
    // Examination results
    public function findMedicalVisitExaminationResults($med_visit_id){
        $med_visit_exam_results = medical_examination_results::where('medical_visit_id', $med_visit_id)->with('icd10')->orderBy('results_lookup_id')->get();
        return $med_visit_exam_results;
    }
    // get all medical visits for a benefiter
    public function get_all_medical_visits_for_benefiter($id){
        $benefiter_medical_history_list = medical_visits::where('benefiter_id', $id)->with('doctor', 'medicalLocation')->get();
        return $benefiter_medical_history_list;
    }
    // count benefiter's medical visits
    public function count_medical_visits_for_a_benefiter($id){
        $medical_visits_number = medical_visits::where('benefiter_id', $id)->count();
        return $medical_visits_number;
    }
    // Lab results
    public function findMedicalVisitLabResults($med_visit_id){
        $med_visit_lab_results = medical_laboratory_results::where('medical_visit_id', $med_visit_id)->get();
        return $med_visit_lab_results;
    }
    // Diagnosis results
    public function findMedicalVisitDiagnosisResults($med_visit_id){
        $med_visit_diagnosis_results = medical_diagnosis_results::where('medical_visit_id', $med_visit_id)->get();
        return $med_visit_diagnosis_results;
    }
    // Medication
    public function findMedicalVisitMedication($med_visit_id){
        $med_visit_medication = medical_medication::where('medical_visit_id', $med_visit_id)->with('medical_medication_lookup')->get();
        return $med_visit_medication;
    }
    // Hospitalization
    public function findMedicalVisitHospitalizations($med_visit_id){
        $med_visit_hospitalizations = medical_hospitalizations::where('medical_visit_id', $med_visit_id)->get();
        return $med_visit_hospitalizations;
    }
    // Referrals
    public function findMedicalVisitReferrals($med_visit_id){
        $med_visit_referrals = medical_referrals::where('medical_visit_id', $med_visit_id)->get();
        return $med_visit_referrals;
    }
    // Uploads
    public function findMedicalVisitUploads($med_visit_id){
        $med_visit_uploads = medical_uploads::where('medical_visit_id', $med_visit_id)->get();
        return $med_visit_uploads;
    }
    // find name of ICD10 list by id
    public function getICD10By_id($id){
        $description = ICD10::where('id', '=', $id)->first()->description;
        $code = ICD10::where('id', '=', $id)->first()->code;
        $result = $code .': '. $description;
        return $result;
    }
    // find medication name by id
    public function getMedicationLookupBy_id($id){
        $medicine_name = medical_medication_lookup::where('id', '=', $id)->first()->description;
        return $medicine_name;
    }
    public function getAllMedicalLocations(){
        return medical_location_lookup::get()->all();
    }
    // find medical medicinal name from lookup using partial name
    public function get_full_medication_name($partial_name){
        $full_medication_name = medical_medication_lookup::where('description','LIKE', '%'.$partial_name.'%' );
        return $full_medication_name;
    }
    // find ICD10 from lookup using partial name
    public function get_full_icd10_description($partial_name){
        $full_icd10_description = ICD10::where('description','LIKE', '%'.$partial_name.'%' )
                                        ->orWhere('code', 'LIKE', '%'.$partial_name.'%')->get();
        return $full_icd10_description;
    }
    // finds and returns all the benefiter's referrals ordered by medical visit
    public function findAllMedicalVisitsReferralsForBenefiter($visitsList){
        $result = array();
        foreach($visitsList as $visit){
            $tmp = medical_referrals::where('medical_visit_id', '=', $visit->id)->get();
            array_push($result, $tmp);
        }
        return $result;
    }

    /**
     * Stores an uploaded file of a medical visit in the private storage.
     *
     * The file gets a random name: the name from the browser is only its title.
     * Nobody can open it by its address: MedicalUploadController serves it to
     * logged-in users. The controllers validate the type and the size before.
     *
     * @return array{0: string, 1: string} The title and the path in the storage.
     */
    private function storeMedicalUpload(\Illuminate\Http\UploadedFile $file, int $medicalVisitId): array
    {
        $title = 'medical_visit-'.$medicalVisitId.'-'.preg_replace('/[^\pL\pN._ -]+/u', '_', basename($file->getClientOriginalName()));
        $path = $file->store('medical-uploads', 'local');
        if ($path === false) {
            throw new \RuntimeException('The medical file could not be stored.');
        }

        return [mb_substr($title, 0, 250), $path];
    }

    /**
     * Everything about one visit of a beneficiary, for its window and its edit form.
     *
     * @return array<string, mixed>|null Null when the visit is not a visit of this beneficiary.
     *     The keys are the names of the view variables (med_visit_…).
     */
    public function visitDetails(int $benefiterId, int $visitId): ?array
    {
        $visit = $this->findMedicalVisitsForBenefiter($benefiterId)->firstWhere('id', $visitId);
        if ($visit === null) {
            return null;
        }
        $examination = $this->findMedicalVisitExamination($visit['id']);

        return [
            'doctor_id' => $visit['doctor']['id'] ?? null,
            'med_visit_doctor' => trim(($visit['doctor']['name'] ?? '') . ' ' . ($visit['doctor']['lastname'] ?? '')),
            'med_visit_date' => $this->datesHelper->getFinelyFormattedStringDateFromDBDate($visit['medical_visit_date'] ?? $visit['created_at']),
            'med_visit_location' => $visit['medicalLocation']['description'] ?? '',
            'med_visit_location_id' => $visit['medical_location_id'],
            'med_visit_incident_type' => $visit['medicalIncidentType']['description'] ?? '',
            'med_visit_incident_type_id' => $visit['medical_incident_id'],
            'med_visit_chronic_conditions' => $this->findMedicalChronicConditionsForBenefiter($benefiterId, $visit['id']),
            'med_visit_height' => $examination['height'] ?? '',
            'med_visit_weight' => $examination['weight'] ?? '',
            'med_visit_temperature' => $examination['temperature'] ?? '',
            'med_visit_blood_pressure_systolic' => $examination['blood_pressure_systolic'] ?? '',
            'med_visit_blood_pressure_diastolic' => $examination['blood_pressure_diastolic'] ?? '',
            'med_visit_skull_perimeter' => $examination['skull_perimeter'] ?? '',
            'med_visit_exam_results' => $this->findMedicalVisitExaminationResults($visit['id']),
            'med_visit_lab_results' => $this->findMedicalVisitLabResults($visit['id']),
            'med_visit_diagnosis_results' => $this->findMedicalVisitDiagnosisResults($visit['id']),
            'med_visit_medication' => $this->findMedicalVisitMedication($visit['id']),
            'med_visit_hospitalizations' => $this->findMedicalVisitHospitalizations($visit['id']),
            'med_visit_referrals' => $this->findMedicalVisitReferrals($visit['id']),
            'med_visit_uploads' => $this->findMedicalVisitUploads($visit['id']),
        ];
    }
}
