<div class="basic-info-form">
{{--{!! Form::model($user, array('url' => 'PUT_STH_HERE!!!')) !!}--}}
{!! Form::open(array('url' => 'PUT_STH_HERE!!!')) !!}
    <div class="personal-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">1. Προσωπικά Στοιχεία</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('lastname', 'ΕΠΩΝΥΜΟ') !!}
                            {!! Form::text('lastname', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('name', 'ΟΝΟΜΑ') !!}
                            {!! Form::text('name', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('gender', 'ΦΥΛΟ') !!}
                            <div class="make-inline">
                                {!! Form::radio('gender', 1, true, array('class' => 'make-inline')) !!}
                                {!! Form::label('gender', 'Άνδρας', array('class' => 'radio-value')) !!}
                                {!! Form::radio('gender', 2, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('gender', 'Γυναίκα', array('class' => 'radio-value')) !!}
                            </div>
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('ΗΜΕΡ. ΓΕΝΝΗΣΗΣ') !!}
                            <div class="make-inline">
                                {!! Form::text('birth_day', null, array('class' => 'custom-input-text date-text-input')) !!}<span class="bold-slash">/</span>
                                {!! Form::text('birth_month', null, array('class' => 'custom-input-text date-text-input')) !!}<span class="bold-slash">/</span>
                                {!! Form::text('birth_year', null, array('class' => 'custom-input-text date-text-input')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('fathers_name', 'ΠΑΤΡΩΝΥΜΟ') !!}
                            {!! Form::text('fathers_name', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('mothers_name', 'ΜΗΤΡΩΝΥΜΟ') !!}
                            {!! Form::text('mothers_name', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('nationality_country', 'ΧΩΡΑ ΕΘΝΙΚΟΤΗΤΑΣ') !!}
                            {!! Form::text('nationality_country', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('origin_country', 'ΧΩΡΑ ΚΑΤΑΓΩΓΗΣ') !!}
                            {!! Form::text('origin_country', null, array('class' => 'custom-input-text')) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('arrival_date', 'ΗΜΕΡ. ΑΦΙΞΗΣ') !!}
                            {!! Form::text('arrival_date', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('telephone', 'ΤΗΛ. ΕΠΙΚΟΙΝΩΝΙΑΣ') !!}
                            {!! Form::text('telephone', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-6">
                            {!! Form::label('address', 'Δ/ΝΣΗ ΚΑΤΟΙΚΙΑΣ') !!}
                            {!! Form::text('address', null, array('class' => 'custom-input-text address')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="family-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">2. Οικογενειακή Κατάσταση</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group float-left width-100-percent">
                            <div class="col-md-2 make-inline">
                                {!! Form::radio('marital_status', 1, true, array('class' => 'make-inline')) !!}
                                {!! Form::label('marital_status', 'Άγαμος', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-2 make-inline">
                                {!! Form::radio('marital_status', 2, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('marital_status', 'Έγγαμος', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-2 make-inline">
                                {!! Form::radio('marital_status', 3, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('marital_status', 'Διαζευμένος/η', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-2 make-inline">
                                {!! Form::radio('marital_status', 4, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('marital_status', 'Χήρος/α', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-2 make-inline">
                                {!! Form::radio('marital_status', 5, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('marital_status', 'Εν διαστάσει', array('class' => 'radio-value')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-2">
                            {!! Form::label('number_of_children', 'ΑΡΙΘΜΟΣ ΠΑΙΔΙΩΝ') !!}
                            {!! Form::text('number_of_children', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group make-inline padding-left-right-15 margin-right-30 float-left col-md-6">
                            {!! Form::label('relatives_residence', 'ΤΟΠΟΣ ΔΙΑΜΟΝΗΣ ΣΥΓΓΕΝΙΚΩΝ ΠΡΟΣΩΠΩΝ') !!}
                            {!! Form::text('relatives_residence', null, array('class' => 'custom-input-text address')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="legal-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">3. Νομικό Καθεστώς</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group make-inline padding-left-right-15 float-left width-100-percent">
                            {!! Form::checkbox('legal_status', 1, false, array('class' => 'float-left')) !!}
                            {!! Form::label('deportation', '∆ιοικητική απόφαση απέλασης', array('class' => 'float-left')) !!}
                            {!! Form::text('deportation', null, array('class' => 'custom-input-text make-inline float-left')) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group make-inline padding-left-right-15 float-left">
                            {!! Form::label('Ημ. Λήξης') !!}
                            <div class="make-inline">
                                {!! Form::text('deportation_day', null, array('class' => 'custom-input-text date-text-input')) !!}<span class="bold-slash">/</span>
                                {!! Form::text('deportation_month', null, array('class' => 'custom-input-text date-text-input')) !!}<span class="bold-slash">/</span>
                                {!! Form::text('deportation_year', null, array('class' => 'custom-input-text date-text-input')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group make-inline padding-left-right-15 float-left width-100-percent">
                            {!! Form::checkbox('legal_status', 2, false, array('class' => 'float-left')) !!}
                            {!! Form::label('asylum_application', 'Αρ. δελτίου αιτήσαντος ασύλου', array('class' => 'float-left')) !!}
                            {!! Form::text('asylum_application', null, array('class' => 'custom-input-text float-left')) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group make-inline padding-left-right-15 float-left">
                            {!! Form::label('Ημ. Λήξης') !!}
                            <div class="make-inline">
                                {!! Form::text('asylum_day', null, array('class' => 'custom-input-text date-text-input')) !!}<span class="bold-slash">/</span>
                                {!! Form::text('asylum_month', null, array('class' => 'custom-input-text date-text-input')) !!}<span class="bold-slash">/</span>
                                {!! Form::text('asylum_year', null, array('class' => 'custom-input-text date-text-input')) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group make-inline padding-left-right-15 float-left width-100-percent">
                            {!! Form::checkbox('legal_status', 3, false, array('class' => 'float-left')) !!}
                            {!! Form::label('refugee', 'Αρ. δελτίου πρόσφυγα', array('class' => 'float-left')) !!}
                            {!! Form::text('refugee', null, array('class' => 'custom-input-text float-left')) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group make-inline padding-left-right-15 float-left">
                            {!! Form::label('Ημ. Λήξης') !!}
                            <div class="make-inline">
                                {!! Form::text('refugee_day', null, array('class' => 'custom-input-text date-text-input')) !!}<span class="bold-slash">/</span>
                                {!! Form::text('refugee_month', null, array('class' => 'custom-input-text date-text-input')) !!}<span class="bold-slash">/</span>
                                {!! Form::text('refugee_year', null, array('class' => 'custom-input-text date-text-input')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="education-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">4. Εκπαίδευση</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group float-left width-100-percent">
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 1, true, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'Αναλφάβητος', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 2, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'Δημοτικό', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 3, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'Γυμνάσιο', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 4, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'Λύκειο', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 5, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'Επαγγελματικό Λύκειο', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 6, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'ΤΕΙ', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 7, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'ΑΕΙ', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 8, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'Μεταπτυχιακός τίτλος', array('class' => 'radio-value')) !!}
                            </div>
                            <div class="col-md-3 make-inline">
                                {!! Form::radio('education_status', 9, false, array('class' => 'make-inline')) !!}
                                {!! Form::label('education_status', 'Διδακτορικός τίτλος', array('class' => 'radio-value')) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="languages-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">5. Γλώσσες Eπικοινωνίας</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15 language-div">
                        <div class="form-group float-left width-100-percent">
                            <div class="col-md-3 make-inline">
                                {!! Form::select('language', array('1' => 'English', '2' => 'Greek'), null, array('class' => 'language-selection')) !!}
                            </div>
                            <div class="col-md-2 make-inline">
                                {!! Form::select('language_level', array('1' => 'χαμηλό', '2' => 'μέτριο', '3' => 'καλό', '4' => 'πολύ καλό', '5' => 'άριστο'), null, array('class' => 'make-inline level-selection')) !!}
                                <a class="color-green" href="javascript:void(0)"><span class="glyphicon glyphicon-plus-sign make-inline"></span></a>
                                <a class="color-red hide-element" href="javascript:void(0)"><span class="glyphicon glyphicon-minus-sign make-inline"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group float-left padding-left-right-15 width-100-percent">
                            {!! Form::checkbox('interpreter', true, false, array('class' => 'float-left')) !!}
                            {!! Form::label('interpreter', 'Χρήση διερμηνέα', array('class' => 'float-left')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="work-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">6. Εργασία</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group float-left col-md-2">
                            {!! Form::radio('working', 1, true, array('id' => 'show_work_legally')) !!}
                            {!! Form::label('working', 'Ναι', array('class' => 'radio-value')) !!}
                        </div>
                        <div class="form-group float-left col-md-2">
                            {!! Form::radio('working', 2, false, array('id' => 'hide_work_legally')) !!}
                            {!! Form::label('working', 'Όχι', array('class' => 'radio-value')) !!}
                        </div>
                    </div>
                </div>
                <div id="working_legally_div" class="row">
                    <div class="padding-left-right-15">
                        <span class="float-left padding-left-right-15">Εργάζεται: </span>
                        <div class="form-group float-left col-md-2">
                            {!! Form::radio('working_legally', 1, true) !!}
                            {!! Form::label('working_legally', 'Νόμιμα', array('class' => 'radio-value')) !!}
                        </div>
                        <div class="form-group float-left col-md-2">
                            {!! Form::radio('working_legally', 2, false) !!}
                            {!! Form::label('working_legally', 'Παράνομα', array('class' => 'radio-value')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="country-abandon-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">7. Λόγος εγκατάλειψης χώρας</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group padding-left-right-15">
                            {!! Form::text('country_abandon', null, array('class' => 'custom-input-text width-100-percent')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="travel-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">8. Ταξίδι</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group padding-left-right-15">
                            {!! Form::label('travel_route', 'Διαδρομή') !!}
                            {!! Form::text('travel_route', null, array('class' => 'custom-input-text')) !!}
                        </div>
                        <div class="form-group padding-left-right-15">
                            {!! Form::label('travel_duration', 'Διάρκεια') !!}
                            {!! Form::text('travel_duration', null, array('class' => 'custom-input-text')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="detention-info form-section no-bottom-border">
        <div class="underline-header">
            <h1 class="record-section-header padding-left-right-15">9. Διάρκεια Κράτησης</h1>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="padding-left-right-15">
                        <div class="form-group padding-left-right-15">
                            {!! Form::text('detention', null, array('class' => 'custom-input-text width-100-percent')) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-section align-text-center">
        {!! Form::submit('Αποθήκευση Βασικών Στοιχείων', array('class' => 'submit-button')) !!}
    </div>
{!! Form::close() !!}
</div>