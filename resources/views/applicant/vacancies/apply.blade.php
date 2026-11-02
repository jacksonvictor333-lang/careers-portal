@extends('applicant.layout')

@section('title', $mode === 'edit' ? 'Change application letter' : 'Apply')

@push('styles')
    @include('applicant.vacancies._styles')
@endpush

@section('content')
<div class="ap-wrap wide">

    @include('applicant.partials.header', [
        'title'    => $mode === 'edit' ? 'Change your application letter' : 'Apply for this post',
        'subtitle' => $vacancy->title . ' - ' . $vacancy->employer . ' (Ref: ' . $vacancy->reference_number . ')',
        'icon'     => 'bi-envelope-paper',
    ])

    <div class="vc-alert ok" style="background:#EFF8FF;color:#175CD3;border-color:#B2DDFF">
        <i class="bi bi-info-circle-fill"></i>
        <span>
            @if($mode === 'edit')
                You can replace your letter until <b>{{ $vacancy->closes_at->format('d F Y') }}</b> (closing date of the advert).
            @else
                Attach your application letter as a PDF. After you apply you can still replace it until
                <b>{{ $vacancy->closes_at->format('d F Y') }}</b>.
            @endif
        </span>
    </div>

    <form method="POST" action="{{ $action }}" class="vc-panel" enctype="multipart/form-data" novalidate>
        @csrf
        @if($mode === 'edit')
            @method('PUT')
        @endif

        <div class="vc-ph">Attach your letter</div>
        <div class="vc-pb">
            <div class="vc-field">
                <label class="t" for="letter_file">Application letter (PDF) <span style="color:#D92D20">*</span></label>
                <input type="file" id="letter_file" name="letter_file" accept="application/pdf,.pdf" required>
                <div class="vc-hint">PDF only, maximum 2 MB. The file is saved with your application and is shown when you open <b>Letter</b>.</div>

                @if($current)
                    <div class="vc-att" style="margin-top:10px;cursor:default">
                        <i class="bi bi-file-earmark-pdf" style="color:#D92D20;font-size:18px"></i>
                        <span>Current letter: <b>{{ basename((string) $current->file_path) }}</b>
                            <small>Choosing a new file replaces it.</small></span>
                    </div>
                @endif
            </div>

            <label class="vc-confirm">
                <input type="checkbox" name="confirm" value="1" @checked(old('confirm')) required>
                <span><b>I confirm my letter is addressed to:</b>
                    <div class="a">@foreach(($vacancy->letter_address_lines ?? config('application.letter_address', [])) as $line){{ $line }}@if(! $loop->last), @endif @endforeach</div>
                    @error('confirm')<div style="margin-top:6px;color:#B42318;font-size:12.5px">{{ $message }}</div>@enderror
                </span>
            </label>

            <div class="vc-actions">
                <a class="vc-btn ghost" href="{{ $mode === 'edit' ? route('applicant.my-applications') : route('vacancies.show', $vacancy->id) }}">Cancel</a>
                <button type="submit" class="vc-btn"><i class="bi bi-send-check"></i> {{ $mode === 'edit' ? 'Save letter' : 'Submit application' }}</button>
            </div>
        </div>
    </form>

</div>
@endsection
