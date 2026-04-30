<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Models\Registration;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(): View
    {
        $registrations = Registration::with(['team.sport', 'tournament.organizer'])
            ->latest()
            ->paginate(20);

        return view('admin.registrations.index', compact('registrations'));
    }

    public function create(): View
    {
        $teams       = Team::active()->with('sport')->orderBy('team_name')->get();
        $tournaments = Tournament::whereIn('status', ['upcoming', 'ongoing'])->with('sport')->orderBy('tournament_name')->get();

        return view('admin.registrations.create', compact('teams', 'tournaments'));
    }

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