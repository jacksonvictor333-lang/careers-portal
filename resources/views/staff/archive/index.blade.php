@extends('staff.layout')
@section('title', 'Archive')

@section('content')
<div class="st-head"><div><h1>Archive</h1><p>Adverts whose closing date has passed.</p></div></div>
<form class="st-search" method="GET" action="{{ route('staff.archive') }}"><input type="text" name="q" value="{{ $q }}" placeholder="Search by title, employer or reference..."><button class="st-btn" type="submit"><i class="bi bi-search"></i> Search</button></form>
<section class="st-card"><div class="st-wrap"><table class="st-t">
    <thead><tr><th>Reference</th><th>Post</th><th>Employer</th><th>Closed on</th><th>Applications</th><th class="r"></th></tr></thead>
    <tbody>
    @forelse($vacancies as $v)
        <tr><td>{{ $v->reference_number }}</td><td><b>{{ $v->title }}</b></td><td>{{ $v->employer }}</td><td>{{ $v->closes_at->format('d M Y') }}</td><td>{{ $v->applications_count }}</td>
            <td class="r"><a class="st-btn ghost sm" href="{{ route('staff.vacancies.show', $v->id) }}"><i class="bi bi-eye"></i> View</a></td></tr>
    @empty
        <tr><td colspan="6" class="st-empty">No archived adverts.</td></tr>
    @endforelse
    </tbody>
</table></div>
@if($vacancies->hasPages())<div class="st-pager">@if($vacancies->onFirstPage())<span class="dis">Previous</span>@else<a href="{{ $vacancies->previousPageUrl() }}">Previous</a>@endif<span class="cur">{{ $vacancies->currentPage() }} / {{ $vacancies->lastPage() }}</span>@if($vacancies->hasMorePages())<a href="{{ $vacancies->nextPageUrl() }}">Next</a>@else<span class="dis">Next</span>@endif</div>@endif
</section>
@endsection
