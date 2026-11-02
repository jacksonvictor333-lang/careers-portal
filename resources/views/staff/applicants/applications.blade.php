@extends('staff.layout')
@section('title', 'Applications')

@section('content')
@php $title = 'Applications'; $cur = 'apps'; $manage = auth('staff')->user()->canManage(); @endphp
@include('staff.applicants.partials-cv-head')

<section class="st-card"><div class="st-ch">Jobs applied <span class="st-pill b">{{ $apps->count() }}</span></div>
    <div class="st-wrap"><table class="st-t">
        <thead><tr><th>#</th><th>Post</th><th>Employer</th><th>Applied</th><th>Status</th><th>Interview</th><th>Interview no.</th><th class="r">Action</th></tr></thead>
        <tbody>
        @forelse($apps as $i => $a)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><b>{{ $a->vacancy_title }}</b><div class="st-sub">{{ $a->reference_number }}</div></td>
                <td>{{ $a->institution }}</td>
                <td>{{ optional($a->applied_at)->format('d M Y') }}</td>
                <td><span class="st-pill b">{{ $a->status_label }}</span>@if($a->status_reason)<div class="st-sub">{{ $a->status_reason }}</div>@endif</td>
                <td>{{ $a->interview_type ?: '—' }}@if($a->interview_at)<div class="st-sub">{{ $a->interview_at->format('d M Y') }}@if($a->interview_time) {{ substr((string) $a->interview_time, 0, 5) }}@endif</div>@endif</td>
                <td>{{ $a->interview_number ?: '—' }}</td>
                <td class="r">@if($a->letter)<a class="st-btn ghost sm" target="_blank" href="{{ route('staff.applications.letter', $a->id) }}"><i class="bi bi-file-earmark-text"></i> Letter</a>@endif
                    @if($manage)<a class="st-btn sm" href="{{ route('staff.applications.edit', $a->id) }}"><i class="bi bi-pencil-square"></i> Update</a>@endif</td>
            </tr>
        @empty
            <tr><td colspan="8" class="st-empty">This applicant has not applied for any job yet.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>
@endsection
