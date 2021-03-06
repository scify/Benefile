<div class="legal-folder-form">
<?php
    $p = "legal_folder_form.";
    // format correctly the dates!
    $datesHelper = new \app\Services\DatesHelper();
    if (isset($benefiter) and $benefiter != null){
        if ($benefiter->birth_date != null) {
            $benefiter->birth_date = $datesHelper->getFinelyFormattedStringDateFromDBDate($benefiter->birth_date);
        }
        if ($benefiter->arrival_date != null) {
            $benefiter->arrival_date = $datesHelper->getFinelyFormattedStringDateFromDBDate($benefiter->arrival_date);
        }
    }
?>
{!! Form::open(array('url' => 'benefiter/'.$benefiter->id.'/legal-folder')) !!}
@if (Auth::user()->user_role_id == 1 || Auth::user()->user_role_id == 3)

{{--<hr>--}}
    <div class="personal-family-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">1. @lang("social_folder_form.personal_family_info")</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('folder_number', Lang::get('basic_info_form.folder_number')) !!}
                            {!! Form::text('folder_number', $benefiter->folder_number, array('class' => 'custom-input-text text-align-right width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('lastname', Lang::get('basic_info_form.lastname')) !!}
                            {!! Form::text('lastname', $benefiter->lastname, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('name', Lang::get('basic_info_form.name')) !!}
                            {!! Form::text('name', $benefiter->name, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        {{-- GENDER --}}
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('gender_id', Lang::get('basic_info_form.gender')) !!}
                            <div class="make-inline">
                                <?php
                                    $male = false;
                                    $female = false;
                                     $other = false;
                                    if($benefiter->gender_id == 1){
                                        $male = true;
                                    } elseif ($benefiter->gender_id == 2) {
                                        $female = true;
                                    } else {
                                        $other = true;
                                    }
                                ?>
                                <div class="make-inline width-100-percent">
                                    {!! Form::radio('gender_id', 1, $male, array('class' => 'make-inline', 'disabled' => 'disabled')) !!}
                                    {!! Form::label('gender_id', Lang::get('basic_info_form.male'), array('class' => 'radio-value')) !!}
                                    {!! Form::radio('gender_id', 2, $female, array('class' => 'make-inline', 'disabled' => 'disabled')) !!}
                                    {!! Form::label('gender_id', Lang::get('basic_info_form.female'), array('class' => 'radio-value')) !!}
                                    {!! Form::radio('gender_id', 3, $other, array('class' => 'make-inline', 'disabled' => 'disabled')) !!}
                                    {!! Form::label('gender_id', Lang::get('basic_info_form.other'), array('class' => 'radio-value')) !!}
                                </div>
                            </div>
                        </div>
                        {{-- Birth date --}}
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                            {!! Form::label('birth_date', Lang::get('basic_info_form.birth_date')) !!}
                            {!! Form::text('birth_date', $benefiter->birth_date, array('class' => 'custom-input-text width-80-percent date-input', 'disabled' => 'disabled')) !!}<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove color-red clear-date hide"></span></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                       {{-- FATHERS NAME --}}
                       <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                           {!! Form::label('fathers_name', Lang::get('basic_info_form.fathers_name')) !!}
                           {!! Form::text('fathers_name', $benefiter->fathers_name, array('class' => 'custom-input-text width-100-percent' , 'disabled')) !!}
                       </div>
                       {{-- MOTHERS NAME --}}
                       <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                           {!! Form::label('mothers_name', Lang::get('basic_info_form.mothers_name')) !!}
                           {!! Form::text('mothers_name', $benefiter->mothers_name, array('class' => 'custom-input-text width-100-percent' , 'disabled')) !!}
                       </div>
                       {{-- NATIONALITY --}}
                       <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                           {!! Form::label('nationality_country', Lang::get('basic_info_form.nationality_country')) !!}
                           {!! Form::text('nationality_country', $benefiter->nationality_country, array('class' => 'custom-input-text width-100-percent' , 'disabled')) !!}
                       </div>
                       {{-- ORIGIN COUNTRY --}}
                       <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                           {!! Form::label('origin_country', Lang::get('basic_info_form.origin_country')) !!}
                           {!! Form::text('origin_country', $benefiter->origin_country, array('class' => 'custom-input-text width-100-percent', 'disabled')) !!}
                       </div>
                       {{-- ETHNICITY --}}
                       <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                           {!! Form::label('ethnic_group', Lang::get('basic_info_form.ethnic_group')) !!}
                           {!! Form::text('ethnic_group', $benefiter->ethnic_group, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                       </div>
                    </div>
                </div>

                <div class="row">
                    <div class="padding-left-right-15">
                        {{-- ARRIVAL DATE --}}
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('arrival_date', Lang::get('basic_info_form.arrival_date')) !!}
                            {!! Form::text('arrival_date', $benefiter->arrival_date, array('class' => 'custom-input-text width-80-percent date-input', 'disabled' => 'disabled')) !!}
                        </div>
                        {{-- TELEPHONE --}}
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('telephone', Lang::get('basic_info_form.telephone')) !!}
                            <?php
                                if($benefiter->telephone == 0){
                                    $benefiter->telephone = "";
                                }
                            ?>
                            {!! Form::text('telephone', $benefiter->telephone, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        {{-- ADDRESS --}}
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('address', Lang::get('basic_info_form.address')) !!}
                            {!! Form::text('address', $benefiter->address, array('class' => 'custom-input-text address width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        {{-- CHILDRENS NAME --}}
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-4">
                            {!! Form::label('children_names', Lang::get('basic_info_form.children_names')) !!}
                            {!! Form::text('children_names', $benefiter->children_names, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="legal-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">2. @lang($p."legal_info")</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <?php
                            for($i = 0; $i < 8; $i++){
                                $legal_folder_status[$i] = false;
                            }
                            if(isset($legal_folder) and $legal_folder != null){
                                $legal_folder_status[$legal_folder->legal_folder_status_id - 1] = true;
                            } else {
                                $legal_folder_status[0] = true;
                            }
                        ?>
                        <div class="form-group float-left width-100-percent">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 1, $legal_folder_status[0], array('class' => 'make-inline', 'id' => 'asylum')) !!}
                                        {!! Form::label('asylum', Lang::get('legal_folder_form.asylum'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 2, $legal_folder_status[1], array('class' => 'make-inline legal-status', 'id' => 'no-legal')) !!}
                                        {!! Form::label('no-legal', Lang::get('legal_folder_form.no_legal'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 3, $legal_folder_status[2], array('class' => 'make-inline legal-status', 'id' => 'asylum-number')) !!}
                                        {!! Form::label('asylum-number', Lang::get('legal_folder_form.asylum_number'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 4, $legal_folder_status[3], array('class' => 'make-inline legal-status', 'id' => 'refugee-number')) !!}
                                        {!! Form::label('refugee-number', Lang::get('legal_folder_form.refugee_number'), array('class' => 'radio-value')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 5, $legal_folder_status[4], array('class' => 'make-inline legal-status', 'id' => 'stay-permit-ack')) !!}
                                        {!! Form::label('stay-permit-ack', Lang::get('legal_folder_form.stay_permit_ack'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 6, $legal_folder_status[5], array('class' => 'make-inline legal-status', 'id' => 'stay-permit')) !!}
                                        {!! Form::label('stay-permit', Lang::get('legal_folder_form.stay_permit'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 7, $legal_folder_status[6], array('class' => 'make-inline legal-status', 'id' => 'european')) !!}
                                        {!! Form::label('european', Lang::get('legal_folder_form.european'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 8, $legal_folder_status[7], array('class' => 'make-inline legal-status', 'id' => 'minor')) !!}
                                        {!! Form::label('minor', Lang::get('legal_folder_form.minor'), array('class' => 'radio-value')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="asylum-request dynamic-form-section hide">
                            <h1 class="record-section-header padding-left-right-15">@lang($p."asylum")</h1>
                            <?php
                                $asylum_request_date = null;
                                $request_progress = null;
                                for($i = 0; $i < 2; $i++){
                                    $procedure[$i] = false;
                                }
                                $procedure[0] = true;
                                for($i = 0; $i < 3; $i++){
                                    $request_status[$i] = "";
                                }
                                $request_status[0] = "selected";
                                if(isset($asylum_request) and $asylum_request != null){
                                    $asylum_request_date = $datesHelper->getFinelyFormattedStringDateFromDBDate($asylum_request->request_date);
                                    $procedure[$asylum_request->procedure_id - 1] = true;
                                    $request_status[0] = "";
                                    $request_status[$asylum_request->request_status_id - 1] = "selected";
                                    $request_progress = $asylum_request->request_progress;
                                }
                            ?>
                            <div class="row">
                                <div class="form-group float-left padding-left-right-15 width-100-percent">
                                    <div class="col-md-2 make-inline">
                                        {!! Form::label('asylum_request_date', Lang::get('legal_folder_form.asylum_request_date')) !!}
                                        {!! Form::text('asylum_request_date', $asylum_request_date, array('class' => 'custom-input-text width-80-percent date-input', 'placeholder' => Lang::get('dates_common.date_placeholder'))) !!}<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove color-red clear-date"></span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group float-left padding-left-right-15 width-100-percent">
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('procedure', 1, $procedure[0], array('class' => 'make-inline', 'id' => 'procedure-old')) !!}
                                        {!! Form::label('procedure-old', Lang::get('legal_folder_form.procedure_old'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('procedure', 2, $procedure[1], array('class' => 'make-inline', 'id' => 'procedure-new')) !!}
                                        {!! Form::label('procedure-new', Lang::get('legal_folder_form.procedure_new'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="request-status col-md-6 make-inline hide">
                                        {!! Form::label('request_status', Lang::get('legal_folder_form.request_status')) !!}
                                        <select name="request_status" class="request-status-selection">
                                            <option value="1" {{ $request_status[0] }}>α'</option>
                                            <option value="2" {{ $request_status[1] }}>β'</option>
                                            <option value="3" {{ $request_status[2] }}>μεταγενέστερο</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="padding-left-right-15">
                                    <div class="form-group padding-left-right-15">
                                        {!! Form::label('request_progress', Lang::get('legal_folder_form.request_progress')) !!}
                                        {!! Form::textarea('request_progress', $request_progress, array('class' => 'custom-input-textarea width-100-percent')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                            for($i = 0; $i < 8; $i++){
                                $action[$i] = false;
                            }
                            $action[0] = true;
                            for($i = 0; $i < 8; $i++){
                                $result[$i] = false;
                            }
                            $result[0] = true;
                            if(isset($legal_status) and $legal_status != null){
                                $selected;
                                $action[$legal_status->action_id - 1] = true;
                                $result[$legal_status->result_id - 1] = true;
                            }
                        ?>
                        <div class="legal-status-div dynamic-form-section hide">
                            <h1 class="record-section-header padding-left-right-15">@lang($p."no_legal")</h1>
                            <div class="row">
                                <div class="form-group padding-left-right-15 float-left width-100-percent">
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('action', 1, $action[0], array('class' => 'make-inline', 'id' => 'action-none')) !!}
                                        {!! Form::label('action-none', Lang::get('legal_folder_form.action_none'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('action', 2, $action[1], array('class' => 'make-inline', 'id' => 'action-refusal')) !!}
                                        {!! Form::label('action-refusal', Lang::get('legal_folder_form.action_refusal'), array('class' => 'radio-value')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="results hide">
                                <div class="row">
                                    <div class="form-group padding-left-right-15">
                                        <div class="col-md-1 make-inline">
                                            {!! Form::label('result', Lang::get('legal_folder_form.result')) !!}
                                        </div>
                                        <div class="col-md-2 make-inline">
                                            {!! Form::radio('result', 1, $result[0], array('class' => 'make-inline', 'id' => 'positive')) !!}
                                            {!! Form::label('positive', Lang::get('legal_folder_form.positive'), array('class' => 'radio-value')) !!}
                                        </div>
                                        <div class="col-md-2 make-inline">
                                            {!! Form::radio('result', 2, $result[1], array('class' => 'make-inline', 'id' => 'negative')) !!}
                                            {!! Form::label('negative', Lang::get('legal_folder_form.negative'), array('class' => 'radio-value')) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="penalties form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">3. @lang($p."penalties")</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                    $penalty_text = null;
                    for($i = 0; $i < 2; $i++){
                        $penalty[$i] = false;
                    }
                    $penalty[0] = true;
                    if(isset($legal_folder) and $legal_folder != null){
                        $penalty[$legal_folder->penalty_id - 1] = true;
                        $penalty_text = $legal_folder->penalty_text;
                    }
                ?>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group float-left width-100-percent">
                            <div class="col-xs-1 make-inline">
                                {!! Form::label('penalty', Lang::get('legal_folder_form.penalty')) !!}
                            </div>
                            <div class="col-xs-2 make-inline">
                                {!! Form::radio('penalty', 1, $penalty[0], array('class' => 'make-inline', 'id' => 'penalty-yes')) !!}
                                {!! Form::label('penalty-yes', Lang::get('legal_folder_form.yes'), array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-xs-2 make-inline">
                                {!! Form::radio('penalty', 2, $penalty[1], array('class' => 'make-inline', 'id' => 'penalty-no')) !!}
                                {!! Form::label('penalty-no', Lang::get('legal_folder_form.no'), array('class' => 'radio-value')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="penalty-text row hide">
                    <div class="padding-left-right-15">
                        <div class="padding-left-right-15">
                            <div class="form-group float-left width-100-percent">
                                {!! Form::textarea('penalty_text', $penalty_text, array('class' => 'custom-input-textarea width-100-percent')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($legal_folder == null)
    <div class="form-section align-text-center">
        {!! Form::submit(Lang::get('legal_folder_form.save_legal_folder'), array('class' => 'submit-button')) !!}
    @else
    <div class="form-section align-text-center no-bottom-border">
        {!! Form::submit(Lang::get('legal_folder_form.edit_legal_folder'), array('class' => 'submit-button')) !!}
    @endif
    </div>
    {!! Form::close() !!}
    @if($legal_folder != null)
    <div class="lawyer-action-div form-section">
        <div class="row">
            <!-- ACCESS LEVEL -->
            @if (Auth::user()->user_role_id == 5 || Auth::user()->user_role_id == 1)
            <div class="col-xs-12">
                <button id="add-new-session" class="float-right margin-30 float-right session-button lighter-green-background">@lang($p."add_new_session")</button>
            </div>
            @endif
        </div>
        <div class="lawyer-actions">
            <div class="underline-header">
                <h1 class="record-section-header padding-left-right-15">4. @lang($p."lawyer_actions")</h1>
            </div>
            <div class="new-session dynamic-form-section">
                <h1 class="record-section-header padding-left-right-15">@lang($p."new_lawyer_action")</h1>
                {!! Form::open(array('url' => 'benefiter/'.$benefiter->id.'/legal-session')) !!}
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group col-xs-3">
                                    {!! Form::label('legal_name', Lang::get('basic_info_form.user_name')) !!}<i class="fa fa-asterisk asterisk"></i>
                                    {!! Form::text('legal_name', Auth::user()->name . ' ' . Auth::user()->lastname, array('class' => 'custom-input-text', 'disabled' => 'disabled')) !!}
                                </div>
                                <div class="form-group col-xs-3">
                                    {!! Form::label('medical_location_id', Lang::get('basic_info_form.exam_location')) !!}<i class="fa fa-asterisk asterisk"></i>
                                    {!! Form::select('medical_location_id', $medical_locations_array) !!}
                                </div>
                                <div class="form-group col-xs-3">
                                    {!! Form::label('legal_date', Lang::get('basic_info_form.date')) !!}<i class="fa fa-asterisk asterisk"></i>
                                    {!! Form::text('legal_date', null, array('class' => 'custom-input-text width-80-percent date-input', 'placeholder' => Lang::get('dates_common.date_placeholder'))) !!}<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove color-red clear-date"></span></a>
                                </div>
                            </div>
                        </div>
                        <?php
                            for($i = 0; $i < 5; $i++){
                                $lawyer[$i] = false;
                            }
                            if(isset($lawyer_action) and $lawyer_action != null){
                                foreach($lawyer_action as $law_action){
                                    $lawyer[$law_action->lawyer_action_id - 1] = true;
                                }
                            }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group make-inline padding-left-right-15 float-left width-100-percent">
                                    {!! Form::checkbox('lawyer_action[]', 1, $lawyer[0], array('class' => 'float-left', 'id' => 'rights_advise')) !!}
                                    {!! Form::label('rights_advise', Lang::get('legal_folder_form.rights_advise'), array('class' => 'float-left')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group make-inline padding-left-right-15 float-left width-100-percent">
                                    {!! Form::checkbox('lawyer_action[]', 2, $lawyer[1], array('class' => 'float-left', 'id' => 'asylum_advise')) !!}
                                    {!! Form::label('asylum_advise', Lang::get('legal_folder_form.asylum_advise'), array('class' => 'float-left')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group make-inline padding-left-right-15 float-left width-100-percent">
                                    {!! Form::checkbox('lawyer_action[]', 3, $lawyer[2], array('class' => 'float-left', 'id' => 'interview_preparation')) !!}
                                    {!! Form::label('interview_preparation', Lang::get('legal_folder_form.interview_preparation'), array('class' => 'float-left')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group make-inline padding-left-right-15 float-left width-100-percent">
                                    {!! Form::checkbox('lawyer_action[]', 4, $lawyer[3], array('class' => 'float-left', 'id' => 'appeal')) !!}
                                    {!! Form::label('appeal', Lang::get('legal_folder_form.appeal'), array('class' => 'float-left')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group make-inline padding-left-right-15 float-left width-100-percent">
                                    {!! Form::checkbox('lawyer_action[]', 5, $lawyer[4], array('class' => 'float-left', 'id' => 'detention_lift')) !!}
                                    {!! Form::label('detention_lift', Lang::get('legal_folder_form.detention_lift'), array('class' => 'float-left')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group padding-left-right-15">
                                    {!! Form::label('legal_comments', Lang::get('basic_info_form.comments')) !!}
                                    {!! Form::textarea('legal_comments', null, array('class' => 'custom-input-textarea width-100-percent non-printable')) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row padding-bottom-30">
                            <div class="col-md-12">
                                <div class="text-center">
                                    {!! Form::submit(Lang::get('legal_folder_form.save_lawyer_action'), array('class' => 'submit-button')) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            {{-- Ιστορικό δράσεων νομικού --}}
            <div class="row">
                <div class="col-md-12">
                    <div class="row padding-bottom-30">
                        <div class="padding-left-right-15">
                            <div class="table-responsive col-xs-12">
                                <table id="legal-sessions-history">
                                    <thead>
                                        <tr>
                                            <th>@lang("basic_info_form.counter")</th>
                                            <th>@lang("basic_info_form.user_name")</th>
                                            <th>@lang("basic_info_form.location")</th>
                                            <th>@lang("basic_info_form.date")</th>
                                            <th>@lang($p."actions")</th>
                                            <th>@lang("basic_info_form.comments")</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($legalSessionsHistory))
                                    @foreach($legalSessionsHistory as $i => $legalSessionsHistorySingleRow)
                                        <tr>
                                            <td>{{$i+1}}</td>
                                            <td>{{$legalSessionsHistorySingleRow->getUserName()}}</td>
                                            <td>{{$legalSessionsHistorySingleRow->getLocation()}}</td>
                                            <td>{{$datesHelper->getFinelyFormattedStringDateFromDBDate($legalSessionsHistorySingleRow->getDate())}}</td>
                                            <td>{{$legalSessionsHistorySingleRow->getLawyerActions()}}</td>
                                            <td>{{$legalSessionsHistorySingleRow->getComments()}}</td>
                                        </tr>
                                    @endforeach
                                    @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>@lang("basic_info_form.counter")</th>
                                            <th>@lang("basic_info_form.user_name")</th>
                                            <th>@lang("basic_info_form.location")</th>
                                            <th>@lang("basic_info_form.date")</th>
                                            <th>@lang($p."actions")</th>
                                            <th>@lang("basic_info_form.comments")</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@else
    <div class="personal-family-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">1. @lang("social_folder_form.personal_family_info")</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-2">
                            {!! Form::label('folder_number', Lang::get('basic_info_form.folder_number')) !!}
                            {!! Form::text('folder_number', $benefiter->folder_number, array('class' => 'custom-input-text text-align-right width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-2">
                            {!! Form::label('lastname', Lang::get('basic_info_form.lastname')) !!}
                            {!! Form::text('lastname', $benefiter->lastname, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-2">
                            {!! Form::label('name', Lang::get('basic_info_form.name')) !!}
                            {!! Form::text('name', $benefiter->name, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-2">
                            {!! Form::label('fathers_name', Lang::get('basic_info_form.fathers_name')) !!}
                            {!! Form::text('fathers_name', $benefiter->fathers_name, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-3">
                            {!! Form::label('children_names', Lang::get('basic_info_form.children_names')) !!}
                            {!! Form::text('children_names', $benefiter->children_names, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-2">
                            {!! Form::label('telephone', Lang::get('basic_info_form.telephone')) !!}
                            {!! Form::text('telephone', $benefiter->telephone, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-2">
                            {!! Form::label('origin_country', Lang::get('basic_info_form.origin_country')) !!}
                            {!! Form::text('origin_country', $benefiter->origin_country, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-3">
                            {!! Form::label('ethnic_group', Lang::get('basic_info_form.ethnic_group')) !!}
                            {!! Form::text('ethnic_group', $benefiter->ethnic_group, array('class' => 'custom-input-text width-100-percent', 'disabled' => 'disabled')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-md-3">
                            {!! Form::label('birth_date', Lang::get('basic_info_form.birth_date')) !!}
                            {!! Form::text('birth_date', $benefiter->birth_date, array('class' => 'custom-input-text width-80-percent date-input', 'disabled' => 'disabled')) !!}<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove color-red clear-date hide"></span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="legal-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">2. @lang($p."legal_info")</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <?php
                        for($i = 0; $i < 8; $i++){
                            $legal_folder_status[$i] = false;
                        }
                        if(isset($legal_folder) and $legal_folder != null){
                            $legal_folder_status[$legal_folder->legal_folder_status_id - 1] = true;
                        } else {
                            $legal_folder_status[0] = true;
                        }
                        ?>
                        <div class="form-group float-left width-100-percent">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 1, $legal_folder_status[0], array('class' => 'make-inline', 'id' => 'asylum', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('asylum', Lang::get('legal_folder_form.asylum'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 2, $legal_folder_status[1], array('class' => 'make-inline legal-status', 'id' => 'no-legal', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('no-legal', Lang::get('legal_folder_form.no_legal'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 3, $legal_folder_status[2], array('class' => 'make-inline legal-status', 'id' => 'asylum-number', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('asylum-number', Lang::get('legal_folder_form.asylum_number'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 4, $legal_folder_status[3], array('class' => 'make-inline legal-status', 'id' => 'refugee-number', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('refugee-number', Lang::get('legal_folder_form.refugee_number'), array('class' => 'radio-value')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 5, $legal_folder_status[4], array('class' => 'make-inline legal-status', 'id' => 'stay-permit-ack', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('stay-permit-ack', Lang::get('legal_folder_form.stay_permit_ack'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 6, $legal_folder_status[5], array('class' => 'make-inline legal-status', 'id' => 'stay-permit', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('stay-permit', Lang::get('legal_folder_form.stay_permit'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 7, $legal_folder_status[6], array('class' => 'make-inline legal-status', 'id' => 'european', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('european', Lang::get('legal_folder_form.european'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-xs-3 make-inline">
                                        {!! Form::radio('legal_folder_status', 8, $legal_folder_status[7], array('class' => 'make-inline legal-status', 'id' => 'minor', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('minor', Lang::get('legal_folder_form.minor'), array('class' => 'radio-value')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="asylum-request dynamic-form-section hide">
                            <h1 class="record-section-header padding-left-right-15">@lang($p."asylum")</h1>
                            <?php
                                $asylum_request_date = null;
                                $request_progress = null;
                                for($i = 0; $i < 2; $i++){
                                    $procedure[$i] = false;
                                }
                                $procedure[0] = true;
                                for($i = 0; $i < 3; $i++){
                                    $request_status[$i] = "";
                                }
                                $request_status[0] = "selected";
                                if(isset($asylum_request) and $asylum_request != null){
                                    $asylum_request_date = $datesHelper->getFinelyFormattedStringDateFromDBDate($asylum_request->request_date);
                                    $procedure[$asylum_request->procedure_id - 1] = true;
                                    $request_status[0] = "";
                                    $request_status[$asylum_request->request_status_id - 1] = "selected";
                                    $request_progress = $asylum_request->request_progress;
                                }
                            ?>
                            <div class="row">
                                <div class="form-group float-left padding-left-right-15 width-100-percent">
                                    <div class="col-md-2 make-inline">
                                        {!! Form::label('asylum_request_date', Lang::get('legal_folder_form.asylum_request_date')) !!}
                                        {!! Form::text('asylum_request_date', $asylum_request_date, array('class' => 'custom-input-text width-80-percent date-input', 'disabled' => 'disabled')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group float-left padding-left-right-15 width-100-percent">
                                    <div class="col-md-3 make-inline">
                                        {!! Form::radio('procedure', 1, $procedure[0], array('class' => 'make-inline', 'id' => 'procedure-old', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('procedure', Lang::get('legal_folder_form.procedure_old'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-md-3 make-inline">
                                        {!! Form::radio('procedure', 2, $procedure[1], array('class' => 'make-inline', 'id' => 'procedure-new', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('procedure', Lang::get('legal_folder_form.procedure_new'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="request-status col-md-6 make-inline hide">
                                        {!! Form::label('request_status', Lang::get('legal_folder_form.request_status')) !!}
                                        <select name="request_status" class="request-status-selection" disabled>
                                            <option value="1" {{ $request_status[0] }} disabled>α'</option>
                                            <option value="2" {{ $request_status[1] }} disabled>β'</option>
                                            <option value="3" {{ $request_status[2] }} disabled>μεταγενέστερο</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="padding-left-right-15">
                                    <div class="form-group padding-left-right-15">
                                        {!! Form::label('request_progress', Lang::get('legal_folder_form.request_progress')) !!}
                                        {!! Form::textarea('request_progress', $request_progress, array('class' => 'custom-input-textarea width-100-percent', 'disabled' => 'disabled')) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="legal-status-div  dynamic-form-section hide">
                            <h1 class="record-section-header padding-left-right-15">@lang($p."no_legal")</h1>
                            <?php
                                for($i = 0; $i < 8; $i++){
                                    $action[$i] = false;
                                }
                                $action[0] = true;
                                for($i = 0; $i < 8; $i++){
                                    $result[$i] = false;
                                }
                                $result[0] = true;
                                if(isset($legal_status) and $legal_status != null){
                                    $selected;
                                    $action[$legal_status->action_id - 1] = true;
                                    $result[$legal_status->result_id - 1] = true;
                                }
                            ?>
                            <div class="row">
                                <div class="form-group padding-left-right-15 float-left width-100-percent">
                                    <div class="col-md-3 make-inline">
                                        {!! Form::radio('action', 1, $action[0], array('class' => 'make-inline', 'id' => 'action-none', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('action', Lang::get('legal_folder_form.action_none'), array('class' => 'radio-value')) !!}
                                    </div>
                                    <div class="col-md-3 make-inline">
                                        {!! Form::radio('action', 2, $action[1], array('class' => 'make-inline', 'id' => 'action-refusal', 'disabled' => 'disabled')) !!}
                                        {!! Form::label('action', Lang::get('legal_folder_form.action_refusal'), array('class' => 'radio-value')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="results hide">
                                <div class="row">
                                    <div class="form-group padding-left-right-15">
                                        <div class="col-md-1 make-inline">
                                            {!! Form::label('result', Lang::get('legal_folder_form.result')) !!}
                                        </div>
                                        <div class="col-md-2 make-inline">
                                            {!! Form::radio('result', 1, $result[0], array('class' => 'make-inline', 'disabled' => 'disabled')) !!}
                                            {!! Form::label('result', Lang::get('legal_folder_form.positive'), array('class' => 'radio-value')) !!}
                                        </div>
                                        <div class="col-md-2 make-inline">
                                            {!! Form::radio('result', 2, $result[1], array('class' => 'make-inline', 'disabled' => 'disabled')) !!}
                                            {!! Form::label('result', Lang::get('legal_folder_form.negative'), array('class' => 'radio-value')) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="penalties form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">3. @lang($p."penalties")</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                    $penalty_text = null;
                    for($i = 0; $i < 2; $i++){
                        $penalty[$i] = false;
                    }
                    $penalty[0] = true;
                    if(isset($legal_folder) and $legal_folder != null){
                        $penalty[$legal_folder->penalty_id - 1] = true;
                        $penalty_text = $legal_folder->penalty_text;
                    }
                ?>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group float-left width-100-percent">
                            <div class="col-md-1 make-inline">
                                {!! Form::label('penalty', Lang::get('legal_folder_form.penalty')) !!}
                            </div>
                            <div class="col-md-2 make-inline">
                                {!! Form::radio('penalty', 1, $penalty[0], array('class' => 'make-inline', 'id' => 'penalty-yes', 'disabled' => 'disabled')) !!}
                                {!! Form::label('penalty', Lang::get('legal_folder_form.yes'), array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-2 make-inline">
                                {!! Form::radio('penalty', 2, $penalty[1], array('class' => 'make-inline', 'id' => 'penalty-no', 'disabled' => 'disabled')) !!}
                                {!! Form::label('penalty', Lang::get('legal_folder_form.no'), array('class' => 'radio-value')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="penalty-text row hide">
                    <div class="padding-left-right-15">
                        <div class="padding-left-right-15">
                            <div class="form-group float-left width-100-percent">
                                {!! Form::textarea('penalty_text', $penalty_text, array('class' => 'custom-input-textarea width-100-percent', 'disabled' => 'disabled')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="lawyer-actions form-section">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">4. @lang($p."lawyer_actions")</h1>
        </div>
        {{-- Ιστορικό δράσεων νομικού --}}
        <div class="row">
            <div class="col-md-12">
                <div class="row padding-bottom-30">
                    <div class="padding-left-right-15">
                        <div class="table-responsive col-xs-12">
                            <table id="legal-sessions-history">
                                <thead>
                                <tr>
                                    <th>@lang("basic_info_form.counter")</th>
                                    <th>@lang("basic_info_form.user_name")</th>
                                    <th>@lang("basic_info_form.location")</th>
                                    <th>@lang("basic_info_form.date")</th>
                                    <th>@lang($p."actions")</th>
                                    <th>@lang("basic_info_form.comments")</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($legalSessionsHistory))
                                    @foreach($legalSessionsHistory as $i => $legalSessionsHistorySingleRow)
                                        <tr>
                                            <td>{{$i+1}}</td>
                                            <td>{{$legalSessionsHistorySingleRow->getUserName()}}</td>
                                            <td>{{$legalSessionsHistorySingleRow->getLocation()}}</td>
                                            <td>{{$datesHelper->getFinelyFormattedStringDateFromDBDate($legalSessionsHistorySingleRow->getDate())}}</td>
                                            <td>{{$legalSessionsHistorySingleRow->getLawyerActions()}}</td>
                                            <td>{{$legalSessionsHistorySingleRow->getComments()}}</td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>@lang("basic_info_form.counter")</th>
                                    <th>@lang("basic_info_form.user_name")</th>
                                    <th>@lang("basic_info_form.location")</th>
                                    <th>@lang("basic_info_form.date")</th>
                                    <th>@lang($p."actions")</th>
                                    <th>@lang("basic_info_form.comments")</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endif
</div>
