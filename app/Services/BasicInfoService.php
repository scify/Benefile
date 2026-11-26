<?php namespace App\Services;

use App\Models\LegalSession;
use App\Models\Users_roles;
use App\Models\Users_subroles;
use App\Models\ViewModels\AllFoldersUsageHistory;
use App\Models\BasicFolderHistory;
use App\Models\Beneficiary;
use App\Models\Referral;
use App\Models\ReferralType;
use App\Models\BeneficiaryIncident;
use App\Models\Location;
use App\Models\MedicalVisit;
use App\Models\PsychosocialSession;
use App\Models\User;
use App\Services\GreekStringConversionHelper;
use App\Services\DatesHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Validator;
use Illuminate\Support\Facades\Log;

class BasicInfoService{

    private $datesHelper;

    public function __construct(DatesHelper $datesHelper, private readonly GreekStringConversionHelper $greekStringConversion, private readonly \Illuminate\Contracts\Auth\Guard $auth){
        $this->datesHelper = $datesHelper;
    }

    // validate the basic info
    public function basicInfoValidation($request, $id){
        $rules = array(
            'folder_number' => 'max:255|required|unique:benefiters,folder_number,'.$id,
            'name' => 'required|max:255',
            'lastname' => 'required|max:255',
            'fathers_name' => 'max:255',
            'mothers_name' => 'max:255',
            'nationality_country' => 'max:255',
            'origin_country' => 'max:255',
            'ethnic_group' => 'max:255',
            'birth_date' => 'date',
            'arrival_date' => 'date',
            'address' => 'max:255',
            'telephone' => 'min:5|max:20',
            'number_of_children' => 'integer',
            'chidren_names' => 'max:2000',
            'relatives_residence' => 'max:255',
            'education_specialization' => 'max:255',
            'working_title' => 'max:255',
            'travel_route' => 'max:255',
            'travel_duration' => 'max:255',
            'detention_date' => 'date',
            'social_history' => 'max:2000',
            'updated_by_date' => 'required|date',
            'updated_by_comments' => 'max:5000',
        );
        $legal_status_texts = $request['legal_status_text'];
        foreach($legal_status_texts as $i => $legal_status_text){
            $rules["legal_status_text." . $i] = 'max:255';
        }
        $legal_status_exp_dates = $request['legal_status_exp_date'];
        foreach($legal_status_exp_dates as $i => $legal_status_exp_date){
            $rules["legal_status_exp_date." . $i] = 'date';
            $legal_status_exp_dates[$i] = $this->datesHelper->makeDBFriendlyDate($legal_status_exp_date);
        }
        $request['birth_date'] = $this->datesHelper->makeDBFriendlyDate($request['birth_date']);
        $request['arrival_date'] = $this->datesHelper->makeDBFriendlyDate($request['arrival_date']);
        $request['detention_date'] = $this->datesHelper->makeDBFriendlyDate($request['detention_date']);
        return Validator::make($request, $rules);
    }

    // insert into DB benefiter table
    public function saveBasicInfoToDB($request){
        // if interpreter checkbox has no value, it should have '0' value
        if(!array_key_exists('interpreter' ,$request)){
           $request['interpreter'] = 0;
        }
        // if gender checkbox has no value, it should have null value
        if(!array_key_exists('gender_id' ,$request)){
            $request['gender_id'] = null;
        }
        // get work title id from work title name
        $request['working_title'] = $this->getWorkTitleIdFromDBAndInsertNewWorkTitleIfNeeded($request['working_title']);
        // if legal status is not existent, add it
        if(!array_key_exists('legal_status' ,$request)){
            $request['legal_status'] = null;
        }
        $attributes = array_intersect_key($this->getBenefiterArrayForDBInsert($request), array_flip((new Beneficiary())->getFillable()));
        return app(BeneficiaryRegistrar::class)->register($attributes, function (Beneficiary $benefiter) use ($request): void {
            $languagesLevelsMerge = $this->mergeUniqueLanguagesLevelWithNoDuplicatedLanguageArrays($request);
            if(!empty($languagesLevelsMerge)) {
                $this->saveLanguagesToDB($benefiter->id, $languagesLevelsMerge);
            }
            $this->saveLegalStatusesToDB($benefiter->id, $request);
            $this->saveBasicFolderUpdateHistory($benefiter->id, $request['medical_location_id'], $request['updated_by_date'], $request['updated_by_comments']);
        });
    }

