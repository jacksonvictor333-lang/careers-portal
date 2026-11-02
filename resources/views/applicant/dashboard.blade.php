@extends('applicant.layout')

@section('title', 'My Dashboard')

@push('styles')
<style>
    .db-hello { display: flex; align-items: center; gap: 8px; margin: 0 0 14px; font-size: 15px; font-weight: 700; }
    .db-hello i { color: #F79009; }

    /* ---- Takwimu: kisanduku kimoja chenye sehemu nne ---- */
    .db-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); margin-bottom: 18px; overflow: hidden;
                background: linear-gradient(90deg, #F3F8EC 0%, #fff 22%, #fff 78%, #EEF6F0 100%);
                border: 1px solid var(--line); border-radius: 6px; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
    .db-stat { display: block; padding: 22px 12px; text-align: center; text-decoration: none; color: var(--ink);
               border-left: 1px solid var(--line); transition: background .15s; }
    .db-stat:first-child { border-left: 0; }
    .db-stat:hover { background: rgba(29,78,137,.05); color: var(--ink); }
    .db-stat-lbl { font-size: 13px; color: var(--muted); }
    .db-stat-num { margin-top: 6px; font-size: 32px; font-weight: 800; color: var(--navy); line-height: 1.1; }

    .db-grid { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 18px; align-items: start; }
    .db-panel { background: #fff; border: 1px solid var(--line); border-radius: 6px; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
    .db-panel + .db-panel { margin-top: 18px; }
    .db-ph { display: flex; align-items: center; gap: 10px; padding: 16px 22px; font-size: 16px; font-weight: 700; border-bottom: 1px solid var(--line); }
    .db-ph i { font-size: 18px; color: var(--navy); }
    .db-pb { padding: 18px 22px; }

    .db-tips { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .db-tip { padding: 16px 18px; border-radius: 6px; background: linear-gradient(135deg, #EAF2FD, #F4F8FE); border: 1px solid #D5E4F8; }
    .db-tip h3 { display: flex; align-items: center; gap: 10px; margin: 0 0 8px; font-size: 14px; font-weight: 600; }
    .db-tip h3 span { display: grid; place-items: center; width: 26px; height: 26px; border-radius: 50%; font-size: 12px; color: #fff; background: var(--navy); }
    .db-tip p { margin: 0 0 0 36px; font-size: 13px; line-height: 1.55; color: #374151; }
    .db-feedback { display: flex; align-items: center; gap: 10px; margin: 16px 0 0; font-size: 13px; color: #374151; }
    .db-feedback a { color: var(--navy); font-weight: 600; }

    .db-pct { font-size: 20px; font-weight: 800; color: var(--navy); }
    .db-bar { height: 8px; margin: 8px 0 6px; border-radius: 999px; background: #E5E7EB; overflow: hidden; }
    .db-bar > i { display: block; height: 100%; border-radius: 999px; background: linear-gradient(90deg, #12B76A, #1D4E89); }
    .db-hint { font-size: 12px; color: var(--muted); }
    .db-todo { margin: 12px 0 0; padding: 0; list-style: none; }
    .db-todo li { display: flex; align-items: center; gap: 8px; padding: 5px 0; font-size: 13px; }
    .db-todo li.done { color: var(--muted); }
    .db-todo li.done i { color: #12B76A; }
    .db-todo li:not(.done) i { color: #D92D20; }
    .db-todo a { color: inherit; text-decoration: none; }
    .db-todo li:not(.done) a { color: var(--navy); font-weight: 600; }
    .db-todo a:hover { text-decoration: underline; }

    .db-alert-btn { display: block; width: 100%; padding: 11px 14px; border: 0; border-radius: 6px; cursor: pointer;
                    font: inherit; font-size: 13.5px; font-weight: 600; text-align: center; text-decoration: none; color: #fff; background: var(--navy); }
    .db-alert-btn:hover { opacity: .92; color: #fff; }

    @media (max-width: 1000px) { .db-grid { grid-template-columns: 1fr; } .db-tips { grid-template-columns: 1fr; } }
    @media (max-width: 640px)  { .db-stats { grid-template-columns: 1fr 1fr; } .db-stat:nth-child(3) { border-left: 0; } .db-stat:nth-child(n+3) { border-top: 1px solid var(--line); } }
</style>
@endpush

@section('content')
@php
    $myApps  = Route::has('applicant.my-applications') ? route('applicant.my-applications') : '#';
    $missing = collect($checklist)->where('done', false)->count();
@endphp

<div class="ap-wrap wide">

    @if(session('success'))
        <div class="ap-alert ok" style="margin:0 0 16px">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="db-hello">{{ $greeting }} {{ strtoupper($applicant->first_name ?: $applicant->full_name) }} <i class="bi bi-brightness-high-fill"></i></div>

    {{-- ===== TAKWIMU ===== --}}
    <div class="db-stats">
        <a href="{{ $myApps }}" class="db-stat">
            <div class="db-stat-lbl">Adverts Applied</div>
            <div class="db-stat-num">{{ number_format($stats['applications']) }}</div>
        </a>
        <a href="{{ $myApps }}" class="db-stat">
            <div class="db-stat-lbl">Shortlisted</div>
            <div class="db-stat-num">{{ number_format($stats['shortlisted']) }}</div>
        </a>
        <a href="{{ $myApps }}" class="db-stat">
            <div class="db-stat-lbl">Interview Attended</div>
            <div class="db-stat-num">{{ number_format($stats['interviews']) }}</div>
        </a>
        <a href="{{ $myApps }}" class="db-stat">
            <div class="db-stat-lbl">Oral Interview Attended</div>
            <div class="db-stat-num">{{ number_format($stats['orals']) }}</div>
        </a>
    </div>

    <div class="db-grid">

        {{-- ===== KUSHOTO: MAELEKEZO ===== --}}
        <section class="db-panel">
            <div class="db-ph"><i class="bi bi-lightbulb"></i> APPLICATION TIPS</div>
            <div class="db-pb">
                <div class="db-tips">
                    <div class="db-tip">
                        <h3><span>1</span> Complete your Profile</h3>
                        <p>Please complete ALL of the required fields of the forms found in the left-hand menu of this page.</p>
                    </div>
                    <div class="db-tip">
                        <h3><span>2</span> Apply to a vacancy online</h3>
                        <p>Click on the Vacancies tab at the top of the page. Select a vacancy of interest. Read the job requirements thoroughly. Click 'Apply' for this vacancy.</p>
                    </div>
                </div>
                <div class="db-feedback">
                    <i class="bi bi-chat-square-text"></i>
                    <span>If you are experiencing additional problems, please give us feedback
                        <a href="{{ Route::has('feedback') ? route('feedback') : '#' }}">here</a>.</span>
                </div>
            </div>
        </section>

        {{-- ===== KULIA: WASIFU + ALERTS ===== --}}
        <aside>
            <section class="db-panel">
                <div class="db-ph"><i class="bi bi-person-circle"></i> Profile Completeness</div>
                <div class="db-pb">
                    <div class="db-pct">{{ $completeness }}%</div>
                    <div class="db-bar" role="progressbar" aria-valuenow="{{ $completeness }}" aria-valuemin="0" aria-valuemax="100"><i style="width: {{ $completeness }}%"></i></div>
                    <div class="db-hint">
                        @if($missing === 0)
                            Your profile is complete. Good luck with your applications!
                        @else
                            Complete your profile to improve your chances of being hired.
                        @endif
                    </div>

                    <ul class="db-todo">
                        @foreach($checklist as $item)
                            <li class="{{ $item['done'] ? 'done' : '' }}">
                                <i class="bi {{ $item['done'] ? 'bi-check-circle-fill' : 'bi-circle' }}"></i>
                                <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}">{{ $item['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            <section class="db-panel">
                <div class="db-ph"><i class="bi bi-bell"></i> Job Alerts</div>
                <div class="db-pb">
                    <p style="margin:0 0 14px; font-size:13px; line-height:1.55; color:#374151">
                        Stay updated with the latest job opportunities! Subscribe to receive job alerts directly in your email.
                    </p>
                    <a class="db-alert-btn" href="{{ Route::has('applicant.job-alerts') ? route('applicant.job-alerts') : '#' }}">Subscribe to Job Alerts</a>
                </div>
            </section>
        </aside>

    </div>
</div>

@endsection
