<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zentry — Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #f0f2ff; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; }
        .brand { text-align: center; margin-bottom: 24px; }
        .brand-icon { width: 48px; height: 48px; background: #1a2233; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
        .brand-icon svg { width: 22px; height: 22px; fill: #22c55e; }
        .brand-name { font-family: 'Manrope', sans-serif; font-size: 22px; font-weight: 800; color: #0f172a; }
        .brand-sub { font-size: 10px; font-weight: 600; letter-spacing: .12em; color: #94a3b8; margin-top: 2px; }
        .card { background: #fff; border-radius: 14px; padding: 32px; width: 100%; max-width: 420px; border: 0.5px solid rgba(15,23,42,0.08); }
        .card-title { font-family: 'Manrope', sans-serif; font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .card-sub { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .form-group { margin-bottom: 14px; }
        .form-label { font-size: 10px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #374151; display: block; margin-bottom: 5px; }
        .input-wrap { position: relative; }
        .form-control { width: 100%; padding: 10px 12px; border: 1px solid rgba(15,23,42,0.12); border-radius: 8px; font-size: 13px; font-family: 'Inter', sans-serif; color: #0f172a; background: #f8faff; outline: none; transition: border .12s; }
        .form-control:focus { border-color: #22c55e; background: #fff; }
        .form-control.has-eye { padding-right: 40px; }
        .eye-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px; display: flex; align-items: center; }
        .eye-btn:hover { color: #64748b; }
        .form-error { font-size: 11px; color: #ef4444; margin-top: 4px; }
        .btn-submit { width: 100%; background: #22c55e; color: #fff; border: none; border-radius: 8px; padding: 11px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; margin-top: 8px; transition: background .12s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { background: #16a34a; }
        .login-link { text-align: center; margin-top: 20px; font-size: 12px; color: #64748b; }
        .login-link a { color: #22c55e; font-weight: 600; text-decoration: none; }
        .name-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .pass-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    </style>
</head>
<body>

<div class="brand">
    <div class="brand-icon">
        <svg viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
    </div>
    <div class="brand-name">ZENTRY</div>
    <div class="brand-sub">TOURNAMENT ENGINE</div>
</div>

<div class="card">
    <div class="card-title">Create Account</div>
    <div class="card-sub">Join Zentry to follow tournaments and results.</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="name-row">
            <div class="form-group">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-control"
                       value="{{ old('first_name') }}" required autofocus>
                @error('first_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-control"
                       value="{{ old('last_name') }}" required>
                @error('last_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}" required>
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="pass-row">
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="reg-password"
                           class="form-control has-eye" required>
                    <button type="button" class="eye-btn" onclick="togglePassword('reg-password', this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <div class="input-wrap">
                    <input type="password" name="password_confirmation" id="reg-confirm"
                           class="form-control has-eye" required>
                    <button type="button" class="eye-btn" onclick="togglePassword('reg-confirm', this)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Create Account
        </button>
    </form>

    <div class="login-link" style="margin-top:20px;">
        Already have an account? <a href="{{ route('login') }}">Sign in</a>
    </div>
</div>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.innerHTML = isPassword
        ? `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>`
        : `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
}
</script>
</body>
</html>