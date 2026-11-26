@extends('layouts.mainPanel')

@section('panel-title')
    @lang('legal_folder_form.legal_folder')
@stop

@section('menu-item', 'edit-benefiter')

@section('panel-headLinks')
    <link href="{{ asset('vendor/vanillajs-datepicker/datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/records/new_record_panel.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/records/validation_errors.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/records/record_form.css')}}" rel="stylesheet" type="text/css">
@stop

@section('benefiter-info')
    <x-beneficiary-header :benefiter="$benefiter ?? null" />
@stop

@section('main-window-content')

    @include('partials.select-panel')

    <x-form-messages :success="$success ?? null" />

    @include('partials.forms.legal_folder_form')

@stop

@section('panel-scripts')
    @include('partials.datepicker-scripts')
    <script src="{{asset('js/forms.js')}}"></script>
    <script src="{{ asset('js/records/legal_folder.js') }}"></script>
@stop