    // edit an already saved benefiter
    public function editBasicInfo($request, $id){
        // if interpreter checkbox has no value, it should have '0' value
        if(!array_key_exists('interpreter' ,$request)){
            $request['interpreter'] = 0;
        }
        // if gender checkbox has no value, it should have null value
        if(!array_key_exists('gender_id' ,$request)){
            $request['gender_id'] = null;
        }
        // get work title id from work title name
        $request['working_title'] = $this->getWorkTitleIdFromDBAndInsertNewWorkTitleIfNeeded($request['working_title']);
        Beneficiary::where('id', '=', $id)->update($this->getBenefiterArrayForDBInsert($request));
        $this->editLanguagesInDB($id, $this->mergeUniqueLanguagesLevelWithNoDuplicatedLanguageArrays($request));
        if(!array_key_exists('legal_status' ,$request)){
            $request['legal_status'] = null;
        }
        $this->editLegalStatusesInDB($id, $request);
        $this->saveBasicFolderUpdateHistory($id, $request['medical_location_id'], $request['updated_by_date'], $request['updated_by_comments']);
    }

    // insert a new row with the latest basic folder's update info
    private function saveBasicFolderUpdateHistory($id, $medical_location_id, $updated_by_date, $comments){
        BasicFolderHistory::create(array(
            'benefiter_id' => $id,
            'user_id' => $this->auth->id(),
            'medical_location_id' => $medical_location_id,
            'update_date' => $this->datesHelper->makeDBFriendlyDate($updated_by_date),
            'comments' => $comments,
        ));
    }

    // get all languages from languages DB table
    public function getAllLanguages(){
        return \DB::table('languages')->get();
    }

    // get all language levels from language_levels DB table
    public function getAllLanguageLevels(){
        return \DB::table('language_levels')->get();
    }

    // finds a benefiter from its id
    public function findExistentBenefiter($id){
        return Beneficiary::where('id', '=', $id)->with('gender')->first();
    }

    // gets all benefiter's legal statuses
    public function getLegalStatusesByBenefiterId($id){
        return \App\Models\Folders\BeneficiaryLegalStatus::query()->where('benefiter_id', '=', $id)->get();
    }

    // gets all benefiter's languages and languages levels
    public function getLanguagesAndLanguagesLevelsByBenefiterId($id){
        return \App\Models\Folders\BeneficiaryLanguage::query()->where('benefiter_id', '=', $id)->get();
    }

    // gets an array with legal statuses from request in case of validation failure
    public function getLegalStatusesArrayFromRequest($legal_statuses, $legal_texts, $legal_exp_dates){
        $temp = array();
        if($legal_statuses != null) {
            foreach ($legal_statuses as $legal_status) {
                array_push($temp, (object)$this->getLegalStatusArrayForValidationFailure($legal_status, $legal_texts[$legal_status - 1], $legal_exp_dates[$legal_status - 1]));
            }
        }
        return $temp;
    }

    // returns languages and levels input from request when validation fails
    public function getLanguagesAndLanguagesLevelsFromRequest($request){
        $languages_levels = $this->mergeUniqueLanguagesLevelWithNoDuplicatedLanguageArrays($request);
        for($i = 0; $i < legacy_count($languages_levels); $i++){
            $languages_levels[$i] = (object) $languages_levels[$i];
        }
        return $languages_levels;
    }

    // delete a benefiter from his id
    public function deleteBenefiter($benefiterId){
        $benefiter = Beneficiary::where('id', '=', $benefiterId)->first();
        $benefiter->folder_number = $this->datesHelper->getCurrentTimeString() . "_" . $benefiter->folder_number;
        $benefiter->save();
        Beneficiary::where('id', '=', $benefiterId)->delete();
    }

