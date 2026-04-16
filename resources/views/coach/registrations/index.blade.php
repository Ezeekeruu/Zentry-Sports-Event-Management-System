@extends('layouts.coach')

@section('content')
<div class="page-header">
    <div class="page-title">Registrations</div>
    <div class="page-subtitle">Request tournament slots for your team.</div>
</div>

@if(!$team)
    <div class="card" style="text-align:center;padding:28px;">
        <div style="font-family:'Manrope',sans-serif;font-size:18px;font-weight:700;margin-bottom:8px;">No Team Assigned</div>
        <div style="font-size:13px;color:#64748b;">You cannot register to tournaments until an admin assigns a team to your account.</div>
    </div>
@else
    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">Submit Registration</div>
        <form method="POST" action="{{ route('coach.registrations.store') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Team</label>
                    <input type="text" class="form-control" value="{{ $team->team_name }} ({{ $team->sport->sport_name ?? 'N/A' }})" readonly>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Tournament</label>
                    <select name="tournament_id" class="form-control" required>
                        <option value="">Select tournament</option>
                        @foreach($availableTournaments as $tournament)
                            <option value="{{ $tournament->id }}" {{ old('tournament_id') == $tournament->id ? 'selected' : '' }}>
                                {{ $tournament->tournament_name }} ({{ $tournament->start_date->format('M d') }} - {{ $tournament->end_date->format('M d, Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('tournament_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <button type="submit" class="btn-primary" style="padding:9px 16px;">Submit</button>
                </div>
            </div>
            <div class="form-group" style="margin-top:10px;margin-bottom:0;">
                <label class="form-label">Notes <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Optional notes for organizer...">
                @error('notes') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </form>
    </div>

    <div class="card">
        <div style="font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;margin-bottom:14px;">My Registration Requests</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tournament</th>
                        <th>Sport</th>
                        <th>Organizer</th>
                        <th>Date Sent</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registrations as $registration)
                        <tr>
                            <td style="font-size:12px;font-weight:600;">{{ $registration->tournament->tournament_name ?? '—' }}</td>
                            <td><span class="badge badge-blue">{{ $registration->tournament->sport->sport_name ?? '—' }}</span></td>
                            <td style="font-size:11px;color:#64748b;">
                                {{ $registration->tournament->organizer->first_name ?? '—' }}
                                {{ $registration->tournament->organizer->last_name ?? '' }}
                            </td>
                            <td style="font-size:11px;color:#64748b;">{{ $registration->registration_date?->format('M d, Y') }}</td>
                            <td>
                                @if($registration->status === 'approved')
                                    <span class="badge badge-green">APPROVED</span>
                                @elseif($registration->status === 'rejected')
                                    <span class="badge badge-red">REJECTED</span>
                                @else
                                    <span class="badge badge-amber">PENDING</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;font-size:13px;">
                                No registration requests yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
