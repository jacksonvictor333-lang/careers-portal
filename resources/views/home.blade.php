@extends('applicant.layout')
@section('title', 'Home')

@push('styles')
<style>
    /* Ukurasa unajaza upana mzima (unapuuza nafasi ya ndani ya .main) */
    .hm-bleed { margin: -32px -16px -48px; }

    .hm-hero {
        position: relative; display: grid; place-items: center; min-height: 460px; padding: 70px 20px 80px; text-align: center; overflow: hidden;
        background:
            radial-gradient(420px 250px at 17% 16%, rgba(29,78,137,.26), transparent 70%),
            radial-gradient(520px 300px at 37% 36%, rgba(29,78,137,.22), transparent 70%),
            radial-gradient(380px 250px at 62% 18%, rgba(29,78,137,.20), transparent 70%),
            radial-gradient(320px 280px at 93% 24%, rgba(18,183,106,.20), transparent 70%),
            radial-gradient(280px 240px at 6% 88%, rgba(252,209,22,.20), transparent 70%),
            #FAFBFD;
    }
    .hm-flag { display: inline-flex; gap: 4px; margin-bottom: 20px; }
    .hm-flag i { display: block; width: 6px; height: 36px; border-radius: 3px; }
    .hm-flag i:nth-child(1) { background: #1EB53A; }
    .hm-flag i:nth-child(2) { background: #FCD116; }
    .hm-flag i:nth-child(3) { background: #0B1320; }
    .hm-flag i:nth-child(4) { background: #00A3DD; }
    .hm-hero h1 { margin: 0 0 14px; font-size: 48px; line-height: 1.1; font-weight: 800; letter-spacing: -.02em; color: #0B1320; }
    .hm-hero h1 span { color: var(--navy); }
    .hm-hero p { max-width: 700px; margin: 0 auto 28px; font-size: 17px; line-height: 1.6; color: #4B5563; }
    .hm-actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 14px; }
    .hm-btn { display: inline-flex; align-items: center; gap: 8px; height: 48px; padding: 0 24px; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 6px; border: 1px solid var(--navy); }
    .hm-btn.fill { color: #fff; background: var(--navy); box-shadow: 0 6px 16px -6px rgba(29,78,137,.6); }
    .hm-btn.fill:hover { background: #163C6B; color: #fff; }
    .hm-btn.line { color: var(--navy); background: #fff; }
    .hm-btn.line:hover { background: #F3F8FD; color: var(--navy); }

    .hm-tips { padding: 36px 20px 40px; background: #fff; border-top: 1px solid var(--line); }
    .hm-tips-in { max-width: 1000px; margin: 0 auto; }
    .hm-tips h2 { margin: 0 0 18px; font-size: 22px; font-weight: 500; text-align: center; text-transform: uppercase; letter-spacing: .02em; }
    .hm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .hm-tip { padding: 16px 20px; border-radius: 6px; background: linear-gradient(135deg, #E6F0FD, #F1F6FD); }
    .hm-tip h3 { display: flex; align-items: center; gap: 10px; margin: 0 0 8px; font-size: 15px; font-weight: 600; }
    .hm-tip h3 b { display: grid; place-items: center; width: 24px; height: 24px; font-size: 12px; color: #fff; background: var(--navy); border-radius: 50%; }
    .hm-tip p { display: flex; gap: 10px; margin: 0; font-size: 14px; line-height: 1.6; color: #374151; }
    .hm-tip p i { flex-shrink: 0; margin-top: 2px; color: var(--navy); font-size: 17px; }
    .hm-feedback { display: flex; align-items: center; gap: 10px; margin: 16px 0 0; font-size: 14px; color: #374151; }
    .hm-feedback a { color: var(--navy); font-weight: 600; }

    @media (max-width: 760px) { .hm-hero h1 { font-size: 34px; } .hm-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="hm-bleed">

    <section class="hm-hero">
        <div>
            <div class="hm-flag" aria-hidden="true"><i></i><i></i><i></i><i></i></div>
            <h1>Welcome to <span>{{ config('portal.name', 'Careers Portal') }}</span></h1>
            <p>{{ config('portal.name', 'Careers Portal') }} is an online platform designed to enable job seekers to apply for vacant positions across various government institutions and agencies.</p>

            <div class="hm-actions">
                <a class="hm-btn fill" href="{{ Route::has('vacancies') ? route('vacancies') : '#' }}"><i class="bi bi-search"></i> Browse Vacancies</a>
                <a class="hm-btn line" href="{{ Route::has('applicant.register') ? route('applicant.register') : route('applicant.login') }}"><i class="bi bi-person-plus"></i> Create Account</a>
            </div>
        </div>
    </section>

    <section class="hm-tips">
        <div class="hm-tips-in">
            <h2>Application Tips</h2>
            <div class="hm-grid">
                <div class="hm-tip">
                    <h3><b>1</b> Complete your Profile</h3>
                    <p><i class="bi bi-person-lines-fill"></i><span>After logging into your account, please complete ALL of the required fields in the forms found in the left-hand menu.</span></p>
                </div>
                <div class="hm-tip">
                    <h3><b>2</b> Apply to a vacancy online</h3>
                    <p><i class="bi bi-card-checklist"></i><span>Click on the vacancies tab at the top of the page. Select a vacancy of interest. Read the job requirements thoroughly. Click 'apply' for this vacancy.</span></p>
                </div>
            </div>
            <p class="hm-feedback"><i class="bi bi-chat-square-text"></i>
                <span>If you are experiencing additional problems, please give us feedback <a href="{{ Route::has('feedback') ? route('feedback') : '#' }}">here</a>.</span></p>
        </div>
    </section>

</div>
@endsection