    // returns the work title name using the benefiter's id
    public function getWorkTitleNameFromBenefiterId($id){
        $benefiter = \DB::table('benefiters')->where('id' , '=', $id)->first();
        if ($benefiter != null) {
            $workTitle = \App\Models\Folders\WorkTitle::query()->where('id', '=', $benefiter->work_title_id)->first();
            if ($workTitle != null){
                return $workTitle->work_title;
            }
        }
        return null;
    }

    // returns all the country abandon reasons from DB
    public function getAllCountryAbandonReasons(){
        return \DB::table('country_abandon_reasons_lookup')->get();
    }

    // get all languages keys from basic info's form $request
    private function getLanguageKeysArray($request){
        // make an array with all languages keys
        $keys = array_keys($request);
        $languages = array();
        foreach($keys as $key){
            // check if string "language" is contained in array's keys
            if (strpos($key, "language") !== false && strpos($key, "language_level") === false){
                array_push($languages, $key);
            }
        }
        return $languages;
    }

    // deletes duplicate languages selected by user
    private function deleteDuplicatedLanguages($languagesKeys, $request){
        // make sure all languages selected are different
        $languages_id = array();
        foreach($languagesKeys as $language){
            // if this is not a language level
            if(strpos($language, "language_level") === false){
                $languages_id[$language] = $request[$language];
            }
        }
        return array_unique($languages_id);
    }

    // deletes duplicate languages selected by user
    private function getUniqueLanguagesLevels($languagesUnique, $request){
        // make sure you get all unique languages levels
        $keys = array_keys($languagesUnique);
        $languages_levels_id = array();
        foreach($keys as $key){
            $level_key = str_replace("language", "language_level", $key);
            $languages_levels_id[$key] = $request[$level_key];
        }
        return $languages_levels_id;
    }

    // merges the language and language level array with the deleted duplicates languages array
    private function mergeUniqueLanguagesLevelWithNoDuplicatedLanguageArrays($request){
        $languagesKeys = $this->getLanguageKeysArray($request);
        $languagesUnique = $this->deleteDuplicatedLanguages($languagesKeys, $request);
        $levelsUnique = $this->getUniqueLanguagesLevels($languagesUnique, $request);
        $keys = array_keys($languagesUnique);
        $merge = array();
        foreach($keys as $key){
            // if a correct language is selected
            if($languagesUnique[$key] != 1) {
                // if a non correct language level is selected, make it null
                if($levelsUnique[$key] == 1){
                    $levelsUnique[$key] = null;
                }
                // make array containing language id and level id and...
                $temp = array(
                    'language_id' => $languagesUnique[$key],
                    'language_level_id' => $levelsUnique[$key],
                );
                // ...push it into the $merge array
                array_push($merge, $temp);
            }
        }
        return $merge;
    }

    // make and return an array that will be appropriate for DB insert
    private function getBenefiterArrayForDBInsert($request){
        return array(
            "folder_number" => $request['folder_number'],
            "lastname" => $request['lastname'],
            "name" => $request['name'],
            "gender_id" => $request['gender_id'],
            "birth_date" => $this->datesHelper->makeDBFriendlyDate($request['birth_date']),
            "fathers_name" => $request['fathers_name'],
            "mothers_name" => $request['mothers_name'],
            "nationality_country" => $request['nationality_country'],
            "origin_country" => $request['origin_country'],
            "ethnic_group" => $request['ethnic_group'],
            "arrival_date" => $this->datesHelper->makeDBFriendlyDate($request['arrival_date']),
            "telephone" => $request['telephone'],
            "address" => $request['address'],
            "marital_status_id" => $request['marital_status'],
            "number_of_children" => $request['number_of_children'],
            "children_names" => $request['children_names'],
            "relatives_residence" => $request['relatives_residence'],
            "language_interpreter_needed" => $request['interpreter'],
            "education_id" => $request['education_status'],
            "education_specialization" => $request['education_specialization'],
            "is_benefiter_working" => $request['working'],
            "work_title_id" => $request['working_title'],
            "working_legally" => $request['working_legally'],
            "country_abandon_reason_id" => $request['country_abandon_reason'],
            "travel_route" => $request['travel_route'],
            "travel_duration" => $request['travel_duration'],
            "detention_date" => $this->datesHelper->makeDBFriendlyDate($request['detention_date']),
            "social_history" => $request['social_history'],
            "document_manager_id" => $this->auth->id(),
        );
    }

