<?php

namespace App\Http\Controllers;

use App\Models\Benefiters_Tables_Models\Benefiter;
use App\Models\Benefiters_Tables_Models\File_import_schema;
use Illuminate\Support\Facades\Input;
use Carbon\Carbon;

class UploadFileController extends Controller
{
    // returns upload csv view
    public function getUploadCSV(){
        return view('uploadExcel');
    }

    // uploads the file to server
    public function excelUpload(){
        // get the file from the post request
        $file = Input::file('file');
        // set file name
        $filename = Carbon::now('Europe/Athens') . '-' . $file->getClientOriginalName();
        // set file path
        $filepath = public_path() . '/uploads/uploadedExcels/';
        // move file to correct location
        $file->move($filepath, $filename);
        $fullFilePath = $filepath . $filename;
        // insert values to DB
        $this->fileImport($fullFilePath);
    }

    // inserts all values to DB
    private function fileImport($filePath){
        header('Content-Type: text/html; charset=UTF-8');
        // Call benefiter table in db
        //
        $file_import_Fields = ['id',
            'folder_number', 'name', 'lastname', 'fathers_name', 'mothers_name', 'gender', 'origin_country',
            'nationality_country', 'birth_date', 'arrival_date', 'address', 'telephone', 'marital_status',
            'number_of_children', 'relatives_residence', 'legal_status', 'legal_status_details', 'legal_status_exp_date',
            'education', 'language', 'language_level', 'other_language', 'language_interpreter_needed', 'is_benefiter_working',
            'work_title', 'working_legally',
            'country_abandon_reason', 'travel_route', 'travel_duration', 'detention_duration', 'has_social_reference',
            'social_reference_actions', 'social_reference_date', 'has_medical_reference', 'medical_reference_actions',
            'medical_reference_date', 'has_legal_reference', 'legal_reference_actions', 'legal_reference_date',
            'has_educational_reference', 'educational_reference_actions', 'educational_reference_date', 'social_history'
        ];

        // Import csv
        $csvFile = file($filePath);
        // Iterate between all rows of the csv file and add each value to the benefiters table
        for($i=1; $i<count($csvFile); $i++) {
            $colValues = str_getcsv( $csvFile[$i]);
            $file_import = new File_import_schema();
            for($j=1; $j<count($colValues); $j++){
                $file_import->$file_import_Fields[$j] = $colValues[$j];
            }
            $file_import->save();
        }
        $this->selectAppropriateDBTableForEachFileRowColumns();
    }

    // selects the appropriate DB table for each column of a row
    private function selectAppropriateDBTableForEachFileRowColumns(){
        $allFileRows = File_import_schema::get();
        if($allFileRows != null) {
            foreach ($allFileRows as $singleRow) {
                Benefiter::insert($this->selectBenefitersColumnsAndValuesFromFileRow($singleRow));
//                $benefiterReferralsColumns = $this->selectBenefitersReferralsColumnsAndValuesFromFileRow($singleRow);
            }
        }
    }

    // selects and returns all the columns - values inserted from file that correspond to the benefiters DB table
    private function selectBenefitersColumnsAndValuesFromFileRow($singleRow){
        $singleRow->gender = $this->getGenderId($singleRow->gender);
        $singleRow->marital_status = $this->getMaritalStatusId($singleRow->marital_status);
        $singleRow->education = $this->getEducationId($singleRow->education);
        return array(
                'folder_number' => $singleRow->folder_number,
                'name' => $singleRow->name,
                'lastname' => $singleRow->lastname,
                'fathers_name' => $singleRow->fathers_name,
                'mothers_name' => $singleRow->mothers_name,
                'birth_date' => $singleRow->birth_date,
                'arrival_date' => $singleRow->arrival_date,
                'gender_id' => $singleRow->gender,
                'origin_country' => $singleRow->origin_country,
                'nationality_country' => $singleRow->nationality_country,
                'address' => $singleRow->address,
                'telephone' => $singleRow->telephone,
                'marital_status_id' => $singleRow->marital_status,
                'number_of_children' => $singleRow->number_of_children,
                'relatives_residence' => $singleRow->relatives_residence,
                'education_id' => $singleRow->education,
                'language_interpreter_needed' => $singleRow->language_interpreter_needed,
                'is_benefiter_working' => $singleRow->is_benefiter_working,
//                'work_title_id' => $singleRow->work_title,// make it id
                'working_legally' => $singleRow->working_legally,
                'country_abandon_reason' => $singleRow->country_abandon_reason,
                'travel_route' => $singleRow->travel_route,
                'travel_duration' => $singleRow->travel_duration,
                'detention_duration' => $singleRow->detention_duration,
                'social_history' => $singleRow->social_history,
            );
    }

    // get id from gender name
    private function getGenderId($genderFromFile){
        $genders = \DB::table('genders_lookup')->get();
        // change the gender name to gender id
        foreach($genders as $gender){
            if($genderFromFile == $gender->gender){
                return $gender->id;
            }
        }
        // gender not found
        return null;
    }

    // get id from marital status name
    private function getMaritalStatusId($maritalStatusFromFile){
        $marital_statuses = \DB::table('marital_status_lookup')->get();
        // change the marital status name to the marital status id
        foreach($marital_statuses as $marital_status){
            if($maritalStatusFromFile == $marital_status->marital_status_title){
                return $marital_status->id;
            }
        }
        // marital status not found
        return null;
    }

    // get id from education name
    private function getEducationId($educationFromFile){
        $educations = \DB::table('education_lookup')->get();
        // change the education name to the education name id
        foreach($educations as $education){
            if($educationFromFile == $education->education_title){
                return $education->id;
            }
        }
        // education not found
        return null;
    }
}
