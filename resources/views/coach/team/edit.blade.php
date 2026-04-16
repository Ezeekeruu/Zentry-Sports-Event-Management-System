@extends('layouts.coach')

@section('topbar-action')
    <a href="{{ route('coach.team.show') }}" class="btn-secondary">← Back to Team</a>
@endsection

@section('content')
<div style="max-width:520px;margin:0 auto;">
    <div class="page-header">
        <div class="page-title">Edit Team</div>
        <div class="page-subtitle">Update basic team information.</div>
    </div>
    <div class="card">
        <form method="POST" action="{{ route('coach.team.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Team Name</label>
                <input type="text" name="team_name" class="form-control"
                       value="{{ old('team_name',$team->team_name) }}" required>
                @error('team_name') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Founded Date <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                <input type="date" name="founded_at" class="form-control"
                       value="{{ old('founded_at',$team->founded_at?->format('Y-m-d')) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Team Logo <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                @if($team->logo_src)
                    <div style="margin-bottom:10px;">
                        <img src="{{ $team->logo_src }}" alt="{{ $team->team_name }} logo" style="width:56px;height:56px;object-fit:cover;border-radius:10px;border:1px solid rgba(15,23,42,0.08);background:#fff;">
                    </div>
                @endif
                <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png,.webp,.gif,image/*">
                <div style="font-size:11px;color:#94a3b8;margin-top:6px;">Upload a new PNG, JPG, WEBP, or GIF file up to 2MB to replace the current logo.</div>
                @error('logo') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('coach.team.show') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