    // save languages and languages level for benefiter with $benefiterId to DB
    private function saveLanguagesToDB($benefiterId, $languagesAndLevels){
        $languagesAndLevelsDBFriendly = $this->getLanguagesArrayForDBInsert($benefiterId, $languagesAndLevels);
        foreach($languagesAndLevelsDBFriendly as $languageAndLevel){
            \App\Models\Folders\BeneficiaryLanguage::query()->insert($languageAndLevel);
        }
    }

    // edit languages and languages level for benefiter with $benefiterId to DB
    private function editLanguagesInDB($benefiterId, $languagesAndLevels){
        $languagesAndLevelsDBFriendly = $this->getLanguagesArrayForDBInsert($benefiterId, $languagesAndLevels);
        $temp = \App\Models\Folders\BeneficiaryLanguage::query()->where('benefiter_id', '=', $benefiterId)->get();
        $ids_inserted = array();
        foreach($languagesAndLevelsDBFriendly as $languageAndLevel){
            $languageInserted = false;
            foreach($temp as $lang_in_db) {
                // if language and level already exist in DB update the row
                if($languageAndLevel['language_id'] == $lang_in_db->language_id){
                    \App\Models\Folders\BeneficiaryLanguage::query()->where('id', '=', $lang_in_db->id)->update($languageAndLevel);
                    array_push($ids_inserted, $languageAndLevel['language_id']);
                    $languageInserted = true;
                    break;
                }
            }
            // insert a new row
            if(!$languageInserted){
                \App\Models\Folders\BeneficiaryLanguage::query()->insert($languageAndLevel);
                array_push($ids_inserted, $languageAndLevel['language_id']);
            }
        }
        // delete all removed languages
        foreach($temp as $lang_in_db){
            $found = false;
            foreach($ids_inserted as $language_id){
                if($lang_in_db->language_id == $language_id){
                    $found = true;
                }
            }
            // if not found delete DB row
            if(!$found){
                \App\Models\Folders\BeneficiaryLanguage::query()->where('benefiter_id', '=', $benefiterId)->where('language_id', '=', $lang_in_db->language_id)->delete();
            }
        }
    }

    // edit legal statuses in DB
    private function editLegalStatusesInDB($benefiterId, $request){
        $legal_statuses_checked = $request['legal_status'];
        $temp = \App\Models\Folders\BeneficiaryLegalStatus::query()->where('benefiter_id', '=', $benefiterId)->get();
        $ids_inserted = array();
        if($legal_statuses_checked != null) {
            foreach ($legal_statuses_checked as $legal_status_checked) {
                $legalStatusInserted = false;
                foreach ($temp as $legal_status_db){
                    if($legal_status_db->legal_lookup_id == $legal_status_checked){
                        \App\Models\Folders\BeneficiaryLegalStatus::query()->where('id', '=', $legal_status_db->id)->update($this->getLegalStatusArrayForDBInsert($benefiterId, intval($legal_status_checked), $request));
                        array_push($ids_inserted, $legal_status_checked);
                        $legalStatusInserted = true;
                        break;
                    }
                }
                if(!$legalStatusInserted) {
                    \App\Models\Folders\BeneficiaryLegalStatus::query()->insert($this->getLegalStatusArrayForDBInsert($benefiterId, intval($legal_status_checked), $request));
                }
            }
        }
        // delete all removed legal statuses
        foreach($temp as $legal_status_db){
            $found = false;
            foreach($ids_inserted as $legal_id){
                if($legal_status_db->legal_lookup_id == $legal_id){
                    $found = true;
                }
            }
            // if not found delete DB row
            if(!$found){
                \App\Models\Folders\BeneficiaryLegalStatus::query()->where('benefiter_id', '=', $benefiterId)->where('legal_lookup_id', '=', $legal_status_db->legal_lookup_id)->delete();
            }
        }
    }

