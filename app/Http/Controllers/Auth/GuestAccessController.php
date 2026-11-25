<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class GuestAccessController extends Controller
{
    public function loginAsGuest(): RedirectResponse
    {
        $evaluator = User::where('email', 'evaluator@zums.edu')->first()
            ?? User::first();

        if ($evaluator) {
            Auth::login($evaluator);
        }

        $demoWorkspace = Workspace::where('is_public_demo', true)->first()
            ?? Workspace::first();

        if ($demoWorkspace) {
            return redirect()
                ->route('workspaces.show', $demoWorkspace)
                ->with('success', 'Signed in via 1-Click Evaluator Demo Access.');
        }

        return redirect()->route('workspaces.index');
    }
}
