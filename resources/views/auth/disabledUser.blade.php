<?php
    $p = 'auth/disabledUser.';
?>

@extends('layouts.login-register-layout')

@section('title')
    <title>@lang($p.'disabled_user')</title>
@stop

@section('log-content')
    <div id="reset">
        <div class="panel-body">
           <div class="reset-password-text margin-bottom-50">@lang($p.'disabled_title')</div>
           <div class="reset-password-text">
               <b>@lang($p.'disabled_text')</b>
           </div>

           <div class="white">
               <div class="bottomDiv" >
                  @if (config('benefile.organisation.contact_url'))@lang($p.'need_help')<a class="white" target="_blank" rel="noopener" href="{{ config('benefile.organisation.contact_url') }}"><b>@lang($p.'contact')</b></a>@endif
              </div>
          </div>
        </div>
    </div>
@stop