    // returns array for benefiters_languages DB insertions
    private function getLanguagesArrayForDBInsert($benefiterId, $languagesAndLevels){
        $languagesForDB = array();
        foreach($languagesAndLevels as $languageAndLevel){
            // create a temp array with values needed and...
            $temp = array(
                'benefiter_id' => $benefiterId,
                'language_id' => $languageAndLevel['language_id'],
                'language_level_id' => $languageAndLevel['language_level_id'],
            );
            // ...push it into $languagesForDB
            array_push($languagesForDB, $temp);
        }
        return $languagesForDB;
    }

    // saves legal statuses for benefiter with $benefiterId to DB
    private function saveLegalStatusesToDB($benefiterId, $request){
        $legal_statuses_checked = $request['legal_status'];
        if($legal_statuses_checked != null) {
            foreach ($legal_statuses_checked as $legal_status_checked) {
                \App\Models\Folders\BeneficiaryLegalStatus::query()->insert($this->getLegalStatusArrayForDBInsert($benefiterId, intval($legal_status_checked), $request));
            }
        }
    }

    // returns an array for legal status DB table insert
    private function getLegalStatusArrayForDBInsert($benefiterId, $legal_status_checked, $request){
        return array(
            "benefiter_id" => $benefiterId,
            "exp_date" => $this->datesHelper->makeDBFriendlyDate($request['legal_status_exp_date'][$legal_status_checked - 1]),
            "description" => $request['legal_status_text'][$legal_status_checked - 1],
            "legal_lookup_id" => $legal_status_checked,
        );
    }

    // returns an array with the legal status from request in case of validation failure
    private function getLegalStatusArrayForValidationFailure($legal_status, $legal_text, $legal_exp_date){
        return array(
            "exp_date" => $this->datesHelper->getDateStringFromSimpleString($legal_exp_date),
            "description" => $legal_text,
            "legal_lookup_id" => $legal_status,
        );
    }

    // returns the work title id of a work title name
    private function getWorkTitleIdFromDBAndInsertNewWorkTitleIfNeeded($workTitleFromForm){
        // initialize the GreekStringConversionHelper service
        $greekStringConversion = $this->greekStringConversion;
        $workTitleId = null;
        $allWorkTitlesAvailable = \App\Models\Folders\WorkTitle::query()->get();
        $workTitleFromForm = $greekStringConversion->grstrtoupper($workTitleFromForm);
        // if there are some work titles in DB
        if ($allWorkTitlesAvailable->isNotEmpty()){
            // check for each one if the names are the same after greek string conversion to uppercase
            foreach($allWorkTitlesAvailable as $work_title){
                $work_title->work_title = $greekStringConversion->grstrtoupper($work_title->work_title);
                // if strings are the same set the $workTitleId to the DB's id
                if(strcasecmp($work_title->work_title, $workTitleFromForm) == 0){
                    $workTitleId = $work_title->id;
                    break;
                }
            }
        }
        // if work title was not found in DB
        if ($workTitleId == null) {
            $workTitleId = \App\Models\Folders\WorkTitle::query()->insertGetId(array('work_title' => $workTitleFromForm));
        }
        return $workTitleId;
    }








    /**
     * The beneficiaries with the same name, last name, birth date and father's name as the form.
     *
     * Only the fields that the form has are compared. The values are bound parameters.
     *
     * @return array<int, object>|null Null when the form has none of these fields.
     */
    public function checkForDuplicates($request){
        $query = \DB::table('benefiters')->select('id', 'name', 'lastname', 'fathers_name', 'birth_date');
        $hasCriteria = false;
        foreach (['name', 'lastname', 'fathers_name'] as $field) {
            if (!empty($request[$field])) {
                $query->where($field, (string) $request[$field]);
                $hasCriteria = true;
            }
        }
        if (!empty($request['birth_date'])) {
            $query->where('birth_date', $this->datesHelper->makeDBSearchFriendlyDate(
                $this->datesHelper->makeDBFriendlyDate((string) $request['birth_date'])));
            $hasCriteria = true;
        }
        if (!$hasCriteria) {
            Log::info('No query parameters have been passed!');
            return null;
        }
        return $query->get()->all();
    }

}
