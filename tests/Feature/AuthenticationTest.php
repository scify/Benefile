<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;

/**
 * Login, registration and the first administrator.
 */
class AuthenticationTest extends FeatureTestCase
{
    public function test_a_user_logs_in_and_out(): void
    {
        $user = User::factory()->create(['email' => 'doctor@example.test']);

        $this->post('/auth/login', ['email' => 'doctor@example.test', 'password' => 'password-for-tests'])
            ->assertRedirect('benefiters-list');
        $this->assertAuthenticatedAs($user);

        $this->post('/auth/logout')->assertRedirect('auth/login');
        $this->assertGuest();
    }

    public function test_logout_is_not_possible_with_a_link(): void
    {
        $this->actingAs(User::factory()->create())->get('/auth/logout')->assertMethodNotAllowed();
    }

    public function test_logins_are_throttled_after_five_failures(): void
    {
        User::factory()->create(['email' => 'doctor@example.test']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/auth/login', ['email' => 'doctor@example.test', 'password' => 'wrong']);
        }
        $this->post('/auth/login', ['email' => 'doctor@example.test', 'password' => 'password-for-tests'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_a_new_user_waits_for_activation(): void
    {
        $this->post('/auth/register', [
            'name' => 'Νίκη',
            'lastname' => 'Παππά',
            'email' => 'new@example.test',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
            'user_role_id' => User::ROLE_LAWYER,
        ])->assertRedirect('awaitingActivation');

        $user = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertSame(User::ROLE_LAWYER, $user->user_role_id);
        $this->assertSame(0, $user->activation_status);
        $this->assertFalse($user->isActive());
    }

    public function test_nobody_registers_as_an_administrator(): void
    {
        $this->post('/auth/register', [
            'name' => 'Mallory',
            'lastname' => 'Admin',
            'email' => 'mallory@example.test',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
            'user_role_id' => User::ROLE_ADMIN,
            'activation_status' => 1,
        ])->assertSessionHasErrors('user_role_id');

        $this->assertDatabaseMissing('users', ['email' => 'mallory@example.test']);
    }

    public function test_the_registration_form_cannot_set_the_status(): void
    {
        $this->post('/auth/register', [
            'name' => 'Mallory',
            'lastname' => 'Active',
            'email' => 'active@example.test',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
            'user_role_id' => User::ROLE_PSYCHOLOGIST,
            'activation_status' => 1,
        ]);

        $this->assertFalse(User::where('email', 'active@example.test')->firstOrFail()->isActive());
    }

    public function test_a_user_resets_a_forgotten_password(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'doctor@example.test']);

        $this->post('/password/email', ['email' => 'doctor@example.test'])->assertSessionHas('status');
        // The same answer for an address without an account.
        $this->post('/password/email', ['email' => 'nobody@example.test'])->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->post('/password/reset', [
            'token' => $token,
            'email' => 'doctor@example.test',
            'password' => 'a-new-long-password',
            'password_confirmation' => 'a-new-long-password',
        ])->assertRedirect('auth/login');

        $this->post('/auth/login', ['email' => 'doctor@example.test', 'password' => 'a-new-long-password'])
            ->assertRedirect('benefiters-list');
    }

    public function test_the_command_creates_an_active_administrator(): void
    {
        $this->artisan('benefile:create-admin')
            ->expectsQuestion('First name', 'Άννα')
            ->expectsQuestion('Last name', 'Διαχειρίστρια')
            ->expectsQuestion('Email', 'admin@example.test')
            ->expectsQuestion('Password (at least 12 characters)', 'a-very-long-password')
            ->assertSuccessful();

        $admin = User::where('email', 'admin@example.test')->firstOrFail();
        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->isActive());
    }

    public function test_seeding_a_real_installation_adds_no_users(): void
    {
        $this->app['env'] = 'production';
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

        $this->assertSame(0, User::count());
        $this->assertDatabaseCount('benefiters', 0);
        $this->assertDatabaseHas('users_roles', ['id' => User::ROLE_ADMIN]);
    }
}
