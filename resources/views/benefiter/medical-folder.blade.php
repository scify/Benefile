@extends('layouts.mainPanel')

@section('menu-item', ($benefiter->id ?? -1) == -1 ? 'new-benefiter' : 'edit-benefiter')

@section('panel-title')
    @lang('partials/forms/new_medical_visit_form.medical_folder')
@stop

@section('panel-headLinks')
    <link href="{{ asset('vendor/vanillajs-datepicker/datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/uploadExcel/dropzone.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/records/new_record_panel.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/records/validation_errors.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/records/record_form.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/tom-select/tom-select.default.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('bootstrap-3.3.6/dist/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" media="print">
    <link href="{{asset('css/records/record_form.css')}}" rel="stylesheet" type="text/css" media="print">
    <link href="{{ asset('css/print/generic_print.css') }}" rel="stylesheet" type="text/css" media="print">
    <link href="{{ asset('css/print/medical_visit_print.css') }}" rel="stylesheet" type="text/css" media="print">
@stop

@section('benefiter-info')
    @if(isset($benefiter) and $benefiter != null)
        <div class="benefiter-info">
            <h2>{{$benefiter->name}} {{$benefiter->lastname}} / {{$benefiter->folder_number}}</h2>
        </div>
    @endif
@stop

@section('main-window-content')

    <?php
        $tab = "medical";
        $p = "partials/forms/new_medical_visit_form.";
    ?>
    @include('partials.select-panel')

    @include('partials.forms.medical-visit.new_medical_visit')
@stop

@section('panel-scripts')
    <script src="{{ asset('vendor/tom-select/tom-select.complete.min.js') }}"></script>
    @include('partials.datepicker-scripts')
    <script src="{{asset('js/forms.js')}}"></script>
    <script src="{{asset('js/records/dynamic_rows.js')}}"></script>
    <script src="{{asset('js/records/medical_visit.js')}}"></script>
@stop
