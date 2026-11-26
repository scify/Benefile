<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Users for the tests. By default an active doctor; use the states for other roles and statuses.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password-for-tests',
            'user_role_id' => User::ROLE_DOCTOR,
            'user_subrole_id' => 1,
            'activation_status' => 1,
            'is_deactivated' => 0,
        ];
    }

    public function role(int $role): static
    {
        return $this->state(fn () => ['user_role_id' => $role, 'user_subrole_id' => $role === User::ROLE_DOCTOR ? 1 : null]);
    }

    public function waitingForActivation(): static
    {
        return $this->state(fn () => ['activation_status' => 0, 'is_deactivated' => 0]);
    }

    public function deactivated(): static
    {
        return $this->state(fn () => ['activation_status' => 0, 'is_deactivated' => 1]);
    }
}
