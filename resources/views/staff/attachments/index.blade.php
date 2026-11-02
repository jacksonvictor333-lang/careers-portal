@extends('staff.layout')
@section('title', 'Attachments')

@section('content')
<div class="st-head"><div><h1>Attachments</h1><p>Files applicants uploaded under Other Attachments.</p></div></div>
<form class="st-search" method="GET" action="{{ route('staff.attachments') }}"><input type="text" name="q" value="{{ $q }}" placeholder="Search by applicant, type or title..."><button class="st-btn" type="submit"><i class="bi bi-search"></i> Search</button></form>
<section class="st-card"><div class="st-wrap"><table class="st-t">
    <thead><tr><th>Applicant</th><th>Type</th><th>Uploaded</th><th class="r">File</th></tr></thead>
    <tbody>
    @forelse($attachments as $a)
        <tr><td><a href="{{ route('staff.applicants.show', $a->applicant_id) }}">{{ optional($a->applicant)->full_name ?: 'N/A' }}</a><div class="st-sub">{{ optional($a->applicant)->email }}</div></td>
            <td>{{ $a->type }}@if($a->title && $a->title !== $a->type)<div class="st-sub">{{ $a->title }}</div>@endif</td>
            <td>{{ optional($a->created_at)->format('d M Y') }}</td>
            <td class="r"><a class="st-btn ghost sm" target="_blank" href="{{ route('staff.files', ['path' => $a->attachment_path]) }}"><i class="bi bi-paperclip"></i> Open</a></td></tr>
    @empty
        <tr><td colspan="4" class="st-empty">No attachments.</td></tr>
    @endforelse
    </tbody>
</table></div>
@if($attachments->hasPages())<div class="st-pager">@if($attachments->onFirstPage())<span class="dis">Previous</span>@else<a href="{{ $attachments->previousPageUrl() }}">Previous</a>@endif<span class="cur">{{ $attachments->currentPage() }} / {{ $attachments->lastPage() }}</span>@if($attachments->hasMorePages())<a href="{{ $attachments->nextPageUrl() }}">Next</a>@else<span class="dis">Next</span>@endif</div>@endif
</section>
@endsection
