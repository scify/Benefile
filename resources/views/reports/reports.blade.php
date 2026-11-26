<?php
    $p = 'reports.';
?>

@extends('layouts.mainPanel')

@section('panel-title')
    @lang('layouts/mainPanel.reports')
@stop

@section('menu-item', 'reports')

@section('panel-headLinks')
    <link href="{{ asset('css/records/record_form.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/reports/reports.css') }}" rel="stylesheet" type="text/css">
@stop

@section('main-window-content')
    {{-- download .csv with all the benefiters folders history --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'download_csv')</h1>
                </div>
                <div class="text-center margin-top-60 margin-bottom-50">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <a class="simple-button font-size-1_2em display-block" href="{{ url('download-benefiters-csv') }}">@lang($p.'download_all_benefiters_csv')</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <a class="simple-button font-size-1_2em display-block" href="{{ url('download-history-csv') }}">@lang($p.'download_folders_history_csv')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- REPORT: users numbers divided by their roles --}}
    <div class="users-report form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">@lang($p."users")</h1>
        </div>
        <div class="row">
        <div class="col-md-12">
            <div id="doctors" class="col-xs-3">
                <img class="make-inline width-120px height-120px img-responsive" src="{{ asset('/img/benefile_3_doctors.jpg') }}" />
                <div class="make-inline">
                    <p class="users-title">@lang($p."doctors")</p>
                    <p class="users-counter">{{ $users_roles_count['doctors'] }}</p>
                </div>
            </div>
            <div id="legals" class="col-xs-3">
                <img class="make-inline width-120px height-120px img-responsive" src="{{ asset('/img/benefile_3_legals.jpg') }}" />
                <div class="make-inline">
                    <p class="users-title">@lang($p."legals")</p>
                    <p class="users-counter">{{ $users_roles_count['legals'] }}</p>
                </div>
            </div>
            <div id="psychologists" class="col-xs-3">
                <img class="make-inline width-120px height-120px img-responsive" src="{{ asset('/img/benefile_3_psychologists.jpg') }}" />
                <div class="make-inline">
                    <p class="users-title">@lang($p."psychologists")</p>
                    <p class="users-counter">{{ $users_roles_count['psychologists'] }}</p>
                </div>
            </div>
            <div id="socials" class="col-xs-3">
                <img class="make-inline width-120px height-120px img-responsive" src="{{ asset('/img/benefile_3_socials.jpg') }}" />
                <div class="make-inline">
                    <p class="users-title">@lang($p."socials")</p>
                    <p class="users-counter">{{ $users_roles_count['socials'] }}</p>
                </div>
            </div>
        </div>
        </div>
    </div>
    {{-- REPORT: Benefiters VS gerder --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">@lang($p."benefiters")</h1>
        </div>
        <div id="benefiters_vs_gerder" class="row padding-30 row-eq-height display-table ">
            <div class="col-md-10 col-equal-height">
                {{-- male icons --}}
                @for($i=0 ; $i< $report_benefiters_vs_gender['male_percentage'] ; $i++)
                    <icon class="glyphicon glyphicon-user gender-icon male-color"></icon>
                @endfor
                {{-- female icons --}}
                @for($i=0 ; $i< $report_benefiters_vs_gender['female_percentage'] ; $i++)
                    <icon class="glyphicon glyphicon-user gender-icon female-color"></icon>
                @endfor
                {{-- others icons --}}
                @for($i=0 ; $i< $report_benefiters_vs_gender['other_percentage'] ; $i++)
                    <icon class="glyphicon glyphicon-user gender-icon other-color"></icon>
                @endfor
            </div>

            <div class="col-md-2 col-equal-height">
                {{-- male --}}
                <div class=" male-color padding-bottom-30">
                    <div class="gender-number">{{ $report_benefiters_vs_gender['male'] }}</div>

                    <div class="gender-text">@lang($p.'male')</div>
                </div>
                {{-- female --}}
                <div class=" female-color padding-bottom-30">
                    <div class="gender-number">{{ $report_benefiters_vs_gender['female'] }}</div>

                    <div class="gender-text">@lang($p.'female')</div>
                </div>
                {{-- other --}}
                <div class=" other-color">
                    <div class="gender-number">{{ $report_benefiters_vs_gender['other'] }}</div>

                    <div class="gender-text">@lang($p.'other')</div>
                </div>

            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters marital statuses --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'h1-marital-status')</h1>
                </div>
                <div id="maritalStatusReport"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Medical visits location --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'h1-medical-visits-location')</h1>
                </div>
                <div id="medicalStatusReport"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters work titles --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'h1-work-title')</h1>
                </div>
                <div id="benefiters-work-title-chart" data-benefiters-registered="@lang($p."benefiters_registered")"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters work titles end --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'h1-medical-visits')</h1>
                </div>
                    <div id="benefiters-per-medical-visits-chart"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters age report end --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'h1-age-report')</h1>
                </div>
                <div id="ageReport"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters legal statuses --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'h1-legal-status')</h1>
                </div>
                <div id="legalStatusReport"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters registration numbers per month --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'h1-registration-status')</h1>
                </div>
                <div id="registrations_per_month" class="padding-left-right-15" data-registrations-per-month="@lang($p."registrations_per_month")"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters vs education --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'report-education')</h1>
                </div>
                <div id="benefiter_vs_education"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters vs doctor --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'report-doctors-type')</h1>
                </div>
                <div id="benefiter_vs_doctor_type"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters vs clinical conditions --}}
    <div class="benefiters-report form-section no-bottom-border">
        <div class="row">
            <div class="col-md-7 padding-right-0">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'report-clinical-condition')</h1>
                </div>
                <div id="benefiter_vs_clinical_conditions" data-number-of-benefiters="@lang($p."number_of_benefiters")"></div>
            </div>
            <div class="col-md-5 left-border">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'report-med-visits-per-month')</h1>
                </div>
                <div id="medical_visits_per_month" class="padding-left-right-15" data-medical-visits-per-month="@lang($p."medical_visits_per_month")"></div>
            </div>
        </div>
    </div>
    {{-- REPORT: Benefiters vs phycological support --}}
    <div class="benefiters-report form-section">
        <div class="row">
            <div class="col-md-12">
                <div class="underline-header">
                    <h1 class="record-section-header padding-left-right-15">@lang($p.'report-phycological-support')</h1>
                </div>
                <div id="benefiter_vs_phycological_support" data-number-of-benefiters="@lang($p."number_of_benefiters")"></div>
            </div>
        </div>
    </div>
