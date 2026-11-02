@extends('staff.layout')
@section('title', 'Update application')

@section('content')
@php
    $t = fn ($v) => $v ? substr((string) $v, 0, 5) : '';
    $d = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : '';
@endphp
<div class="st-head">
    <div><h1>Update application</h1>
        <p>{{ optional($app->applicant)->full_name }} &middot; {{ $app->vacancy_title }} &middot; {{ $app->reference_number }}</p></div>
    <a class="st-btn ghost" href="{{ route('staff.applicants.show', $app->applicant_id) }}">Back to applicant</a>
</div>

<section class="st-card"><div class="st-ch">Status and interview details</div><div class="st-cb">
<form method="POST" action="{{ route('staff.applications.update', $app->id) }}">
    @csrf @method('PUT')
    <div class="st-form st-f">
        <div><label class="t">Status <span class="req">*</span></label>
            <select name="status">
                @foreach($statuses as $s)<option value="{{ $s }}" {{ old('status', $app->status) === $s ? 'selected' : '' }}>{{ $s }}</option>@endforeach
            </select></div>
        <div><label class="t">Interview type</label>
            <select name="interview_type"><option value="">— none —</option>
                @foreach($types as $ty)<option value="{{ $ty }}" {{ old('interview_type', $app->interview_type) === $ty ? 'selected' : '' }}>{{ $ty }}</option>@endforeach
            </select><div class="st-hint">Required for Shortlisted, Interview and Oral Interview.</div></div>
        <div class="full"><label class="t">Short reason (shown to the applicant)</label>
            @if(! empty($comments))
                <select onchange="if(this.value){document.getElementById('status_reason').value=this.value}" style="margin-bottom:6px"><option value="">Choose a ready-made comment...</option>
                    @foreach($comments as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach</select>
            @endif
            <input type="text" id="status_reason" name="status_reason" maxlength="200" value="{{ old('status_reason', $app->status_reason) }}" placeholder="e.g. Shortlisted"></div>
        <div><label class="t">Interview number</label>
            <input type="text" name="interview_number" value="{{ old('interview_number', $app->interview_number) }}" placeholder="Auto-generated if empty">
            <div class="st-hint">Example: NITN/26/12464/0015</div></div>
        <div></div>
        <div><label class="t">Interview date</label><input type="date" name="interview_at" value="{{ old('interview_at', $d($app->interview_at)) }}"></div>
        <div><label class="t">Interview time</label><input type="time" name="interview_time" value="{{ old('interview_time', $t($app->interview_time)) }}"></div>
        <div class="full"><label class="t">Venue</label><input type="text" name="interview_venue" value="{{ old('interview_venue', $app->interview_venue) }}"></div>
        <div><label class="t">Oral interview date (if separate)</label><input type="date" name="oral_at" value="{{ old('oral_at', $d($app->oral_at)) }}"></div>
        <div><label class="t">Oral interview time</label><input type="time" name="oral_time" value="{{ old('oral_time', $t($app->oral_time)) }}"></div>
        <div class="full"><label class="t">Oral interview venue</label><input type="text" name="oral_venue" value="{{ old('oral_venue', $app->oral_venue) }}"></div>
    </div>
    <div class="st-hint" style="margin-top:10px">"Not Selected" applicants do not see any timetable; clear the dates if they were set earlier.</div>
    <div style="margin-top:16px"><button class="st-btn" type="submit"><i class="bi bi-check2"></i> Save</button></div>
</form>
</div></section>

<section class="st-card"><div class="st-ch">Status history</div><div class="st-wrap"><table class="st-t">
    <thead><tr><th>When</th><th>From</th><th>To</th><th>Note</th><th>By</th></tr></thead>
    <tbody>
        @forelse($app->statusLogs as $log)
            <tr><td>{{ $log->created_at->format('d M Y H:i') }}</td><td>{{ $log->from_status ?: '—' }}</td>
                <td><span class="st-pill b">{{ $log->to_status }}</span></td><td>{{ $log->note ?: '—' }}</td>
                <td>{{ optional($log->staff)->name ?: 'System' }}</td></tr>
        @empty
            <tr><td colspan="5" class="st-empty">No changes recorded yet.</td></tr>
        @endforelse
    </tbody>
</table></div></section>
@endsection
