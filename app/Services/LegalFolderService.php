<?php namespace App\Services;

use App\Models\LegalSession;
use App\Models\Benefiters_Tables_Models\medical_location_lookup;
use App\Models\User;
use App\Models\ViewModels\LegalSessionsHistory;
use App\Services\DatesHelper;
use Carbon\Carbon;
use Validator;

class LegalFolderService{

    private $datesHelper;

    public function __construct(){
        // initialize DatesHelper
        $this->datesHelper = new DatesHelper();
    }

    // validation for legal folder form
    public function legalFolderValidator($request){
        return Validator::make($request,
            array(
                'asylum_request_date' => 'date',
                'request_progress' => 'max:2000',
                'penalty_text' => 'max:2000',
            )
        );
    }

    // validation for legal session form
    public function legalSessionValidator($request){
        return Validator::make($request,
                array(
                    'legal_date' => 'date|required',
                    'legal_comments' => 'max:2000',
                )
            );
    }

    // save legal folder form's input in DB
    public function saveLegalFolderToDB($request, $id){
        $legalFolder = $this->findLegalFolderFromBenefiterId($id);
        if($legalFolder == null) {
            $legalFolderId = \DB::table('legal_folder')->insertGetId($this->getLegalFolderArrayForDBInsert($request['legal_folder_status'], $request['penalty'], $request['penalty_text'], $id));
            // check if a new asylum_request row should be inserted or...
            if ($request['legal_folder_status'] == '1') {
                \DB::table('asylum_request')->insert($this->getAsylumRequestArrayForDBInsert($request['asylum_request_date'], $request['procedure'], $request['request_status'], $request['request_progress'], $legalFolderId));
            } else { // ...a new no_legal_status row
                \DB::table('legal_section_status')->insert($this->getLegalSectionStatusArrayForDBInsert($request['legal_folder_status'], $request['action'], $request['result'], $legalFolderId));
            }
        } else {
            \DB::table('legal_folder')->where('id', '=', $legalFolder->id)->update($this->getLegalFolderArrayForDBInsert($request['legal_folder_status'], $request['penalty'], $request['penalty_text'], $id));
            // ON EDIT: delete the row from the non checked table if it is existent
            // check if a new asylum_request row should be inserted/edited or...
            $asylum_request = $this->findAsylumRequestFromLegalFolderId($legalFolder->id);
            $legal_status = $this->findLegalSectionStatusFromLegalFolderId($legalFolder->id);
            if ($request['legal_folder_status'] == '1') {
                if($asylum_request != null){
                    \DB::table('asylum_request')->where('id', '=', $asylum_request->id)->update($this->getAsylumRequestArrayForDBInsert($request['asylum_request_date'], $request['procedure'], $request['request_status'], $request['request_progress'], $legalFolder->id));
                } else {
                    \DB::table('asylum_request')->insert($this->getAsylumRequestArrayForDBInsert($request['asylum_request_date'], $request['procedure'], $request['request_status'], $request['request_progress'], $legalFolder->id));
                }
                if($legal_status != null){
                    \DB::table('legal_section_status')->where('id', '=', $legal_status->id)->delete();
                }
            } else { // ...if a new no_legal_status row should be inserted/edited
                if($legal_status != null){
                    \DB::table('legal_section_status')->where('id', '=', $legal_status->id)->update($this->getLegalSectionStatusArrayForDBInsert($request['legal_folder_status'], $request['action'], $request['result'], $legalFolder->id));
                } else {
                    \DB::table('legal_section_status')->insert($this->getLegalSectionStatusArrayForDBInsert($request['legal_folder_status'], $request['action'], $request['result'], $legalFolder->id));
                }
                if($asylum_request != null){
                    \DB::table('asylum_request')->where('id', '=', $asylum_request->id)->delete();
                }
            }
        }
    }

    // gets legal folder using the benefiter's id
    public function findLegalFolderFromBenefiterId($benefiterId){
        return \DB::table('legal_folder')->where('benefiter_id', '=', $benefiterId)->first();
    }

    // gets asylum request using the legal folder's id
    public function findAsylumRequestFromLegalFolderId($legalFolderId){
        return \DB::table('asylum_request')->where('legal_folder_id', '=', $legalFolderId)->first();
    }

    // gets no legal status using the legal folder's id
    public function findLegalSectionStatusFromLegalFolderId($legalFolderId){
        return \DB::table('legal_section_status')->where('legal_folder_id', '=', $legalFolderId)->first();
    }

    // returns an array suitable for legal_folder DB table insert
    private function getLegalFolderArrayForDBInsert($legalFolderStatus, $penalty, $penaltyText, $id){
        $penaltyText = ($penalty == '1') ? $penaltyText : "";
        return array(
            'benefiter_id' => $id,
            'legal_folder_status_id' => $legalFolderStatus,
            'penalty_id' => $penalty,
            'penalty_text' => $penaltyText,
        );
    }

