@extends('staff.layout')
@section('title', $scheme->exists ? 'Edit scheme' : 'New scheme')

@section('content')
@php $ex = $scheme->exists; $v = fn ($k, $d = null) => old($k, $scheme->{$k} ?? $d); @endphp
<div class="st-head"><div><h1>{{ $ex ? 'Edit scheme of service' : 'New scheme of service' }}</h1></div><a class="st-btn ghost" href="{{ route('staff.schemes') }}">Back</a></div>

<form method="POST" action="{{ $ex ? route('staff.schemes.update', $scheme->id) : route('staff.schemes.store') }}" novalidate>
    @csrf @if($ex) @method('PUT') @endif
    <section class="st-card"><div class="st-ch">Post</div><div class="st-cb"><div class="st-form">
        <div class="st-f @error('code') err @enderror"><label class="t">Code <span class="req">*</span></label><input type="text" name="code" value="{{ $v('code') }}" placeholder="ICT-OFF-II">@error('code')<div class="st-err">{{ $message }}</div>@enderror</div>
        <div class="st-f @error('title') err @enderror"><label class="t">Title / cadre <span class="req">*</span></label><input type="text" name="title" value="{{ $v('title') }}" placeholder="Assistant ICT Officer II">@error('title')<div class="st-err">{{ $message }}</div>@enderror</div>
        <div class="st-f"><label class="t">Salary scale</label><select name="salary_scale_id"><option value="">Select...</option>
            @foreach($scales as $s)<option value="{{ $s->id }}" @selected((string) $v('salary_scale_id') === (string) $s->id)>{{ $s->code }}</option>@endforeach</select></div>
        <div class="st-f @error('min_experience_years') err @enderror"><label class="t">Minimum experience (years) <span class="req">*</span></label><input type="number" min="0" name="min_experience_years" value="{{ $v('min_experience_years', 0) }}">@error('min_experience_years')<div class="st-err">{{ $message }}</div>@enderror</div>
        <div class="st-f"><label class="t">Lowest education level</label><select name="min_education_level"><option value="">Any</option>@foreach($levels as $l)<option value="{{ $l }}" @selected($v('min_education_level') === $l)>{{ $l }}</option>@endforeach</select></div>
        <div class="st-f @error('max_education_level') err @enderror"><label class="t">Highest education level</label><select name="max_education_level"><option value="">Any</option>@foreach($levels as $l)<option value="{{ $l }}" @selected($v('max_education_level') === $l)>{{ $l }}</option>@endforeach</select>@error('max_education_level')<div class="st-err">{{ $message }}</div>@enderror</div>
        <div class="st-f full"><label class="t">Field of study (categories)</label><div class="st-checks">@foreach($categories as $c)<label><input type="checkbox" name="required_categories[]" value="{{ $c }}" @checked(in_array($c, (array) $chosen, true))> {{ $c }}</label>@endforeach</div></div>
        <div class="st-f full"><label class="t">Duties and responsibilities</label><textarea name="duties" style="min-height:140px">{{ $v('duties') }}</textarea><div class="st-hint">One item per line.</div></div>
        <div class="st-f full"><label class="t">Qualifications and experience</label><textarea name="requirements" style="min-height:140px">{{ $v('requirements') }}</textarea><div class="st-hint">One item per line.</div></div>
        <div class="st-f full"><label style="display:flex;align-items:center;gap:8px;font-size:13.5px"><input type="checkbox" name="is_active" value="1" style="width:auto;height:auto" {{ old('is_active', $scheme->is_active ?? true) ? 'checked' : '' }}> Active (can be chosen when creating an advert)</label></div>
    </div></div></section>
    <div style="display:flex;gap:8px;justify-content:flex-end"><a class="st-btn ghost" href="{{ route('staff.schemes') }}">Cancel</a><button class="st-btn" type="submit"><i class="bi bi-check-lg"></i> Save</button></div>
</form>
@endsection
