<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Who may open and change what.
 */
class AccessControlTest extends FeatureTestCase
{
    public function test_guests_go_to_the_login_page(): void
    {
        foreach (['/benefiters-list', '/benefiter/1/medical-folder', '/search', '/reports', '/main-panel/users-list'] as $path) {
            $this->get($path)->assertRedirect('/auth/login');
        }
    }

    public function test_a_user_waiting_for_activation_has_no_access(): void
    {
        $user = User::factory()->waitingForActivation()->create();

        $this->actingAs($user)->get('/benefiters-list')->assertRedirect('awaitingActivation');
    }

    public function test_a_deactivated_user_has_no_access(): void
    {
        $user = User::factory()->deactivated()->create();

        $this->actingAs($user)->get('/benefiters-list')->assertRedirect('disabledUser');
        $this->actingAs($user)->post('/benefiter/1/basic-info/referrals')->assertRedirect('disabledUser');
    }

    public function test_every_active_user_can_read_every_folder(): void
    {
        $lawyer = User::factory()->role(User::ROLE_LAWYER)->create();

        foreach (['basic-info', 'medical-folder', 'legal-folder', 'social-folder'] as $folder) {
            $this->actingAs($lawyer)->get("/benefiter/1/$folder")->assertOk();
        }
    }

    /**
     * @return array<string, array{0: int, 1: string, 2: bool}>
     */
    public static function folderChanges(): array
    {
        return [
            'lawyer, medical folder' => [User::ROLE_LAWYER, '/benefiter/1/medical-folder', false],
            'psychologist, medical visit' => [User::ROLE_PSYCHOLOGIST, '/benefiter/1/editMedicalVisit', false],
            'doctor, legal folder' => [User::ROLE_DOCTOR, '/benefiter/1/legal-folder', false],
            'social worker, legal session' => [User::ROLE_SOCIAL_WORKER, '/benefiter/1/legal-session', false],
            'doctor, psychosocial session' => [User::ROLE_DOCTOR, '/benefiter/1/session-save', false],
            'lawyer, social folder' => [User::ROLE_LAWYER, '/benefiter/1/social-folder', false],
            'social worker, psychological session' => [User::ROLE_SOCIAL_WORKER, '/benefiter/1/session-save', false],
            'psychologist, social history' => [User::ROLE_PSYCHOLOGIST, '/benefiter/1/social-folder', false],
        ];
    }

    #[DataProvider('folderChanges')]
    public function test_only_the_profession_of_a_folder_can_change_it(int $role, string $path, bool $allowed): void
    {
        $user = User::factory()->role($role)->create();

        $response = $this->actingAs($user)->post($path, []);

        $allowed ? $this->assertNotSame(403, $response->status()) : $response->assertForbidden();
    }

    public function test_only_administrators_manage_users_imports_and_exports(): void
    {
        $doctor = User::factory()->create();

        foreach (['/main-panel/users-list', '/new-location', '/new-benefiter/uploadCSV', '/download-benefiters-csv', '/download-history-csv'] as $path) {
            $this->actingAs($doctor)->get($path)->assertForbidden();
        }
        $this->actingAs($doctor)->post('/main-panel/users-list', ['user_id' => $doctor->id])->assertForbidden();
    }

    public function test_only_administrators_delete_a_beneficiary_and_only_with_post(): void
    {
        $doctor = User::factory()->create();
        $admin = User::factory()->role(User::ROLE_ADMIN)->create();

        $this->actingAs($doctor)->post('/benefiter/1/delete')->assertForbidden();
        $this->actingAs($admin)->get('/benefiter/1/delete')->assertMethodNotAllowed();
        $this->assertDatabaseHas('benefiters', ['id' => 1, 'deleted_at' => null]);

        $this->actingAs($admin)->post('/benefiter/1/delete');
        $this->assertSoftDeleted('benefiters', ['id' => 1]);
    }
}
