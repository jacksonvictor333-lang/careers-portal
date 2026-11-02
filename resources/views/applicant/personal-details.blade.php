@extends('applicant.layout')

@section('title', 'Personal Details')

@push('styles')
<style>
    .pd-title { margin: 0 0 14px; padding-bottom: 10px; font-size: 22px; font-weight: 500; border-bottom: 1px solid var(--line); }
    .pd-sec { margin-bottom: 18px; background: #fff; border: 1px solid var(--line); border-radius: 6px; box-shadow: 0 1px 2px rgba(16,24,40,.04); }
    .pd-sh { display: flex; align-items: center; gap: 10px; padding: 15px 22px; font-size: 16px; font-weight: 700; }
    .pd-sh i { font-size: 18px; color: var(--navy); }
    .pd-list { margin: 0; padding: 0; border-top: 1px solid var(--line); }
    .pd-row { display: flex; justify-content: space-between; gap: 20px; padding: 11px 22px; font-size: 13px; border-bottom: 1px solid #F0F1F3; }
    .pd-row span { color: #374151; }
    .pd-row b { font-weight: 500; text-align: right; text-transform: uppercase; color: #111827; }
    .pd-row b.plain { text-transform: none; }
    .pd-foot { padding: 12px 22px 16px; background: #F9FAFB; border-top: 1px solid var(--line); border-radius: 0 0 6px 6px; }
    .pd-btn { display: inline-flex; align-items: center; gap: 8px; height: 34px; padding: 0 14px; font: inherit; font-size: 12.5px; font-weight: 600; color: #fff; background: var(--navy); border: 0; border-radius: 3px; cursor: pointer; }
    .pd-btn:hover { background: #163C6B; }
    .pd-btn.ghost { color: var(--ink); background: #fff; border: 1px solid #D1D5DB; }
    .pd-btn.ghost:hover { background: #F3F4F6; }
    .pd-note { margin: 0 22px 12px; padding: 8px 12px; font-size: 12.5px; color: #175CD3; background: #EFF8FF; border: 1px solid #B2DDFF; border-radius: 4px; }

    .pd-form { padding: 6px 22px 18px; border-top: 1px solid var(--line); }
    .pd-form[hidden] { display: none; }
    .pd-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px 18px; margin-top: 12px; }
    .pd-grid.two { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .pd-f label { display: block; margin-bottom: 4px; font-size: 12.5px; color: #374151; }
    .pd-f label .req { color: #D92D20; }
    .pd-f input, .pd-f select { width: 100%; height: 38px; padding: 0 10px; font: inherit; font-size: 13px; color: var(--ink); background: #fff; border: 1px solid #D1D5DB; border-radius: 4px; outline: none; }
    .pd-f input:focus, .pd-f select:focus { border-color: var(--navy); box-shadow: 0 0 0 3px rgba(29,78,137,.14); }
    .pd-f.err input, .pd-f.err select { border-color: #F04438; background: #FFFBFA; }
    .pd-err { margin-top: 3px; font-size: 12px; color: #B42318; }
    .pd-pre { display: flex; align-items: stretch; }
    .pd-pre span { display: grid; place-items: center; padding: 0 10px; font-size: 13px; color: #6B7280; background: #F3F4F6; border: 1px solid #D1D5DB; border-right: 0; border-radius: 4px 0 0 4px; white-space: nowrap; }
    .pd-pre input { border-radius: 0 4px 4px 0; }
    .pd-actions { display: flex; gap: 8px; margin-top: 16px; }

    .pd-contact { display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); }
    .pd-col { padding: 4px 22px 8px; }
    .pd-col + .pd-col { border-left: 1px solid var(--line); }
    .pd-col h3 { display: flex; align-items: center; gap: 10px; margin: 8px 0 12px; padding-bottom: 10px; font-size: 15px; font-weight: 500; color: var(--navy); border-bottom: 1px solid var(--line); }
    .pd-col .pd-f { margin-bottom: 14px; }

    @media (max-width: 900px) { .pd-grid, .pd-grid.two, .pd-contact { grid-template-columns: 1fr; } .pd-col + .pd-col { border-left: 0; border-top: 1px solid var(--line); } }
</style>
@endpush

@section('content')
@php
    $a        = $applicant;
    $fullName = trim(implode(' ', array_filter([$a->first_name, $a->middle_name, $a->last_name])));
    $dob      = $a->date_of_birth ? $a->date_of_birth->format('F j, Y') : null;
    $nin      = $a->nida_number;

    $locs     = $regions;   // ['Mkoa' => ['Wilaya', ...]] kutoka database
    $tz       = config('locations.countries.0');
    $country  = old('residence_country', $a->residence_country ?: $tz);

    // Fomu inafunguka yenyewe ikiwa kuna makosa ya uthibitisho kwenye sehemu hiyo
    $errId    = $errors->hasAny(['first_name','middle_name','last_name','nida_number','gender','date_of_birth','birth_region','birth_district','nationality']);
    $errOther = $errors->hasAny(['marital_status','disability','disability_details']);
    $phoneNum = old('phone', ltrim(preg_replace('/^\+?255/', '', (string) preg_replace('/\s+/', '', (string) $a->phone)), '0'));
    $postalNo = old('postal_address', preg_replace('/^P\.?O\.?\s*BOX\s*/i', '', (string) $a->postal_address));
@endphp

<div class="ap-wrap wide">

    <h1 class="pd-title">Personal Details</h1>

    @if(session('success'))
        <div class="ap-alert ok" style="margin-bottom:14px"><i class="bi bi-check-circle-fill"></i><span>{{ session('success') }}</span></div>
    @endif

    {{-- ================= 1) PERSONAL INFORMATION ================= --}}
    <section class="pd-sec">
        <div class="pd-sh"><i class="bi bi-person-vcard"></i> Personal Information</div>

        <div id="idView" @if($errId) hidden @endif>
            <dl class="pd-list">
                <div class="pd-row"><span>NIN</span><b class="plain">{{ $nin ?: '—' }}</b></div>
                <div class="pd-row"><span>Full Name</span><b>{{ $fullName ?: '—' }}</b></div>
                <div class="pd-row"><span>Gender</span><b>{{ $a->gender ?: '—' }}</b></div>
                <div class="pd-row"><span>Date of Birth</span><b class="plain">{{ $dob ?: '—' }}</b></div>
                <div class="pd-row"><span>Region of Birth</span><b>{{ $a->birth_region ?: '—' }}</b></div>
                <div class="pd-row"><span>District of Birth</span><b>{{ $a->birth_district ?: '—' }}</b></div>
                <div class="pd-row"><span>Nationality</span><b>{{ $a->nationality ?: '—' }}</b></div>
            </dl>
            <div class="pd-foot">
                <button type="button" class="pd-btn" onclick="pdToggle('id', true)"><i class="bi bi-arrow-repeat"></i> Update NIDA Details</button>
            </div>
        </div>

        <form id="idForm" class="pd-form" method="POST" action="{{ route('applicant.personal-details.identity') }}" novalidate @if(! $errId) hidden @endif>
            @csrf
            @method('PUT')
            <div class="pd-note"><i class="bi bi-info-circle"></i> These details will be retrieved from NIDA using your NIN. For now you can enter them yourself.</div>
            <div class="pd-grid">
                <div class="pd-f @error('nida_number') err @enderror"><label for="nida_number">NIN</label>
                    <input type="text" id="nida_number" name="nida_number" value="{{ old('nida_number', $nin) }}" placeholder="1999-1121-1412-5000-0114">@error('nida_number')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('first_name') err @enderror"><label for="first_name">First name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $a->first_name) }}">@error('first_name')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('middle_name') err @enderror"><label for="middle_name">Middle name</label>
                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $a->middle_name) }}">@error('middle_name')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('last_name') err @enderror"><label for="last_name">Last name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $a->last_name) }}">@error('last_name')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('gender') err @enderror"><label for="gender">Gender <span class="req">*</span></label>
                    <select id="gender" name="gender"><option value="">Select...</option>
                        @foreach(['Male', 'Female'] as $g)<option value="{{ $g }}" @selected(old('gender', $a->gender) === $g)>{{ $g }}</option>@endforeach
                    </select>@error('gender')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('date_of_birth') err @enderror"><label for="date_of_birth">Date of Birth <span class="req">*</span></label>
                    <input type="date" id="date_of_birth" name="date_of_birth" max="{{ date('Y-m-d') }}" value="{{ old('date_of_birth', optional($a->date_of_birth)->format('Y-m-d')) }}">@error('date_of_birth')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('birth_region') err @enderror"><label for="birth_region">Region of Birth <span class="req">*</span></label>
                    <select id="birth_region" name="birth_region" data-districts="birth_district"><option value="">Select...</option>
                        @foreach($locs as $r => $ds)<option value="{{ $r }}" @selected(old('birth_region', $a->birth_region) === $r)>{{ $r }}</option>@endforeach
                    </select>@error('birth_region')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('birth_district') err @enderror"><label for="birth_district">District of Birth <span class="req">*</span></label>
                    <select id="birth_district" name="birth_district" data-selected="{{ old('birth_district', $a->birth_district) }}"><option value="">Select...</option></select>
                    @error('birth_district')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('nationality') err @enderror"><label for="nationality">Nationality <span class="req">*</span></label>
                    <input type="text" id="nationality" name="nationality" value="{{ old('nationality', $a->nationality ?: 'Tanzanian') }}">@error('nationality')<div class="pd-err">{{ $message }}</div>@enderror</div>
            </div>
            <div class="pd-actions">
                <button type="submit" class="pd-btn"><i class="bi bi-check-lg"></i> Save Personal Information</button>
                <button type="button" class="pd-btn ghost" onclick="pdToggle('id', false)">Cancel</button>
            </div>
        </form>
    </section>

    {{-- ================= 2) OTHER PERSONAL DETAILS ================= --}}
    <section class="pd-sec">
        <div class="pd-sh"><i class="bi bi-person-circle"></i> Other Personal Details</div>

        <div id="otherView" @if($errOther) hidden @endif>
            <dl class="pd-list">
                <div class="pd-row"><span>Marital Status:</span><b class="plain">{{ $a->marital_status ?: '—' }}</b></div>
                <div class="pd-row"><span>Disability:</span><b class="plain">{{ $a->disability ?: '—' }}@if($a->disability === 'Yes' && $a->disability_details) ({{ $a->disability_details }})@endif</b></div>
            </dl>
            <div class="pd-foot">
                <button type="button" class="pd-btn" onclick="pdToggle('other', true)"><i class="bi bi-pencil-square"></i> Update Other Personal Details</button>
            </div>
        </div>

        <form id="otherForm" class="pd-form" method="POST" action="{{ route('applicant.personal-details.other') }}" novalidate @if(! $errOther) hidden @endif>
            @csrf
            @method('PUT')
            <div class="pd-grid">
                <div class="pd-f @error('marital_status') err @enderror"><label for="marital_status">Marital Status <span class="req">*</span></label>
                    <select id="marital_status" name="marital_status"><option value="">Select...</option>
                        @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $m)<option value="{{ $m }}" @selected(old('marital_status', $a->marital_status) === $m)>{{ $m }}</option>@endforeach
                    </select>@error('marital_status')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('disability') err @enderror"><label for="disability">Disability <span class="req">*</span></label>
                    <select id="disability" name="disability"><option value="">Select...</option>
                        @foreach(['No', 'Yes'] as $d)<option value="{{ $d }}" @selected(old('disability', $a->disability) === $d)>{{ $d }}</option>@endforeach
                    </select>@error('disability')<div class="pd-err">{{ $message }}</div>@enderror</div>
                <div class="pd-f @error('disability_details') err @enderror" id="disWrap"><label for="disability_details">Type of disability <span class="req">*</span></label>
                    <input type="text" id="disability_details" name="disability_details" value="{{ old('disability_details', $a->disability_details) }}">@error('disability_details')<div class="pd-err">{{ $message }}</div>@enderror</div>
            </div>
            <div class="pd-actions">
                <button type="submit" class="pd-btn"><i class="bi bi-check-lg"></i> Save</button>
                <button type="button" class="pd-btn ghost" onclick="pdToggle('other', false)">Cancel</button>
            </div>
        </form>
    </section>

    {{-- ================= 3) CONTACT DETAILS ================= --}}
    <section class="pd-sec">
        <div class="pd-sh"><i class="bi bi-telephone"></i> Contact Details</div>

        <form method="POST" action="{{ route('applicant.personal-details.contact') }}" novalidate style="border-top:1px solid var(--line)">
            @csrf
            @method('PUT')
            <div class="pd-contact">
                <div class="pd-col">
                    <h3><i class="bi bi-globe2"></i> Current Residence</h3>

                    <div class="pd-f @error('residence_country') err @enderror"><label for="residence_country">Country of Residence <span class="req">*</span></label>
                        <select id="residence_country" name="residence_country" data-home="{{ $tz }}">
                            @foreach(config('locations.countries', []) as $c)<option value="{{ $c }}" @selected($country === $c)>{{ $c }}</option>@endforeach
                        </select>@error('residence_country')<div class="pd-err">{{ $message }}</div>@enderror</div>

                    <div class="pd-f @error('region') err @enderror" data-home-only><label for="region">Current Resident Region <span class="req">*</span></label>
                        <select id="region" name="region" data-districts="district"><option value="">Select...</option>
                            @foreach($locs as $r => $ds)<option value="{{ $r }}" @selected(old('region', $a->region) === $r)>{{ $r }}</option>@endforeach
                        </select>@error('region')<div class="pd-err">{{ $message }}</div>@enderror</div>

                    <div class="pd-f @error('district') err @enderror" data-home-only><label for="district">Current Resident District <span class="req">*</span></label>
                        <select id="district" name="district" data-selected="{{ old('district', $a->district) }}"><option value="">Select...</option></select>
                        @error('district')<div class="pd-err">{{ $message }}</div>@enderror</div>

                    <div class="pd-f @error('phone_digits') err @enderror"><label for="phone">Mobile Number <span class="req">*</span></label>
                        <div class="pd-pre"><span>+255</span><input type="text" id="phone" name="phone" inputmode="numeric" maxlength="12" value="{{ $phoneNum }}" placeholder="653621675"></div>
                        @error('phone_digits')<div class="pd-err">{{ $message }}</div>@enderror</div>
                </div>

                <div class="pd-col">
                    <h3><i class="bi bi-envelope-paper"></i> Postal Address</h3>

                    <div class="pd-f @error('postal_address') err @enderror"><label for="postal_address">Postal Address <span class="req">*</span></label>
                        <div class="pd-pre"><span>P.O BOX</span><input type="text" id="postal_address" name="postal_address" value="{{ $postalNo }}" placeholder="34"></div>
                        @error('postal_address')<div class="pd-err">{{ $message }}</div>@enderror</div>

                    <div class="pd-f @error('postal_region') err @enderror"><label for="postal_region">Region <span class="req">*</span></label>
                        <select id="postal_region" name="postal_region"><option value="">Select...</option>
                            @foreach($locs as $r => $ds)<option value="{{ $r }}" @selected(old('postal_region', $a->postal_region) === $r)>{{ $r }}</option>@endforeach
                        </select>@error('postal_region')<div class="pd-err">{{ $message }}</div>@enderror</div>
                </div>
            </div>

            <div class="pd-foot" style="border-radius:0 0 6px 6px">
                <button type="submit" class="pd-btn"><i class="bi bi-check-lg"></i> Save Contact Details</button>
            </div>
        </form>
    </section>

</div>
@endsection

@push('scripts')
<script>
    const PD_LOCS = @json($regions);

    // Onyesha / ficha fomu ya kuhariri ya sehemu moja
    function pdToggle(name, edit) {
        document.getElementById(name + 'View').hidden = edit;
        document.getElementById(name + 'Form').hidden = !edit;
        if (edit) { document.getElementById(name + 'Form').querySelector('input, select').focus(); }
    }

    // Wilaya zinabadilika kulingana na mkoa
    function pdFillDistricts(regionSel) {
        const target = document.getElementById(regionSel.dataset.districts);
        const keep   = target.dataset.selected || '';
        target.innerHTML = '<option value="">Select...</option>';
        (PD_LOCS[regionSel.value] || []).forEach(function (d) {
            const o = document.createElement('option');
            o.value = o.textContent = d;
            if (d === keep) { o.selected = true; }
            target.appendChild(o);
        });
        target.dataset.selected = '';   // chaguo la awali linatumika mara moja tu
    }
    document.querySelectorAll('select[data-districts]').forEach(function (sel) {
        pdFillDistricts(sel);
        sel.addEventListener('change', function () { pdFillDistricts(sel); });
    });

    // Mkoa/wilaya wa makazi ni lazima tu kwa anayeishi Tanzania
    const country = document.getElementById('residence_country');
    function pdHome() {
        const home = country.value === country.dataset.home;
        document.querySelectorAll('[data-home-only]').forEach(function (el) {
            el.hidden = !home;
            el.querySelectorAll('select').forEach(function (s) { s.disabled = !home; });
        });
    }
    country.addEventListener('change', pdHome);
    pdHome();

    // Aina ya ulemavu inaonekana tu ukichagua "Yes"
    const dis = document.getElementById('disability'), disWrap = document.getElementById('disWrap');
    function pdDis() { disWrap.hidden = dis.value !== 'Yes'; disWrap.querySelector('input').disabled = dis.value !== 'Yes'; }
    dis.addEventListener('change', pdDis);
    pdDis();
</script>
@endpush
