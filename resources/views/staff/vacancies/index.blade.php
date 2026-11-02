@extends('staff.layout')
@section('title', 'Vacancies')

@section('content')
<div class="st-head"><div><h1>Vacancies</h1><p>Create and manage job adverts.</p></div>
    @if(auth('staff')->user()->canManage())<a class="st-btn" href="{{ route('staff.vacancies.create') }}"><i class="bi bi-plus-lg"></i> New vacancy</a>@endif</div>

<form class="st-search" method="GET" action="{{ route('staff.vacancies') }}">
    <input type="text" name="q" value="{{ $q }}" placeholder="Search by title, employer or reference...">
    <button class="st-btn" type="submit"><i class="bi bi-search"></i> Search</button>
</form>

<section class="st-card">
    <div class="st-wrap">
        <table class="st-t">
            <thead><tr><th>Reference</th><th>Post</th><th>Level</th><th>Field of study</th><th>Closes</th><th>Applied</th><th>Status</th><th class="r">Actions</th></tr></thead>
            <tbody>
                @forelse($vacancies as $v)
                    @php
                        $st = ! $v->is_published ? ['n', 'Unpublished'] : ($v->is_open ? ['g', 'Open'] : ['r', 'Closed']);
                    @endphp
                    <tr>
                        <td>{{ $v->reference_number }}</td>
                        <td><a href="{{ route('staff.vacancies.show', $v->id) }}"><b>{{ $v->title }}</b></a><div class="st-sub">{{ $v->employer }}</div></td>
                        <td>{{ $v->education_label ?: 'Any' }}@if($v->min_experience_years > 0)<div class="st-sub">{{ $v->min_experience_years }}+ yr(s)</div>@endif</td>
                        <td>{{ $v->required_categories_list ? implode(', ', $v->required_categories_list) : 'Any' }}</td>
                        <td>{{ $v->closes_at->format('d M Y') }}</td>
                        <td>{{ $v->applications_count }}</td>
                        <td><span class="st-pill {{ $st[0] }}">{{ $st[1] }}</span></td>
                        <td class="r">
                            <a class="st-btn ghost sm" href="{{ route('staff.vacancies.show', $v->id) }}"><i class="bi bi-eye"></i> View</a>
                            @if(auth('staff')->user()->canManage())
                            <a class="st-btn ghost sm" href="{{ route('staff.vacancies.edit', $v->id) }}"><i class="bi bi-pencil"></i> Edit</a>
                            <form method="POST" action="{{ route('staff.vacancies.toggle', $v->id) }}" style="display:inline">@csrf
                                <button class="st-btn ghost sm" type="submit">{{ $v->is_published ? 'Unpublish' : 'Publish' }}</button></form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="st-empty">No vacancies found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vacancies->hasPages())
        <div class="st-pager">
            @if($vacancies->onFirstPage())<span class="dis">Previous</span>@else<a href="{{ $vacancies->previousPageUrl() }}">Previous</a>@endif
            <span class="cur">{{ $vacancies->currentPage() }} / {{ $vacancies->lastPage() }}</span>
            @if($vacancies->hasMorePages())<a href="{{ $vacancies->nextPageUrl() }}">Next</a>@else<span class="dis">Next</span>@endif
        </div>
    @endif
</section>
@endsection
