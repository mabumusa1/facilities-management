<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InviteRegisterController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('auth/RegisterInvite', [
            'code' => $request->query('code', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // The actual registration is handled by Fortify's CreateNewUserForInvite action.
        // This controller method is a fallback; Fortify intercepts POST /register.
        return redirect()->route('register', ['code' => $request->input('code')]);
    }
}
