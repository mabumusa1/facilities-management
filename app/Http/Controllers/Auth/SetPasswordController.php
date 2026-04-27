<?php

namespace App\Http\Controllers\Auth;

use App\Concerns\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class SetPasswordController extends Controller
{
    use PasswordValidationRules;

    public function create(Request $request, string $token): Response
    {
        $user = User::query()
            ->where('invitation_token', hash('sha256', $token))
            ->where('invitation_expires_at', '>', now())
            ->where('status', User::STATUS_INVITATION_PENDING)
            ->first();

        $tokenValid = $user !== null;

        return Inertia::render('auth/SetPassword', [
            'token' => $token,
            'email' => $user?->email ?? '',
            'tokenValid' => $tokenValid,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ]);

        $user = User::query()
            ->where('invitation_token', hash('sha256', $validated['token']))
            ->where('invitation_expires_at', '>', now())
            ->where('status', User::STATUS_INVITATION_PENDING)
            ->first();

        abort_unless($user !== null, 410);

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'status' => User::STATUS_ACTIVE,
            'invitation_token' => null,
            'invitation_expires_at' => null,
            'email_verified_at' => now(),
        ])->save();

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
