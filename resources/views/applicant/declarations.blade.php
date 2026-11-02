@extends('applicant.layout')

@section('title', 'Declarations')

@section('content')
<div class="ap-wrap wide">

    @include('applicant.partials.header', [
        'title'    => 'Declaration',
        'subtitle' => 'Confirm that the information you provided is true and complete.',
        'icon'     => 'bi-patch-check',
    ])

    @if($applicant->declaration_accepted_at)
        <div class="ap-alert ok" style="margin-bottom:14px" role="status">
            <i class="bi bi-check-circle-fill"></i>
            <span>
                Declared by <b>{{ $applicant->declaration_name }}</b>
                on {{ $applicant->declaration_accepted_at->format('d M Y, H:i') }}.
                You can re-confirm below if you update your information.
            </span>
        </div>
    @endif

    <div class="ap-card">
        <div class="ap-card-body" style="padding-top:22px">

            <div class="ap-statement">
                <p>I declare that:</p>
                <ol>
                    @foreach(\App\Models\DocumentContent::lines('declaration', [
                        'All the information I have provided in this application is true, complete and correct to the best of my knowledge.',
                        'I have attached genuine copies of my certificates and other supporting documents.',
                        'I understand that providing false information or forged documents may lead to disqualification, or to dismissal and legal action if I am employed.',
                        'I authorise the recruiting institution to verify the information and documents I have provided.',
                    ]) as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ol>
            </div>

            <form method="POST" action="{{ route('applicant.declaration.store') }}" novalidate>
                @csrf

                <div class="ap-form-grid">
                    <div class="ap-field @error('declaration_name') has-error @enderror" style="grid-column: 1 / -1">
                        <label for="declaration_name">Type your full name as a signature <span class="req">*</span></label>
                        <input type="text" id="declaration_name" name="declaration_name"
                               value="{{ old('declaration_name', $applicant->declaration_name ?: $applicant->full_name) }}">
                        @error('declaration_name')
                            <div class="ap-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="ap-field @error('agree') has-error @enderror" style="grid-column: 1 / -1">
                        <label class="ap-check" for="agree">
                            <input type="checkbox" id="agree" name="agree" value="1" @checked(old('agree'))>
                            I have read and agree to the declaration above.
                        </label>
                        @error('agree')
                            <div class="ap-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="ap-form-actions">
                    <button type="submit" class="ap-btn primary"><i class="bi bi-check-lg"></i> Submit declaration</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
