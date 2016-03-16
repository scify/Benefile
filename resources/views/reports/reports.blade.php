<?php
    $p = 'reports.';
?>

@extends('layouts.mainPanel')

@section('panel-title')
    @lang('layouts/mainPanel.reports')
@stop

@section('panel-headLinks')
    <link href="{{ asset('css/records/record_form.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/reports/reports.css') }}" rel="stylesheet" type="text/css">
@stop

@section('main-window-content')
    {{-- report for users numbers divided by their roles --}}
    <div class="users-report form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">@lang($p."users")</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="doctors" class="col-md-3">
                    <img class="make-inline width-85px" src="{{ asset('/img/benefile_3_doctors.jpg') }}" />
                    <div class="make-inline">
                        <p class="users-title">@lang($p."doctors")</p>
                        <p class="users-counter">{{ $users_roles_count['doctors'] }}</p>
                    </div>
                </div>
                <div id="legals" class="col-md-3">
                    <img class="make-inline width-85px" src="{{ asset('/img/benefile_3_legals.jpg') }}" />
                    <div class="make-inline">
                        <p class="users-title">@lang($p."legals")</p>
                        <p class="users-counter">{{ $users_roles_count['legals'] }}</p>
                    </div>
                </div>
                <div id="psychologists" class="col-md-3">
                    <img class="make-inline width-85px" src="{{ asset('/img/benefile_3_psychologists.jpg') }}" />
                    <div class="make-inline">
                        <p class="users-title">@lang($p."psychologists")</p>
                        <p class="users-counter">{{ $users_roles_count['psychologists'] }}</p>
                    </div>
                </div>
                <div id="socials" class="col-md-3">
                    <img class="make-inline width-85px" src="{{ asset('/img/benefile_3_socials.jpg') }}" />
                    <div class="make-inline">
                        <p class="users-title">@lang($p."socials")</p>
                        <p class="users-counter">{{ $users_roles_count['socials'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="benefiters-report form-section">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">@lang($p."benefiters")</h1>
        </div>

        {{-- REPORT: Benefiters VS gerder --}}
        <div id="benefiters_vs_gerder" class="row padding-30 row-eq-height display-table ">
            <div class="col-md-10 col-equal-height">
            {{-- the loop will from 0 to 100 with three if conditions (and a counter) that will change the color class according to the gender type --}}
               @for($i=0 ; $i<100 ; $i++)
                    <icon class="glyphicon glyphicon-user gender-icon"></icon>
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

        <div class="row">
            <div class="col-md-12">
                {{-- Benefiters marital statuses --}}
                <div class="col-md-6">
                    <canvas id="maritalStatusReport" height="400" width="400"></canvas>
                </div>
            </div>
        </div>
        {{-- Benefiters marital statuses end --}}
        <div class="row">
            <div class="col-md-12">
                <div id="benefiters-work-title" class="col-md-12">
                    <canvas id="benefiters-work-title-canvas" height="400" width="1000"></canvas>
                </div>
            </div>
        </div>


    </div>
@stop

@section('panel-scripts')
<script src="{{ asset('js/chart.min.js') }}"></script>
<script src="{{ asset('js/reports/reports.js') }}"></script>
    {{-- Marital status graph --}}
	<script>
		(function() {
			 var ctx = document.getElementById("maritalStatusReport").getContext("2d");
			 var chart = {
				labels: [ @foreach ($benefitersMaritalStatuses as $maritalStatus) {!! json_encode($maritalStatus->marital_status_title) !!}, @endforeach ],
				datasets: [
					{
					label: "My Data",                    
                    fillColor: "rgba(220,220,220,0.5)",
                    strokeColor: "rgba(220,220,220,0.8)",
                    highlightFill: "rgba(220,220,220,0.75)",
                    highlightStroke: "rgba(220,220,220,1)",
					data: [ @foreach ($benefitersMaritalStatuses as $maritalStatus) {!! json_encode($maritalStatus->marital_counter) !!}, @endforeach ],
					}
				]
			};
			var myLineChart = new Chart(ctx).Bar(chart);
            /*
			 * bezierCurve: false
			 * });
             */
		})();
	</script>
	<script>
	// TODO REMOVE!!! left for new pie charts reference ONLY!!!
        /*(function(){
            var $benefiters_work_title = $("#benefiters-work-title-canvas").get(0).getContext("2d");
            var $data = [
                @if(!empty($benefiters_work_title))
                    @foreach($benefiters_work_title as $key => $value)
                        {
                            value: {!! $value !!},
                            color: "#46BFBD",
                            highlight: "#46BFBD",
                            @if($key == "")
                            label: "-"
                            @else
                            label: "{!! $key !!}"
                            @endif
                        },
                    @endforeach
                @endif
            ];
            var $options = {segmentShowStroke: true};
            new Chart($benefiters_work_title).Pie($data, $options);
        })();*/
    </script>
    <script>
        (function(){
            var $benefiters_work_title = $("#benefiters-work-title-canvas").get(0).getContext("2d");
            var $data = {
                @if(!empty($benefiters_work_title))
                labels: [ @foreach($benefiters_work_title as $key => $value) @if($key != "") "{!! $key !!}", @else "-", @endif @endforeach ],
                datasets: [
                    {
                        label: "",
                        fillColor: "rgba(151,187,205,0.5)",
                        strokeColor: "rgba(151,187,205,0.8)",
                        highlightFill: "rgba(151,187,205,0.75)",
                        highlightStroke: "rgba(151,187,205,1)",
                        data: [ @foreach($benefiters_work_title as $key => $value) {!! $value !!}, @endforeach ]
                    }
                ]
                @else
                labels: [ "" ],
                datasets: [
                    {
                        label: "-",
                        fillColor: "rgba(151,187,205,0.5)",
                        strokeColor: "rgba(151,187,205,0.8)",
                        highlightFill: "rgba(151,187,205,0.75)",
                        highlightStroke: "rgba(151,187,205,1)",
                        data: [ 0 ]
                    }
                ]
                @endif
            };
            new Chart($benefiters_work_title).Bar($data, {});
        })();
    </script>
@stop
