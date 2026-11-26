@extends('layouts.mainPanel')

@section('panel-title')
    @if($benefiter->id == null) @lang('basic_info_form.new_benefiter') @else @lang('basic_info_form.basic_info') @endif
@stop

@section('menu-item', ($benefiter->id ?? -1) == -1 ? 'new-benefiter' : 'edit-benefiter')

@section('panel-headLinks')
    <link href="{{ asset('vendor/vanillajs-datepicker/datepicker.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/records/new_record_panel.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/records/validation_errors.css') }}" rel="stylesheet" type="text/css">
    <link href="{{asset('css/records/record_form.css')}}" rel="stylesheet" type="text/css">
    <link href="{{asset('bootstrap-3.3.6/dist/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" media="print">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css' media="print">
    <link href="{{asset('css/records/record_form.css')}}" rel="stylesheet" type="text/css" media="print">
    <link href="{{ asset('css/print/generic_print.css') }}" rel="stylesheet" type="text/css" media="print">
    <link href="{{ asset('css/print/basic_info_print.css') }}" rel="stylesheet" type="text/css" media="print">
@stop

@section('benefiter-info')
    @if(isset($benefiter) and $benefiter != null)
        <div class="benefiter-info">
            <h2>{{$benefiter->name}} {{$benefiter->lastname}} / {{$benefiter->folder_number}}</h2>
        </div>
    @endif
@stop

@section('main-window-content')

    @include('partials.select-panel')

    @if (legacy_count($errors) > 0)
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

    @include('partials.forms.basic_info_form')
@stop

@section('panel-scripts')
    @include('partials.datepicker-scripts')
    {{-- A new beneficiary: look for folders with the same name. --}}
    @if($benefiter->id == -1)
    <script src="{{ asset('js/records/check_if_benefiter_already_exists.js') }}"></script>
    @endif
    <script src="{{asset('js/forms.js')}}"></script>
    <script src="{{asset('js/records/dynamic_rows.js')}}"></script>
    <script src="{{asset('js/records/basic_info.js')}}"></script>
@stop
