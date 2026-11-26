<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * What the forms of the four folders save. The payloads are those that the
 * real forms send (field names, empty fields as '', dates as dd-mm-yyyy).
 */
class FolderWorkflowsTest extends FeatureTestCase
{
    /**
     * The fields of the basic information form of a new beneficiary.
     *
     * @return array<string, mixed>
     */
    private function basicInfo(array $changes = []): array
    {
        return array_merge([
            'folder_number' => 'NEW-1', 'lastname' => 'Νέος', 'name' => 'Ωφελούμενος', 'gender_id' => '2',
            'birth_date' => '01-02-1990', 'fathers_name' => '', 'mothers_name' => '', 'nationality_country' => '',
            'origin_country' => 'Συρία', 'ethnic_group' => '', 'arrival_date' => '', 'telephone' => '6900000000',
            'address' => '', 'marital_status' => '1', 'number_of_children' => '', 'relatives_residence' => '',
            'children_names' => '',
            'legal_status' => ['1'], 'legal_status_text' => array_fill(0, 8, ''), 'legal_status_exp_date' => array_merge(['31-12-2030'], array_fill(0, 7, '')),
            'education_status' => '1', 'education_specialization' => '', 'language' => '1', 'language_level' => '1',
            'working' => '1', 'working_title' => '', 'working_legally' => '1', 'country_abandon_reason' => '1',
            'travel_route' => '', 'travel_duration' => '', 'detention_date' => '', 'social_history' => '',
            'medical_location_id' => '1', 'updated_by_date' => '26-11-2026', 'updated_by_comments' => 'Πρώτη καταγραφή',
        ], $changes);
    }

    /**
     * The fields of a medical visit with one row in each section.
     *
     * @return array<string, mixed>
     */
    private function medicalVisit(array $changes = []): array
    {
        return array_merge([
            'benefiter_id' => '1', 'doctor_id' => '1',
            'examination_date' => '20-11-2026', 'medical_location_id' => '1', 'medical_incident_id' => '1',
            'chronic_conditions' => ['Άσθμα'],
            'height' => '170', 'weight' => '70', 'temperature' => '36.6',
            'blood_pressure_systolic' => '120', 'blood_pressure_diastolic' => '80', 'skull_perimeter' => '',
            'examResultLoukup' => [['1'], [''], [''], [''], [''], [''], [''], [''], ['']],
            'examResultDescription' => array_merge(['Παρατήρηση'], array_fill(0, 8, '')),
            'lab_results' => ['Αιμοσφαιρίνη 13'], 'diagnosis_results' => ['Ίωση'],
            'medication_new_name' => ['Depon'], 'medication_dosage' => ['500mg'], 'medication_duration' => ['5'],
            'supply_from_praksis_hidden' => ['1'],
            'hospitalization' => [''], 'hospitalization_date' => [''],
            'referrals' => ['Καρδιολόγος'], 'is_done_id' => ['0'],
            'upload_file_description' => [''], 'upload_file_title' => [null],
        ], $changes);
    }

    private function latestVisitId(): int
    {
        return (int) DB::table('medical_visits')->max('id');
    }

    public function test_a_new_beneficiary_gets_a_file_and_a_psychosocial_folder(): void
    {
        $user = User::factory()->role(User::ROLE_SOCIAL_WORKER)->create();

        $this->actingAs($user)->post('/benefiter/-1/basic-info', $this->basicInfo())->assertRedirect();

        $id = (int) DB::table('benefiters')->where('folder_number', 'NEW-1')->value('id');
        $this->assertGreaterThan(0, $id);
        $this->assertDatabaseHas('benefiters', ['id' => $id, 'lastname' => 'Νέος', 'birth_date' => '1990-02-01', 'gender_id' => 2]);
        $this->assertDatabaseHas('social_folder', ['benefiter_id' => $id]);
        $this->assertDatabaseHas('benefiters_legal_status', ['benefiter_id' => $id, 'legal_lookup_id' => 1]);
        $this->assertDatabaseHas('basic_folder_update_history', ['benefiter_id' => $id, 'user_id' => $user->id]);
    }

