<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect($this->redirectBasedOnRole($request->user()->role));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect each role to their own home screen after login
     */
    private function redirectBasedOnRole(string $role): string
    {
        return match ($role) {
            'admin'     => route('admin.dashboard'),
            'organizer' => route('organizer.tournaments.index'),
            'coach'     => route('coach.team.show'),
            'player'    => route('player.dashboard'),
            'fan'       => route('public.tournaments.index'),
            default     => '/',
        };
    }
}