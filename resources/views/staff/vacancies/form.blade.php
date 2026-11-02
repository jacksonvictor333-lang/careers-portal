@extends('staff.layout')
@section('title', $vacancy->exists ? 'Edit vacancy' : 'New vacancy')

@section('content')
@php
    $exists = $vacancy->exists;
    $v = fn ($k, $d = null) => old($k, $vacancy->{$k} ?? $d);
    $date = fn ($k) => old($k, optional($vacancy->{$k})->format('Y-m-d'));
@endphp
<div class="st-head"><div><h1>{{ $exists ? 'Edit vacancy' : 'New vacancy' }}</h1>
    <p>This advert appears under Vacancies in the applicant portal once it is published.</p></div></div>

<form method="POST" action="{{ $exists ? route('staff.vacancies.update', $vacancy->id) : route('staff.vacancies.store') }}" novalidate>
    @csrf
    @if($exists) @method('PUT') @endif

    <section class="st-card">
        <div class="st-ch">Post details</div>
        <div class="st-cb"><div class="st-form">
            <div class="st-f full"><label class="t" for="scheme_of_service_id">Scheme of service</label>
                <select id="scheme_of_service_id" name="scheme_of_service_id"><option value="">None - fill the details manually</option>
                    @foreach($schemes as $sc)
                        <option value="{{ $sc->id }}" @selected((string) $v('scheme_of_service_id') === (string) $sc->id)
                            data-title="{{ $sc->title }}" data-scale="{{ optional($sc->salaryScale)->code }}" data-min="{{ $sc->min_education_level }}" data-max="{{ $sc->max_education_level }}"
                            data-exp="{{ $sc->min_experience_years }}" data-duties="{{ $sc->duties }}" data-req="{{ $sc->requirements }}" data-cats="{{ $sc->required_categories }}">{{ $sc->title }} ({{ $sc->code }})</option>
                    @endforeach
                </select>
                <div class="st-hint">Choosing a scheme fills the title, salary scale, duties, qualifications, education levels, field of study and experience. You can still edit them.</div></div>
            <div class="st-f @error('title') err @enderror"><label class="t" for="title">Post title <span class="req">*</span></label>
                <input type="text" id="title" name="title" value="{{ $v('title') }}">@error('title')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f @error('reference_number') err @enderror"><label class="t" for="reference_number">Reference number <span class="req">*</span></label>
                <input type="text" id="reference_number" name="reference_number" value="{{ $v('reference_number') }}" placeholder="PSRS/2026/XXX-001">@error('reference_number')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f @error('employer_id') err @enderror"><label class="t" for="employer_id">Employer <span class="req">*</span></label>
                <select id="employer_id" name="employer_id"><option value="">Select...</option>
                    @foreach($employers as $e)<option value="{{ $e->id }}" @selected((string) $v('employer_id') === (string) $e->id)>{{ $e->name }}</option>@endforeach
                </select>
                <div class="st-hint">Not listed? <a href="{{ route('staff.employers.create') }}">Add the employer</a> first (its address is used for the application letter).</div>
                @error('employer_id')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f"><label class="t" for="location">Location</label><input type="text" id="location" name="location" value="{{ $v('location') }}"></div>
            <div class="st-f"><label class="t" for="salary_scale">Salary scale</label><input type="text" id="salary_scale" name="salary_scale" value="{{ $v('salary_scale') }}" placeholder="TGS D"></div>
            <div class="st-f @error('number_of_posts') err @enderror"><label class="t" for="number_of_posts">Number of posts <span class="req">*</span></label>
                <input type="number" min="1" id="number_of_posts" name="number_of_posts" value="{{ $v('number_of_posts', 1) }}">@error('number_of_posts')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f full"><label class="t" for="description">About the post</label><textarea id="description" name="description">{{ $v('description') }}</textarea></div>
            <div class="st-f"><label class="t" for="duties">Duties and responsibilities</label><textarea id="duties" name="duties" style="min-height:140px">{{ $v('duties') }}</textarea><div class="st-hint">One item per line.</div></div>
            <div class="st-f"><label class="t" for="requirements">Qualifications and experience (text shown to applicants)</label><textarea id="requirements" name="requirements" style="min-height:140px">{{ $v('requirements') }}</textarea><div class="st-hint">One item per line.</div></div>
        </div></div>
    </section>

    <section class="st-card">
        <div class="st-ch">Who can apply <span class="st-sub">Applicants are checked against these rules automatically</span></div>
        <div class="st-cb"><div class="st-form">
            <div class="st-f @error('min_education_level') err @enderror"><label class="t" for="min_education_level">Lowest education level</label>
                <select id="min_education_level" name="min_education_level"><option value="">Any</option>
                    @foreach($levels as $l)<option value="{{ $l }}" @selected($v('min_education_level') === $l)>{{ $l }}</option>@endforeach
                </select>@error('min_education_level')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f @error('max_education_level') err @enderror"><label class="t" for="max_education_level">Highest education level</label>
                <select id="max_education_level" name="max_education_level"><option value="">Any</option>
                    @foreach($levels as $l)<option value="{{ $l }}" @selected($v('max_education_level') === $l)>{{ $l }}</option>@endforeach
                </select>
                <div class="st-hint">Same as the lowest level = that level only. Applicants above or below are declined.</div>
                @error('max_education_level')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f full"><label class="t">Field of study (category)</label>
                <div class="st-checks">
                    @foreach($categories as $c)<label><input type="checkbox" name="required_categories[]" value="{{ $c }}" @checked(in_array($c, (array) $chosen, true))> {{ $c }}</label>@endforeach
                </div>
                <div class="st-hint">Tick the categories accepted. None ticked = any field. The applicant needs a qualification in one of these, at the level above.</div>
                @error('required_categories.*')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f @error('min_experience_years') err @enderror"><label class="t" for="min_experience_years">Minimum experience (years) <span class="req">*</span></label>
                <input type="number" min="0" id="min_experience_years" name="min_experience_years" value="{{ $v('min_experience_years', 0) }}">@error('min_experience_years')<div class="st-err">{{ $message }}</div>@enderror</div>
        </div></div>
    </section>

    <section class="st-card">
        <div class="st-ch">Dates and letter</div>
        <div class="st-cb"><div class="st-form">
            <div class="st-f @error('posted_at') err @enderror"><label class="t" for="posted_at">Posting date <span class="req">*</span></label>
                <input type="date" id="posted_at" name="posted_at" value="{{ $date('posted_at') }}">@error('posted_at')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f @error('closes_at') err @enderror"><label class="t" for="closes_at">Closing date <span class="req">*</span></label>
                <input type="date" id="closes_at" name="closes_at" value="{{ $date('closes_at') }}">@error('closes_at')<div class="st-err">{{ $message }}</div>@enderror</div>
            <div class="st-f full"><label class="t" for="letter_address">Letter address for this advert (optional)</label>
                <textarea id="letter_address" name="letter_address" placeholder="Leave empty to use the employer's address">{{ $v('letter_address') }}</textarea>
                <div class="st-hint">One address line per line. Empty = the employer's address is used.</div></div>
            <div class="st-f full"><label style="display:flex;align-items:center;gap:8px;font-size:13.5px"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $vacancy->is_published ?? true))> Published (visible to applicants)</label></div>
        </div></div>
    </section>

    <div style="display:flex;gap:8px;justify-content:flex-end">
        <a class="st-btn ghost" href="{{ $exists ? route('staff.vacancies.show', $vacancy->id) : route('staff.vacancies') }}">Cancel</a>
        <button class="st-btn" type="submit"><i class="bi bi-check-lg"></i> {{ $exists ? 'Save changes' : 'Save vacancy' }}</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var sel = document.getElementById('scheme_of_service_id');
    if (!sel) return;
    var set = function (id, val) { var el = document.getElementById(id); if (el && val !== undefined) el.value = val || ''; };
    sel.addEventListener('change', function () {
        var o = sel.options[sel.selectedIndex];
        if (!o || !o.value) return;
        set('title', o.dataset.title); set('salary_scale', o.dataset.scale); set('duties', o.dataset.duties); set('requirements', o.dataset.req);
        set('min_education_level', o.dataset.min); set('max_education_level', o.dataset.max); set('min_experience_years', o.dataset.exp);
        var cats = (o.dataset.cats || '').split(/\r?\n/).map(function (x) { return x.trim(); }).filter(Boolean);
        document.querySelectorAll('input[name="required_categories[]"]').forEach(function (c) { c.checked = cats.indexOf(c.value) !== -1; });
    });
})();
</script>
@endpush
