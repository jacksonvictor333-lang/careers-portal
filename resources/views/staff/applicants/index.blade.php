@extends('staff.layout')
@section('title', 'Applicants')

@section('content')
@php
    $staff = auth('staff')->user();
    $manage = $staff->canManage(); $admin = $staff->isAdmin();
    $tabs = ['list' => ['Applicant List', 'bi-people'], 'pending' => ['Pending Activation', 'bi-hourglass-split'], 'locked' => ['Locked Applicants', 'bi-lock'], 'requests' => ['Lock/Unlock Requests', 'bi-clipboard-check'], 'nida' => ['NIDA Profiling', 'bi-person-vcard']];
    $page = isset($requests) ? $requests : $applicants;
@endphp

<div class="st-tabs">
    @foreach($tabs as $key => [$label, $icon])
        <a href="{{ route('staff.applicants', ['tab' => $key]) }}" class="{{ $tab === $key ? 'on' : '' }}"><i class="bi {{ $icon }}"></i> {{ $label }}
            @if($key === 'requests' && $counts['requests'] > 0)<span class="n">{{ $counts['requests'] }}</span>@endif
            @if($key === 'pending' && $counts['pending'] > 0)<span class="n">{{ $counts['pending'] }}</span>@endif
            @if($key === 'locked' && $counts['locked'] > 0)<span class="n">{{ $counts['locked'] }}</span>@endif</a>
    @endforeach
</div>

<form class="st-search" id="liveForm" method="GET" action="{{ route('staff.applicants') }}" style="margin-bottom:0">
    <input type="hidden" name="tab" value="{{ $tab }}"><input type="hidden" name="per" value="{{ $per }}">
    <input type="text" name="q" id="liveQ" value="{{ $q }}" placeholder="Search by name, initials (e.g. J V V), email or phone..." autocomplete="off" autofocus style="max-width:none;width:100%;height:42px;font-size:14px;padding:0 14px">
</form>

<div id="applicantsBox">
@include('staff.applicants._results')
</div>

@if($admin)
<div class="dm-back" id="emailModal" hidden>
    <form class="dm-form st-f" method="POST" id="emailForm" action="">
        @csrf @method('PUT')
        <h2>Change email</h2><p id="emailWho"></p>
        <div style="margin-bottom:12px"><label class="t">New email <span class="req">*</span></label><input type="email" name="email" id="emailNew" required autocomplete="off"></div>
        <div style="margin-bottom:16px"><label class="t">Confirm new email <span class="req">*</span></label><input type="email" name="email_confirmation" id="emailConf" required autocomplete="off"><div class="st-err" id="emailErr" hidden>The two email addresses do not match.</div></div>
        <div style="display:flex;gap:8px;justify-content:flex-end"><button type="button" class="st-btn ghost" onclick="closeEmail()">Cancel</button><button type="submit" class="st-btn"><i class="bi bi-check2"></i> Save</button></div>
    </form>
</div>
@push('scripts')
<script>
    var emailBase = @js(url('staff/applicants'));
    function openEmail(id, current, name) {
        document.getElementById('emailForm').action = emailBase + '/' + id + '/email';
        document.getElementById('emailWho').textContent = (name || 'Applicant') + ' - current: ' + current;
        document.getElementById('emailNew').value = ''; document.getElementById('emailConf').value = '';
        document.getElementById('emailErr').hidden = true;
        document.getElementById('emailModal').hidden = false; document.getElementById('emailNew').focus();
        document.querySelectorAll('details.st-dd[open]').forEach(function (d) { d.removeAttribute('open'); });
    }
    function closeEmail() { document.getElementById('emailModal').hidden = true; }
    document.getElementById('emailForm').addEventListener('submit', function (e) {
        var ok = document.getElementById('emailNew').value.trim().toLowerCase() === document.getElementById('emailConf').value.trim().toLowerCase();
        document.getElementById('emailErr').hidden = ok; if (!ok) e.preventDefault();
    });
    document.getElementById('emailModal').addEventListener('click', function (e) { if (e.target.id === 'emailModal') closeEmail(); });
</script>
@endpush
@endif
@push('scripts')
<script>
    // Utafutaji wa moja kwa moja: unaanza kuandika tu, orodha inasasishwa (bila kupakia ukurasa upya)
    (function () {
        var q = document.getElementById('liveQ'), box = document.getElementById('applicantsBox'), form = document.getElementById('liveForm'), t, ctl;
        function load(url) {
            if (ctl) { ctl.abort(); } ctl = new AbortController();
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: ctl.signal })
                .then(function (r) { return r.text(); })
                .then(function (html) { box.innerHTML = html; history.replaceState(null, '', url.replace(/[?&]ajax=1/, '')); })
                .catch(function () {});
        }
        function run() {
            var p = new URLSearchParams(new FormData(form)); p.set('ajax', '1');
            load(form.action + '?' + p.toString());
        }
        q.addEventListener('input', function () { clearTimeout(t); t = setTimeout(run, 180); });
        form.addEventListener('submit', function (e) { e.preventDefault(); run(); });
        box.addEventListener('click', function (e) {
            var a = e.target.closest('.st-pager a'); if (!a) { return; }
            e.preventDefault(); load(a.href + (a.href.indexOf('?') === -1 ? '?' : '&') + 'ajax=1');
        });
        q.focus(); var v = q.value; q.value = ''; q.value = v;
    })();
</script>
@endpush
@endsection
