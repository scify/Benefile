<?php

namespace Tests\Feature;

use App\Models\MedicalUpload;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * SQL injection, the medical files and the CSV exports.
 */
class DataSecurityTest extends FeatureTestCase
{
    public function test_the_search_finds_beneficiaries_by_name(): void
    {
        $response = $this->actingAs(User::factory()->create())->get('/results?fname=Ben-name-1');

        $response->assertOk()->assertSee('Ben-lastname-1');
    }

    public function test_the_search_sends_only_the_columns_of_the_results_table(): void
    {
        $reason = DB::table('country_abandon_reasons_lookup')->value('id');
        $this->assertNotNull($reason);
        DB::table('benefiters')->where('id', 1)->update(['children_names' => 'Not for the search', 'country_abandon_reason_id' => $reason]);

        $results = $this->actingAs(User::factory()->create())->getJson('/results?fname=Ben-name-1')->assertOk()->json();

        $this->assertArrayNotHasKey('children_names', $results[0]);
        $this->assertArrayNotHasKey('document_manager_id', $results[0]);
        $this->assertSame(DB::table('country_abandon_reasons_lookup')->where('id', $reason)->value('description'), $results[0]['country_abandon_reason']);
    }

    public function test_the_search_is_safe_from_sql_injection(): void
    {
        $user = User::factory()->create();
        $injections = [
            ['lastname' => "' OR '1'='1"],
            ['lastname' => "x%' UNION SELECT password FROM users -- "],
            ['medical_location_id' => '999 OR 1=1'],
            ['doctor_name' => '" OR "1"="1'],
            ['telephone' => "1' OR '1'='1"],
        ];

        foreach ($injections as $query) {
            $this->actingAs($user)->get('/results?'.http_build_query($query))->assertOk()->assertDontSee('Ben-lastname-1');
        }
        $this->assertDatabaseCount('benefiters', 6);
    }

    public function test_the_duplicate_check_is_safe_from_sql_injection(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->post('/check-for-possible-duplicates', ['name' => "' OR '1'='1"]);

        $response->assertOk();
        $this->assertStringNotContainsString('Ben-lastname-1', $response->getContent());
    }

    public function test_a_doctor_cannot_upload_a_script(): void
    {
        $doctor = User::factory()->create();

        $this->actingAs($doctor)->post('/benefiter/1/medical-folder', [
            'upload_file_title' => [UploadedFile::fake()->create('shell.php', 1, 'application/x-php')],
        ])->assertSessionHasErrors('upload_file_title.0');
    }

    public function test_medical_files_are_private_and_only_for_active_users(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('medical-uploads/abc.pdf', 'PDF content');
        $upload = new MedicalUpload();
        $upload->title = 'medical_visit-1-lab-results.pdf';
        $upload->description = 'Lab results';
        $upload->path = 'medical-uploads/abc.pdf';
        $upload->medical_visit_id = (int) DB::table('medical_visits')->value('id');
        $upload->save();

        $this->get('/medical-uploads/'.$upload->id)->assertRedirect('/auth/login');
        $this->actingAs(User::factory()->deactivated()->create())->get('/medical-uploads/'.$upload->id)->assertRedirect('disabledUser');

        $response = $this->actingAs(User::factory()->create())->get('/medical-uploads/'.$upload->id);
        $response->assertOk()->assertDownload('medical_visit-1-lab-results.pdf');
        $this->assertFileDoesNotExist(public_path('uploads/medical-visit-uploads/medical_visit-1-lab-results.pdf'));
    }

    public function test_the_csv_export_does_not_run_formulas(): void
    {
        DB::table('benefiters')->where('id', 1)->update(['name' => '=HYPERLINK("http://example.test","x")', 'lastname' => 'Say "hello"']);

        $response = $this->actingAs(User::factory()->role(User::ROLE_ADMIN)->create())->get('/download-benefiters-csv');

        $response->assertOk();
        $csv = (string) $response->getContent();
        $this->assertStringContainsString("\"'=HYPERLINK(\"\"http://example.test\"\",\"\"x\"\")\"", $csv);
        $this->assertStringContainsString('"Say ""hello"""', $csv);
    }

    public function test_a_work_title_cannot_run_a_script_in_the_reports(): void
    {
        // Every user can add a work title: the basic information form adds what it does not find.
        $titleId = DB::table('work_title_list_lookup')->insertGetId(['work_title' => '</script><script>alert(1)</script>']);
        DB::table('benefiters')->where('id', 1)->update(['work_title_id' => $titleId]);

        $response = $this->actingAs(User::factory()->create())->get('/reports');

        $response->assertOk();
        $this->assertStringNotContainsString('<script>alert(1)</script>', (string) $response->getContent());
    }
}
