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
    @if(isset($benefiter) and $benefiter != null)
        <div class="benefiter-info">
            <h2>{{$benefiter->name}} {{$benefiter->lastname}} / {{$benefiter->folder_number}}</h2>
        </div>
    @endif
@stop

@section('main-window-content')

    @include('partials.select-panel')

    @if (legacy_count($errors) > 0 || Session::has('flash_message'))
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

    @include('partials.forms.legal_folder_form')

@stop

@section('panel-scripts')
    @include('partials.datepicker-scripts')
    <script src="{{asset('js/forms.js')}}"></script>
    <script src="{{ asset('js/records/legal_folder.js') }}"></script>
@stop
