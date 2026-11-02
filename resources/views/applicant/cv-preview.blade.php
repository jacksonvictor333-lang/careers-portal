@extends('applicant.layout')

@section('title', 'CV Preview')

@section('content')
<div class="ap-wrap wide">

    <div class="no-print">
        @include('applicant.partials.header', [
            'title'    => 'CV Preview',
            'subtitle' => 'This is how your information appears. Use Print to save it as PDF.',
            'icon'     => 'bi-file-earmark-person',
        ])

        <div style="margin-bottom:14px">
            <button type="button" class="ap-btn primary" onclick="window.print()"><i class="bi bi-printer"></i> Print / Save as PDF</button>
        </div>
    </div>

    <div class="ap-card cv">
        <div class="ap-card-body" style="padding-top:26px">

            {{-- Kichwa --}}
            <div class="cv-head">
                <div class="cv-photo">
                    @if($applicant->photo_url)
                        <img src="{{ $applicant->photo_url }}" alt="">
                    @else
                        {{ $applicant->initials() }}
                    @endif
                </div>
                <div>
                    <h2>{{ $applicant->full_name }}</h2>
                    <div class="cv-meta">
                        <span>{{ $applicant->application_number }}</span>
                        <span>{{ $applicant->email }}</span>
                        @if($applicant->phone)<span>{{ $applicant->phone }}</span>@endif
                        @if($applicant->region || $applicant->district)
                            <span>{{ collect([$applicant->district, $applicant->region])->filter()->implode(', ') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Personal --}}
            <h3 class="cv-h">Personal Details</h3>
            <dl class="cv-dl">
                <div><dt>Gender</dt><dd>{{ $applicant->gender ?: '—' }}</dd></div>
                <div><dt>Date of birth</dt><dd>{{ $applicant->date_of_birth ? $applicant->date_of_birth->format('d M Y') : '—' }}</dd></div>
                <div><dt>Place of birth</dt><dd>{{ $applicant->place_of_birth ?: '—' }}</dd></div>
                <div><dt>Marital status</dt><dd>{{ $applicant->marital_status ?: '—' }}</dd></div>
                <div><dt>Nationality</dt><dd>{{ $applicant->nationality ?: '—' }}</dd></div>
                <div><dt>Address</dt><dd>{{ $applicant->address ?: '—' }}</dd></div>
            </dl>

            {{-- Academic --}}
            <h3 class="cv-h">Academic Qualifications</h3>
            @forelse($applicant->academicQualifications as $a)
                <div class="cv-row">
                    <div class="when">{{ $a->start_year ? $a->start_year . ' – ' : '' }}{{ $a->end_year }}</div>
                    <div>
                        <b>{{ $a->level }}</b>@if($a->programme), {{ $a->programme }}@endif
                        <div class="sub">{{ $a->institution }}@if($a->country), {{ $a->country }}@endif @if($a->exam_number) · Exam No. {{ $a->exam_number }}@endif @if($a->gpa_display) · GPA {{ $a->gpa_display }}@endif</div>
                    </div>
                </div>
            @empty
                <p class="cv-none">Not provided.</p>
            @endforelse

            {{-- Professional --}}
            <h3 class="cv-h">Professional Qualifications</h3>
            @forelse($applicant->professionalQualifications as $p)
                <div class="cv-row">
                    <div class="when">{{ $p->year_obtained }}</div>
                    <div>
                        <b>{{ $p->title }}</b>
                        <div class="sub">{{ $p->awarding_body }}</div>
                    </div>
                </div>
            @empty
                <p class="cv-none">Not provided.</p>
            @endforelse

            {{-- Work --}}
            <h3 class="cv-h">Working Experience</h3>
            @forelse($applicant->workExperiences as $w)
                <div class="cv-row">
                    <div class="when">{{ $w->period }}</div>
                    <div>
                        <b>{{ $w->job_title }}</b>, {{ $w->employer }}
                        @if($w->location)<div class="sub">{{ $w->location }}</div>@endif
                        @if($w->duties)<div class="duties">{!! nl2br(e($w->duties)) !!}</div>@endif
                    </div>
                </div>
            @empty
                <p class="cv-none">Not provided.</p>
            @endforelse

            {{-- Training --}}
            <h3 class="cv-h">Training and Workshops</h3>
            @forelse($applicant->trainings as $t)
                <div class="cv-row">
                    <div class="when">{{ $t->period }}</div>
                    <div>
                        <b>{{ $t->title }}</b>
                        <div class="sub">{{ $t->institution }}@if($t->location), {{ $t->location }}@endif</div>
                    </div>
                </div>
            @empty
                <p class="cv-none">Not provided.</p>
            @endforelse

            {{-- Languages + Computer --}}
            <h3 class="cv-h">Language Proficiency</h3>
            @forelse($applicant->languageProficiencies as $l)
                <div class="cv-row">
                    <div class="when">{{ $l->language }}</div>
                    <div class="sub" style="margin-top:0">
                        Speaking: {{ $l->speaking }} · Reading: {{ $l->reading }} · Writing: {{ $l->writing }} · Understanding: {{ $l->understanding }}
                    </div>
                </div>
            @empty
                <p class="cv-none">Not provided.</p>
            @endforelse

            <h3 class="cv-h">Computer Literacy</h3>
            @forelse($applicant->computerSkills as $c)
                <div class="cv-row">
                    <div class="when">{{ $c->level }}</div>
                    <div>{{ $c->skill }}</div>
                </div>
            @empty
                <p class="cv-none">Not provided.</p>
            @endforelse

            {{-- Referees --}}
            <h3 class="cv-h">Referees</h3>
            @forelse($applicant->referees as $r)
                <div class="cv-row">
                    <div class="when">
                        <b style="color:var(--ink); font-size:14px">{{ $r->name }}</b>
                        <div style="margin-top:1px">{{ $r->position }}</div>
                    </div>
                    <div>
                        <div><i class="bi bi-telephone" style="color:var(--muted)"></i> {{ $r->phone }}</div>
                        @if($r->email)<div><i class="bi bi-envelope" style="color:var(--muted)"></i> {{ $r->email }}</div>@endif
                        <div class="sub"><i class="bi bi-building"></i> {{ $r->organization }}</div>
                    </div>
                </div>
            @empty
                <p class="cv-none">Not provided.</p>
            @endforelse

            {{-- Declaration --}}
            @if($applicant->declaration_accepted_at)
                <h3 class="cv-h">Declaration</h3>
                <p class="cv-decl">
                    I declare that the information provided is true and complete.<br>
                    <b>{{ $applicant->declaration_name }}</b> &middot; {{ $applicant->declaration_accepted_at->format('d M Y') }}
                </p>
            @endif

        </div>
    </div>

</div>
@endsection
