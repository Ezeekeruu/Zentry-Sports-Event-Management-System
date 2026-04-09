<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    
      //Organizer sees only registrations for their own tournaments
     
    public function index(Request $request): View
    {
        $registrations = Registration::with(['team.sport', 'tournament'])
            ->whereHas('tournament', function ($q) use ($request) {
                $q->where('organizer_id', $request->user()->id);
            })
            ->latest()
            ->paginate(15);

        return view('organizer.registrations.index', compact('registrations'));
    }

    public function store(RegistrationRequest $request): RedirectResponse
    {
        Registration::create([
            'team_id'           => $request->team_id,
            'tournament_id'     => $request->tournament_id,
            'registration_date' => $request->registration_date,
            'status'            => 'pending',
            'notes'             => $request->notes,
        ]);

        return redirect()
            ->route('organizer.registrations.index')
            ->with('success', 'Team registered successfully. Awaiting approval.');
    }

    public function approve(Registration $registration): RedirectResponse
    {
        $this->authorizeOwnership($registration);

        $registration->update(['status' => 'approved']);

        return back()->with('success', "{$registration->team->team_name} approved.");
    }

    public function reject(Registration $registration): RedirectResponse
    {
        $this->authorizeOwnership($registration);

        $registration->update(['status' => 'rejected']);

        return back()->with('success', "{$registration->team->team_name} rejected.");
    }

    private function authorizeOwnership(Registration $registration): void
    {
         if ((int) $registration->tournament->organizer_id !== (int) auth()->id())  {
            abort(403, 'You do not own this tournament.');
        }
    }
}
