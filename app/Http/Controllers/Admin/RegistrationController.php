<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Admin sees ALL registrations across all tournaments
     */
    public function index(): View
    {
        $registrations = Registration::with(['team.sport', 'tournament.organizer'])
            ->latest()
            ->paginate(20);

        return view('admin.registrations.index', compact('registrations'));
    }

    /**
     * Admin uses RegistrationRequest — all BR checks still apply
     * Admin approves immediately on store
     */
    public function store(RegistrationRequest $request): RedirectResponse
    {
        Registration::create([
            'team_id'           => $request->team_id,
            'tournament_id'     => $request->tournament_id,
            'registration_date' => $request->registration_date,
            'status'            => 'approved',
            'notes'             => $request->notes,
        ]);

        return redirect()
            ->route('admin.registrations.index')
            ->with('success', 'Team registered and approved.');
    }

    public function approve(Registration $registration): RedirectResponse
    {
        $registration->update(['status' => 'approved']);

        return back()->with('success', 'Registration approved.');
    }

    public function reject(Registration $registration): RedirectResponse
    {
        $registration->update(['status' => 'rejected']);

        return back()->with('success', 'Registration rejected.');
    }

    public function destroy(Registration $registration): RedirectResponse
    {
        $registration->delete();

        return back()->with('success', 'Registration removed.');
    }
}