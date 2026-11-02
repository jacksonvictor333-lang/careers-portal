@extends('staff.layout')
@section('title', 'Application Check')

@section('content')
@php $title = 'Application Check'; $cur = 'check'; @endphp
@include('staff.applicants.partials-cv-head')

<section class="st-card"><div class="st-ch">Does this applicant meet the requirements of an advert?</div><div class="st-cb">
    <form method="GET" action="{{ route('staff.applicants.check', $applicant->id) }}" class="st-f" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap">
        <div style="flex:1;min-width:280px;max-width:560px"><label class="t">Advert</label>
            <select name="vacancy" required><option value="">Select advert...</option>
                @foreach($vacancies as $v)<option value="{{ $v->id }}" {{ optional($vacancy)->id === $v->id ? 'selected' : '' }}>{{ $v->title }} — {{ $v->employer }} ({{ $v->reference_number }})</option>@endforeach
            </select></div>
        <button class="st-btn" type="submit"><i class="bi bi-check2-circle"></i> Check</button>
    </form>
</div></section>

@if($result)
<section class="st-card"><div class="st-ch">{{ $vacancy->title }}
    <span class="st-pill {{ $result['ok'] ? 'g' : 'r' }}">{{ $result['ok'] ? 'Meets requirements' : 'Does not meet requirements' }}</span></div>
    <div class="st-wrap"><table class="st-t">
        <thead><tr><th style="width:60px">Result</th><th>Requirement</th><th>Applicant</th></tr></thead>
        <tbody>
        @forelse($result['checks'] as $c)
            <tr><td><span class="st-pill {{ $c['ok'] ? 'g' : 'r' }}">{{ $c['ok'] ? 'Pass' : 'Fail' }}</span></td>
                <td>{{ $c['label'] }}</td><td>{{ $c['detail'] }}</td></tr>
        @empty
            <tr><td colspan="3" class="st-empty">This advert has no specific requirements.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</section>
@endif
@endsection
