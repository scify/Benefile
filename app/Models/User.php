<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * A user of BeneFile: an administrator, a doctor, a lawyer, a social worker or a psychologist.
 *
 * @property int $id
 * @property string $name
 * @property string $lastname
 * @property string $email
 * @property int $user_role_id
 * @property int|null $user_subrole_id
 * @property int $activation_status 1 when an administrator has activated the account.
 * @property int $is_deactivated 1 when an administrator has deactivated the account.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 1;
    public const ROLE_DOCTOR = 2;
    public const ROLE_LAWYER = 3;
    public const ROLE_SOCIAL_WORKER = 4;
    public const ROLE_PSYCHOLOGIST = 5;

    protected $table = 'users';

    /**
     * The role and the status are not here: only an administrator changes them.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'lastname', 'email', 'password'];

    /** @var list<string> */
    protected $hidden = ['password', 'remember_token'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'user_role_id' => 'integer',
            'user_subrole_id' => 'integer',
            'activation_status' => 'integer',
            'is_deactivated' => 'integer',
        ];
    }

    /** @return HasOne<Users_roles, $this> */
    public function role(): HasOne
    {
        return $this->hasOne(Users_roles::class, 'id', 'user_role_id');
    }

    /** @return HasOne<Users_subroles, $this> */
    public function subrole(): HasOne
    {
        return $this->hasOne(Users_subroles::class, 'id', 'user_subrole_id');
    }

    public function isAdmin(): bool
    {
        return $this->user_role_id === self::ROLE_ADMIN;
    }

    /**
     * Whether the user has one of these roles. Administrators have every role.
     */
    public function hasRole(int ...$roles): bool
    {
        return $this->isAdmin() || in_array($this->user_role_id, $roles, true);
    }

    /**
     * Whether the user may work in BeneFile: activated, and not deactivated.
     */
    public function isActive(): bool
    {
        return $this->activation_status === 1 && $this->is_deactivated === 0;
    }
}
