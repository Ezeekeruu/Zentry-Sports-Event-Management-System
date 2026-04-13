@extends('layouts.organizer')

@section('content')
<div class="page-title" style="margin-bottom:20px;">Registrations</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Team</th><th>Sport</th><th>Tournament</th><th>Reg. Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                <tr>
                    <td style="font-weight:600;font-size:13px;">{{ $reg->team->team_name ?? '—' }}</td>
                    <td><span class="badge badge-blue">{{ $reg->team->sport->sport_name ?? '—' }}</span></td>
                    <td style="font-size:12px;">{{ Str::limit($reg->tournament->tournament_name ?? '—', 28) }}</td>
                    <td style="font-size:11px;color:#64748b;">{{ $reg->registration_date?->format('M d, Y') }}</td>
                    <td>
                        @if($reg->status==='approved') <span class="badge badge-green">APPROVED</span>
                        @elseif($reg->status==='pending') <span class="badge badge-amber">PENDING</span>
                        @else <span class="badge badge-red">REJECTED</span> @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            @if($reg->status==='pending')
                                <form method="POST" action="{{ route('organizer.registrations.approve', $reg) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" style="background:#dcfce7;color:#14532d;border:none;border-radius:7px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('organizer.registrations.reject', $reg) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-danger" style="padding:5px 10px;font-size:11px;">Reject</button>
                                </form>
                            @elseif($reg->status==='approved')
                                <form method="POST" action="{{ route('organizer.registrations.reject', $reg) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn-danger" style="padding:5px 10px;font-size:11px;">Revoke</button>
                                </form>
                            @elseif($reg->status==='rejected')
                                <form method="POST" action="{{ route('organizer.registrations.approve', $reg) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" style="background:#dcfce7;color:#14532d;border:none;border-radius:7px;padding:5px 10px;font-size:11px;font-weight:600;cursor:pointer;font-family:'Inter',sans-serif;">Re-approve</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:30px;">No registrations found for your tournaments.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($registrations->hasPages())
    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding-top:12px;border-top:0.5px solid rgba(15,23,42,0.06);">
        <div style="font-size:12px;color:#94a3b8;">Showing {{ $registrations->firstItem() }}–{{ $registrations->lastItem() }} of {{ $registrations->total() }}</div>
        <div style="display:flex;gap:4px;">
            @foreach($registrations->getUrlRange(1,$registrations->lastPage()) as $page => $url)
                @if($page==$registrations->currentPage())
                    <span style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#fff;background:#1a2233;">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" style="padding:6px 10px;border-radius:5px;font-size:12px;font-weight:500;color:#0f172a;border:0.5px solid rgba(15,23,42,0.1);background:#fff;text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
