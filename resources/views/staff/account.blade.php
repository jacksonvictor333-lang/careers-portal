@extends('staff.layout')
@section('title', 'Profile')

@section('content')
<div class="st-center">
<div class="st-head"><div><h1>Profile</h1><p>Your account details and password.</p></div></div>

<section class="st-card"><div class="st-ch">Personal details</div><div class="st-cb">
    <form method="POST" action="{{ route('staff.account.profile') }}">
        @csrf @method('PUT')
        <div class="st-form st-f">
            <div class="{{ $errors->has('first_name') ? 'err' : '' }}"><label class="t">First name <span class="req">*</span></label><input type="text" name="first_name" value="{{ old('first_name', $me->first_name) }}" required></div>
            <div><label class="t">Middle name</label><input type="text" name="middle_name" value="{{ old('middle_name', $me->middle_name) }}"></div>
            <div class="{{ $errors->has('last_name') ? 'err' : '' }}"><label class="t">Last name <span class="req">*</span></label><input type="text" name="last_name" value="{{ old('last_name', $me->last_name) }}" required></div>
            <div class="{{ $errors->has('phone') ? 'err' : '' }}"><label class="t">Mobile number</label><input type="text" name="phone" value="{{ old('phone', $me->phone) }}" placeholder="+255712345678" inputmode="tel"></div>
            <div><label class="t">Email</label><input type="text" value="{{ $me->email }}" disabled><div class="st-hint">Only an administrator can change it.</div></div>
            <div><label class="t">Role</label><div style="padding-top:6px"><span class="st-pill b">{{ $me->role_label }}</span></div>
                <div class="st-hint">Last login: {{ $me->last_login_at ? $me->last_login_at->format('d M Y H:i') : '—' }}</div></div>
        </div>
        <div style="margin-top:16px"><button class="st-btn" type="submit"><i class="bi bi-check2"></i> Save profile</button></div>
    </form>
</div></section>

<section class="st-card"><div class="st-ch">Change password</div><div class="st-cb">
    <form method="POST" action="{{ route('staff.account.password') }}" class="st-f">
        @csrf @method('PUT')
        <div style="margin-bottom:12px"><label class="t">Current password</label><input type="password" name="current_password" required autocomplete="current-password"></div>
        <div style="margin-bottom:12px"><label class="t">New password</label><input type="password" name="password" required autocomplete="new-password"><div class="st-hint">At least 8 characters, with letters and numbers.</div></div>
        <div style="margin-bottom:14px"><label class="t">Confirm new password</label><input type="password" name="password_confirmation" required autocomplete="new-password"></div>
        <button class="st-btn" type="submit"><i class="bi bi-key"></i> Update password</button>
    </form>
</div></section>
</div>
@endsection
