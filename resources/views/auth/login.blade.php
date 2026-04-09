<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zentry — Login</title>
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
        .forgot { text-align: right; margin-top: -8px; margin-bottom: 14px; }
        .forgot a { font-size: 11px; color: #22c55e; font-weight: 600; text-decoration: none; }
        .btn-submit { width: 100%; background: #22c55e; color: #fff; border: none; border-radius: 8px; padding: 11px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; margin-top: 8px; transition: background .12s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-submit:hover { background: #16a34a; }
        .register-link { text-align: center; margin-top: 20px; font-size: 12px; color: #64748b; }
        .register-link a { color: #22c55e; font-weight: 600; text-decoration: none; }
        .divider { display: flex; align-items: center; gap: 10px; margin: 16px 0; }
        .divider hr { flex: 1; border: none; border-top: 0.5px solid rgba(15,23,42,0.1); }
        .divider span { font-size: 11px; color: #94a3b8; }
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
    <div class="card-title">Welcome Back</div>
    <div class="card-sub">Enter your credentials to access the console.</div>

    @if(session('status'))
        <div style="background:#dcfce7;border-left:3px solid #22c55e;color:#14532d;padding:10px 14px;border-radius:7px;font-size:12px;margin-bottom:16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <input type="password" name="password" id="login-password"
                       class="form-control has-eye" required>
                <button type="button" class="eye-btn" onclick="togglePassword('login-password', this)">
                    <svg id="eye-icon-login" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="forgot">
            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot Password?</a>
            @endif
        </div>

        <button type="submit" class="btn-submit">
            Login
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
    </form>

    <div class="divider">
        <hr><span>New to Zentry?</span><hr>
    </div>

    <div class="register-link">
        <a href="{{ route('register') }}">Create an account</a>
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