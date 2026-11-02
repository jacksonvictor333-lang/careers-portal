@extends('applicant.layout')

@section('title', $vacancy->title)

@push('styles')
    @include('applicant.vacancies._styles')
@endpush

@section('content')
@php
    $left  = $vacancy->days_left;
    $badge = ! $vacancy->is_open ? ['r', 'Closed'] : ($left <= 3 ? ['a', $left === 0 ? 'Closes today' : $left . ' day(s) left'] : ['g', $left . ' days left']);
@endphp
<div class="ap-wrap wide">

    <p style="margin:0 0 12px;font-size:13px"><a href="{{ route('vacancies') }}" style="color:var(--navy);font-weight:600"><i class="bi bi-arrow-left"></i> All vacancies</a></p>

    @if(session('error'))
        <div class="vc-alert"><i class="bi bi-exclamation-circle-fill"></i><span>{{ session('error') }}</span></div>
    @endif
    @if(session('success'))
        <div class="vc-alert ok"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span></div>
    @endif

    <div class="vc-grid">

        <div>
            <section class="vc-panel">
                <div class="vc-pb" style="padding:20px">
                    <h1 style="margin:0 0 4px;font-size:22px;color:var(--ink)">{{ $vacancy->title }}</h1>
                    <div class="vc-employer" style="font-size:14.5px">{{ $vacancy->employer }}</div>
                    <div class="vc-ref">Ref: {{ $vacancy->reference_number }}</div>
                </div>
            </section>

            @if($vacancy->description)
                <section class="vc-panel"><div class="vc-ph">About the post</div><div class="vc-pb">{{ $vacancy->description }}</div></section>
            @endif

            @if($vacancy->duties_list)
                <section class="vc-panel"><div class="vc-ph">Duties and responsibilities</div>
                    <div class="vc-pb"><ul>@foreach($vacancy->duties_list as $d)<li>{{ $d }}</li>@endforeach</ul></div></section>
            @endif

            @if($vacancy->requirements_list)
                <section class="vc-panel"><div class="vc-ph">Qualifications and experience</div>
                    <div class="vc-pb"><ul>@foreach($vacancy->requirements_list as $r)<li>{{ $r }}</li>@endforeach</ul></div></section>
            @endif

        </div>

        <aside>
            <section class="vc-panel">
                <div class="vc-ph">Advert details</div>
                <div class="vc-pb">
                    <div class="vc-kv"><span>Closing date</span><b>{{ $vacancy->closes_at->format('d M Y') }}</b></div>
                    <div class="vc-kv"><span>Status</span><span class="vc-badge {{ $badge[0] }}">{{ $badge[1] }}</span></div>
                    @if($vacancy->required_categories_list)
                        <div class="vc-kv"><span>Field of study</span><b style="text-align:right">{{ implode(', ', $vacancy->required_categories_list) }}</b></div>
                    @endif
                    <div class="vc-kv"><span>Posted</span><b>{{ $vacancy->posted_at->format('d M Y') }}</b></div>
                    <div class="vc-kv"><span>Number of posts</span><b>{{ $vacancy->number_of_posts }}</b></div>
                    @if($vacancy->location)<div class="vc-kv"><span>Location</span><b>{{ $vacancy->location }}</b></div>@endif
                    @if($vacancy->salary_scale)<div class="vc-kv"><span>Salary scale</span><b>{{ $vacancy->salary_scale }}</b></div>@endif
                </div>
            </section>

            <section class="vc-panel">
                <div class="vc-ph">Where to send your letter</div>
                <div class="vc-pb">
                    <div class="vc-addr"><b>Address your application letter to</b>@foreach(($vacancy->letter_address_lines ?? config('application.letter_address', [])) as $line){{ $line }}@if(! $loop->last)<br>@endif @endforeach</div>
                </div>
            </section>

            <section class="vc-panel">
                <div class="vc-ph">Apply</div>
                <div class="vc-pb">
                    @if(! $applicant)
                        <p style="margin:0 0 12px">Log in to your applicant account to apply for this post.</p>
                        <a class="vc-btn" href="{{ route('applicant.login') }}"><i class="bi bi-box-arrow-in-right"></i> Login to apply</a>

                    @elseif($existing)
                        <div class="vc-alert ok" style="margin-bottom:12px"><i class="bi bi-check-circle-fill"></i>
                            <span><b>Application Received.</b> Applied on {{ $existing->applied_at->format('d M Y') }}.</span></div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                            <a class="vc-btn" href="{{ route('applicant.my-applications') }}">My Applications</a>
                            @if($existing->can_edit)
                                <a class="vc-btn ghost" href="{{ route('applicant.my-applications.letter.edit', $existing->id) }}"><i class="bi bi-pencil-square"></i> Change letter</a>
                            @endif
                        </div>

                    @else
                        @if($eligibility['checks'])
                            <div style="margin-bottom:10px">
                                @foreach($eligibility['checks'] as $c)
                                    <div class="vc-check {{ $c['ok'] ? 'ok' : 'no' }}">
                                        <i class="bi {{ $c['ok'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                        <div>{{ $c['label'] }}<small>{{ $c['detail'] }}</small></div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(! $vacancy->is_open)
                            <div class="vc-alert"><i class="bi bi-lock-fill"></i><span>This advert is closed.</span></div>
                        @elseif(! $eligibility['ok'])
                            @foreach($eligibility['checks'] as $c)
                                @if(! $c['ok'] && $c['fix'])
                                    <div class="vc-alert" style="margin-bottom:10px"><i class="bi bi-exclamation-circle-fill"></i><span>{{ $c['fix'] }}</span></div>
                                    <a class="vc-btn ghost" style="margin-bottom:10px" href="{{ Route::has($c['fix_route']) ? route($c['fix_route']) : '#' }}">{{ $c['fix_label'] }}</a>
                                @endif
                            @endforeach
                        @else
                            <button type="button" class="vc-btn" onclick="baOpen()"><i class="bi bi-send"></i> Apply now</button>
                        @endif
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>