@stop

@section('panel-scripts')
    @php
        $reportData = [
            'texts' => [
                'periods' => [
                    'days' => __('reports.period_10_days'),
                    'month' => __('reports.period_1_month'),
                    'year' => __('reports.period_1_year'),
                    'max' => __('reports.period_max'),
                ],
                'benefiters' => __('reports.balloon_benefiters'),
                'doctorsTitle' => __('reports.benefiters-Vs-doctors-tile'),
                'workTitle' => __('reports.benefiters_registered'),
                'numberOfBenefiters' => __('reports.number_of_benefiters'),
                'visitsPerMonth' => __('reports.medical_visits_per_month'),
                'registrationsPerMonth' => __('reports.registrations_per_month'),
                'visitsX' => __('reports.medical-visit-x-title'),
                'visitsY' => __('reports.medical-visit-y-title'),
            ],
            'ages' => collect($benefiters_age)->map(fn ($age) => [
                'title' => $age->ageInYears === null ? 'n/a' : $age->ageInYears.' - '.($age->ageInYears + 9),
                'value' => $age->counter,
            ])->values(),
            'maritalStatuses' => collect($benefitersMaritalStatuses)->map(fn ($status) => [
                'title' => $status->marital_status_title,
                'value' => $status->marital_counter,
            ])->values(),
            'legalStatuses' => collect($benefiters_legal_statuses)->map(fn ($status) => [
                'title' => $status->description,
                'value' => $status->legal_counter,
            ])->values(),
            'visitLocations' => collect($medical_visits_location)->map(fn ($visit) => [
                'title' => $visit->location,
                'value' => $visit->counter,
            ])->values(),
            'workTitles' => collect($benefiters_work_title)->map(fn ($count, $title) => [
                'title' => (string) $title !== '' ? (string) $title : '-',
                'value' => (string) $count,
            ])->values(),
            'visitsPerBenefiter' => collect($benefiters_medical_visits)->map(fn ($visits) => [
                'title' => (string) $visits->visits_counter,
                'value' => $visits->benefiters_counter,
            ])->values(),
        ];
    @endphp
    {{-- The texts and the data of the charts. --}}
    <script type="application/json" id="reports-data">@json($reportData)</script>
    <script src="{{ asset('js/amcharts/amcharts.js') }}"></script>
    <script src="{{ asset('js/amcharts/pie.js') }}"></script>
    <script src="{{ asset('js/amcharts/serial.js') }}"></script>
    <script src="{{ asset('js/amcharts/themes/light.js') }}"></script>
    <script src="{{ asset('js/amcharts/amstock.js') }}"></script>
    <script src="{{ asset('js/reports/reports.js') }}"></script>
@stop
