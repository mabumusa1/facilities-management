<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\UserInvitationMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserInvitationController extends Controller
{
    public function resend(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isInvitationPending(), 400);

        $user->forceFill([
            'invitation_token' => hash('sha256', $plainTextToken = Str::random(40)),
            'invitation_expires_at' => now()->addHours(72),
        ])->save();

        $setPasswordUrl = route('set-password.create', ['token' => $plainTextToken]);

        Mail::to($user)->queue(new UserInvitationMail($user, $setPasswordUrl));

        return back();
    }

    public function revoke(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isInvitationPending(), 400);

        $user->forceFill([
            'invitation_token' => null,
            'invitation_expires_at' => null,
        ])->save();

        return back();
    }
}
