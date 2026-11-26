<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/*
 * The first administrator of a new installation. New users register on the
 * site, and an administrator activates them: someone has to be the first.
 */
Artisan::command('benefile:create-admin', function () {
    $name = text('First name', required: true);
    $lastname = text('Last name', required: true);
    $email = text('Email', required: true, validate: fn (string $value) => Validator::make(['email' => $value], ['email' => ['email', 'unique:users,email']])->errors()->first('email') ?: null);
    $secret = password('Password (at least 12 characters)', required: true, validate: fn (string $value) => mb_strlen($value) < 12 ? 'The password must have at least 12 characters.' : null);

    $user = new User(['name' => $name, 'lastname' => $lastname, 'email' => $email, 'password' => $secret]);
    $user->user_role_id = User::ROLE_ADMIN;
    $user->user_subrole_id = null;
    $user->activation_status = 1;
    $user->is_deactivated = 0;
    $user->save();

    $this->info("Administrator {$email} created.");
})->purpose('Create an administrator of BeneFile');
