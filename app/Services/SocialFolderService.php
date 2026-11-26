<?php namespace App\Services;

use Validator;
use App\Models\PsychosocialSession;

class SocialFolderService{

    // validates the social folder view form input
    public function socialFolderValidation($request){
        return Validator::make($request, array(
            'comments' => 'max:2000',
        ));
    }

    // validates the social folder view form input
    public function sessionValidation($request){
        return Validator::make($request, array(
            'session_date' => 'date',
            'session_comments' => 'max:2000',
        ));
    }

    // saves the social folder in DB
    public function saveSocialFolderToDB($request, $benefiterId){
        if(!array_key_exists('psychosocial_statuses', $request)){
            $request['psychosocial_statuses'] = null;
        }
        $social_folder = \App\Models\Folders\SocialFolder::query()->where('benefiter_id', '=', $benefiterId)->first();
        if($social_folder == null) {
            \App\Models\Folders\SocialFolder::query()->insert($this->getSocialFolderArrayForDBInsert($request, $benefiterId));
        } else {
            \App\Models\Folders\SocialFolder::query()->where('benefiter_id', '=', $benefiterId)->update($this->getSocialFolderArrayForDBInsert($request, $benefiterId));
        }
    }

    // save a new session in DB
    public function saveNewSessionToDB($request, $benefiterId){
        $this->savePsychosocialSessionToDB($request, $this->getSocialFolderFromBenefiterId($benefiterId)->id);
    }

    // update an existing session in DB
    /**
     * Saves a changed session. Only a session of the psychosocial folder of this beneficiary changes.
     */
    public function saveEditedSessionToDB($request, $session_id, $benefiterId){
        PsychosocialSession::where('id', $session_id)
            ->where('social_folder_id', $this->getSocialFolderFromBenefiterId($benefiterId)?->id)
            ->update($this->getPsychosocialSessionArrayForDBEdit($request));
    }

    // delete a session
    /**
     * Deletes a session. Only a session of the psychosocial folder of this beneficiary is deleted.
     */
    public function deleteSessionById($session_id, $benefiterId){
        PsychosocialSession::where('id', '=', $session_id)
            ->where('social_folder_id', $this->getSocialFolderFromBenefiterId($benefiterId)?->id)
            ->delete();
    }

    // gets all the rows from psychosocial_support_lookup DB table to display them in social folder view
    public function getAllPsychosocialSupportSubjects(){
        return \DB::table('psychosocial_support_lookup')->get();
    }

    // gets the social folder from benefiter's id
    public function getSocialFolderFromBenefiterId($id){
        return \App\Models\Folders\SocialFolder::query()->where('benefiter_id', '=', $id)->first();
    }

    // gets all benefiter's psychosocial support by benefiter id
    public function getBenefiterPsychosocialSupport($id){
        return \DB::table('benefiters_psychosocial_support')->where('benefiter_id', '=', $id)->get();
    }

    // gets all benefiter's sessions by benefiter id
    public function getAllSessionsFromBenefiterId($id){
        return PsychosocialSession::where('social_folder_id', '=', $this->getSocialFolderFromBenefiterId($id)->id)->orderBy('session_date', 'desc')->get();
    }

    // returns an array suitable for social_folder DB insertion
    private function getSocialFolderArrayForDBInsert($request, $benefiterId){
        return array(
            'benefiter_id' => $benefiterId,
            'comments' => $request['comments'],
        );
    }



    // saves a row to the psychosocial_sessions table in DB
    private function savePsychosocialSessionToDB($request, $socialFolderId){
        if(isset($request['session_date']) && isset($request['session_comments'])) {
            $psychosocialSession = new PsychosocialSession($this->getPsychosocialSessionArrayForDBInsert($request, $socialFolderId));
			$psychosocialSession->save();
        }
    }

    // gets array suitable for psychosocial_sessions DB table insertion
    private function getPsychosocialSessionArrayForDBInsert($request, $socialFolderId){
        $temp = $this->getPsychosocialSessionArrayForDBEdit($request);
        $temp['social_folder_id'] = $socialFolderId;
        $temp['psychologist_id'] = auth()->id();
        return $temp;
    }

    // get psychosocial session array for DB row edit
    private function getPsychosocialSessionArrayForDBEdit($request){
        $datesHelper = new DatesHelper();
        return array(
            'session_date' => $datesHelper->makeDBFriendlyDate($request['session_date']),
            'psychosocial_theme_id' => $request['psychosocial_theme'],
            'session_comments' => $request['session_comments'],
            'medical_location_id' => $request['medical_location_id'],
        );
    }
}
