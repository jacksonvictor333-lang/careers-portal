<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Staff Login - {{ config('portal.name', 'Careers Portal') }}</title>
<style>
    * { box-sizing: border-box; }
    body { margin:0; min-height:100vh; display:flex; flex-direction:column; font-family:Inter,"Segoe UI",system-ui,Arial,sans-serif; background:radial-gradient(420px 250px at 17% 14%, rgba(29,78,137,.18), transparent 70%),radial-gradient(320px 280px at 94% 24%, rgba(18,183,106,.16), transparent 70%),radial-gradient(300px 260px at 6% 90%, rgba(252,209,22,.18), transparent 70%),#FAFBFD; color:#111827; }
    main { flex:1; display:grid; place-items:center; padding:30px 16px; }
    .card { width:100%; max-width:400px; padding:28px 30px; background:#fff; border:1px solid #E5E7EB; border-radius:6px; box-shadow:0 18px 40px -20px rgba(16,24,40,.2); }
    h1 { margin:0 0 4px; font-size:21px; text-align:center; } p.s { margin:0 0 20px; font-size:13.5px; text-align:center; color:#6B7280; }
    .bars { display:flex; justify-content:center; gap:4px; margin-bottom:12px; } .bars i { width:18px; height:4px; border-radius:3px; }
    .bars i:nth-child(1){background:#0A8A3F} .bars i:nth-child(2){background:#FCD116} .bars i:nth-child(3){background:#0B1320} .bars i:nth-child(4){background:#1E6FDC}
    label { display:block; margin:12px 0 5px; font-size:13.5px; font-weight:500; }
    input[type=email], input[type=password] { width:100%; height:42px; padding:0 12px; font:inherit; font-size:14.5px; border:1px solid #D1D5DB; border-radius:4px; outline:none; }
    input:focus { border-color:#1D4E89; box-shadow:0 0 0 3px rgba(29,78,137,.14); }
    .rem { display:flex; align-items:center; gap:8px; margin:12px 0 16px; font-size:13.5px; color:#4B5563; }
    button { width:100%; height:44px; font:inherit; font-size:15px; font-weight:600; color:#fff; background:#1D4E89; border:0; border-radius:4px; cursor:pointer; } button:hover { background:#163C6B; }
    .alert { margin-bottom:12px; padding:10px 12px; font-size:13px; color:#B42318; background:#FEF3F2; border:1px solid #FECDCA; border-radius:4px; }
    .err { margin-top:4px; font-size:12.5px; color:#B42318; }
</style>
</head>
<body>
@include('applicant.partials.banner')
<main>
    <div class="card">
        <div class="bars" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
        <h1>Staff Login</h1>
        <p class="s">Sign in to manage vacancies and applicants</p>

        @if($errors->has('credentials'))<div class="alert" role="alert">{{ $errors->first('credentials') }}</div>@endif

        <form method="POST" action="{{ route('staff.login.store') }}" novalidate>
            @csrf
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus required>
            @error('email')<div class="err">{{ $message }}</div>@enderror

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            @error('password')<div class="err">{{ $message }}</div>@enderror

            <label class="rem"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button type="submit">Login</button>
        </form>
    </div>
</main>
</body>
</html>