    public function test_the_basic_information_is_updated(): void
    {
        $user = User::factory()->role(User::ROLE_SOCIAL_WORKER)->create();

        $this->actingAs($user)->post('/benefiter/1/basic-info', $this->basicInfo(['folder_number' => 'KK1', 'lastname' => 'Αλλαγμένο']))->assertRedirect();

        $this->assertDatabaseHas('benefiters', ['id' => 1, 'lastname' => 'Αλλαγμένο', 'folder_number' => 'KK1']);
        $this->assertSame(1, DB::table('benefiters')->where('folder_number', 'KK1')->count());
    }

    public function test_a_doctor_saves_a_complete_medical_visit(): void
    {
        Storage::fake('local');
        $doctor = User::factory()->create();
        $payload = $this->medicalVisit(['upload_file_description' => ['Εξετάσεις'], 'upload_file_title' => [UploadedFile::fake()->create('lab.pdf', 20, 'application/pdf')]]);

        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', $payload)->assertSessionHasNoErrors();

        $visit = $this->latestVisitId();
        $this->assertDatabaseHas('medical_visits', ['id' => $visit, 'benefiter_id' => 1, 'medical_location_id' => 1]);
        $this->assertDatabaseHas('medical_examinations', ['medical_visit_id' => $visit, 'height' => 170]);
        $this->assertDatabaseHas('medical_examination_results', ['medical_visit_id' => $visit, 'icd10_id' => 1]);
        $this->assertDatabaseHas('medical_laboratory_results', ['medical_visit_id' => $visit, 'laboratory_results' => 'Αιμοσφαιρίνη 13']);
        $this->assertDatabaseHas('medical_diagnosis_results', ['medical_visit_id' => $visit]);
        $this->assertDatabaseHas('medical_medication', ['medical_visit_id' => $visit, 'dosage' => '500mg', 'supply_from_praksis' => 1]);
        $this->assertDatabaseHas('medical_referrals', ['medical_visit_id' => $visit]);
        $this->assertDatabaseHas('medical_uploads', ['medical_visit_id' => $visit, 'description' => 'Εξετάσεις']);
        $this->assertSame(1, DB::table('medical_chronic_conditions')->where('medical_visit_id', $visit)->count());
    }

    public function test_a_visit_is_saved_for_the_logged_in_doctor_and_the_beneficiary_of_the_address(): void
    {
        $doctor = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', $this->medicalVisit(['benefiter_id' => '2', 'doctor_id' => (string) $other->id]))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('medical_visits', ['id' => $this->latestVisitId(), 'benefiter_id' => 1, 'doctor_id' => $doctor->id]);
    }

    public function test_a_visit_is_saved_completely_or_not_at_all(): void
    {
        $doctor = User::factory()->create();
        $visitsBefore = DB::table('medical_visits')->count();

        // An ICD-10 code that does not exist: the examination results cannot be saved.
        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', $this->medicalVisit(['examResultLoukup' => [['999999'], [''], [''], [''], [''], [''], [''], [''], ['']]]));

        $this->assertSame($visitsBefore, DB::table('medical_visits')->count());
    }

    public function test_editing_a_visit_updates_adds_and_removes_its_rows(): void
    {
        $doctor = User::factory()->create();
        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', $this->medicalVisit(['lab_results' => ['A', 'B']]));
        $visit = $this->latestVisitId();

        $edit = fn (array $changes) => $this->actingAs($doctor)->post('/benefiter/1/editMedicalVisit', $this->medicalVisit(array_merge([
            'selected_medical_visit_id' => (string) $visit, 'medication_name_from_lookup' => [''],
        ], $changes)));

        $edit(['lab_results' => ['A2', 'B2', 'C2'], 'height' => '171'])->assertRedirect();
        $this->assertSame(['A2', 'B2', 'C2'], DB::table('medical_laboratory_results')->where('medical_visit_id', $visit)->orderBy('id')->pluck('laboratory_results')->all());
        $this->assertDatabaseHas('medical_examinations', ['medical_visit_id' => $visit, 'height' => 171]);

        $edit(['lab_results' => ['only']])->assertRedirect();
        $this->assertSame(['only'], DB::table('medical_laboratory_results')->where('medical_visit_id', $visit)->pluck('laboratory_results')->all());
    }

