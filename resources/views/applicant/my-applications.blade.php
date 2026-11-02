@extends('applicant.layout')

@section('title', 'My Applications')

@push('styles')
<style>
    .ma-title { margin: 0 0 14px; padding-bottom: 12px; font-size: 22px; font-weight: 600; border-bottom: 1px solid var(--line); }

    .ma-flash { display: flex; gap: 10px; align-items: flex-start; margin: 0 0 12px; padding: 10px 14px; font-size: 13px; color: #B42318; background: #FEF3F2; border: 1px solid #FECDCA; border-radius: 4px; }
    .ma-flash.ok { color: #067647; background: #ECFDF3; border-color: #ABEFC6; }
    .ma-editnote { margin-top: 6px; font-size: 11.5px; color: #6B7280; }
    a.ma-view { text-decoration: none; }
    .ma-bar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; font-size: 13px; color: #374151; }
    .ma-bar label { display: inline-flex; align-items: center; gap: 8px; }
    .ma-bar select, .ma-bar input[type=search] {
        height: 36px; padding: 0 12px; font: inherit; font-size: 13px; color: var(--ink);
        background: #fff; border: 1px solid #D1D5DB; border-radius: 6px; outline: none;
    }
    .ma-bar input[type=search] { width: 240px; }
    .ma-bar select:focus, .ma-bar input[type=search]:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(29,78,137,.14); }

    .ma-box { background: #fff; border: 1px solid var(--line); }
    .ma-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
    .ma-table th { padding: 13px 16px; text-align: left; font-size: 12.5px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase;
                   color: #374151; background: #F3F4F6; border-bottom: 1px solid var(--line); border-left: 1px solid var(--line); white-space: nowrap; }
    .ma-table th:first-child, .ma-table td:first-child { border-left: 0; }
    .ma-table td { padding: 14px 16px; vertical-align: middle; border-left: 1px solid var(--line); border-bottom: 1px solid var(--line); }
    .ma-table .sn { width: 70px; }
    .ma-line { display: flex; align-items: center; gap: 8px; line-height: 1.9; }
    .ma-line i { color: #6B7280; }
    .ma-line .k { color: #6B7280; }
    .ma-line b { font-weight: 600; color: #111827; }
    .ma-pill { display: inline-flex; align-items: center; gap: 6px; margin-top: 6px; padding: 3px 11px; border-radius: 999px; font-size: 12px; border: 1px solid; }
    .ma-pill.st-gray { color: #067647; background: #ECFDF3; border-color: #ABEFC6; }   /* Application Received */
    .ma-pill.st-blue { color: #175CD3; background: #EFF8FF; border-color: #B2DDFF; }
    .ma-pill.st-amber { color: #B54708; background: #FFFAEB; border-color: #FEDF89; }
    .ma-pill.st-purple { color: #5925DC; background: #F4F3FF; border-color: #D9D6FE; }
    .ma-pill.st-green { color: #067647; background: #ECFDF3; border-color: #ABEFC6; }
    .ma-pill.st-red { color: #B42318; background: #FEF3F2; border-color: #FECDCA; }
    .ma-na { color: #374151; }
    .ma-sub { margin-top: 2px; font-size: 12px; color: #6B7280; }
    .ma-view { display: inline-flex; align-items: center; gap: 6px; height: 34px; padding: 0 14px; border: 0; border-radius: 4px; cursor: pointer;
               font: inherit; font-size: 12.5px; font-weight: 600; color: #fff; background: var(--navy); }
    .ma-actions { display: flex; flex-wrap: nowrap; align-items: center; gap: 6px; }
    .ma-actions .ma-view { white-space: nowrap; }
    .ma-st { font-size: 12.5px; font-weight: 700; letter-spacing: .02em; }
    .ma-rule { height: 1px; margin: 8px 0; background: #E5E7EB; }
    .ma-chip { display: inline-block; padding: 2px 10px; font-size: 11.5px; color: #067647; background: #ECFDF3; border: 1px solid #ABEFC6; border-radius: 999px; }

    /* ---- Dirisha la barua (PDF) ---- */
    .lt-modal { position: fixed; inset: 0; z-index: 200; display: flex; align-items: center; justify-content: center; padding: 18px; background: rgba(16,24,40,.55); }
    .lt-modal[hidden] { display: none; }
    .lt-box { display: flex; flex-direction: column; width: min(900px, 100%); height: min(92vh, 900px); background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,.3); }
    .lt-head { display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-bottom: 1px solid var(--line); }
    .lt-head h2 { flex: 1; margin: 0; font-size: 15px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .lt-head a, .lt-head button { display: inline-flex; align-items: center; gap: 6px; height: 32px; padding: 0 12px; font: inherit; font-size: 12.5px; font-weight: 600;
                                   border-radius: 6px; cursor: pointer; text-decoration: none; border: 1px solid #D1D5DB; color: var(--ink); background: #fff; }
    .lt-head a.dl { color: #fff; background: var(--navy); border-color: var(--navy); }
    .tt-box { width: min(700px, 100%); height: auto; max-height: 92vh; }
    .tt-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid var(--line); }
    .tt-head h2 { display: inline-flex; align-items: center; gap: 8px; margin: 0; padding: 10px 16px; font-size: 16px; font-weight: 400; background: #F9FAFB; border-radius: 4px; }
    .tt-x { width: 34px; height: 34px; display: grid; place-items: center; border: 0; background: none; cursor: pointer; font-size: 16px; color: #6B7280; border-radius: 4px; }
    .tt-x:hover { background: #F3F4F6; }
    .tt-body { padding: 14px 16px 18px; overflow: auto; }
    .tt-card { margin-bottom: 12px; padding: 14px 16px; background: #fff; border: 1px solid var(--line); border-radius: 4px; }
    .tt-card h3 { margin: 0 0 8px; padding-bottom: 8px; font-size: 16px; font-weight: 500; border-bottom: 1px dotted #D1D5DB; }
    .tt-card h3.tt-sub { font-size: 14px; border-bottom-style: solid; border-color: var(--line); }
    .tt-det { display: flex; align-items: center; gap: 8px; line-height: 1.9; font-size: 13px; }
    .tt-det i { color: #6B7280; }
    .tt-det span { color: #6B7280; }
    .tt-stage { margin-bottom: 10px; padding: 12px 14px; background: #F9FAFB; border: 1px solid var(--line); border-radius: 4px; }
    .tt-stage:last-child { margin-bottom: 0; }
    .tt-stage-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid var(--line); }
    .tt-stage-head b { font-size: 14.5px; }
    .tt-venue { margin: 2px 0 0 24px; font-size: 13px; font-weight: 700; line-height: 1.5; text-transform: uppercase; }
    .tt-info { padding: 10px 14px; font-size: 13px; color: #1D4ED8; background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 4px; }
    .tt-info i { margin-right: 6px; }
    .tt-empty { margin: 4px 0; padding: 14px; text-align: center; font-size: 13.5px; color: var(--muted); }
    .tt-pill { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; border: 1px solid; }
    .tt-completed { color: #067647; background: #ECFDF3; border-color: #ABEFC6; }
    .tt-upcoming { color: #B54708; background: #FFFAEB; border-color: #FEDF89; }
    .tt-pending { color: #475467; background: #F2F4F7; border-color: #E4E7EC; }
    .lt-frame { flex: 1; width: 100%; border: 0; background: #E5E7EB; }
    .ma-view:hover { opacity: .92; }

    .ma-detail td { background: #F6F9FD; border-left: 0; }
    .ma-detail dl { display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 12px 28px; margin: 0; }
    .ma-detail dt { margin-bottom: 2px; font-size: 12px; color: var(--muted); }
    .ma-detail dd { margin: 0; font-size: 14px; font-weight: 500; }

    .ma-foot { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; padding: 14px 22px; font-size: 13px; color: #374151; }
    .ma-pager { display: flex; gap: 6px; }
    .ma-pager button { height: 32px; padding: 0 12px; font: inherit; font-size: 13px; cursor: pointer; color: var(--ink); background: #fff; border: 1px solid #D1D5DB; border-radius: 6px; }
    .ma-pager button:disabled { opacity: .45; cursor: default; }
    .ma-empty { padding: 36px 16px; text-align: center; color: var(--muted); }
    @media (max-width: 760px) { .ma-box { overflow-x: auto; } .ma-table { min-width: 720px; } .ma-bar input[type=search] { width: 100%; } }

    /* ---- Ukubwa mdogo zaidi ---- */
    .ma-title { margin-bottom: 10px; padding-bottom: 8px; font-size: 19px; }
    .ma-bar { margin-bottom: 10px; font-size: 12.5px; }
    .ma-bar select, .ma-bar input[type=search] { height: 32px; font-size: 12.5px; }
    .ma-bar input[type=search] { width: 210px; }
    .ma-table { font-size: 12.5px; }
    .ma-table th { padding: 9px 12px; font-size: 11.5px; }
    .ma-table td { padding: 9px 12px; }
    .ma-table .sn { width: 56px; }
    .ma-line { line-height: 1.6; }
    .ma-pill { margin-top: 3px; padding: 2px 9px; font-size: 11.5px; }
    .ma-view { height: 30px; padding: 0 12px; font-size: 12px; }
    .ma-detail dl { gap: 8px 22px; }
    .ma-detail dd { font-size: 13px; }
    .ma-foot { padding: 9px 14px; font-size: 12.5px; }
    .ma-pager button { height: 28px; padding: 0 10px; font-size: 12.5px; }
</style>
@endpush

@section('content')
<div class="ap-wrap wide">

    <h1 class="ma-title">My Applications</h1>

    @if(session('success'))
        <div class="ma-flash ok"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
        <div class="ma-flash"><i class="bi bi-exclamation-circle-fill"></i><span>{{ session('error') }}</span></div>
    @endif

    <div class="ma-bar">
        <label>Show
            <select id="maSize">
                <option>10</option><option>25</option><option>50</option><option>100</option>
            </select>
            entries
        </label>
        <label>Search:
            <input type="search" id="maSearch" placeholder="Search applications...">
        </label>
    </div>

    <div class="ma-box">
        <table class="ma-table">
            <thead>
                <tr>
                    <th class="sn">S/N</th>
                    <th>Post details</th>
                    <th>Interview number</th>
                    <th>Status reason</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach($applications as $app)
                <tbody class="ma-item">
                    <tr>
                        <td class="sn"></td>
                        <td>
                            <div class="ma-line"><i class="bi bi-file-earmark-text"></i><span class="k">Post:</span> <b>{{ $app->vacancy_title }}</b></div>
                            <div class="ma-line"><i class="bi bi-building"></i><span class="k">Employer:</span> <b>{{ $app->institution }}</b></div>
                            <span class="ma-pill {{ $app->status_class }}"><i class="bi {{ $app->status_icon }}"></i> {{ $app->status_label }}</span>
                            @if($app->can_edit)
                                <div class="ma-editnote">You can replace your letter until {{ $app->vacancy->closes_at->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td class="ma-na">{{ $app->interview_number ?: 'N/A' }}</td>
                        <td>
                            @if($app->status !== 'Submitted')
                                <div class="ma-st">{{ strtoupper($app->status) }}</div>
                                @if($app->reason_detail)
                                    <div class="ma-rule"></div>
                                    <span class="ma-chip">{{ $app->reason_detail }}</span>
                                @endif
                            @else
                                <span class="ma-na">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="ma-actions">

                                @if(Route::has('applicant.my-applications.letter'))
                                    <button type="button" class="ma-view ma-letter" data-title="{{ $app->vacancy_title }}"
                                            data-src="{{ route('applicant.my-applications.letter', $app->id) }}" onclick="maLetter(this)">
                                        <i class="bi bi-eye"></i> Letter
                                    </button>
                                @endif
                                @if($app->can_edit)
                                    <a class="ma-view" href="{{ route('applicant.my-applications.letter.edit', $app->id) }}"><i class="bi bi-pencil-square"></i> Edit</a>
                                @endif
                                @if($app->has_timetable)
                                <button type="button" class="ma-view" onclick="maTimetable(this)"
                                        data-title="{{ $app->vacancy_title }}"
                                        data-employer="{{ $app->institution }}"
                                        data-number="{{ $app->interview_number }}"
                                        data-schedule="{{ json_encode($app->schedule) }}">
                                    <i class="bi bi-calendar-event"></i> Timetable
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        </table>

        <div class="ma-empty" id="maEmpty" @if($applications->isNotEmpty()) hidden @endif>
            @if($applications->isEmpty())
                You have not applied for any job yet.
                @if(Route::has('vacancies'))
                    <a href="{{ route('vacancies') }}" style="color:var(--navy);font-weight:600">Browse vacancies</a>
                @endif
            @else
                No matching applications found.
            @endif
        </div>

        <div class="ma-foot">
            <span id="maInfo"></span>
            <div class="ma-pager">
                <button type="button" id="maPrev">Previous</button>
                <span id="maNums" style="display:flex;gap:6px"></span>
                <button type="button" id="maNext">Next</button>
            </div>
        </div>
    </div>

</div>

{{-- Dirisha la ratiba ya usaili --}}
<div class="lt-modal" id="ttModal" hidden role="dialog" aria-modal="true" aria-labelledby="ttTitle">
    <div class="lt-box tt-box">
        <div class="tt-head">
            <h2 id="ttTitle"><i class="bi bi-calendar3"></i> Interview Timetable</h2>
            <button type="button" id="ttClose" class="tt-x" onclick="maTimetableClose()" aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="tt-body">
            <section class="tt-card">
                <h3>Application Details</h3>
                <div class="tt-det"><i class="bi bi-file-earmark-text"></i><span>Post:</span> <b id="ttPost"></b></div>
                <div class="tt-det"><i class="bi bi-building"></i><span>Employer:</span> <b id="ttEmployer"></b></div>
                <div class="tt-det" id="ttNumberRow" hidden><i class="bi bi-hash"></i><span>Interview number:</span> <b id="ttNumber"></b></div>
            </section>

            <section class="tt-card">
                <h3 class="tt-sub">Interview Timetable</h3>
                <div id="ttStages"></div>
                <p class="tt-empty" id="ttEmpty" hidden>No interview schedule has been set for this application yet.</p>
            </section>

            <div class="tt-info"><i class="bi bi-lightbulb"></i>
                <b>Taarifa Zaidi:</b> Kwa taarifa zaidi tembelea <u>{{ config('portal.website', 'www.ajira.go.tz') }}</u>
            </div>
        </div>
    </div>
</div>

{{-- Dirisha la barua ya maombi (PDF) --}}
<div class="lt-modal" id="ltModal" hidden role="dialog" aria-modal="true" aria-labelledby="ltTitle">
    <div class="lt-box">
        <div class="lt-head">
            <h2 id="ltTitle">Application letter</h2>
            <a class="dl" id="ltDownload" href="#"><i class="bi bi-download"></i> Download</a>
            <button type="button" id="ltClose" onclick="maLetterClose()"><i class="bi bi-x-lg"></i> Close</button>
        </div>
        <iframe class="lt-frame" id="ltFrame" title="Application letter" src="about:blank"></iframe>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Dirisha la ratiba ya usaili (timetable) bila kufungua tab nyingine
    function maTimetable(btn) {
        const rows = JSON.parse(btn.dataset.schedule || '[]');
        const box  = document.getElementById('ttStages');
        box.innerHTML = '';

        document.getElementById('ttPost').textContent     = btn.dataset.title;
        document.getElementById('ttEmployer').textContent = btn.dataset.employer || '';
        document.getElementById('ttNumber').textContent   = btn.dataset.number || '';
        document.getElementById('ttNumberRow').hidden     = !btn.dataset.number;
        document.getElementById('ttEmpty').hidden        = rows.length > 0;

        const line = (icon, label, value) => {
            const d = document.createElement('div');
            d.className = 'tt-det';
            d.innerHTML = '<i class="bi ' + icon + '"></i><span></span> <b></b>';
            d.querySelector('span').textContent = label;
            d.querySelector('b').textContent = value;
            return d;
        };

        rows.forEach(r => {
            const st = document.createElement('div');
            st.className = 'tt-stage';

            const head = document.createElement('div');
            head.className = 'tt-stage-head';
            const name = document.createElement('b');
            name.textContent = r.stage;
            const pill = document.createElement('span');
            pill.className = 'tt-pill tt-' + r.state.toLowerCase();
            pill.textContent = r.state;
            head.appendChild(name); head.appendChild(pill);
            st.appendChild(head);

            st.appendChild(line('bi-calendar3', 'Date:', r.date_long));
            st.appendChild(line('bi-clock', 'Time:', r.time));

            const v = line('bi-geo-alt', 'Venue:', '');
            v.querySelector('b').remove();
            st.appendChild(v);
            const venue = document.createElement('div');
            venue.className = 'tt-venue';
            venue.textContent = r.venue;
            st.appendChild(venue);

            box.appendChild(st);
        });

        document.getElementById('ttModal').hidden = false;
        document.body.style.overflow = 'hidden';
        document.getElementById('ttClose').focus();
    }
    function maTimetableClose() {
        document.getElementById('ttModal').hidden = true;
        document.body.style.overflow = '';
    }
    document.getElementById('ttModal').addEventListener('click', e => { if (e.target.id === 'ttModal') maTimetableClose(); });

    // Dirisha la barua ya maombi (PDF) bila kufungua tab nyingine
    function maLetter(btn) {
        const modal = document.getElementById('ltModal');
        document.getElementById('ltTitle').textContent = 'Application letter - ' + btn.dataset.title;
        document.getElementById('ltDownload').href = btn.dataset.src + '?download=1';
        document.getElementById('ltFrame').src = btn.dataset.src;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        document.getElementById('ltClose').focus();
    }
    function maLetterClose() {
        document.getElementById('ltModal').hidden = true;
        document.getElementById('ltFrame').src = 'about:blank';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key !== 'Escape') return;
        if (!document.getElementById('ltModal').hidden) maLetterClose();
        if (!document.getElementById('ttModal').hidden) maTimetableClose();
    });
    document.getElementById('ltModal').addEventListener('click', e => { if (e.target.id === 'ltModal') maLetterClose(); });

    // Tafuta + idadi ya safu + kurasa (upande wa kivinjari)
    (function () {
        const items = Array.from(document.querySelectorAll('.ma-item'));
        const size  = document.getElementById('maSize');
        const q     = document.getElementById('maSearch');
        const info  = document.getElementById('maInfo');
        const prev  = document.getElementById('maPrev');
        const next  = document.getElementById('maNext');
        const empty = document.getElementById('maEmpty');
        const hasAny = items.length > 0;
        let page = 1;

        function render() {
            const term = q.value.trim().toLowerCase();
            const per  = parseInt(size.value, 10);
            const hits = items.filter(el => el.textContent.toLowerCase().includes(term));
            const pages = Math.max(1, Math.ceil(hits.length / per));
            page = Math.min(page, pages);
            const from = (page - 1) * per;

            items.forEach(el => { el.hidden = true; });
            hits.slice(from, from + per).forEach((el, i) => {
                el.hidden = false;
                el.querySelector('.sn').textContent = from + i + 1;
            });

            if (hasAny) empty.hidden = hits.length > 0;
            const shownTo = Math.min(from + per, hits.length);
            info.textContent = hits.length
                ? 'Showing ' + (from + 1) + ' to ' + shownTo + ' of ' + hits.length + ' entries'
                : 'Showing 0 to 0 of 0 entries';
            prev.disabled = page <= 1;
            next.disabled = page >= pages;

            // Namba za kurasa: 1 2 3 ... (ukurasa wa sasa umeangaziwa)
            const nums = document.getElementById('maNums');
            nums.innerHTML = '';
            for (let n = 1; n <= pages; n++) {
                if (pages > 7 && n !== 1 && n !== pages && Math.abs(n - page) > 1) {
                    if (n === 2 || n === pages - 1) { const d = document.createElement('span'); d.textContent = '…'; d.style.padding = '0 4px'; nums.appendChild(d); }
                    continue;
                }
                const b = document.createElement('button');
                b.type = 'button'; b.textContent = n;
                if (n === page) { b.style.background = 'var(--navy)'; b.style.color = '#fff'; b.style.borderColor = 'var(--navy)'; }
                b.addEventListener('click', () => { page = n; render(); });
                nums.appendChild(b);
            }
        }

        size.addEventListener('change', () => { page = 1; render(); });
        q.addEventListener('input',    () => { page = 1; render(); });
        prev.addEventListener('click', () => { page--; render(); });
        next.addEventListener('click', () => { page++; render(); });
        render();
    })();
</script>
@endpush
