@extends('staff.layout')
@section('title', 'Scheme Of Service')

@section('content')
@php $manage = auth('staff')->user()->canManage(); @endphp
<div class="st-head"><div><h1>Scheme Of Service</h1><p>Each scheme carries the job information (qualifications, duties, salary scale). Pick one when creating an advert.</p></div>
    @if($manage)<a class="st-btn" href="{{ route('staff.schemes.create') }}"><i class="bi bi-plus-lg"></i> New scheme</a>@endif</div>

<form class="st-search" method="GET" action="{{ route('staff.schemes') }}"><input type="text" name="q" value="{{ $q }}" placeholder="Search by title or code..."><button class="st-btn" type="submit"><i class="bi bi-search"></i> Search</button></form>

<section class="st-card"><div class="st-wrap"><table class="st-t">
    <thead><tr><th>Code</th><th>Title</th><th>Scale</th><th>Education</th><th>Experience</th><th>Adverts</th><th>Status</th><th class="r"></th></tr></thead>
    <tbody>
    @forelse($schemes as $s)
        <tr><td>{{ $s->code }}</td><td><b>{{ $s->title }}</b></td><td>{{ optional($s->salaryScale)->code ?: '—' }}</td>
            <td>{{ $s->min_education_level ?: 'Any' }}@if($s->max_education_level && $s->max_education_level !== $s->min_education_level) &ndash; {{ $s->max_education_level }}@endif</td>
            <td>{{ $s->min_experience_years }} yr</td><td>{{ $s->vacancies_count }}</td>
            <td><span class="st-pill {{ $s->is_active ? 'g' : 'n' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="r">@if($manage)<a class="st-btn ghost sm" href="{{ route('staff.schemes.edit', $s->id) }}"><i class="bi bi-pencil"></i> Edit</a>@endif</td></tr>
    @empty
        <tr><td colspan="8" class="st-empty">No schemes yet.</td></tr>
    @endforelse
    </tbody>
</table></div>
@if($schemes->hasPages())<div class="st-pager">@if($schemes->onFirstPage())<span class="dis">Previous</span>@else<a href="{{ $schemes->previousPageUrl() }}">Previous</a>@endif<span class="cur">{{ $schemes->currentPage() }} / {{ $schemes->lastPage() }}</span>@if($schemes->hasMorePages())<a href="{{ $schemes->nextPageUrl() }}">Next</a>@else<span class="dis">Next</span>@endif</div>@endif
</section>
@endsection
