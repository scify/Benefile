<?php
    $p = "new_location.";
?>

@extends('layouts.mainPanel')

@section('panel-title')
    @lang($p.'add_location')
@stop

@section('panel-headLinks')
    <link href="{{asset('css/records/new_record_panel.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/records/validation_errors.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/records/record_form.css')}}" rel="stylesheet" type="text/css">
@stop

@section('main-window-content')
@if (count($errors) > 0 || Session::has('flash_message'))
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@else
    @if(isset($success) and $success != null)
    <div class="alert alert-success">
        <ul>
            <li>{{ $success }}</li>
        </ul>
    </div>
    @endif
@endif
<div class="add-new-location form-section">
    <div class="underline-header">
        <h1 class="record-section-header padding-left-right-15">@lang($p."add_location")</h1>
    </div>
    <div class="row">
        <div class="col-md-12 padding-left-right-15">
            {!! Form::open(array('url' => 'save-location')) !!}
            <div class="form-group col-md-6 padding-left-right-15 text-center">
                {!! Form::label('new_location', Lang::get($p.'new_location_name')) !!}
                {!! Form::text('new_location', null, array('class' => 'custom-input-text margin-0-auto')) !!}
            </div>
            <div class="col-md-6 text-center">
                {!! Form::submit(Lang::get($p.'save_location'), array('class' => 'submit-button')) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@stop

@section('panel-scripts')
    <script src="{{asset('js/forms.js')}}"></script>
@stop
