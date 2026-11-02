<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Staff') - {{ config('portal.name', 'Careers Portal') }} Staff</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    :root { --navy:#1D4E89; --navy-d:#12335C; --ink:#111827; --muted:#6B7280; --line:#E5E7EB; --bg:#F5F6F8; --danger:#B42318; }
    * { box-sizing: border-box; }
    body { margin:0; font-family: Inter, "Segoe UI", system-ui, Arial, sans-serif; color:var(--ink); background:var(--bg); font-size:14px; }
    a { color: var(--navy); }

    .st-shell { display:grid; grid-template-columns: 240px minmax(0,1fr); min-height:100vh; align-items:start; }
    /* Menyu inabaki mahali pake ukiscroll; ni ukurasa (kulia) tu unaosogea */
    /* Banner inabaki juu ukiscroll; menyu inabaki chini yake */
    .st-top { position:sticky; top:0; z-index:60; }
    .st-top .pb-banner img { height:auto; min-height:0; }   /* picha nzima, nembo ya taifa haikatwi */
    .st-side { position:sticky; top:var(--st-top-h,98px); height:calc(100vh - var(--st-top-h,98px)); display:flex; flex-direction:column; background:var(--navy-d); color:#DCE8F7; }
    .st-brand { padding:14px 10px 12px; line-height:0; text-align:center; background:#fff; border-bottom:1px solid var(--line); }
    .st-brand img { display:inline-block; width:auto; height:104px; max-width:100%; border:0; border-radius:0; background:none; }
    .st-brand .nm { line-height:1.2; }
    .st-brand small { display:block; margin-top:2px; font-size:11.5px; font-weight:500; color:#9CC0EE; letter-spacing:.06em; text-transform:uppercase; }
    .st-menu { list-style:none; margin:0; padding:12px; flex:1; min-height:0; overflow-y:auto; }
    .st-menu a { display:flex; align-items:center; gap:12px; padding:10px 12px; margin-bottom:2px; font-size:14px; color:#DCE8F7; text-decoration:none; border-radius:4px; }
    .st-menu a:hover { background:rgba(255,255,255,.08); }
    .st-menu a.on { color:#fff; background:rgba(255,255,255,.16); font-weight:600; }
    .st-menu i { font-size:17px; }
    .st-user { padding:14px 18px; font-size:12.5px; border-top:1px solid rgba(255,255,255,.12); }
    .st-user b { display:block; color:#fff; font-size:13.5px; }
    .st-user button { margin-top:8px; padding:6px 12px; font:inherit; font-size:12.5px; color:#fff; background:transparent; border:1px solid rgba(255,255,255,.4); border-radius:4px; cursor:pointer; }
    .st-user button:hover { background:rgba(255,255,255,.1); }

    .st-main { padding:0 30px 40px; min-width:0; }
    .st-topbar { position:sticky; top:var(--st-top-h,98px); z-index:40; display:flex; justify-content:flex-end; margin:0 -30px 18px; padding:8px 30px; background:rgba(255,255,255,.82); backdrop-filter:blur(6px); border-bottom:1px solid var(--line); }
    .st-dd.st-me > summary { width:auto; height:36px; padding:0 12px; gap:8px; display:flex; align-items:center; font-size:13px; color:#374151; border:1px solid #D1D5DB; border-radius:999px; background:#fff; }
    .st-dd.st-me > summary i:first-child { font-size:22px; color:var(--navy); }
    .st-dd.st-me .st-pop { top:42px; min-width:240px; }
    .st-main > .st-alert:first-of-type, .st-main > .st-otp:first-of-type { margin-top:6px; }
    .st-head { margin-top:6px; }
    .st-otp { margin-bottom:16px; padding:16px 20px; background:#FFFBEB; border:1px solid #FDE68A; border-left:4px solid #F79009; border-radius:6px; }
    .st-otp.slim { padding:9px 16px; }
    .st-otp.slim .row1 { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
    .st-otp.slim .code { margin:0; padding:2px 12px; font-size:15px; letter-spacing:.14em; }
    .st-otp.slim .st-sub { margin-top:3px; }
    .st-otp.rl { background:#ECFDF3; border-color:#ABEFC6; border-left-color:#12B76A; }
    .st-otp.slim.rl .code { border-color:#12B76A; }
    .st-otp.rl.bad { background:#FEF3F2; border-color:#FECDCA; border-left-color:#F04438; }
    .rl-url { flex:1; min-width:260px; height:34px; padding:0 10px; font:12.5px ui-monospace,Consolas,monospace; color:#344054; background:#fff; border:1px dashed #12B76A; border-radius:6px; text-overflow:ellipsis; }
    .st-otp .code { display:inline-block; margin:6px 10px 6px 0; padding:6px 16px; font:700 24px/1.2 ui-monospace,Consolas,monospace; letter-spacing:.18em; color:#111827; background:#fff; border:1px dashed #F79009; border-radius:4px; user-select:all; }
    .dm-back { position:fixed; inset:0; z-index:100; display:grid; place-items:center; padding:20px; background:rgba(16,24,40,.55); }
    .dm-back[hidden] { display:none; }
    .dm-box { display:flex; flex-direction:column; width:min(1000px,100%); height:min(92vh,880px); background:#fff; border-radius:8px; overflow:hidden; box-shadow:0 30px 60px -20px rgba(0,0,0,.5); }
    .dm-head { display:flex; align-items:center; gap:10px; padding:11px 16px; border-bottom:1px solid var(--line); }
    .dm-head h2 { flex:1; margin:0; font-size:15px; }
    .dm-head button, .dm-head a { display:inline-flex; align-items:center; gap:6px; height:32px; padding:0 12px; font:inherit; font-size:12.5px; font-weight:600; color:var(--navy); text-decoration:none; background:#fff; border:1px solid #D1D5DB; border-radius:4px; cursor:pointer; }
    .dm-frame { flex:1; width:100%; border:0; background:#F3F4F6; }
    .dm-form { width:min(440px,100%); padding:22px; background:#fff; border-radius:8px; box-shadow:0 30px 60px -20px rgba(0,0,0,.5); }
    .dm-form h2 { margin:0 0 4px; font-size:17px; } .dm-form p { margin:0 0 14px; font-size:13px; color:var(--muted); }
    .st-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
    .st-head h1 { margin:0; font-size:22px; font-weight:600; }
    .st-head p { margin:2px 0 0; font-size:13px; color:var(--muted); }

    .st-card { margin-bottom:16px; background:#fff; border:1px solid var(--line); border-radius:6px; }
    .st-ch { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:13px 20px; font-size:15px; font-weight:700; border-bottom:1px solid var(--line); }
    .st-cb { padding:16px 20px; }

    .st-btn { display:inline-flex; align-items:center; gap:7px; height:36px; padding:0 14px; font:inherit; font-size:13px; font-weight:600; color:#fff; background:var(--navy); border:1px solid var(--navy); border-radius:4px; cursor:pointer; text-decoration:none; }
    .st-btn:hover { background:#163C6B; color:#fff; }
    .st-btn.ghost { color:var(--navy); background:#fff; }
    .st-btn.ghost:hover { background:#F3F8FD; color:var(--navy); }
    .st-btn.sm { height:30px; padding:0 10px; font-size:12px; }

    .st-alert { display:flex; gap:10px; align-items:flex-start; margin-bottom:14px; padding:10px 14px; font-size:13px; color:#B42318; background:#FEF3F2; border:1px solid #FECDCA; border-radius:4px; }
    .st-alert.ok { color:#067647; background:#ECFDF3; border-color:#ABEFC6; }

    .st-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:14px; margin-bottom:16px; }
    .st-stat { padding:16px 18px; background:#fff; border:1px solid var(--line); border-radius:6px; }
    .st-stat span { display:block; font-size:12.5px; color:var(--muted); }
    .st-stat b { display:block; margin-top:4px; font-size:28px; font-weight:800; color:var(--navy); }

    table.st-t { width:100%; border-collapse:collapse; font-size:13px; }
    .st-t th { padding:10px 14px; text-align:left; font-size:11.5px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; color:#374151; background:#F3F4F6; border-bottom:1px solid var(--line); white-space:nowrap; }
    .st-t td { padding:10px 14px; vertical-align:middle; border-bottom:1px solid #F0F1F3; }
    .st-t tr:last-child td { border-bottom:0; }
    .st-t .r { text-align:right; white-space:nowrap; }
    .st-wrap { overflow-x:auto; }
    .st-sub { font-size:12px; color:var(--muted); }

    .st-pill { display:inline-block; padding:2px 10px; font-size:11.5px; font-weight:600; border:1px solid; border-radius:999px; white-space:nowrap; }
    .st-pill.g { color:#067647; background:#ECFDF3; border-color:#ABEFC6; }
    .st-pill.a { color:#B54708; background:#FFFAEB; border-color:#FEDF89; }
    .st-pill.r { color:#B42318; background:#FEF3F2; border-color:#FECDCA; }
    .st-pill.b { color:#175CD3; background:#EFF8FF; border-color:#B2DDFF; }
    .st-pill.n { color:#475467; background:#F2F4F7; border-color:#E4E7EC; }

    .st-form { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px 20px; }
    .st-form .full { grid-column:1 / -1; }
    .st-f label.t { display:block; margin-bottom:5px; font-size:12.5px; font-weight:600; color:#374151; }
    .st-f label.t .req { color:#D92D20; }
    .st-f input[type=text], .st-f input[type=email], .st-f input[type=number], .st-f input[type=date], .st-f input[type=password], .st-f select, .st-f textarea {
        width:100%; padding:8px 10px; font:inherit; font-size:13.5px; color:var(--ink); background:#fff; border:1px solid #D1D5DB; border-radius:4px; outline:none; }
    .st-f input, .st-f select { height:38px; }
    .st-f textarea { min-height:90px; line-height:1.5; resize:vertical; }
    .st-f input:focus, .st-f select:focus, .st-f textarea:focus { border-color:var(--navy); box-shadow:0 0 0 3px rgba(29,78,137,.14); }
    .st-f.err input, .st-f.err select, .st-f.err textarea { border-color:#F04438; background:#FFFBFA; }
    .st-err { margin-top:4px; font-size:12px; color:var(--danger); }
    .st-hint { margin-top:4px; font-size:12px; color:var(--muted); }
    .st-checks { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:6px 14px; padding:10px 12px; border:1px solid #D1D5DB; border-radius:4px; max-height:210px; overflow:auto; }
    .st-checks label { display:flex; align-items:center; gap:8px; font-size:13px; }

    .st-kv { display:grid; grid-template-columns:repeat(auto-fill,minmax(230px,1fr)); gap:12px 24px; margin:0; }
    .st-kv dt { font-size:12px; color:var(--muted); }
    .st-kv dd { margin:2px 0 0; font-size:13.5px; font-weight:500; }
    .st-empty { padding:6px 0; font-size:13px; font-style:italic; color:var(--muted); }

    .st-search { display:flex; gap:8px; margin-bottom:14px; }
    .st-search input { flex:1; max-width:380px; height:36px; padding:0 12px; font:inherit; font-size:13px; border:1px solid #D1D5DB; border-radius:4px; }
    .st-pager { display:flex; gap:6px; justify-content:flex-end; padding:12px 16px; border-top:1px solid var(--line); }
    .st-pager a, .st-pager span { padding:5px 11px; font-size:12.5px; color:var(--ink); text-decoration:none; border:1px solid #D1D5DB; border-radius:4px; background:#fff; }
    .st-pager span.cur { color:#fff; background:var(--navy); border-color:var(--navy); }
    .st-pager span.dis { opacity:.45; }

    .st-sub-menu summary { display:flex; align-items:center; gap:12px; padding:10px 12px; margin-bottom:2px; font-size:14px; color:#DCE8F7; border-radius:4px; cursor:pointer; list-style:none; }
    .st-sub-menu summary::-webkit-details-marker { display:none; }
    .st-sub-menu summary:hover { background:rgba(255,255,255,.08); }
    .st-sub-menu summary i { font-size:17px; }
    .st-sub-menu summary .caret { margin-left:auto; font-size:12px; transition:transform .15s; }
    .st-sub-menu[open] summary .caret { transform:rotate(180deg); }
    .st-sub-menu a { padding-left:41px; font-size:13.5px; }
    .st-sub2 summary { margin-bottom:2px; }

    .st-tabs { display:flex; gap:2px; flex-wrap:wrap; margin-bottom:14px; border-bottom:1px solid var(--line); }
    .st-tabs a { display:inline-flex; align-items:center; gap:8px; padding:11px 16px; font-size:13.5px; color:#4B5563; text-decoration:none; border-bottom:2px solid transparent; margin-bottom:-1px; }
    .st-tabs a:hover { color:var(--navy); }
    .st-tabs a.on { color:var(--navy); font-weight:600; background:#fff; border:1px solid var(--line); border-bottom-color:#fff; border-top:2px solid var(--navy); border-radius:4px 4px 0 0; }
    .st-tabs .n { padding:0 7px; font-size:11px; font-weight:700; color:#fff; background:#D92D20; border-radius:999px; }
    .st-bar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin:12px 0; font-size:13px; color:#4B5563; }
    .st-bar select { height:32px; margin:0 6px; padding:0 8px; font:inherit; border:1px solid #D1D5DB; border-radius:4px; }
    .st-dd { position:relative; display:inline-block; }
    .st-dd > summary { display:grid; place-items:center; width:30px; height:30px; font-size:18px; color:var(--navy); list-style:none; cursor:pointer; border-radius:4px; }
    .st-dd > summary::-webkit-details-marker { display:none; }
    .st-dd > summary:hover { background:#EEF4FB; }
    .st-pop { position:absolute; right:0; top:32px; z-index:30; min-width:230px; padding:6px 0; text-align:left; background:#fff; border:1px solid var(--line); border-radius:6px; box-shadow:0 12px 30px -8px rgba(16,24,40,.25); }
    .st-pop a, .st-pop button { display:flex; align-items:center; gap:10px; width:100%; padding:8px 16px; font:inherit; font-size:13px; color:#1F2937; text-align:left; text-decoration:none; background:none; border:0; cursor:pointer; }
    .st-pop a:hover, .st-pop button:hover { background:#F3F8FD; }
    .st-pop button:disabled { opacity:.4; cursor:not-allowed; }
    .st-pop i { width:16px; font-size:15px; color:#475467; }
    .st-pop hr { margin:4px 0; border:0; border-top:1px solid var(--line); }
    .st-pill.s { color:#fff; font-weight:700; border:0; border-radius:3px; padding:2px 8px; }
    .st-pill.s.g { background:#079455; } .st-pill.s.r { background:#F04438; } .st-pill.s.a { background:#F79009; }


    /* ===== Mtindo ule ule wa applicant: mandharinyuma laini (kama Home), menyu nyeupe, kadi zenye pembe laini ===== */
    :root { --bg:#FAFBFD; --navy-d:#163C6B; }
    body {
        background:
            radial-gradient(420px 250px at 17% 12%, rgba(29,78,137,.16), transparent 70%),
            radial-gradient(520px 300px at 45% 30%, rgba(29,78,137,.10), transparent 70%),
            radial-gradient(320px 280px at 96% 22%, rgba(18,183,106,.14), transparent 70%),
            radial-gradient(300px 260px at 8% 92%, rgba(252,209,22,.16), transparent 70%),
            #FAFBFD;
        background-attachment: fixed;
    }
    .st-side { background:#fff; color:#374151; border-right:1px solid var(--line); }
    .st-brand { color:var(--ink); border-bottom:1px solid var(--line); }
    .st-brand small { color:var(--navy); }
    .st-menu a, .st-sub-menu summary { color:#374151; font-weight:500; border-left:3px solid transparent; border-radius:8px; }
    .st-menu a i, .st-sub-menu summary i { color:#8A94A6; }
    .st-menu a:hover, .st-sub-menu summary:hover { background:#F3F4F6; color:var(--ink); }
    .st-menu a:hover i, .st-sub-menu summary:hover i { color:var(--navy); }
    .st-menu a.on { background:#EAF1FA; color:var(--navy); font-weight:600; border-left-color:var(--navy); }
    .st-menu a.on i { color:var(--navy); }
    .st-sub-menu a { padding-left:41px; }
    .st-user { color:var(--muted); border-top:1px solid var(--line); }
    .st-user b { color:var(--ink); }
    .st-user button { color:var(--navy); border:1px solid #BFD3EC; background:#fff; border-radius:8px; }
    .st-user button:hover { background:#EEF4FB; }
    .st-user div { color:var(--muted) !important; }
    .st-card, .st-stat { border-radius:14px; box-shadow:0 1px 2px rgba(16,24,40,.04), 0 14px 32px -22px rgba(16,24,40,.18); }
    .st-ch { background:rgba(255,255,255,.6); border-radius:14px 14px 0 0; }
    .st-t th { background:#F8FAFC; }
    .st-btn, .st-f input, .st-f select, .st-f textarea { border-radius:8px; }
    .st-tabs a.on { border-radius:8px 8px 0 0; }
    .cv-left, .cv-right { border-radius:14px; }
    /* ===== Majedwali yote ya staff: maandishi madogo na mistari myembamba ===== */
    .pbar { display:inline-block; width:56px; height:6px; margin-right:7px; vertical-align:middle; background:#EAECF0; border-radius:9px; overflow:hidden; }
    .pbar i { display:block; height:100%; border-radius:9px; }
    .ptxt { font-size:11.5px; font-weight:600; }
    table.st-t { font-size:12px; }
    .st-t th { padding:7px 12px; font-size:10.5px; }
    .st-t td { padding:5px 12px; line-height:1.25; }
    .st-t td.nm { font-size:12px; font-weight:400; }
    .st-t .st-btn.sm { height:26px; padding:0 9px; font-size:11.5px; }
    .st-t .st-pill { padding:1px 8px; font-size:10.5px; }
    .st-t .st-pill.s { padding:1px 7px; }
    .st-t .st-dd > summary { width:24px; height:24px; font-size:15px; }
    .st-t .st-sub { font-size:11px; }
    /* Fomu kubwa zaidi, zikiwa katikati ya ukurasa */
    .st-center { max-width:900px; margin:0 auto; }
    .st-center .st-card { max-width:none !important; }
    .st-center .st-cb { padding:24px 30px; }
    .st-center .st-ch { padding:16px 30px; font-size:16px; }
    .st-center .st-form { gap:18px 26px; }
    .st-center .st-f label.t { font-size:13.5px; margin-bottom:6px; }
    .st-center .st-f input, .st-center .st-f select { height:44px; font-size:14.5px; }
    .st-center .st-btn { height:42px; padding:0 20px; font-size:14px; }
    .st-center .st-head h1 { font-size:24px; }
    @media (max-width:900px) { .st-shell { grid-template-columns:1fr; } .st-side { position:static; height:auto; } .st-stats { grid-template-columns:1fr 1fr; } .st-form { grid-template-columns:1fr; } .st-main { padding:18px 14px; } }
</style>
@stack('styles')
</head>
<body>
@php $uri = request()->route() ? request()->route()->getName() : ''; $staff = auth('staff')->user(); @endphp
<div class="st-top" id="stTop">@include('applicant.partials.banner')</div>
<div class="st-shell">
    <aside class="st-side">
        <div class="st-brand">@if(file_exists(public_path('images/emblem-cut.png')))<img src="{{ asset('images/emblem-cut.png') }}" alt="Careers Portal Staff - United Republic of Tanzania" title="Careers Portal Staff">@elseif(file_exists(public_path('images/emblem.png')))<img src="{{ asset('images/emblem.png') }}" alt="United Republic of Tanzania">@else<i class="bi bi-shield-fill-check" style="font-size:44px"></i>@endif</div>
        <ul class="st-menu">
            @php
                $is = fn (...$p) => collect($p)->contains(fn ($x) => str_starts_with($uri, $x));
                $admin = $staff && $staff->isAdmin();
                $manage = $staff && $staff->canManage();
                $pendingCount = $staff ? \App\Models\Applicant::where('is_active', false)->where('is_locked', false)->count() : 0;
            @endphp
            <li><a href="{{ route('staff.dashboard') }}" class="{{ $uri === 'staff.dashboard' ? 'on' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            @if($admin)<li><a href="{{ route('staff.users') }}" class="{{ $is('staff.users') ? 'on' : '' }}"><i class="bi bi-person-gear"></i> Users</a></li>@endif
            <li><a href="{{ route('staff.employers') }}" class="{{ $is('staff.employers') ? 'on' : '' }}"><i class="bi bi-building"></i> Employer</a></li>
            <li><a href="{{ route('staff.applicants') }}" class="{{ $is('staff.applicants') ? 'on' : '' }}"><i class="bi bi-people"></i> Applicant @if(($pendingCount ?? 0) > 0)<span style="margin-left:auto;padding:0 7px;font-size:11px;font-weight:700;color:#fff;background:#D92D20;border-radius:999px">{{ $pendingCount }}</span>@endif</a></li>
            <li><a href="{{ route('staff.salary-scales') }}" class="{{ $is('staff.salary-scales') ? 'on' : '' }}"><i class="bi bi-cash-coin"></i> Salary Scales</a></li>
            <li><a href="{{ route('staff.schemes') }}" class="{{ $is('staff.schemes') ? 'on' : '' }}"><i class="bi bi-diagram-3"></i> Scheme Of Service</a></li>
            <li><a href="{{ route('staff.permits') }}" class="{{ $is('staff.permits') ? 'on' : '' }}"><i class="bi bi-file-earmark-check"></i> Job Permit</a></li>
            <li><details class="st-sub-menu" {{ $is('staff.vacancies') ? 'open' : '' }}>
                <summary><i class="bi bi-megaphone"></i> Adverts <i class="bi bi-chevron-down caret"></i></summary>
                <a href="{{ route('staff.vacancies') }}" class="{{ $uri === 'staff.vacancies' || $uri === 'staff.vacancies.show' ? 'on' : '' }}">All adverts</a>
                @if($manage)<a href="{{ route('staff.vacancies.create') }}" class="{{ $uri === 'staff.vacancies.create' ? 'on' : '' }}">New advert</a>@endif
            </details></li>
            <li><a href="{{ route('staff.archive') }}" class="{{ $is('staff.archive') ? 'on' : '' }}"><i class="bi bi-archive"></i> Archive</a></li>
            <li><a href="{{ route('staff.attachments') }}" class="{{ $is('staff.attachments') ? 'on' : '' }}"><i class="bi bi-paperclip"></i> Attachments</a></li>
            <li><details class="st-sub-menu" {{ $is('staff.settings') ? 'open' : '' }}>
                <summary><i class="bi bi-gear"></i> Settings <i class="bi bi-chevron-down caret"></i></summary>
                @if($admin)
                    @php
                        $lk = request()->route('key');
                        $settingsLinks = [
                            ['Manage Facilities', route('staff.settings.lookup', 'facilities'), $lk === 'facilities'],
                            ['Academic Levels', route('staff.settings.levels'), $uri === 'staff.settings.levels'],
                            ['Academic Programmes', route('staff.settings.lookup', 'academic-programmes'), $lk === 'academic-programmes'],
                            ['Academic Institutions', route('staff.settings.lookup', 'academic-institutions'), $lk === 'academic-institutions'],
                            ['Manage Institution/Programme', route('staff.settings.institution-programmes'), $uri === 'staff.settings.institution-programmes'],
                            ['NACTVET Programmes', route('staff.settings.lookup', 'nactvet-programmes'), $lk === 'nactvet-programmes'],
                            ['Manage Disabilities', route('staff.settings.lookup', 'disabilities'), $lk === 'disabilities'],
                            ['Computer Skills', route('staff.settings.lookup', 'computer-skills'), $lk === 'computer-skills'],
                            ['Fraud Flag Reject Reasons', route('staff.settings.lookup', 'fraud-reject-reasons'), $lk === 'fraud-reject-reasons'],
                            ['Account Status Reasons', route('staff.settings.lookup', 'account-status-reasons'), $lk === 'account-status-reasons'],
                            ['Manage Shortlist Comments', route('staff.settings.lookup', 'shortlist-comments'), $lk === 'shortlist-comments'],
                            ['Professional Courses', route('staff.settings.lookup', 'professional-courses'), $lk === 'professional-courses'],
                            ['Professional Institutions', route('staff.settings.lookup', 'professional-institutions'), $lk === 'professional-institutions'],
                            ['Manage Categories', route('staff.settings.categories'), $uri === 'staff.settings.categories'],
                        ];
                    @endphp
                    @foreach($settingsLinks as [$lbl, $url, $on])<a href="{{ $url }}" class="{{ $on ? 'on' : '' }}">{{ $lbl }}</a>@endforeach
                    <details class="st-sub-menu st-sub2" {{ $uri === 'staff.settings.document' ? 'open' : '' }}>
                        <summary style="padding-left:41px;font-size:13.5px">Document Contents <i class="bi bi-chevron-down caret"></i></summary>
                        <a href="{{ route('staff.settings.document', 'declaration') }}" class="{{ request()->route('key') === 'declaration' ? 'on' : '' }}" style="padding-left:56px">Declaration statement</a>
                        <a href="{{ route('staff.settings.document', 'activation_notice') }}" class="{{ request()->route('key') === 'activation_notice' ? 'on' : '' }}" style="padding-left:56px">Activation notice</a>
                    </details>
                @endif
            </details></li>
            <li><a href="{{ route('staff.reports') }}" class="{{ $is('staff.reports') ? 'on' : '' }}"><i class="bi bi-bar-chart-line"></i> Reports</a></li>
            <li><a href="{{ route('staff.tools') }}" class="{{ $is('staff.tools') ? 'on' : '' }}"><i class="bi bi-tools"></i> Tools</a></li>
        </ul>

    </aside>

    <main class="st-main">
        <div class="st-topbar">
            <details class="st-dd st-me"><summary><i class="bi bi-person-circle"></i> <span>{{ $staff->email ?? '' }}</span> <i class="bi bi-chevron-down" style="font-size:11px"></i></summary>
                <div class="st-pop">
                    <div style="padding:8px 16px 6px"><b style="font-size:13px">{{ $staff->name ?? '' }}</b><div class="st-sub">{{ $staff->role_label ?? '' }}</div></div><hr>
                    <a href="{{ route('staff.account') }}"><i class="bi bi-person-gear"></i> Profile</a>
                    <form method="POST" action="{{ route('staff.logout') }}">@csrf<button type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
                </div>
            </details>
        </div>
        @if(session('reset_link'))
            @php $rl = session('reset_link'); @endphp
            <div class="st-otp rl {{ $rl['sent'] ? '' : 'bad' }}">
                <b><i class="bi bi-envelope-check-fill"></i> {{ $rl['sent'] ? 'Reset link sent to ' . $rl['email'] : 'The email could not be sent to ' . $rl['email'] }}</b>
                @if($rl['url'])
                    <div style="display:flex;gap:8px;align-items:center;margin:8px 0 4px;flex-wrap:wrap">
                        <input type="text" id="rlUrl" class="rl-url" readonly value="{{ $rl['url'] }}" onclick="this.select()">
                        <button type="button" class="st-btn ghost sm" onclick="navigator.clipboard && navigator.clipboard.writeText(document.getElementById('rlUrl').value); this.textContent='Copied'">Copy link</button>
                    </div>
                @endif
                <div class="st-sub">{{ $rl['sent'] ? 'The applicant can set a new password with this link. It works for 60 minutes.' : 'Check the mail settings (.env), or copy the link above and send it to the applicant yourself.' }}@if($rl['url']) Test link shown because the app is running locally.@endif</div>
            </div>
        @endif
        @if(session('otp'))
            @php $otp = session('otp'); @endphp
            <div class="st-otp slim rl">
                <div class="row1">
                    <b><i class="bi bi-key-fill"></i> Temporary password for {{ $otp['name'] ?: $otp['email'] }}:</b>
                    <span class="code" id="otpCode">{{ $otp['password'] }}</span>
                    <button type="button" class="st-btn ghost sm" onclick="navigator.clipboard && navigator.clipboard.writeText(document.getElementById('otpCode').textContent); this.textContent='Copied'">Copy</button>
                </div>
                <div class="st-sub">Give this password to the applicant now. It is shown only once and replaces the old password. Ask them to change it after signing in.</div>
            </div>
        @endif
        @if(session('success'))<div class="st-alert ok"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span></div>@endif
        @if(session('error'))<div class="st-alert"><i class="bi bi-exclamation-circle-fill"></i><span>{{ session('error') }}</span></div>@endif
        @if($errors->any())
            <div class="st-alert"><i class="bi bi-exclamation-circle-fill"></i>
                <div><b>Please fix the following:</b><ul style="margin:4px 0 0;padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
        @endif

        @yield('content')
    </main>
</div>
<script>(function(){var t=document.getElementById('stTop');function set(){if(t){document.documentElement.style.setProperty('--st-top-h',t.offsetHeight+'px');}}set();window.addEventListener('resize',set);window.addEventListener('load',set);var im=t&&t.querySelector('img');if(im){im.addEventListener('load',set);}})();</script>
<script>document.addEventListener('click',function(e){document.querySelectorAll('details.st-dd[open]').forEach(function(d){if(!d.contains(e.target))d.removeAttribute('open');});});</script>
<div class="dm-back" id="docModal" hidden>
    <div class="dm-box">
        <div class="dm-head"><h2 id="docTitle">Document</h2>
            <a href="#" id="docDownload" download><i class="bi bi-download"></i> Download</a>
            <button type="button" id="docClose"><i class="bi bi-x-lg"></i> Close</button></div>
        <iframe class="dm-frame" id="docFrame" title="Document" src="about:blank"></iframe>
    </div>
</div>
<script>
(function () {
    // Faili/barua za waombaji zinafunguka kwenye dirisha la ndani (popup), si tab nyingine
    var modal = document.getElementById('docModal'), frame = document.getElementById('docFrame');
    function close() { modal.hidden = true; frame.src = 'about:blank'; }
    document.getElementById('docClose').onclick = close;
    modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) close(); });
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href]');
        if (!a) return;
        var h = a.getAttribute('href') || '';
        if (h.indexOf('/staff/files') === -1 && !/\/staff\/applications\/\d+\/letter/.test(h)) return;
        e.preventDefault();
        document.getElementById('docTitle').textContent = (a.textContent || 'Document').trim() || 'Document';
        document.getElementById('docDownload').href = h;
        frame.src = h; modal.hidden = false;
    });
})();
</script>
@stack('scripts')
</body>
</html>
