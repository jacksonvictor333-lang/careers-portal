@extends('staff.layout')
@section('title', $employer->exists ? 'Edit employer' : 'New employer')

@section('content')
@php $exists = $employer->exists; $v = fn ($k, $d = null) => old($k, $employer->{$k} ?? $d); @endphp
<div class="st-head"><div><h1>{{ $exists ? 'Edit employer' : 'New employer' }}</h1><p>Used for the "address your letter to" box on adverts.</p></div></div>

<form method="POST" action="{{ $exists ? route('staff.employers.update', $employer->id) : route('staff.employers.store') }}" novalidate>
    @csrf
    @if($exists) @method('PUT') @endif
    <section class="st-card"><div class="st-cb"><div class="st-form">
        <div class="st-f full @error('name') err @enderror"><label class="t" for="name">Employer name <span class="req">*</span></label>
            <input type="text" id="name" name="name" value="{{ $v('name') }}">@error('name')<div class="st-err">{{ $message }}</div>@enderror</div>
        <div class="st-f"><label class="t" for="addressee">Addressed to</label>
            <input type="text" id="addressee" name="addressee" value="{{ $v('addressee') }}" placeholder="The Permanent Secretary,"></div>
        <div class="st-f"><label class="t" for="phone">Phone</label><input type="text" id="phone" name="phone" value="{{ $v('phone') }}"></div>
        <div class="st-f"><label class="t" for="address_lines">Address lines</label>
            <textarea id="address_lines" name="address_lines" placeholder="Ministry of Finance,&#10;P.O. Box 123,&#10;Dodoma.">{{ $v('address_lines') }}</textarea>
            <div class="st-hint">One line per line.</div></div>
        <div class="st-f @error('email') err @enderror"><label class="t" for="email">Email</label><input type="email" id="email" name="email" value="{{ $v('email') }}">@error('email')<div class="st-err">{{ $message }}</div>@enderror</div>
        <div class="st-f full"><label style="display:flex;align-items:center;gap:8px;font-size:13.5px"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $employer->is_active ?? true))> Active</label></div>
    </div></div></section>
    <div style="display:flex;gap:8px;justify-content:flex-end">
        <a class="st-btn ghost" href="{{ route('staff.employers') }}">Cancel</a>
        <button class="st-btn" type="submit"><i class="bi bi-check-lg"></i> Save</button>
    </div>
</form>
@endsection