@if($applicant && ! $existing && $vacancy->is_open && $eligibility['ok'])
@php
    $addrLines = $vacancy->letter_address_lines ?? config('application.letter_address', []);
    $subject   = 'YAH: OMBI LA KAZI NAFASI YA ' . mb_strtoupper($vacancy->title) . ' — KUMB. NA. ' . $vacancy->reference_number;
@endphp
<div class="ba-modal" id="baModal" hidden role="dialog" aria-modal="true" aria-labelledby="baTitle">
    <div class="ba-box">
        <div class="ba-head"><i class="bi bi-envelope-paper"></i><div><h2 id="baTitle">Before you apply</h2><p>Your application letter must be addressed correctly</p></div></div>
        <div class="ba-body">
            <div class="ba-lbl">Address your letter to</div>
            <div class="ba-addr" id="baAddr">@foreach($addrLines as $line){{ $line }}@if(! $loop->last)<br>@endif @endforeach</div>
            <div class="ba-lbl">Subject line</div>
            <div class="ba-subj" id="baSubj">{{ $subject }}</div>
            <button type="button" class="ba-copy" onclick="baCopy('baAddr', this)"><i class="bi bi-clipboard"></i> Copy address</button>
            <button type="button" class="ba-copy" onclick="baCopy('baSubj', this)"><i class="bi bi-clipboard"></i> Copy subject</button>
        </div>
        <div class="ba-foot">
            <button type="button" class="vc-btn ghost" onclick="baClose()">Cancel</button>
            <a class="vc-btn" href="{{ route('applicant.vacancies.apply', $vacancy->id) }}"><i class="bi bi-check2"></i> I understand, continue</a>
        </div>
    </div>
</div>
@push('scripts')
<script>
    function baOpen()  { document.getElementById('baModal').hidden = false; }
    function baClose() { document.getElementById('baModal').hidden = true; }
    function baCopy(id, btn) {
        var el = document.getElementById(id), text = el.innerText.replace(/\s+\n/g, '\n').trim();
        var done = function () { var o = btn.innerHTML; btn.innerHTML = '<i class="bi bi-check2"></i> Copied'; setTimeout(function () { btn.innerHTML = o; }, 1500); };
        if (navigator.clipboard) { navigator.clipboard.writeText(text).then(done); }
        else { var t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select(); document.execCommand('copy'); t.remove(); done(); }
    }
    document.getElementById('baModal').addEventListener('click', function (e) { if (e.target.id === 'baModal') baClose(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') baClose(); });
</script>
@endpush
@endif
@endsection
