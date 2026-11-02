@extends('staff.layout')
@section('title', $user->exists ? 'Edit staff user' : 'Add staff user')

@section('content')
<div class="st-center">
@php $isNew = ! $user->exists; @endphp
<div class="st-head"><div><h1>{{ $isNew ? 'Add staff user' : 'Edit ' . $user->name }}</h1></div>
    <a class="st-btn ghost" href="{{ route('staff.users') }}">Back</a></div>

<section class="st-card"><div class="st-cb">
<form method="POST" action="{{ $isNew ? route('staff.users.store') : route('staff.users.update', $user->id) }}">
    @csrf @if(! $isNew) @method('PUT') @endif
    <div class="st-form st-f">
        <div class="{{ $errors->has('first_name') ? 'err' : '' }}"><label class="t">First name <span class="req">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required></div>
        <div><label class="t">Middle name</label><input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"></div>
        <div class="{{ $errors->has('last_name') ? 'err' : '' }}"><label class="t">Last name <span class="req">*</span></label>
            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required></div>
        <div class="{{ $errors->has('phone') ? 'err' : '' }}"><label class="t">Mobile number</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+255712345678" inputmode="tel"></div>
        <div class="{{ $errors->has('email') ? 'err' : '' }}"><label class="t">Email <span class="req">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
        <div><label class="t">Role <span class="req">*</span></label>
            <select name="role">
                @foreach($roles as $key => $label)
                    <option value="{{ $key }}" {{ old('role', $user->role) === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select></div>
        <div><label class="t">Account</label>
            <label style="display:flex;align-items:center;gap:8px;height:38px;font-size:13.5px">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" style="width:auto;height:auto" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}> Active (can sign in)
            </label></div>
        <div class="{{ $errors->has('password') ? 'err' : '' }}"><label class="t">{{ $isNew ? 'Password' : 'New password' }} @if($isNew)<span class="req">*</span>@endif</label>
            <input type="password" name="password" autocomplete="new-password" {{ $isNew ? 'required' : '' }}>
            <div class="st-hint">{{ $isNew ? 'At least 8 characters, letters and numbers.' : 'Leave empty to keep the current password.' }}</div></div>
        <div><label class="t">Confirm password</label><input type="password" name="password_confirmation" autocomplete="new-password"></div>
    </div>
    <div style="margin-top:18px"><button class="st-btn" type="submit"><i class="bi bi-check2"></i> {{ $isNew ? 'Create user' : 'Save changes' }}</button></div>
</form>
</div></section>
</div>
@endsection
