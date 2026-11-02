@extends('applicant.layout')
@section('title', 'Reset password')

@section('content')
<div class="ap-wrap" style="max-width:460px">
    <div class="ap-card">
        <div class="ap-card-head"><h1>Set a new password</h1><p>{{ $applicant->email }}</p></div>
        <div class="ap-card-body">
            @if($errors->any())<div class="ap-alert error" style="margin-bottom:12px">{{ $errors->first() }}</div>@endif
            <form method="POST" action="{{ $action }}">
                @csrf
                <div class="ap-form-grid" style="grid-template-columns:1fr">
                    <div class="ap-field"><label>New password <span class="req">*</span></label><input type="password" name="password" required autocomplete="new-password"><div class="ap-hint">At least 8 characters, with letters and numbers.</div></div>
                    <div class="ap-field"><label>Confirm new password <span class="req">*</span></label><input type="password" name="password_confirmation" required autocomplete="new-password"></div>
                </div>
                <div class="ap-form-actions"><button class="ap-btn primary" type="submit"><i class="bi bi-shield-lock"></i> Change password</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
