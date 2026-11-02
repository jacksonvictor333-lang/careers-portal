@php $cur = $cur ?? 'cv'; @endphp
@push('styles')
<style>
    .cv-bar { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:16px; }
    .cv-bar h1 { margin:0; font-size:17px; font-weight:700; letter-spacing:.02em; text-transform:uppercase; }
    .cv-crumb { margin-top:4px; font-size:13px; color:var(--muted); }
    .cv-crumb a { text-decoration:none; }
    .cv-btns { display:flex; gap:6px; flex-wrap:wrap; }
    .cv-btns a { display:inline-flex; align-items:center; gap:7px; height:34px; padding:0 13px; font-size:12.5px; font-weight:600; color:#fff; text-decoration:none; background:#127B9C; border-radius:3px; }
    .cv-btns a:hover { background:#0E6580; }
    .cv-btns a.on { background:var(--navy-d); }
    .cv-btns a.back { background:var(--navy); }
    .cv-btns .bd { padding:0 7px; font-size:11px; background:#fff; color:#0E6580; border-radius:999px; }
    .cv-grid { display:grid; grid-template-columns:minmax(300px,380px) minmax(0,1fr); gap:24px; align-items:start; }
    .cv-left, .cv-right { background:#fff; border:1px solid var(--line); border-top:2px solid #374151; }
    .cv-photo { padding:22px 0 14px; text-align:center; }
    .cv-photo .im { width:140px; height:140px; margin:0 auto; overflow:hidden; display:grid; place-items:center; font-size:42px; font-weight:700; color:#fff; background:var(--navy); border-radius:50%; }
    .cv-photo .im img { width:100%; height:100%; object-fit:cover; }
    .cv-photo h2 { margin:14px 0 0; font-size:14px; font-weight:600; text-transform:uppercase; }
    .cv-row { display:flex; justify-content:space-between; gap:14px; padding:9px 16px; font-size:13px; border-top:1px solid var(--line); }
    .cv-row b { font-weight:600; white-space:nowrap; }
    .cv-row span { text-align:right; color:#374151; word-break:break-word; }
    .cv-ver { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 16px; font-size:13px; border-top:1px solid var(--line); }
    .cv-ver b { display:flex; align-items:center; gap:8px; font-weight:600; }
    .cv-ver .chk { display:inline-flex; align-items:center; gap:6px; height:30px; padding:0 12px; font-size:12px; color:#98A2B3; background:#F9FAFB; border:1px solid #E4E7EC; border-radius:3px; cursor:not-allowed; }
    .cv-sec { padding:14px 16px 4px; }
    .cv-sec h3 { margin:0 0 10px; padding:0 0 8px 10px; font-size:14.5px; font-weight:700; letter-spacing:.02em; text-transform:uppercase; color:#127B9C; border-bottom:1px solid #127B9C; border-left:3px solid #127B9C; }
    .cv-sec table { width:100%; margin-bottom:12px; border-collapse:collapse; font-size:13px; }
    .cv-sec th { padding:9px 12px; text-align:left; font-size:12.5px; font-weight:700; background:#fff; border:1px solid var(--line); }
    .cv-sec td { padding:9px 12px; vertical-align:middle; border:1px solid var(--line); }
    .cv-tag { display:inline-flex; align-items:center; gap:6px; margin:2px 4px 2px 0; padding:3px 9px; font-size:12px; font-weight:600; color:#fff; border-radius:3px; background:#127B9C; }
    .cv-tag.g { background:#079455; } .cv-tag.l { color:#374151; font-weight:500; background:#F2F4F7; }
    .cv-empty { padding:10px 12px; margin-bottom:12px; font-size:13px; font-style:italic; color:var(--muted); border:1px solid var(--line); }
    .cv-dh { border-top:1px solid var(--line); padding:0 16px 6px; }
    .cv-dh summary { display:flex; align-items:center; gap:8px; padding:12px 0; font-size:13px; font-weight:600; cursor:pointer; list-style:none; }
    .cv-dh summary::-webkit-details-marker { display:none; }
    .cv-dh summary .cnt { padding:0 8px; font-size:11px; color:#175CD3; background:#EFF8FF; border:1px solid #B2DDFF; border-radius:999px; }
    .cv-dh summary .caret { margin-left:auto; font-size:12px; transition:transform .15s; }
    .cv-dh[open] summary .caret { transform:rotate(180deg); }
    .cv-dh .lv { margin:10px 0 4px; padding:4px 8px; font-size:12px; font-weight:700; background:#F3F4F6; border-radius:3px; }
    .cv-dh .it { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:6px 4px; font-size:12.5px; border-bottom:1px solid #F0F1F3; }

    /* ---- Marekebisho ya muonekano (mpangilio ule ule) ---- */
    .cv-left, .cv-right { border:1px solid var(--line); border-top:3px solid var(--navy); border-radius:8px; box-shadow:0 1px 2px rgba(16,24,40,.05), 0 8px 24px -16px rgba(16,24,40,.18); overflow:hidden; }
    .cv-photo { background:linear-gradient(180deg,#F3F8FD,#fff); }
    .cv-photo .im { border:4px solid #fff; box-shadow:0 0 0 2px #C9DBEE, 0 10px 24px -10px rgba(29,78,137,.5); }
    .cv-row b { color:#344054; } .cv-row span { color:#101828; font-weight:500; }
    .cv-row:hover { background:#F9FAFB; }
    .cv-sec h3 { display:flex; align-items:center; gap:8px; border-bottom:2px solid #D6E6EE; border-left:4px solid #127B9C; border-radius:2px; }
    .cv-sec table { border-collapse:separate; border-spacing:0; border:1px solid var(--line); border-radius:6px; overflow:hidden; }
    .cv-sec th { background:#F8FAFC; border:0; border-bottom:1px solid var(--line); color:#344054; }
    .cv-sec td { border:0; border-bottom:1px solid #F0F1F3; }
    .cv-sec tr:last-child td { border-bottom:0; }
    .cv-sec tbody tr:hover td { background:#FAFCFE; }
    .cv-tag { border-radius:4px; }
    .cv-btns a { border-radius:5px; box-shadow:0 1px 2px rgba(16,24,40,.12); }
    .cv-bar h1 { letter-spacing:.04em; }
    .cv-del { display:inline-grid; place-items:center; width:24px; height:24px; margin-left:2px; font-size:12px; color:#B42318; background:#FEF3F2; border:1px solid #FECDCA; border-radius:4px; cursor:pointer; }
    .cv-del:hover { background:#FEE4E2; }
    @media (max-width:1000px) { .cv-grid { grid-template-columns:1fr; } }
</style>
@endpush
<div class="cv-bar">
    <div><h1>{{ $title ?? 'Applicant CV' }}</h1>
        <div class="cv-crumb"><i class="bi bi-house"></i> <a href="{{ route('staff.dashboard') }}">Dashboard</a> / <a href="{{ route('staff.applicants') }}">Applicant</a> / {{ $title ?? 'Applicant CV' }} &middot; {{ $applicant->full_name ?: $applicant->email }}</div></div>
    <div class="cv-btns">
        <a href="{{ route('staff.applicants.show', $applicant->id) }}" class="{{ $cur === 'cv' ? 'on' : '' }}"><i class="bi bi-person-lines-fill"></i> Applicant CV</a>
        <a href="{{ route('staff.applicants.check', $applicant->id) }}" class="{{ $cur === 'check' ? 'on' : '' }}"><i class="bi bi-plus-circle"></i> Application Check</a>
        <a href="{{ route('staff.applicants.history', $applicant->id) }}" class="{{ $cur === 'history' ? 'on' : '' }}"><i class="bi bi-list"></i> Account History</a>
        <a href="{{ route('staff.applicants.applications', $applicant->id) }}" class="{{ $cur === 'apps' ? 'on' : '' }}"><i class="bi bi-card-list"></i> Applications <span class="bd">{{ $applicant->job_applications_count ?? 0 }}</span></a>
        <a href="{{ route('staff.applicants') }}" class="back"><i class="bi bi-arrow-left-circle"></i> Back</a>
    </div>
</div>
