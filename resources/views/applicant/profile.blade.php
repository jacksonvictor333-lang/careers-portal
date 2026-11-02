@extends('applicant.layout')

@section('title', 'My Profile')

@push('styles')
<style>
    .ap-two { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; align-items: start; }
    @media (max-width: 960px) { .ap-two { grid-template-columns: 1fr; } }

    .pf-top { display: flex; align-items: center; gap: 18px; padding: 6px 0 14px; }
    .pf-photo {
        width: 76px; height: 76px; flex-shrink: 0; overflow: hidden; display: grid; place-items: center;
        border-radius: 50%; font-size: 24px; font-weight: 700; color: var(--navy);
        background: #DBEAFE; border: 1px solid #93C5FD;
    }
    .pf-photo img { width: 100%; height: 100%; object-fit: cover; }
    .pf-name { margin: 0 0 2px; font-size: 20px; font-weight: 700; }
    .pf-sub  { margin: 0; font-size: 13.5px; color: var(--muted); }

    .pf-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 14px 28px; margin: 0; padding-top: 14px; border-top: 1px solid var(--line); }
    .pf-list dt { margin-bottom: 2px; font-size: 12px; color: var(--muted); }
    .pf-list dd { margin: 0; font-size: 14.5px; font-weight: 500; word-break: break-word; }
    .pf-note { margin: 14px 0 0; font-size: 13px; color: var(--muted); }
</style>
@endpush

@section('content')
<div class="ap-wrap wide">

    @include('applicant.partials.header', [
        'title'    => 'My Profile',
        'subtitle' => 'Your account details, email and password.',
        'icon'     => 'bi-person-circle',
    ])

    {{-- ===== MAELEZO YA AKAUNTI ===== --}}
    <div class="ap-card" style="margin-bottom:16px">
        <div class="ap-card-title"><h2>Account information</h2></div>
        <div class="ap-card-body">

            <div class="pf-top">
                <div class="pf-photo">
                    @if($applicant->photo_url)
                        <img src="{{ $applicant->photo_url }}" alt="">
                    @else
                        {{ $applicant->initials() }}
                    @endif
                </div>
                <div>
                    <h3 class="pf-name">{{ $applicant->full_name }}</h3>
                    <p class="pf-sub">{{ $applicant->application_number }}</p>
                </div>
            </div>

            <dl class="pf-list">
                <div><dt>First name</dt><dd>{{ $applicant->first_name ?: '—' }}</dd></div>
                <div><dt>Middle name</dt><dd>{{ $applicant->middle_name ?: '—' }}</dd></div>
                <div><dt>Last name</dt><dd>{{ $applicant->last_name ?: '—' }}</dd></div>
                <div><dt>Email address</dt><dd>{{ $applicant->email }}</dd></div>
                <div><dt>Phone</dt><dd>{{ $applicant->phone ?: '—' }}</dd></div>
                <div><dt>Member since</dt><dd>{{ optional($applicant->created_at)->format('d M Y') ?: '—' }}</dd></div>
            </dl>

            <p class="pf-note">
                To change your name, phone or other personal information, go to
                <a class="ap-link" href="{{ Route::has('applicant.personal-details') ? route('applicant.personal-details') : '#' }}">Personal Details</a>.
            </p>
        </div>
    </div>

    <div class="ap-two">

        {{-- ===== BADILISHA EMAIL ===== --}}
        <div class="ap-card">
            <div class="ap-card-title"><h2>Change email</h2></div>
            <div class="ap-card-body" style="padding-top:6px">
                <form method="POST" action="{{ route('applicant.profile.email') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="ap-form-grid" style="grid-template-columns:1fr">
                        <div class="ap-field @error('new_email') has-error @enderror">
                            <label for="new_email">New email address <span class="req">*</span></label>
                            <input type="email" id="new_email" name="new_email" value="{{ old('new_email') }}"
                                   placeholder="{{ $applicant->email }}" autocomplete="email">
                            @error('new_email') <div class="ap-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ap-field @error('email_password') has-error @enderror">
                            <label for="email_password">Current password <span class="req">*</span></label>
                            <input type="password" id="email_password" name="email_password" autocomplete="current-password">
                            <div class="ap-hint">We ask for your password to confirm it is you.</div>
                            @error('email_password') <div class="ap-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="ap-form-actions">
                        <button type="submit" class="ap-btn primary"><i class="bi bi-check-lg"></i> Update email</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== BADILISHA NENOSIRI ===== --}}
        <div class="ap-card">
            <div class="ap-card-title"><h2>Change password</h2></div>
            <div class="ap-card-body" style="padding-top:6px">
                <form method="POST" action="{{ route('applicant.profile.password') }}" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="ap-form-grid" style="grid-template-columns:1fr">
                        <div class="ap-field @error('current_password') has-error @enderror">
                            <label for="current_password">Current password <span class="req">*</span></label>
                            <input type="password" id="current_password" name="current_password" autocomplete="current-password">
                            @error('current_password') <div class="ap-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ap-field @error('password') has-error @enderror">
                            <label for="password">New password <span class="req">*</span></label>
                            <input type="password" id="password" name="password" autocomplete="new-password">
                            <div class="ap-hint">At least 8 characters, with letters and numbers.</div>
                            @error('password') <div class="ap-error">{{ $message }}</div> @enderror
                        </div>

                        <div class="ap-field">
                            <label for="password_confirmation">Confirm new password <span class="req">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="ap-form-actions">
                        <button type="submit" class="ap-btn primary"><i class="bi bi-shield-lock"></i> Change password</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