    // returns an array suitable for asylum_request DB table insert
    private function getAsylumRequestArrayForDBInsert($request_date, $procedure, $request_status, $request_progress, $legalFolderId){
        if($procedure == '1'){
            $request_status = null;
        }
        return array(
            'request_date' => $this->datesHelper->makeDBFriendlyDate($request_date),
            'procedure_id' => $procedure,
            'request_status_id' => $request_status,
            'request_progress' => $request_progress,
            'legal_folder_id' => $legalFolderId,
        );
    }

    // returns an array suitable for no_legal_status DB table insert
    private function getLegalSectionStatusArrayForDBInsert($option_id, $action, $result, $legalFolderId){
        if($action == '1'){
            $result = null;
        }
        return array(
            'legal_option_id' => $option_id,
            'action_id' => $action,
            'result_id' => $result,
            'legal_folder_id' => $legalFolderId,
        );
    }

    // saves lawyer actions in legal_lawyer_action DB table
    private function saveLawyerActionsToDB($lawyer_actions, $legalSessionId){
        if($lawyer_actions != null) {
            foreach ($lawyer_actions as $lawyer_action) {
                \DB::table('legal_lawyer_action')->insert(
                    array(
                        'lawyer_action_id' => $lawyer_action,
                        'legal_session_id' => $legalSessionId,
                    )
                );
            }
        }
    }

    // saves legal session to corresponding DB table
    public function saveLegalSessionToDB($request, $benefiterId){
        $legal_folder = $this->findLegalFolderFromBenefiterId($benefiterId);
        if($legal_folder != null){
            $legalSessionId = \DB::table('legal_sessions')->insertGetId($this->getLegalSessionArrayForDBInsert($request, $legal_folder->id));
            if($legalSessionId != null) {
                $this->saveLawyerActionsToDB($request['lawyer_action'], $legalSessionId);
            }
        }
    }

    // returns an array with data suitable for legal_session DB table insertion
    private function getLegalSessionArrayForDBInsert($request, $legalFolderId){
        return array(
            'legal_folder_id' => $legalFolderId,
            'legal_date' => $this->datesHelper->makeDBFriendlyDate($request['legal_date']),
            'legal_comments' => $request['legal_comments'],
            'user_id' => \Auth::user()->id,
            'medical_location_id' => $request['medical_location_id'],
        );
    }

    // gets all legal sessions history for a benefiter
    public function getLegalSessionsHistoryForSingleBenefiter($benefiterId){
        $history = array();
        $legal_folder = $this->findLegalFolderFromBenefiterId($benefiterId);
        if($legal_folder != null){
            $legalSessions = $this->findLegalSessionsFromLegalFolderIdOrderedByDateDesc($legal_folder->id);
            if($legalSessions != null){
                // fetch all users from DB
                $users = User::get()->toArray();
                // fetch all locations from DB
                $locations = medical_location_lookup::get()->toArray();
                // fetch all lawyer actions from DB
                $lawyerActions = \DB::table('lawyer_action_lookup')->get();
                if($users != null and $locations != null and $lawyerActions != null) {
                    $this->pushLegalSessionsToHistoryArray(
                        $legalSessions, $users, $locations, $lawyerActions, $history);
                }
            }
        }
        return $history;
    }

    // returns all the legal sessions that have the legal folder id
    private function findLegalSessionsFromLegalFolderIdOrderedByDateDesc($legalFolderId){
        return LegalSession::where('legal_folder_id', '=', $legalFolderId)->orderBy('legal_date', 'desc')->get();
    }

    // pushes legal sessions as LegalSessionHistory objects to the history array
    private function pushLegalSessionsToHistoryArray(
        $legalSessions, $users, $locations, $lawyerActions, &$history)
    {
        foreach($legalSessions as $legalSession){
            $sessionLawyerActionsDescriptionList = null;
            $this->displaySessionLawyerActionsAsCommaSeparatedString($legalSession->id,
                $lawyerActions, $sessionLawyerActionsDescriptionList);
            $temp = new LegalSessionsHistory(
                $users[$legalSession->user_id - 1]['name'] . ' ' .
                $users[$legalSession->user_id - 1]['lastname'],
                $locations[$legalSession->medical_location_id - 1]['description'],
                new Carbon($legalSession->legal_date),
                $sessionLawyerActionsDescriptionList,
                $legalSession->legal_comments
            );
            array_push($history, $temp);
        }
    }

    // makes a comma separated string of session's lawyer actions
    private function displaySessionLawyerActionsAsCommaSeparatedString($legalSessionId,
       $lawyerActions, &$sessionLawyerActionsDescriptionList)
    {
        $sessionLawyerActions = $this->findLawyerActionsFromLegalSessionId($legalSessionId);
        if($sessionLawyerActions != null) {
            foreach($sessionLawyerActions as $i => $singleSessionLawyerAction){
                $sessionLawyerActionsDescriptionList .=
                    $lawyerActions[$singleSessionLawyerAction->lawyer_action_id - 1]->description;
                if($i < count($sessionLawyerActions) - 1){
                    $sessionLawyerActionsDescriptionList .= ", ";
                }
            }
        }
    }

    // gets lawyer actions using the legal session's id
    private function findLawyerActionsFromLegalSessionId($legalSessionId){
        return \DB::table('legal_lawyer_action')->where('legal_session_id', '=', $legalSessionId)->get();
    }
}
