@extends('staff.layout')
@section('title', $vacancy->title)

@section('content')
@php $staff = auth('staff')->user(); $canManage = $staff && $staff->canManage(); @endphp
@php $st = ! $vacancy->is_published ? ['n', 'Unpublished'] : ($vacancy->is_open ? ['g', 'Open'] : ['r', 'Closed']); @endphp
<div class="st-head">
    <div><h1>{{ $vacancy->title }}</h1><p>{{ $vacancy->employer }} &middot; Ref: {{ $vacancy->reference_number }} &middot; <span class="st-pill {{ $st[0] }}">{{ $st[1] }}</span></p></div>
    <div><a class="st-btn ghost" href="{{ route('staff.vacancies') }}">Back</a>
         @if($staff->canManage())<a class="st-btn" href="{{ route('staff.vacancies.edit', $vacancy->id) }}"><i class="bi bi-pencil"></i> Edit</a>@endif</div>
</div>

<section class="st-card">
    <div class="st-ch">Advert details</div>
    <div class="st-cb">
        <dl class="st-kv">
            <div><dt>Closing date</dt><dd>{{ $vacancy->closes_at->format('d M Y') }}</dd></div>
            <div><dt>Posted</dt><dd>{{ $vacancy->posted_at->format('d M Y') }}</dd></div>
            <div><dt>Number of posts</dt><dd>{{ $vacancy->number_of_posts }}</dd></div>
            <div><dt>Location</dt><dd>{{ $vacancy->location ?: '—' }}</dd></div>
            <div><dt>Salary scale</dt><dd>{{ $vacancy->salary_scale ?: '—' }}</dd></div>
            <div><dt>Education level</dt><dd>{{ $vacancy->education_label ?: 'Any' }}</dd></div>
            <div><dt>Field of study</dt><dd>{{ $vacancy->required_categories_list ? implode(', ', $vacancy->required_categories_list) : 'Any' }}</dd></div>
            <div><dt>Minimum experience</dt><dd>{{ $vacancy->min_experience_years }} year(s)</dd></div>
            <div style="grid-column:1/-1"><dt>Letter address</dt><dd>{!! nl2br(e(implode("\n", $vacancy->letter_address_lines))) !!}</dd></div>
        </dl>
    </div>
</section>

<section class="st-card">
    <div class="st-ch">Applications <span class="st-pill b">{{ $vacancy->applications->count() }}</span></div>
    @if($canManage && $vacancy->applications->count())
    <form method="POST" action="{{ route('staff.vacancies.bulk', $vacancy->id) }}" id="bulkForm">
        @csrf
        <div class="st-cb st-f" style="background:#F9FAFB;border-bottom:1px solid var(--line)">
            <div style="font-size:12.5px;font-weight:600;margin-bottom:8px">Update ticked applications together</div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px">
                <select name="status">@foreach(\App\Models\JobApplication::STATUSES as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach</select>
                <select name="interview_type"><option value="">Interview type…</option><option>Written</option><option>Practical</option><option>Oral</option></select>
                <input type="date" name="interview_at" title="Interview date">
                <input type="time" name="interview_time" title="Interview time">
                <input type="text" name="interview_venue" placeholder="Venue">
                <input type="text" name="status_reason" placeholder="Short reason" maxlength="200">
            </div>
            <button class="st-btn sm" type="submit" style="margin-top:10px"><i class="bi bi-check2-all"></i> Apply to ticked</button>
            <span class="st-sub">Empty fields keep their current values.</span>
        </div>
    @endif
    <div class="st-wrap">
        <table class="st-t">
            <thead><tr>@if($canManage)<th style="width:34px"><input type="checkbox" onclick="document.querySelectorAll('.pick').forEach(c=>c.checked=this.checked)"></th>@endif<th>Applicant</th><th>Applied</th><th>Status</th><th>Interview no.</th><th class="r">Action</th></tr></thead>
            <tbody>
                @forelse($vacancy->applications as $a)
                    <tr>
                        @if($canManage)<td><input class="pick" type="checkbox" name="ids[]" value="{{ $a->id }}" form="bulkForm"></td>@endif
                        <td><a href="{{ route('staff.applicants.show', $a->applicant_id) }}">{{ optional($a->applicant)->full_name }}</a><div class="st-sub">{{ optional($a->applicant)->email }}</div></td>
                        <td>{{ optional($a->applied_at)->format('d M Y') }}</td>
                        <td><span class="st-pill b">{{ $a->status_label }}</span></td>
                        <td>{{ $a->interview_number ?: '—' }}</td>
                        <td class="r">@if($a->letter)<a class="st-btn ghost sm" target="_blank" href="{{ route('staff.applications.letter', $a->id) }}"><i class="bi bi-file-earmark-text"></i> Letter</a>@endif
                            @if($canManage)<a class="st-btn sm" href="{{ route('staff.applications.edit', $a->id) }}"><i class="bi bi-pencil-square"></i> Update</a>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="st-empty">No one has applied yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($canManage && $vacancy->applications->count())</form>@endif
</section>
@endsection
