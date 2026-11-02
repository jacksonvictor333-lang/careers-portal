<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Application letter - {{ $app->vacancy_title }}</title>
<style>
    @page { margin: 28mm 22mm 24mm 22mm; }
    body { margin: 0; font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11.5px; line-height: 1.6; color: #111827; }
    @if(! ($asPdf ?? false))
    body { max-width: 760px; margin: 0 auto; padding: 36px 40px; }
    @endif
    .head { text-align: center; border-bottom: 2px solid #1D4E89; padding-bottom: 10px; margin-bottom: 18px; }
    .head .rep { font-size: 11px; font-weight: bold; letter-spacing: 1px; }
    .head .inst { margin-top: 2px; font-size: 15px; font-weight: bold; color: #1D4E89; }
    .head .doc { margin-top: 2px; font-size: 11px; color: #6B7280; }
    table { width: 100%; border-collapse: collapse; }
    .meta td { padding: 2px 0; vertical-align: top; }
    .meta td.k { width: 120px; color: #6B7280; }
    .subject { margin: 18px 0 12px; font-size: 12.5px; font-weight: bold; text-decoration: underline; text-transform: uppercase; }
    p { margin: 0 0 10px; text-align: justify; }
    h3 { margin: 18px 0 6px; padding-bottom: 3px; font-size: 11.5px; letter-spacing: .5px; text-transform: uppercase; color: #1D4E89; border-bottom: 1px solid #D1D5DB; }
    .grid td { padding: 5px 8px; border: 1px solid #D1D5DB; vertical-align: top; }
    .grid td.k { width: 150px; background: #F3F4F6; color: #374151; }
    .sched th { padding: 6px 8px; text-align: left; font-size: 10.5px; text-transform: uppercase; color: #374151; background: #F3F4F6; border: 1px solid #D1D5DB; }
    .sched td { padding: 6px 8px; border: 1px solid #D1D5DB; }
    .note { margin-top: 8px; font-size: 10.5px; color: #6B7280; }
    .sign { margin-top: 26px; }
    .foot { margin-top: 30px; padding-top: 8px; text-align: center; font-size: 9.5px; color: #9CA3AF; border-top: 1px solid #E5E7EB; }
</style>
</head>
<body>
@php
    $inst  = config('portal.institution', 'Recruitment Portal');
    $fmt   = fn ($d) => $d ? $d->format('d F Y') : null;

    // Ratiba ya usaili kutoka kwenye model (hatua zote zenye tarehe)
    $rows = $app->has_timetable ? $app->schedule : [];
@endphp

<div class="head">
    <div class="rep">THE UNITED REPUBLIC OF TANZANIA</div>
    <div class="inst">{{ $inst }}</div>
    <div class="doc">Recruitment Portal &middot; Application Letter</div>
</div>

<table class="meta">
    <tr><td class="k">Date</td><td>{{ now('Africa/Dar_es_Salaam')->format('d F Y') }}</td></tr>
    <tr><td class="k">Application No.</td><td>{{ $applicant->application_number }}</td></tr>
    @if($app->reference_number)
        <tr><td class="k">Reference</td><td>{{ $app->reference_number }}</td></tr>
    @endif
    <tr><td class="k">To</td><td>@if($app->vacancy){{ implode(' ', $app->vacancy->letter_address_lines ?? config('application.letter_address', [])) }}@else The Recruitment Panel, {{ $app->institution }}@endif</td></tr>
</table>

<div class="subject">{{ $letter->subject }}</div>

<p>Dear Sir/Madam,</p>

@foreach($letter->paragraphs() as $para)
    <p>{!! nl2br(e($para)) !!}</p>
@endforeach

<h3>Applicant details</h3>
<table class="grid">
    <tr><td class="k">Full name</td><td>{{ $applicant->full_name }}</td></tr>
    <tr><td class="k">Email</td><td>{{ $applicant->email }}</td></tr>
    @if($applicant->phone)
        <tr><td class="k">Phone</td><td>{{ $applicant->phone }}</td></tr>
    @endif
    <tr><td class="k">Post applied for</td><td>{{ $app->vacancy_title }}</td></tr>
    <tr><td class="k">Employer</td><td>{{ $app->institution }}</td></tr>
    <tr><td class="k">Date applied</td><td>{{ $fmt($app->applied_at) ?: '-' }}</td></tr>
    @unless($attachment ?? false)
    <tr><td class="k">Status</td><td>{{ $app->status_label }}@if($app->status_message && $app->status_message !== $app->status_label) &middot; {{ $app->status_message }}@endif</td></tr>
    @endunless
</table>

@if(! ($attachment ?? false) && $app->has_timetable && ($rows || $app->interview_number))
    <h3>Interview schedule</h3>
    @if($app->interview_number)
        <p style="margin-bottom:6px">Interview number: <b>{{ $app->interview_number }}</b></p>
    @endif
    @if($rows)
        <table class="sched">
            <tr><th>Stage</th><th>Date</th><th>Time</th><th>Venue</th></tr>
            @foreach($rows as $r)
                <tr><td>{{ $r['stage'] }}</td><td>{{ $r['date'] }}</td><td>{{ $r['time'] }}</td><td>{{ $r['venue'] }}</td></tr>
            @endforeach
        </table>
        <div class="note">Please bring this letter, your interview number and a valid identification document.</div>
    @endif
@endif

@if($app->attachments->count())
    <h3>Attached documents</h3>
    <table class="grid">
        @foreach($app->attachments as $att)
            <tr><td class="k">{{ $att->type }}</td><td>{{ basename((string) $att->attachment_path) }}</td></tr>
        @endforeach
    </table>
@endif

<div class="sign">
    <p style="margin-bottom:28px">Yours faithfully,</p>
    <b>{{ $applicant->full_name }}</b>
</div>

<div class="foot">Generated by {{ config('portal.name', 'Careers Portal') }} on {{ now('Africa/Dar_es_Salaam')->format('d M Y, H:i') }}</div>

</body>
</html>
