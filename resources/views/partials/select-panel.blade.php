{{-- actions refering to records --}}

    <?php
        /* check which tab is selected */
        $basic_selected = '';
        $medical_selected = '';
        $legal_selected = '';
        $social_selected = '';
        if(isset($tab)){
            if($tab === "medical"){
                $medical_selected = 'selected';
            } else if($tab === "legal"){
                $legal_selected = 'selected';
            } else if($tab === "social"){
                $social_selected = 'selected';
            }
        } else {
            $basic_selected = 'selected';
        }
    ?>

    <div class="no-margin light-green-background width-100-percent" id="actions">
    {{--<div style="width: 100%; background-color: red;">--}}
        <div class="row width-100-percent">
            <div class="col-md-3 record-panel-title">
                <a class="white {{ $basic_selected }}" href="{{ url('/new-benefiter/basic-info') }}">ΒΑΣΙΚΑ ΣΤΟΙΧΕΙΑ</a>
            </div>

            <div class="col-md-3 record-panel-title">
                <a class="white {{ $medical_selected }}" href="{{ url('/new-benefiter/medical-folder') }}">ΙΑΤΡΙΚΟΣ ΦΑΚΕΛΟΣ</a>
            </div>

            <div class="col-md-3 record-panel-title">
                <a class="white {{ $legal_selected }}" href="">ΝΟΜΙΚΟΣ ΦΑΚΕΛΟΣ</a>
            </div>

            <div class="col-md-3 record-panel-title">
                <a class="white {{ $social_selected }}" href="{{ url('/new-benefiter/social-folder') }}">ΚΟΙΝΩΝΙΚΟΣ ΦΑΚΕΛΟΣ</a>
            </div>
        </div>
        {{-- The abone three options will be removed in order to be added dynamically from another view. --}}

    </div>