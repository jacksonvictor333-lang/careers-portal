@extends('staff.layout')
@section('title', $mode === 'email' ? 'Change email' : 'Change password')

@section('content')
<div class="st-head"><div><h1>{{ $mode === 'email' ? 'Change email' : 'Change password' }}</h1><p>{{ $applicant->full_name ?: 'Applicant' }} &middot; {{ $applicant->application_number }}</p></div>
    <a class="st-btn ghost" href="{{ route('staff.applicants') }}">Back</a></div>

<section class="st-card" style="max-width:560px"><div class="st-cb">
    <form method="POST" action="{{ $mode === 'email' ? route('staff.applicants.email.update', $applicant->id) : route('staff.applicants.password.update', $applicant->id) }}" class="st-f">
        @csrf @method('PUT')
        @if($mode === 'email')
            <div style="margin-bottom:12px"><label class="t">Current email</label><input type="text" value="{{ $applicant->email }}" disabled></div>
            <div style="margin-bottom:14px"><label class="t">New email <span class="req">*</span></label><input type="email" name="email" value="{{ old('email') }}" required></div>
        @else
            <div style="margin-bottom:12px"><label class="t">New password <span class="req">*</span></label><input type="password" name="password" required autocomplete="new-password"><div class="st-hint">At least 8 characters, letters and numbers. Tell the applicant to change it after signing in.</div></div>
            <div style="margin-bottom:14px"><label class="t">Confirm password <span class="req">*</span></label><input type="password" name="password_confirmation" required autocomplete="new-password"></div>
        @endif
        <button class="st-btn" type="submit"><i class="bi bi-check2"></i> Save</button>
    </form>
</div></section>
@endsection
