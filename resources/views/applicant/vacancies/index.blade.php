@extends('applicant.layout')

@section('title', 'Vacancies')

@push('styles')
    @include('applicant.vacancies._styles')
@endpush

@section('content')
<div class="ap-wrap wide">

    @include('applicant.partials.header', [
        'title'    => 'Vacancies',
        'subtitle' => 'Open job adverts. Read the requirements, then apply online.',
        'icon'     => 'bi-briefcase',
    ])

    @if(session('error'))
        <div class="vc-alert"><i class="bi bi-exclamation-circle-fill"></i><span>{{ session('error') }}</span></div>
    @endif

    @forelse($vacancies as $v)
        @php
            $left    = $v->days_left;
            $badge   = ! $v->is_open ? ['r', 'Closed'] : ($left <= 3 ? ['a', $left === 0 ? 'Closes today' : $left . ' day(s) left'] : ['g', $left . ' days left']);
            $appId   = $applied[$v->id] ?? null;
        @endphp
        <article class="vc-card {{ $v->is_open ? '' : 'closed' }}">
            <div>
                <h2 class="vc-title"><a href="{{ route('vacancies.show', $v->id) }}">{{ $v->title }}</a></h2>
                <div class="vc-employer">{{ $v->employer }}</div>
                <div class="vc-ref">Ref: {{ $v->reference_number }}</div>
                <div class="vc-chips">
                    @if($v->location)<span class="vc-chip"><i class="bi bi-geo-alt"></i> {{ $v->location }}</span>@endif
                    <span class="vc-chip"><i class="bi bi-people"></i> {{ $v->number_of_posts }} post(s)</span>
                    @if($v->salary_scale)<span class="vc-chip"><i class="bi bi-cash-coin"></i> {{ $v->salary_scale }}</span>@endif
                    @if($v->education_label)<span class="vc-chip"><i class="bi bi-mortarboard"></i> {{ $v->education_label }}</span>@endif
                    @if($v->required_categories_list)<span class="vc-chip"><i class="bi bi-diagram-3"></i> {{ implode(', ', $v->required_categories_list) }}</span>@endif
                    @if($v->min_experience_years > 0)<span class="vc-chip"><i class="bi bi-briefcase"></i> {{ $v->min_experience_years }}+ yr(s) experience</span>@endif
                </div>
            </div>
            <div class="vc-side">
                <div class="vc-close">Closing date<b>{{ $v->closes_at->format('d M Y') }}</b></div>
                <span class="vc-badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                @if($appId)
                    <span class="vc-badge b"><i class="bi bi-check2-circle"></i> Application Received</span>
                @endif
                <a class="vc-btn {{ $appId ? 'ghost' : '' }}" href="{{ route('vacancies.show', $v->id) }}">
                    {{ $appId ? 'View' : ($v->is_open ? 'View & apply' : 'View') }}
                </a>
            </div>
        </article>
    @empty
        <div class="vc-panel"><div class="vc-pb" style="text-align:center;padding:36px">There are no vacancies at the moment. Please check again later.</div></div>
    @endforelse

</div>
@endsection