    public function test_editing_keeps_the_doctor_of_the_visit(): void
    {
        $doctor = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', $this->medicalVisit());
        $visit = $this->latestVisitId();

        $this->actingAs($other)->post('/benefiter/1/editMedicalVisit', $this->medicalVisit([
            'selected_medical_visit_id' => (string) $visit, 'medication_name_from_lookup' => [''], 'doctor_id' => (string) $other->id, 'benefiter_id' => '2',
        ]));

        $this->assertDatabaseHas('medical_visits', ['id' => $visit, 'doctor_id' => $doctor->id, 'benefiter_id' => 1]);
    }

    public function test_a_visit_of_another_beneficiary_cannot_be_edited_through_this_one(): void
    {
        $doctor = User::factory()->create();
        $this->actingAs($doctor)->post('/benefiter/2/medical-folder', $this->medicalVisit(['benefiter_id' => '2']));
        $visitOfTwo = $this->latestVisitId();

        $this->actingAs($doctor)->post('/benefiter/1/editMedicalVisit', $this->medicalVisit([
            'selected_medical_visit_id' => (string) $visitOfTwo, 'medication_name_from_lookup' => [''], 'height' => '199',
        ]))->assertNotFound();
        $this->assertDatabaseMissing('medical_examinations', ['medical_visit_id' => $visitOfTwo, 'height' => 199]);
    }

