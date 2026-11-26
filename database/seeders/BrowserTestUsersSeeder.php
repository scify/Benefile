<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * One active user of each role for the browser tests (tests/e2e), with the password of E2E_PASSWORD.
 *
 * Only for local development and tests: the browser tests run it before they start.
 */
class BrowserTestUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new RuntimeException('BrowserTestUsersSeeder runs only on local and testing environments.');
        }
        $password = (string) getenv('E2E_PASSWORD');
        if (strlen($password) < 12) {
            throw new RuntimeException('Set E2E_PASSWORD (12 characters or more).');
        }

        $roles = [
            'admin' => User::ROLE_ADMIN,
            'doctor' => User::ROLE_DOCTOR,
            'lawyer' => User::ROLE_LAWYER,
            'social' => User::ROLE_SOCIAL_WORKER,
            'psychologist' => User::ROLE_PSYCHOLOGIST,
        ];
        foreach ($roles as $name => $role) {
            $user = User::firstOrNew(['email' => "e2e-$name@example.test"]);
            $user->fill(['name' => 'E2E', 'lastname' => ucfirst($name), 'password' => $password]);
            $user->user_role_id = $role;
            $user->user_subrole_id = $role === User::ROLE_DOCTOR ? 1 : null;
            $user->activation_status = 1;
            $user->is_deactivated = 0;
            $user->save();
        }
    }
}
