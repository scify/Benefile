<?php
    $p = 'search/search.';
?>
@extends('layouts.mainPanel')

@section('panel-title')
    @lang('layouts/mainPanel.search')
@stop

@section('panel-headLinks')
    <link href="{{ asset('/plugins/fontawesome/css/font-awesome.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('plugins/faloading/jquery.faloading.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/datepicker/css/datepicker.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/records/validation_errors.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/records/record_form.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/search/search.css')}}" rel="stylesheet" type="text/css">
@stop

@section('main-window-content')
    <div class="benefiters-search margin-bottom-300px form-section" data-no-lang="@lang($p."binary_0")" data-yes-lang="@lang($p."binary_1")">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">@lang($p."search_parameters")</h1>
        </div>
    {!! Form::open(array('url' => 'results', 'id' => 'search-form', 'method' => 'get')) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('folder_number', Lang::get('basic_info_form.folder_number')) !!}
                            @if(isset($_GET['search']))
                            {!! Form::text('folder_number', $_GET['search'], array('class' => 'custom-input-text width-100-percent')) !!}
                            @else
                            {!! Form::text('folder_number', null, array('class' => 'custom-input-text width-100-percent')) !!}
                            @endif
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('lastname', Lang::get('basic_info_form.lastname')) !!}
                            {!! Form::text('lastname', null, array('class' => 'custom-input-text width-100-percent')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('name', Lang::get('basic_info_form.name')) !!}
                            {!! Form::text('name', null, array('class' => 'custom-input-text width-100-percent')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('gender_id', Lang::get('basic_info_form.gender')) !!}
                            <div class="width-100-percent">
                                {!! Form::radio('gender_id', 1, false, array('class' => 'make-inline', 'id' => 'male')) !!}
                                {!! Form::label('male', Lang::get('basic_info_form.male'), array('class' => 'radio-value')) !!}
                                {!! Form::radio('gender_id', 2, false, array('class' => 'make-inline', 'id' => 'female')) !!}
                                {!! Form::label('female', Lang::get('basic_info_form.female'), array('class' => 'radio-value')) !!}
                                {!! Form::radio('gender_id', 3, false, array('class' => 'make-inline', 'id' => 'other')) !!}
                                {!! Form::label('other', Lang::get('basic_info_form.other'), array('class' => 'radio-value')) !!}
                            </div>
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                            {!! Form::label(Lang::get('basic_info_form.birth_date')) !!}
                            <div>
                                {!! Form::text('birth_date', null, array('class' => 'custom-input-text width-80-percent date-input', 'placeholder' => Lang::get('dates_common.date_placeholder'))) !!}<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove color-red clear-date"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('fathers_name', Lang::get('basic_info_form.fathers_name')) !!}
                            {!! Form::text('fathers_name', null, array('class' => 'custom-input-text width-100-percent')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('age', Lang::get('search/search.age')) !!}
                            {!! Form::text('age', null, array('class' => 'custom-input-text width-100-percent')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('telephone', Lang::get('basic_info_form.telephone')) !!}
                            {!! Form::text('telephone', null, array('class' => 'custom-input-text width-100-percent')) !!}
                        </div>

                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                            {!! Form::label('origin_country', Lang::get('basic_info_form.origin_country')) !!}
                            {!! Form::text('origin_country', null, array('class' => 'custom-input-text width-100-percent')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                            {!! Form::label(Lang::get('search/search.insertion_date')) !!}
                            <div>
                                {!! Form::text('insertion_date', null, array('class' => 'custom-input-text width-80-percent date-input', 'placeholder' => Lang::get('dates_common.date_placeholder'))) !!}<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove color-red clear-date"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="padding-left-right-15">
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                    {!! Form::label('marital_status_id', Lang::get('search/search.marital_status')) !!}
                    <div>
                        <select name="marital_status_id" id="marital-status-id" class="width-100-percent">
                            <option value=0></option>
                            <?php
                                if(!empty($marital_statuses)){
                                    foreach($marital_statuses as $marital_status){
                                        echo "<option value=" . $marital_status->id . ">" . $marital_status->marital_status_title . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                    {!! Form::label('work_title_id', Lang::get('search/search.work')) !!}
                    <div>
                        <select name="work_title_id" id="work-title-id" class="width-100-percent">
                            <option value=0></option>
                            <?php
                                if(!empty($work_titles)){
                                    foreach($work_titles as $work_title){
                                        if($work_title->work_title == ""){
                                            $work_title->work_title = "-";
                                        }
                                        echo "<option value=" . $work_title->id . ">" . $work_title->work_title . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                    {!! Form::label('education_id', Lang::get('search/search.education')) !!}
                    <div>
                        <select name="education_id" id="education-id" class="width-100-percent">
                            <option value=0></option>
                            <?php
                                if(!empty($education_titles)){
                                    foreach($education_titles as $education_title){
                                        echo "<option value=" . $education_title->id . ">" . $education_title->education_title . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                    {!! Form::label('legal_status_id', Lang::get('search/search.legal_status')) !!}
                    <div>
                        <select name="legal_status_id" id="legal-status-id" class="width-100-percent">
                            <option value=0></option>
                            <?php
                                if(!empty($legal_statuses)){
                                    foreach($legal_statuses as $legal_status){
                                        echo "<option value=" . $legal_status->id . ">" . $legal_status->description . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        {{-- MEDICAL --}}
        <div class="row border-top ">
            <div class="padding-left-right-15 margin-top-14px">
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-2">
                    {!! Form::label('doctor_name', Lang::get('search/search.doctor_name')) !!}
                    {!! Form::text('doctor_name', null, array('class' => 'custom-input-text width-100-percent')) !!}
                </div>
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                    {{--{!! Form::label('medical_location_id', Lang::get('partials/forms/new_medical_visit_form.exam_location', array('class' => 'custom-label-width'))) !!}--}}
                    <label class="custom-label-width" for="medical_location_id">@lang('partials/forms/new_medical_visit_form.exam_location')</label>
                   <div>
                        <select id="medical-location-id" name="medical_location_id" class="width-100-percent">
                            <option value=0></option>
                            <?php
                                if(isset($medical_locations) and $medical_locations != null){
                                    foreach($medical_locations as $location){
                                        echo "<option value=" . $location->id . ">" . $location->description . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                    {!! Form::label('incident_type_id', Lang::get('search/search.incident_type')) !!}
                    <div>
                        <select name="incident_type_id" id="incident-type-id" class="width-100-percent">
                            <option value=0></option>
                            <?php
                                if(!empty($medical_incident_types)){
                                    foreach($medical_incident_types as $medical_incident_type){
                                        echo "<option value=" . $medical_incident_type->id . ">" . $medical_incident_type->description . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                    {!! Form::label('incidents_number', Lang::get('search/search.incidents_number')) !!}
                    {!! Form::text('incidents_number', null, array('class' => 'custom-input-text width-100-percent')) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="padding-left-right-15">
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-3">
                    {!! Form::label('drug', Lang::get('search/search.drug')) !!}
                    {!! Form::text('drug', null, array('class' => 'custom-input-text width-100-percent')) !!}
                </div>
                <div class="form-group make-inline padding-left-right-15 float-left col-xs-4">
                    {!! Form::label('examination_results_id', Lang::get('search/search.examination_results')) !!}
                    <div>
                        <select name="examination_results_id" id="examination-results-id" class="width-100-percent">
                            <option value=0></option>
                            <?php
                                if(!empty($medical_examination_results)){
                                    foreach($medical_examination_results as $medical_examination_result){
                                        echo "<option value=" . $medical_examination_result->id . ">" . $medical_examination_result->description . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group make-inline padding-left-right-15 float-left col-xs-5">
                    <div class="row">
                        <div class="padding-left-right-15">
                            {!! Form::label(Lang::get('search/search.incident_dates_range')) !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-6">
                            {{--{!! Form::label(Lang::get('search/search.from')) !!}--}}
                            <label class="float-left">@lang('search/search.from')</label>
                            <div>
                                {!! Form::text('incident_from', null, array('class' => 'custom-input-text width-60per date-input', 'placeholder' => Lang::get('dates_common.date_placeholder'))) !!}<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove color-red clear-date"></span></a>
                            </div>
                        </div>
                        <div class="form-group make-inline padding-left-right-15 float-left col-xs-6">
                            {{--{!! Form::label(Lang::get('search/search.to')) !!}--}}
                            <label class="float-left">@lang('search/search.to')</label>
                            <div>
                                {!! Form::text('incident_to', null, array('class' => 'custom-input-text width-60per date-input', 'placeholder' => Lang::get('dates_common.date_placeholder'))) !!}<a href="javascript:void(0)"><span class="glyphicon glyphicon-remove color-red clear-date"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- BUTTONS --}}
        <div class="row">
            <div class="padding-left-right-15 text-align-right">
                {{-- FETCH ALL BUTTON --}}
                {!! Form::button(Lang::get($p.'fetch_all'), array('class' => 'search-submit-button', 'id' => 'fetch-all-btn')) !!}
                {{-- SUBMIT BUTTON --}}
                {!! Form::submit(Lang::get('layouts/mainPanel.search'), array('class' => 'search-submit-button', 'id' => 'search-btn')) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div id="search-results" class="form-section min-height-300px" style="display: none;" data-url="{{ url('benefiter/-1/basic-info') }}" data-view-folders="{{ Lang::get($p."view_folders") }}">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">@lang($p."search_results")</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="state state-results" class="row padding-bottom-30">
                    <div class="no-margin pos-relative" id="results-to-activate">
                        <div class="text-align-center margin-top-20">
                            <a id="download-link" class="simple-button" href="javascript:void(0)">@lang($p."download_csv")</a>
                        </div>
                        <div class="display padding-20">
                            <table id="results" class="display" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th class="printable">@lang($p."folder_number")</th>
                                    <th class="printable">@lang("basic_info_form.name")</th>
                                    <th class="printable">@lang("basic_info_form.lastname")</th>
                                    <th class="printable">@lang("basic_info_form.telephone")</th>
                                    <th class="hide printable">@lang("basic_info_form.fathers_name")</th>
                                    <th class="hide printable">@lang("basic_info_form.mothers_name")</th>
                                    <th class="hide printable">@lang("basic_info_form.birth_date")</th>
                                    <th class="hide printable">@lang("basic_info_form.arrival_date")</th>
                                    <th class="hide printable">@lang("basic_info_form.address")</th>
                                    <th class="hide printable">@lang("basic_info_form.number_of_children")</th>
                                    <th class="hide printable">@lang("basic_info_form.relatives_residence")</th>
                                    <th class="hide printable">@lang($p."language_interpreter_needed")</th>
                                    <th class="hide printable">@lang($p."is_benefiter_working")</th>
                                    <th class="hide printable">@lang($p."country_abandon_reason")</th>
                                    <th class="hide printable">@lang($p."travel_route")</th>
                                    <th class="hide printable">@lang($p."travel_duration")</th>
                                    <th class="hide printable">@lang($p."detention_duration")</th>
                                    <th class="hide printable">@lang($p."origin_country")</th>
                                    <th class="hide printable">@lang($p."nationality_country")</th>
                                    <th class="hide printable">@lang($p."ethnic_group")</th>
                                    <th class="hide printable">@lang($p."social_history")</th>
                                    <th class="hide printable">@lang($p."marital_status_title")</th>
                                    <th class="hide printable">@lang($p."education_title")</th>
                                    <th class="hide printable">@lang($p."legal_working_status")</th>
                                    <th class="hide printable">@lang($p."work_title")</th>
                                    <th>@lang($p."view")</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>@lang($p."folder_number")</th>
                                    <th>@lang("basic_info_form.name")</th>
                                    <th>@lang("basic_info_form.lastname")</th>
                                    <th>@lang("basic_info_form.telephone")</th>
                                    <th>@lang($p."view")</th>
                                </tr>
                                </tfoot>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="state state-loading min-height-150px padding-left-right-15">
                </div>
                <div class="state state-no-results">
                    <h1>@lang($p."no_results")</h1>
                </div>
                <div class="state state-error">
                    <h1>@lang($p."error")</h1>
                </div>
            </div>
        </div>
    </div>
@stop

@section('panel-scripts')
    <script src="{{ asset('plugins/faloading/jquery.faloading-0.1.min.js') }}"></script>
    <script src="{{asset('js/main-panel/selectSearchInMainPanel.js')}}"></script>
    <script src="{{ asset('/plugins/datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('js/records/custom_datepicker.js') }}"></script>
    <script src="{{asset('js/forms.js')}}"></script>
    <script src="{{ asset('js/search/search.js') }}"></script>
@stop