    public function test_a_lawyer_saves_the_legal_folder_and_a_session(): void
    {
        $lawyer = User::factory()->role(User::ROLE_LAWYER)->create();

        $this->actingAs($lawyer)->post('/benefiter/1/legal-folder', [
            'legal_folder_status' => '1', 'asylum_request_date' => '10-01-2026', 'procedure' => '1', 'request_status' => '1',
            'request_progress' => 'Σε εξέλιξη', 'action' => '1', 'result' => '1', 'penalty' => '1', 'penalty_text' => '',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('legal_folder', ['benefiter_id' => 1]);

        $this->actingAs($lawyer)->post('/benefiter/1/legal-session', [
            'legal_date' => '12-01-2026', 'legal_comments' => 'Συνάντηση', 'lawyer_action' => ['1'], 'medical_location_id' => '1',
        ]);
        $this->assertDatabaseHas('legal_sessions', ['legal_folder_id' => DB::table('legal_folder')->where('benefiter_id', 1)->value('id')]);
    }

    public function test_the_psychosocial_folder_and_its_sessions(): void
    {
        $social = User::factory()->role(User::ROLE_SOCIAL_WORKER)->create();
        $psychologist = User::factory()->role(User::ROLE_PSYCHOLOGIST)->create();
        $folder = (int) DB::table('social_folder')->where('benefiter_id', 1)->value('id');

        $this->actingAs($social)->post('/benefiter/1/social-folder', ['comments' => 'Κοινωνικό ιστορικό'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('social_folder', ['id' => $folder, 'comments' => 'Κοινωνικό ιστορικό']);

        $this->actingAs($psychologist)->post('/benefiter/1/session-save', [
            'session_date' => '26-11-2026', 'psychosocial_theme' => '1', 'medical_location_id' => '1', 'session_comments' => 'Σημείωση',
        ])->assertRedirect();
        $session = (int) DB::table('psychosocial_sessions')->where('social_folder_id', $folder)->where('session_comments', 'Σημείωση')->value('id');
        $this->assertGreaterThan(0, $session);
        $this->assertDatabaseHas('psychosocial_sessions', ['id' => $session, 'psychologist_id' => $psychologist->id]);

        $this->actingAs($psychologist)->post("/benefiter/1/session-edit/$session", [
            'session_date' => '27-11-2026', 'psychosocial_theme' => '2', 'medical_location_id' => '1', 'session_comments' => 'Αλλαγή',
        ])->assertRedirect();
        $this->assertDatabaseHas('psychosocial_sessions', ['id' => $session, 'session_comments' => 'Αλλαγή']);

        $this->actingAs($psychologist)->post("/benefiter/1/session-delete/$session")->assertRedirect();
        $this->assertSoftDeleted('psychosocial_sessions', ['id' => $session]);
    }

    public function test_the_csv_import_adds_the_beneficiaries_of_the_file(): void
    {
        $admin = User::factory()->role(User::ROLE_ADMIN)->create();
        // The example file of the repository, with new file numbers.
        $csv = str_replace(['ΚΚ1,', 'ΚΚ2,'], ['IMP1,', 'IMP2,'], (string) file_get_contents(base_path('docs/examples/import-example.csv')));
        $file = UploadedFile::fake()->createWithContent('import.csv', $csv);

        $this->actingAs($admin)->post('/new-benefiter/uploadCSV', ['file' => $file]);

        $this->assertDatabaseHas('benefiters', ['folder_number' => 'IMP1']);
        $this->assertDatabaseHas('benefiters', ['folder_number' => 'IMP2']);
        $imported = (int) DB::table('benefiters')->where('folder_number', 'IMP1')->value('id');
        $this->assertDatabaseHas('social_folder', ['benefiter_id' => $imported]);
    }

    public function test_the_history_export_lists_the_work_on_the_files(): void
    {
        $admin = User::factory()->role(User::ROLE_ADMIN)->create();

        $response = $this->actingAs($admin)->get('/download-history-csv');

        $response->assertOk();
        $this->assertGreaterThan(1, substr_count((string) $response->getContent(), "\n"));
    }

    public function test_the_history_names_the_right_person_when_user_ids_have_gaps(): void
    {
        $admin = User::factory()->role(User::ROLE_ADMIN)->create();
        $doctor = User::factory()->create(['name' => 'Ιπποκράτης', 'lastname' => 'Κώος']);
        // A gap in the user ids: a user with a lower id no longer exists.
        DB::table('users')->where('id', 2)->delete();
        DB::table('medical_visits')->insert([
            'benefiter_id' => 1, 'doctor_id' => $doctor->id, 'medical_location_id' => 1,
            'medical_incident_id' => 1, 'medical_visit_date' => '2026-11-20', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/download-history-csv');

        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Ιπποκράτης Κώος', (string) $response->getContent());
    }

    public function test_the_history_shows_session_notes_only_to_psychologists_and_administrators(): void
    {
        $folder = (int) DB::table('social_folder')->where('benefiter_id', 1)->value('id');
        DB::table('psychosocial_sessions')->insert([
            'social_folder_id' => $folder, 'psychologist_id' => 1, 'session_date' => '2026-11-20', 'psychosocial_theme_id' => 1,
            'medical_location_id' => 1, 'session_comments' => 'Εμπιστευτική σημείωση', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs(User::factory()->role(User::ROLE_LAWYER)->create())->get('/benefiter/1/basic-info')
            ->assertOk()->assertDontSee('Εμπιστευτική σημείωση');
        $this->actingAs(User::factory()->role(User::ROLE_PSYCHOLOGIST)->create())->get('/benefiter/1/basic-info')
            ->assertOk()->assertSee('Εμπιστευτική σημείωση');
    }

    public function test_a_saved_visit_shows_in_its_window_and_in_its_edit_form(): void
    {
        $doctor = User::factory()->create(['name' => 'Ασκληπιός', 'lastname' => 'Επιδαύριος']);
        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', $this->medicalVisit([
            'lab_results' => ['Εξέταση-ΧΥΖ'], 'medication_new_name' => ['Φάρμακο-ΑΒΓ'], 'referrals' => ['Παραπομπή-ΔΕΖ'], 'height' => '183',
        ]));
        $visit = $this->latestVisitId();

        $window = $this->actingAs($doctor)->get("/benefiter/1/getEachMedicalVisit?current_medical_visit=$visit");
        $window->assertOk()->assertSee('Ασκληπιός Επιδαύριος')->assertSee('Εξέταση-ΧΥΖ')->assertSee('Φάρμακο-ΑΒΓ')->assertSee('Παραπομπή-ΔΕΖ')->assertSee('183');

        $edit = $this->actingAs($doctor)->get("/benefiter/1/editMedicalVisit?medical_visit_id=$visit");
        $edit->assertOk()->assertSee('Εξέταση-ΧΥΖ')->assertSee('Φάρμακο-ΑΒΓ')->assertSee('Παραπομπή-ΔΕΖ')->assertSee('183');

        $this->actingAs($doctor)->get('/benefiter/2/editMedicalVisit?medical_visit_id='.$visit)->assertNotFound();
    }

    public function test_editing_a_visit_removes_the_rows_the_user_empties(): void
    {
        $doctor = User::factory()->create();
        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', $this->medicalVisit());
        $visit = $this->latestVisitId();

        $this->actingAs($doctor)->post('/benefiter/1/editMedicalVisit', $this->medicalVisit([
            'selected_medical_visit_id' => (string) $visit, 'medication_name_from_lookup' => [''],
            'lab_results' => [''], 'referrals' => [''], 'chronic_conditions' => [''],
            'medication_new_name' => ['Ibuprofen'], 'medication_dosage' => ['400mg'], 'medication_duration' => ['3'],
        ]))->assertRedirect();

        $this->assertSame(0, DB::table('medical_laboratory_results')->where('medical_visit_id', $visit)->count());
        $this->assertSame(0, DB::table('medical_referrals')->where('medical_visit_id', $visit)->count());
        $this->assertSame(0, DB::table('medical_chronic_conditions')->where('medical_visit_id', $visit)->count());
        $this->assertSame(['400mg'], DB::table('medical_medication')->where('medical_visit_id', $visit)->pluck('dosage')->all());
        $this->assertSame(1, DB::table('medical_examinations')->where('medical_visit_id', $visit)->count());
        $this->assertSame(1, DB::table('medical_examination_results')->where('medical_visit_id', $visit)->count());
    }

    public function test_the_legal_history_names_the_lawyer_the_place_and_the_actions(): void
    {
        $lawyer = User::factory()->role(User::ROLE_LAWYER)->create(['name' => 'Σόλων', 'lastname' => 'Αθηναίος']);
        DB::table('users')->where('id', 2)->delete();
        $this->actingAs($lawyer)->post('/benefiter/1/legal-folder', [
            'legal_folder_status' => '1', 'asylum_request_date' => '10-01-2026', 'procedure' => '1', 'request_status' => '1',
            'request_progress' => '', 'action' => '1', 'result' => '1', 'penalty' => '1', 'penalty_text' => '',
        ]);
        $this->actingAs($lawyer)->post('/benefiter/1/legal-session', [
            'legal_date' => '12-01-2026', 'legal_comments' => 'Νομική συνάντηση', 'lawyer_action' => ['2'], 'medical_location_id' => '2',
        ]);

        $page = $this->actingAs($lawyer)->get('/benefiter/1/legal-folder');

        $page->assertOk()->assertSee('Σόλων Αθηναίος')->assertSee('Νομική συνάντηση')
            ->assertSee((string) DB::table('lawyer_action_lookup')->where('id', 2)->value('description'))
            ->assertSee((string) DB::table('medical_location_lookup')->where('id', 2)->value('description'));
    }

    public function test_incidents_belong_to_the_beneficiary_of_the_address(): void
    {
        $user = User::factory()->role(User::ROLE_LAWYER)->create();

        $this->actingAs($user)->post('/benefiter/1/new-occurrence-save', [
            'occurrence_date' => '20-11-2026', 'occurrences_comments' => 'Συμβάν', 'benefiter_id' => '2',
        ])->assertRedirect();
        $incident = (int) DB::table('benefiter_occurrences')->where('description', 'Συμβάν')->value('id');
        $this->assertDatabaseHas('benefiter_occurrences', ['id' => $incident, 'benefiter_id' => 1, 'user_id' => $user->id]);

        // Through the address of another beneficiary, the incident does not change.
        $this->actingAs($user)->post("/benefiter/2/edit-occurrence/$incident", ['edited_occurrence_date' => '21-11-2026', 'edited_occurrences_comments' => 'Άλλο']);
        $this->actingAs($user)->post("/benefiter/2/delete-occurrence/$incident");
        $this->assertDatabaseHas('benefiter_occurrences', ['id' => $incident, 'description' => 'Συμβάν']);

        $this->actingAs($user)->post("/benefiter/1/edit-occurrence/$incident", ['edited_occurrence_date' => '21-11-2026', 'edited_occurrences_comments' => 'Αλλαγή']);
        $this->assertDatabaseHas('benefiter_occurrences', ['id' => $incident, 'description' => 'Αλλαγή']);
        $this->actingAs($user)->post("/benefiter/1/delete-occurrence/$incident");
        $this->assertDatabaseMissing('benefiter_occurrences', ['id' => $incident]);
    }

    public function test_referrals_belong_to_the_beneficiary_of_the_address(): void
    {
        $user = User::factory()->role(User::ROLE_SOCIAL_WORKER)->create();

        $this->actingAs($user)->post('/benefiter/1/basic-info/referrals', [
            'benefiter_id' => '2', 'basic_info_referrals_id' => ['1'], 'basic_info_referrals_text' => ['Παραπομπή-Χ'], 'basic_info_referrals_date' => ['20-11-2026'],
        ])->assertRedirect('benefiter/1/basic-info');
        $referral = (int) DB::table('benefiter_referrals')->where('description', 'Παραπομπή-Χ')->value('id');
        $this->assertDatabaseHas('benefiter_referrals', ['id' => $referral, 'benefiter_id' => 1, 'user_id' => $user->id]);

        $this->actingAs($user)->post("/benefiter/2/basic-info/referral-delete/$referral");
        $this->assertDatabaseHas('benefiter_referrals', ['id' => $referral]);
        $this->actingAs($user)->post("/benefiter/1/basic-info/referral-delete/$referral");
        $this->assertDatabaseMissing('benefiter_referrals', ['id' => $referral]);
    }

    public function test_a_visit_without_files_is_saved(): void
    {
        $payload = $this->medicalVisit();
        // A browser sends no upload_file_title when no file is chosen.
        unset($payload['upload_file_title'], $payload['upload_file_description']);

        $this->actingAs(User::factory()->create())->post('/benefiter/1/medical-folder', $payload)->assertSessionHasNoErrors();

        $this->assertDatabaseHas('medical_laboratory_results', ['medical_visit_id' => $this->latestVisitId(), 'laboratory_results' => 'Αιμοσφαιρίνη 13']);
    }

    public function test_the_medicine_and_icd10_lists_find_by_part_of_the_name(): void
    {
        DB::table('medical_medication_lookup')->insert([['description' => 'Depon 500'], ['description' => 'Panadol'], ['description' => '100% Depon']]);
        $doctor = User::factory()->create();

        $medicines = $this->actingAs($doctor)->getJson('/benefiter/getMedicationList?q=depon')->assertOk()->json();
        $this->assertEqualsCanonicalizing(['Depon 500', '100% Depon'], array_column($medicines, 'description'));
        // A % in the query is a character to find, not a wildcard.
        $percent = $this->actingAs($doctor)->getJson('/benefiter/getMedicationList?q=%25')->assertOk()->json();
        $this->assertSame(['100% Depon'], array_column($percent, 'description'));

        $codes = $this->actingAs($doctor)->getJson('/benefiter/getIC10List?q=A0')->assertOk()->json();
        $this->assertNotEmpty($codes);
        $this->assertLessThanOrEqual(50, count($codes));
    }

    public function test_a_visit_that_fails_validation_keeps_the_typed_rows(): void
    {
        $doctor = User::factory()->create();

        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', $this->medicalVisit(['height' => 'tall']))
            ->assertRedirect('benefiter/1/medical-folder')
            ->assertSessionHasErrors();

        $this->actingAs($doctor)->get('/benefiter/1/medical-folder')
            ->assertOk()
            ->assertSee(__('partials/forms/new_medical_visit_form.unsuccess_visit'))
            ->assertSee('Αιμοσφαιρίνη 13');
    }
}
