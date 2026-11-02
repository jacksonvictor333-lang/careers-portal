@extends('staff.layout')
@section('title', 'Reports')

@section('content')
<div class="st-head"><div><h1>Reports</h1><p>Applications per advert and status. Download the list as CSV (opens in Excel).</p></div></div>

<div class="st-stats" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr))">
    @foreach($statuses as $s)<div class="st-stat"><span>{{ $s }}</span><b>{{ number_format($totals[$s] ?? 0) }}</b></div>@endforeach
</div>

<section class="st-card"><div class="st-ch">Applications by advert</div><div class="st-wrap"><table class="st-t">
    <thead><tr><th>Reference</th><th>Post</th><th>Total</th>@foreach($statuses as $s)<th>{{ $s }}</th>@endforeach<th class="r">Download</th></tr></thead>
    <tbody>
    @forelse($vacancies as $v)
        <tr><td>{{ $v->reference_number }}</td><td><b>{{ $v->title }}</b><div class="st-sub">{{ $v->employer }}</div></td><td><b>{{ $v->applications_count }}</b></td>
            @foreach($statuses as $s)<td>{{ $byStatus[$v->id][$s] ?? 0 }}</td>@endforeach
            <td class="r"><a class="st-btn ghost sm" href="{{ route('staff.reports.csv', $v->id) }}"><i class="bi bi-download"></i> CSV</a></td></tr>
    @empty
        <tr><td colspan="{{ 4 + count($statuses) }}" class="st-empty">No adverts.</td></tr>
    @endforelse
    </tbody>
</table></div></section>
@endsection
