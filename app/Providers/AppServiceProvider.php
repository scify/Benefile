<?php

namespace App\Providers;

use App\Models\User;
use App\Support\Form;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * What each role may do. The routes (->can()) and the views (@can) use these names:
     * this is the only place with the rules. Administrators may do everything, and
     * every active user may read every folder and change the basic information.
     */
    public const ABILITIES = [
        'edit-basic-info' => [User::ROLE_SOCIAL_WORKER],
        'edit-medical-folder' => [User::ROLE_DOCTOR],
        'edit-legal-folder' => [User::ROLE_LAWYER],
        'edit-social-history' => [User::ROLE_SOCIAL_WORKER],
        'edit-psychosocial-sessions' => [User::ROLE_PSYCHOLOGIST],
        'see-session-notes' => [User::ROLE_PSYCHOLOGIST],
        'administer' => [],
    ];

    public function register(): void
    {
        // The views call Form::text() and others, as with laravelcollective/html.
        AliasLoader::getInstance()->alias('Form', Form::class);
    }

    public function boot(): void
    {
        foreach (self::ABILITIES as $ability => $roles) {
            Gate::define($ability, fn (User $user): bool => $user->isActive() && $user->hasRole(...$roles));
        }
    }
}
