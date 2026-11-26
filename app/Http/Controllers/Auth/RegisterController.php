<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Registration of a new user. The account waits for an administrator to activate it.
 */
class RegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register')->with('subroles', DB::table('users_subroles')->get());
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            // A new user cannot register as an administrator.
            'user_role_id' => ['required', 'integer', Rule::in([User::ROLE_DOCTOR, User::ROLE_LAWYER, User::ROLE_SOCIAL_WORKER, User::ROLE_PSYCHOLOGIST])],
            'user_subrole_id' => [Rule::requiredIf((int) $request->input('user_role_id') === User::ROLE_DOCTOR), 'nullable', 'integer', 'exists:users_subroles,id'],
        ]);

        $user = new User([
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $user->user_role_id = (int) $data['user_role_id'];
        $user->user_subrole_id = (int) $data['user_role_id'] === User::ROLE_DOCTOR ? (int) $data['user_subrole_id'] : null;
        $user->activation_status = 0;
        $user->is_deactivated = 0;
        $user->save();

        return redirect('awaitingActivation');
    }
}
