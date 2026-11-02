<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('code') - {{ config('portal.name', 'Careers Portal') }}</title>
<style>
    * { box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; display: flex; flex-direction: column; font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; color: #111827; background: #FAFAF9; }
    main { flex: 1; display: grid; place-items: center; padding: 40px 16px; }
    .box { width: 100%; max-width: 520px; padding: 36px 32px; text-align: center; background: #fff; border: 1px solid #E5E7EB; border-radius: 6px; }
    .code { font-size: 56px; font-weight: 800; line-height: 1; color: #1D4E89; }
    h1 { margin: 12px 0 8px; font-size: 20px; }
    p { margin: 0 0 22px; font-size: 14px; line-height: 1.6; color: #4B5563; }
    a.btn { display: inline-block; padding: 10px 20px; font-size: 14px; font-weight: 600; color: #fff; text-decoration: none; background: #1D4E89; border-radius: 4px; }
    footer { padding: 16px; text-align: center; font-size: 12.5px; color: #6B7280; border-top: 1px solid #E5E7EB; background: #fff; }
</style>
</head>
<body>
    @include('applicant.partials.banner')
    <main>
        <div class="box">
            <div class="code">@yield('code')</div>
            <h1>@yield('title')</h1>
            <p>@yield('message')</p>
            <a class="btn" href="{{ url('/') }}">Go to Home</a>
        </div>
    </main>
    <footer>&copy; {{ date('Y') }} {{ config('portal.name', 'Careers Portal') }}</footer>
</body>
</html>
