@extends('applicant.layout')

@section('title', 'Create Account')

@push('styles')
<style>
    .rg-wrap { max-width: 420px; margin: 0 auto; }
    .rg-card { padding: 28px 32px 26px; background: #fff; border: 1px solid var(--line); border-radius: 6px; box-shadow: 0 1px 2px rgba(16,24,40,.04), 0 18px 40px -20px rgba(16,24,40,.18); }
    .rg-head { margin-bottom: 20px; text-align: center; }
    .rg-ico { display: grid; place-items: center; width: 56px; height: 56px; margin: 0 auto 10px; color: var(--navy); background: #DBEAFE; border: 1px solid #93C5FD; border-radius: 50%; }
    .rg-ico svg { width: 26px; height: 26px; }
    .rg-flag { display: flex; justify-content: center; gap: 4px; margin-bottom: 10px; }
    .rg-flag i { display: block; width: 18px; height: 4px; border-radius: 3px; }
    .rg-flag i:nth-child(1) { background: #0A8A3F; } .rg-flag i:nth-child(2) { background: #FCD116; }
    .rg-flag i:nth-child(3) { background: #0B1320; } .rg-flag i:nth-child(4) { background: #1E6FDC; }
    .rg-head h1 { margin: 0 0 4px; font-size: 22px; font-weight: 600; }
    .rg-head p { margin: 0; font-size: 14px; color: var(--muted); }

    .rg-rules { list-style: none; margin: -2px 0 14px; padding: 0; font-size: 12.5px; color: #6B7280; }
    .rg-rules li { display: flex; align-items: center; gap: 8px; padding: 2px 0; }
    .rg-rules i { width: 14px; height: 14px; border: 1.5px solid #D1D5DB; border-radius: 50%; }
    .rg-rules li.ok { color: #067647; } .rg-rules li.ok i { background: #12B76A; border-color: #12B76A; }
    .rg-note { margin: 0 0 14px; padding: 10px 12px; font-size: 12.5px; line-height: 1.5; color: #7A2E0E; background: #FFF4ED; border: 1px solid #F9B98C; border-radius: 6px; }
    .rg-ok { color: #067647 !important; } .rg-bad { color: #B42318 !important; }
    .rg-f { margin-bottom: 14px; }
    .rg-f label { display: block; margin-bottom: 6px; font-size: 14px; font-weight: 500; }
    .rg-c { position: relative; }
    .rg-c svg.lead { position: absolute; left: 12px; top: 50%; width: 18px; height: 18px; color: #9CA3AF; transform: translateY(-50%); pointer-events: none; }
    .rg-c input { width: 100%; height: 44px; padding: 0 42px 0 40px; font: inherit; font-size: 15px; color: var(--ink); background: #fff; border: 1px solid #D1D5DB; border-radius: 4px; outline: none; transition: border-color .15s, box-shadow .15s; }
    .rg-c input::placeholder { color: #9CA3AF; }
    .rg-c input:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(29,78,137,.14); }
    .rg-f.err input { border-color: #F04438; background: #FFFBFA; }
    .rg-eye { position: absolute; right: 6px; top: 50%; display: grid; place-items: center; width: 34px; height: 34px; transform: translateY(-50%); color: #6B7280; background: transparent; border: 0; border-radius: 4px; cursor: pointer; }
    .rg-eye:hover { background: #F3F4F6; color: var(--ink); }
    .rg-eye svg { width: 18px; height: 18px; }
    .rg-eye .off { display: none; }
    .rg-eye[aria-pressed="true"] .on { display: none; }
    .rg-eye[aria-pressed="true"] .off { display: block; }
    .rg-err { margin-top: 5px; font-size: 13px; color: #B42318; }
    .rg-hint { margin-top: 5px; font-size: 12.5px; color: var(--muted); }
    .rg-btn { width: 100%; height: 46px; margin-top: 4px; font: inherit; font-size: 16px; font-weight: 600; color: #fff; background: var(--navy); border: 0; border-radius: 4px; cursor: pointer; }
    .rg-btn:hover { background: #163C6B; }
    .rg-btn:disabled { opacity: .75; cursor: wait; }
    .rg-foot { margin: 18px 0 0; text-align: center; font-size: 14px; color: var(--muted); }
    .rg-foot a { color: var(--navy); font-weight: 600; }
</style>
@endpush

@section('content')
<div class="rg-wrap">
    <section class="rg-card">
        <div class="rg-head">
            <div class="rg-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
            </div>
            <div class="rg-flag" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
            <h1>Create Account</h1>
            <p>Provide your email and password to register</p>
        </div>

        <form method="POST" action="{{ route('applicant.register.store') }}" id="regForm" novalidate>
            @csrf

            <div class="rg-f @error('email') err @enderror">
                <label for="email">Email Address</label>
                <div class="rg-c">
                    <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" autocomplete="email" autofocus required>
                </div>
                @error('email')<div class="rg-err">{{ $message }}</div>@enderror
            </div>

            <div class="rg-f @error('password') err @enderror">
                <label for="password">Password</label>
                <div class="rg-c">
                    <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                    <input type="password" id="password" name="password" placeholder="Create a password" autocomplete="new-password" required>
                    <button type="button" class="rg-eye" data-target="password" aria-label="Show password" aria-pressed="false">
                        <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0 1 12 19c-6.5 0-10-7-10-7a17.8 17.8 0 0 1 4.1-5"/><path d="M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a17.7 17.7 0 0 1-2.2 3.2"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
                @error('password')<div class="rg-err">{{ $message }}</div>@else<div class="rg-hint">At least 8 characters, with letters and numbers.</div>@enderror
            </div>

            <div class="rg-f">
                <label for="password_confirmation">Confirm Password</label>
                <div class="rg-c">
                    <svg class="lead" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm your password" autocomplete="new-password" required>
                    <button type="button" class="rg-eye" data-target="password_confirmation" aria-label="Show password" aria-pressed="false">
                        <svg class="on" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.1 10.1 0 0 1 12 19c-6.5 0-10-7-10-7a17.8 17.8 0 0 1 4.1-5"/><path d="M9.9 5.2A9.7 9.7 0 0 1 12 5c6.5 0 10 7 10 7a17.7 17.7 0 0 1-2.2 3.2"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
                <div class="rg-hint" id="matchMsg" aria-live="polite"></div>
            </div>

            <ul class="rg-rules" id="rules">
                <li data-r="len"><i></i> At least 8 characters</li>
                <li data-r="let"><i></i> Contains letters</li>
                <li data-r="num"><i></i> Contains numbers</li>
            </ul>

            <div class="rg-note"><b>Note:</b> after you register, your account must be activated by the Employment Secretariat before you can fill in your profile or apply for jobs.</div>

            <button type="submit" class="rg-btn" id="regBtn">Create Account</button>
        </form>

        <p class="rg-foot">Already have an account? <a href="{{ route('applicant.login') }}">Login</a></p>
    </section>
</div>
@endsection

@push('scripts')
<script>
    // Onyesha / ficha nenosiri
    document.querySelectorAll('.rg-eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.dataset.target);
            const show  = input.type === 'password';
            input.type  = show ? 'text' : 'password';
            btn.setAttribute('aria-pressed', show ? 'true' : 'false');
            btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        });
    });

    // Ukaguzi wa moja kwa moja: sheria za nenosiri na kulinganisha na confirm
    (function () {
        const pw = document.getElementById('password'), cf = document.getElementById('password_confirmation'), msg = document.getElementById('matchMsg');
        function mark(k, ok) { document.querySelector('#rules [data-r="' + k + '"]').classList.toggle('ok', ok); }
        function check() {
            mark('len', pw.value.length >= 8); mark('let', /[A-Za-z]/.test(pw.value)); mark('num', /[0-9]/.test(pw.value));
            if (!cf.value) { msg.textContent = ''; msg.className = 'rg-hint'; return; }
            const same = pw.value === cf.value;
            msg.textContent = same ? 'Passwords match.' : 'Passwords do not match.';
            msg.className = 'rg-hint ' + (same ? 'rg-ok' : 'rg-bad');
        }
        pw.addEventListener('input', check); cf.addEventListener('input', check);
        document.getElementById('regForm').addEventListener('submit', function (e) {
            if (pw.value !== cf.value) { e.preventDefault(); check(); cf.focus(); window.__regBlocked = true; }
        }, true);
    })();

    // Zuia kubonyeza mara mbili
    document.getElementById('regForm').addEventListener('submit', function (e) {
        if (e.defaultPrevented) { return; }
        const b = document.getElementById('regBtn');
        b.disabled = true;
        b.textContent = 'Creating account...';
    });

    // Ikirudi kwa Back, rudisha kitufe
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) { const b = document.getElementById('regBtn'); b.disabled = false; b.textContent = 'Create Account'; }
    });
</script>
@endpush
