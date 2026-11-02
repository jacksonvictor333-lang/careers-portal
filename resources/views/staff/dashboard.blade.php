@extends('staff.layout')
@section('title', 'Dashboard')

@section('content')
<div class="st-head"><div><h1>Dashboard</h1><p>Overview of vacancies, applicants and applications.</p></div>
    @if(auth('staff')->user()->canManage())<a class="st-btn" href="{{ route('staff.vacancies.create') }}"><i class="bi bi-plus-lg"></i> New vacancy</a>@endif</div>

<div class="st-stats">
    <div class="st-stat"><span>Open vacancies</span><b>{{ number_format($stats['open']) }}</b></div>
    <div class="st-stat"><span>Closed / unpublished</span><b>{{ number_format($stats['closed']) }}</b></div>
    <div class="st-stat"><span>Applicants</span><b>{{ number_format($stats['applicants']) }}</b></div>
    <div class="st-stat"><span>Applications</span><b>{{ number_format($stats['applications']) }}</b></div>
    <a class="st-stat" href="{{ route('staff.applicants', ['tab' => 'pending']) }}" style="text-decoration:none;border-color:{{ $stats['pending'] ? '#F9B98C' : 'var(--line)' }}"><span>Awaiting activation</span><b style="color:{{ $stats['pending'] ? '#B54708' : 'var(--navy)' }}">{{ number_format($stats['pending']) }}</b></a>
</div>

<section class="st-card">
    <div class="st-ch">Recent applications</div>
    <div class="st-wrap">
        <table class="st-t">
            <thead><tr><th>Applicant</th><th>Post</th><th>Employer</th><th>Applied</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($recent as $a)
                    <tr>
                        <td><a href="{{ route('staff.applicants.show', $a->applicant_id) }}">{{ optional($a->applicant)->full_name }}</a><div class="st-sub">{{ optional($a->applicant)->application_number }}</div></td>
                        <td>{{ $a->vacancy_title }}</td>
                        <td>{{ $a->institution }}</td>
                        <td>{{ optional($a->applied_at)->format('d M Y') }}</td>
                        <td><span class="st-pill b">{{ $a->status_label }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="st-empty">No applications yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
