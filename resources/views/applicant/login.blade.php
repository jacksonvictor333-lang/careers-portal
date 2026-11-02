<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('portal.name', 'Careers Portal') }}</title>

    <style>
        :root {
            --green:  #0A8A3F;
            --yellow: #FCD116;
            --black:  #0B1320;
            --blue:   #1E6FDC;
            --navy:   #1D4E89;
            --navy-d: #163C6B;
            --ink:    #111827;
            --muted:  #5B6472;
            --line:   #E5E7EB;
            --danger: #B42318;
            --bg:     #FAFAF8;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex; flex-direction: column;
            font-family: Inter, "Segoe UI", system-ui, -apple-system, Arial, sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        /* ================= NAV ================= */
        .nav {
            display: flex; justify-content: flex-end; align-items: center; gap: 6px;
            padding: 10px 28px;
            background: #fff;
            border-bottom: 1px solid var(--line);
        }
        .nav a {
            padding: 9px 16px; border-radius: 4px;
            font-size: 14px; font-weight: 500; text-transform: uppercase;
            color: #374151; text-decoration: none;
        }
        .nav a:hover { background: #F3F4F6; }
        .nav a.active { background: var(--navy); color: #fff; }

        /* ================= CARD ================= */
        .main { flex: 1; display: grid; place-items: start center; padding: 36px 16px 48px; }

        .card {
            width: 100%; max-width: 420px;
            padding: 28px 32px 26px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(16,24,40,.04), 0 18px 40px -20px rgba(16,24,40,.18);
        }

        .card-head { text-align: center; margin-bottom: 20px; }
        .card-ico {
            width: 56px; height: 56px; margin: 0 auto 10px;
            display: grid; place-items: center;
            border-radius: 50%;
            color: var(--navy); background: #DBEAFE; border: 1px solid #93C5FD;
        }
        .card-ico svg { width: 26px; height: 26px; }
        .flag { display: flex; justify-content: center; gap: 4px; margin-bottom: 10px; }
        .flag i { width: 18px; height: 4px; border-radius: 3px; }
        .flag i:nth-child(1) { background: var(--green); }
        .flag i:nth-child(2) { background: var(--yellow); }
        .flag i:nth-child(3) { background: var(--black); }
        .flag i:nth-child(4) { background: var(--blue); }
        .card-head h1 { margin: 0 0 4px; font-size: 22px; font-weight: 600; }
        .card-head p  { margin: 0; font-size: 14px; color: var(--muted); }

        /* Alert ya seva (mf. nenosiri si sahihi) */
        .alert {
            display: flex; gap: 8px; align-items: flex-start;
            margin-bottom: 14px; padding: 10px 12px;
            border-radius: 6px; font-size: 13px;
            color: var(--danger); background: #FEF3F2; border: 1px solid #FECDCA;
        }
        .alert.ok { color: #067647; background: #ECFDF3; border-color: #ABEFC6; }

        /* ================= FIELDS ================= */
        .field { margin-bottom: 14px; }
        .field label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 500; }

        .control { position: relative; }
        .control > svg.lead {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 18px; height: 18px; color: #9CA3AF; pointer-events: none;
        }
        .control input {
            width: 100%; height: 44px; padding: 0 42px 0 40px;
            font: inherit; font-size: 15px; color: var(--ink);
            background: #fff;
            border: 1px solid #D1D5DB; border-radius: 4px; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .control input::placeholder { color: #9CA3AF; }
        .control input:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(29,78,137,.14); }

        /* Hali ya kosa: inaonekana TU baada ya kosa halisi (seva au baada ya kugusa/kutuma) */
        .field.has-error input { border-color: #F04438; background: #FFFBFA; }
        .field.has-error input:focus { box-shadow: 0 0 0 3px rgba(240,68,56,.14); }
        .error { display: none; margin-top: 5px; font-size: 13px; color: var(--danger); }
        .field.has-error .error { display: block; }

        .eye {
            position: absolute; right: 6px; top: 50%; transform: translateY(-50%);
            width: 34px; height: 34px; display: grid; place-items: center;
            border: 0; border-radius: 6px; background: transparent; color: #6B7280; cursor: pointer;
        }
        .eye:hover { background: #F3F4F6; color: var(--ink); }
        .eye svg { width: 18px; height: 18px; }
        .eye .off { display: none; }
        .eye[aria-pressed="true"] .on  { display: none; }
        .eye[aria-pressed="true"] .off { display: block; }

        .row { display: flex; align-items: center; justify-content: space-between; margin: 2px 0 16px; font-size: 14px; }
        .remember { display: inline-flex; align-items: center; gap: 8px; color: var(--muted); cursor: pointer; }
        .remember input { width: 16px; height: 16px; accent-color: var(--navy); }
        a.link { color: var(--navy); text-decoration: none; font-weight: 500; }
        a.link:hover { text-decoration: underline; }

        .btn {
            width: 100%; height: 46px;
            font: inherit; font-size: 16px; font-weight: 600; color: #fff;
            background: var(--navy); border: 0; border-radius: 4px; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            transition: background .15s;
        }
        .btn:hover { background: var(--navy-d); }
        .btn:disabled { opacity: .75; cursor: wait; }
        .spinner { display: none; width: 16px; height: 16px; border-radius: 50%; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; animation: spin .7s linear infinite; }
        .btn.loading .spinner { display: inline-block; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .foot { margin: 18px 0 0; text-align: center; font-size: 14px; color: var(--muted); }

        :focus-visible { outline: 3px solid rgba(29,78,137,.35); outline-offset: 2px; }

        /* ================= SIMU ================= */
        @media (max-width: 900px) {
            .nav { justify-content: center; flex-wrap: wrap; padding: 8px 12px; }
        }
        @media (max-width: 480px) {
            .card { padding: 22px 18px; }
        }
    </style>
</head>

<body>

    {{-- ================= BANNER (picha: public/images/banner.webp) ================= --}}
    @include('applicant.partials.banner')

    {{-- ================= NAV (LOGIN ni "active" tu, haijirudii kwenye fomu) ================= --}}
    <nav class="nav" aria-label="Main">
        <a href="{{ url('/') }}">Home</a>
        <a href="{{ Route::has('vacancies') ? route('vacancies') : '#' }}">Vacancies</a>
        <a href="{{ Route::has('feedback') ? route('feedback') : '#' }}">Feedback</a>
        <a href="{{ url()->current() }}" class="active" aria-current="page">Login</a>
    </nav>

    {{-- ================= LOGIN CARD ================= --}}
    <main class="main">
        <section class="card" aria-labelledby="loginTitle">

            <div class="card-head">
                <div class="card-ico" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div class="flag" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
                <h1 id="loginTitle">Login</h1>
                <p>Provide your email and password to login</p>
            </div>

            @if(session('status'))
                <div class="alert ok" role="status">{{ session('status') }}</div>
            @endif

            @if($errors->has('auth') || $errors->has('credentials'))
                <div class="alert" role="alert">{{ $errors->first('auth') ?: $errors->first('credentials') }}</div>
            @endif

            <form method="POST" action="{{ route('applicant.login.store') }}" id="loginForm" novalidate>
                @csrf

                {{-- EMAIL --}}
                <div class="field @error('email') has-error @enderror">
                    <label for="email">Email Address</label>
                    <div class="control">
                        <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               placeholder="Enter your email" autocomplete="email" autofocus required
                               aria-describedby="emailErr">
                    </div>
                    <div class="error" id="emailErr" role="alert" data-default="Email is required">@error('email'){{ $message }}@else Email is required @enderror</div>
                </div>

                {{-- PASSWORD --}}
                <div class="field @error('password') has-error @enderror">
                    <label for="password">Password</label>
                    <div class="control">
                        <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                        <input type="password" id="password" name="password"
                               placeholder="Enter your password" autocomplete="current-password" required
                               aria-describedby="passwordErr">
                        <button type="button" class="eye" id="togglePw" aria-label="Show password" aria-pressed="false">
                            <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0 1 12 19c-6.5 0-10-7-10-7a17.8 17.8 0 0 1 4.1-5"/><path d="M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a17.7 17.7 0 0 1-2.2 3.2"/><path d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                    <div class="error" id="passwordErr" role="alert">@error('password'){{ $message }}@else Password is required @enderror</div>
                </div>

                <div class="row">
                    <label class="remember">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me
                    </label>
                    @if(Route::has('password.request'))
                        <a class="link" href="{{ route('password.request') }}">Forgot Password?</a>
                    @endif
                </div>

                <button type="submit" class="btn" id="submitBtn">
                    <span class="spinner" aria-hidden="true"></span>
                    <span id="submitLabel">Login</span>
                </button>
            </form>

            @if(Route::has('applicant.register'))
                <p class="foot">Don't have an account? <a class="link" href="{{ route('applicant.register') }}">Register Now</a></p>
            @endif
        </section>
    </main>

<script>
(function () {
    const form   = document.getElementById('loginForm');
    const email  = document.getElementById('email');
    const pw     = document.getElementById('password');
    const btn    = document.getElementById('submitBtn');
    const label  = document.getElementById('submitLabel');

    const rules = [
        { input: email, err: 'emailErr',    check: v => v.trim() === '' ? 'Email is required'
                                                   : (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) ? '' : 'Enter a valid email address') },
        { input: pw,    err: 'passwordErr', check: v => v === '' ? 'Password is required' : '' },
    ];

    // Onyesha/ficha kosa kwa sehemu moja
    function validate(rule, show) {
        const msg   = rule.check(rule.input.value);
        const field = rule.input.closest('.field');
        const box   = document.getElementById(rule.err);
        if (msg && show) { box.textContent = msg; field.classList.add('has-error'); }
        else if (!msg)   { field.classList.remove('has-error'); }
        return !msg;
    }

    // Makosa yanaonekana tu baada ya mtu kugusa sehemu (blur) au kubonyeza Login
    rules.forEach(rule => {
        rule.input.addEventListener('blur',  () => { if (rule.input.value !== '') validate(rule, true); });
        rule.input.addEventListener('input', () => {
            const field = rule.input.closest('.field');
            if (field.classList.contains('has-error')) validate(rule, true);
        });
    });

    form.addEventListener('submit', function (e) {
        let ok = true;
        rules.forEach(rule => { if (!validate(rule, true)) ok = false; });
        if (!ok) {
            e.preventDefault();
            const first = rules.find(r => r.input.closest('.field').classList.contains('has-error'));
            if (first) first.input.focus();
            return;
        }
        btn.disabled = true;
        btn.classList.add('loading');
        label.textContent = 'Signing in...';
    });

    // Onyesha / ficha nenosiri
    const toggle = document.getElementById('togglePw');
    toggle.addEventListener('click', function () {
        const show = pw.type === 'password';
        pw.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', show ? 'true' : 'false');
        toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });

    // Ikirudi kwa kitufe cha Back, rudisha kitufe katika hali ya kawaida
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) { btn.disabled = false; btn.classList.remove('loading'); label.textContent = 'Login'; }
    });
})();
</script>
</body>
</html>