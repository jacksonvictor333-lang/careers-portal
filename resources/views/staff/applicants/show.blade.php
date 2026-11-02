@extends('staff.layout')
@section('title', 'Applicant CV')

@section('content')
@php
    $f = fn ($path) => $path ? route('staff.files', ['path' => $path]) : null;
    $a = $applicant;
    $age = $a->date_of_birth ? $a->date_of_birth->age : null;
    $v = fn ($x) => filled($x) ? $x : '—';
    $title = 'Applicant CV'; $cur = 'cv';
    $isAdmin = auth('staff')->user()->isAdmin();
    $del = function ($kind, $item) use ($a, $isAdmin) {
        if (! $isAdmin) { return ''; }
        return '<form method="POST" action="' . e(route('staff.applicants.files.delete', $a->id)) . '" style="display:inline" onsubmit="return confirm(\'Delete this file permanently? The applicant will have to upload it again.\')">'
            . csrf_field() . method_field('DELETE') . '<input type="hidden" name="kind" value="' . $kind . '"><input type="hidden" name="item" value="' . $item . '">'
            . '<button type="submit" class="cv-del" title="Delete file"><i class="bi bi-trash"></i></button></form>';
    };
@endphp
@include('staff.applicants.partials-cv-head')

<div class="cv-grid">
    {{-- ============ Kushoto: taarifa binafsi ============ --}}
    <div class="cv-left">
        <div class="cv-photo">
            <div class="im">@if($a->photo_path)<img src="{{ $f($a->photo_path) }}" alt="">@else{{ method_exists($a, 'initials') ? $a->initials() : '' }}@endif</div>
            <h2>{{ $a->full_name ?: 'N/A' }}</h2>
        </div>
        <div class="cv-row"><b>Application No.:</b><span>{{ $v($a->application_number) }}</span></div>
        <div class="cv-row"><b>Address:</b><span>{{ $v(implode(', ', array_filter([$a->postal_address, $a->postal_region]))) }}</span></div>
        <div class="cv-row"><b>Current Residence:</b><span>{{ $v(implode(', ', array_filter([$a->district, $a->region, $a->residence_country]))) }}</span></div>
        <div class="cv-row"><b>Phone number:</b><span>{{ $v($a->phone) }}@if($a->alt_phone)<br>{{ $a->alt_phone }}@endif</span></div>
        <div class="cv-row"><b>Email:</b><span>{{ $v($a->email) }}</span></div>
        <div class="cv-row"><b>NIN:</b><span>{{ $v($a->nida_number) }}</span></div>
        <div class="cv-row"><b>Date Of Birth:</b><span>{{ $a->date_of_birth ? $a->date_of_birth->format('M d, Y') : '—' }}</span></div>
        <div class="cv-row"><b>Age:</b><span>{{ $age !== null ? $age . ' Years' : '—' }}</span></div>
        <div class="cv-row"><b>Gender:</b><span>{{ $a->gender ? strtoupper($a->gender) : '—' }}</span></div>
        <div class="cv-row"><b>Marital Status:</b><span>{{ $v($a->marital_status) }}</span></div>
        <div class="cv-row"><b>Nationality:</b><span>{{ $a->nationality ? strtoupper($a->nationality) : '—' }}</span></div>
        <div class="cv-row"><b>Place of Birth:</b><span>{{ $v(strtoupper(implode(' - ', array_filter([$a->birth_region, $a->birth_district])) ?: (string) $a->place_of_birth)) }}</span></div>
        <div class="cv-row"><b>Disability:</b><span>{{ strcasecmp((string) $a->disability, 'Yes') === 0 ? 'YES' . ($a->disability_details ? ' (' . $a->disability_details . ')' : '') : 'NO' }}
            @if($a->disability_confirmed_at)<span class="cv-tag g">Confirmed</span>@endif</span></div>
        <div class="cv-row"><b>Date Joined:</b><span>{{ optional($a->created_at)->format('M d, Y, g:i a') }}</span></div>
        @php [$pct] = $a->profileCompleteness(); $pc = $pct >= 90 ? '#12B76A' : ($pct >= 50 ? '#1D4E89' : '#F79009'); @endphp
        <div class="cv-row"><b>Profile completeness:</b><span><span class="pbar"><i style="width:{{ $pct }}%;background:{{ $pc }}"></i></span>{{ $pct }}%</span></div>
        <div class="cv-row"><b>Status:</b><span>
            @if($a->is_locked)<span class="st-pill s a">Locked</span>@elseif($a->is_active)<span class="st-pill s g">Active</span>@else<span class="st-pill s r">Not Active</span>@endif</span></div>

        @foreach(['NECTA Verification' => 'bi-award', 'NACTE Verification' => 'bi-mortarboard', 'HESLB Verification' => 'bi-bank'] as $label => $ic)
            <div class="cv-ver"><b><i class="bi {{ $ic }}"></i> {{ $label }}:</b><span class="chk" title="Not connected yet"><i class="bi bi-search"></i> Not connected</span></div>
        @endforeach

        <details class="cv-dh"><summary><i class="bi bi-clock-history"></i> Document History <span class="cnt">{{ collect($docHistory)->flatten(1)->count() }}</span><i class="bi bi-chevron-down caret"></i></summary>
            @forelse($docHistory as $level => $docs)
                <div class="lv">{{ $level }}</div>
                @foreach($docs as $d)
                    <div class="it">
                        <div><b>{{ $d['type'] }}</b> &middot; {{ $d['n'] === 1 ? '1st' : ($d['n'] === 2 ? '2nd' : $d['n'] . 'th') }} upload
                            <div class="st-sub">{{ $d['at'] ? $d['at']->format('d M Y H:i') : '' }}</div></div>
                        <div><span class="cv-tag {{ $d['status'] === 'Current' ? 'g' : 'l' }}">{{ $d['status'] }}</span>
                            @if($d['status'] !== 'Removed')<a class="cv-tag" href="{{ $f($d['path']) }}"><i class="bi bi-eye"></i> View</a>@endif</div>
                    </div>
                @endforeach
            @empty
                <div class="st-empty" style="padding:6px 16px 14px">No documents uploaded yet.</div>
            @endforelse
        </details>
    </div>

    {{-- ============ Kulia: sifa na wasifu ============ --}}
    <div class="cv-right">
        <div class="cv-sec"><h3>Academic Qualification</h3>
            @if($a->academicQualifications->count())
            <table><thead><tr><th style="width:44px">#</th><th>Year</th><th>Programme</th><th>Institution</th><th>Files</th>@if($isAdmin)<th style="width:70px;text-align:center">Action</th>@endif</tr></thead><tbody>
                @foreach($a->academicQualifications as $i => $q)
                    <tr><td>{{ $i + 1 }}</td>
                        <td>@if($q->start_year)<span class="cv-tag l"><i class="bi bi-calendar-event"></i> {{ $q->start_year }}</span>@endif<span class="cv-tag l"><i class="bi bi-calendar-event"></i> {{ $q->end_year }}</span></td>
                        <td><b>{{ $q->level }}</b>@if($q->programme_label)<div class="st-sub">{{ $q->programme_label }}</div>@endif
                            @if($q->exam_number)<span class="cv-tag g"><i class="bi bi-award"></i> {{ $q->exam_number }}</span>@endif
                            @if($q->programme_category)<div class="st-sub">{{ $q->programme_category }}</div>@endif</td>
                        <td>{{ strtoupper((string) $q->institution) }}@if($q->gpa_display)<div class="st-sub"><i class="bi bi-bar-chart"></i> {{ $q->gpa_display }}</div>@endif</td>
                        <td>@if($q->attachment_path)<a class="cv-tag" target="_blank" href="{{ $f($q->attachment_path) }}"><i class="bi bi-file-earmark-check"></i> Certificate</a>@endif
                            @if($q->transcript_path)<a class="cv-tag" target="_blank" href="{{ $f($q->transcript_path) }}"><i class="bi bi-file-earmark-text"></i> Transcript</a>@endif</td>
                        @if($isAdmin)<td style="text-align:center"><form method="POST" action="{{ route('staff.applicants.academic.delete', [$a->id, $q->id]) }}" onsubmit="return confirm('Delete this education level ({{ e($q->level) }}) and its certificate and transcript? The applicant will have to add it again.')">@csrf @method('DELETE')<button type="submit" class="cv-del" title="Delete this education level"><i class="bi bi-trash"></i></button></form></td>@endif</tr>
                @endforeach
            </tbody></table>
            @else<div class="cv-empty">None added.</div>@endif
        </div>

        <div class="cv-sec"><h3>Professional Qualification</h3>
            @if($a->professionalQualifications->count())
            <table><thead><tr><th style="width:44px">#</th><th>Qualification</th><th>Awarding body</th><th>Year</th><th>File</th></tr></thead><tbody>
                @foreach($a->professionalQualifications as $i => $p)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $p->title }}</td><td>{{ $p->awarding_body }}</td><td>{{ $p->year_obtained }}</td>
                        <td>@if($p->attachment_path)<a class="cv-tag" target="_blank" href="{{ $f($p->attachment_path) }}"><i class="bi bi-file-earmark-check"></i> Certificate</a>{!! $del('professional', $p->id) !!}@endif</td></tr>
                @endforeach
            </tbody></table>
            @else<div class="cv-empty">None added.</div>@endif
        </div>

        <div class="cv-sec"><h3>Working Experience</h3>
            @if($a->workExperiences->count())
            <table><thead><tr><th style="width:44px">#</th><th>Period</th><th>Post</th><th>Employer</th><th>Location</th></tr></thead><tbody>
                @foreach($a->workExperiences as $i => $w)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $w->period }}</td><td>{{ $w->job_title }}</td><td>{{ $w->employer }}</td><td>{{ $v($w->location) }}</td></tr>
                @endforeach
            </tbody></table>
            @else<div class="cv-empty">None added.</div>@endif
        </div>

        <div class="cv-sec"><h3>Training and Workshop</h3>
            @if($a->trainings->count())
            <table><thead><tr><th style="width:44px">#</th><th>Period</th><th>Title</th><th>Institution</th><th>File</th></tr></thead><tbody>
                @foreach($a->trainings as $i => $t)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $t->period }}</td><td>{{ $t->title }}</td><td>{{ $t->institution }}</td>
                        <td>@if($t->attachment_path)<a class="cv-tag" target="_blank" href="{{ $f($t->attachment_path) }}"><i class="bi bi-file-earmark-check"></i> Certificate</a>{!! $del('training', $t->id) !!}@endif</td></tr>
                @endforeach
            </tbody></table>
            @else<div class="cv-empty">None added.</div>@endif
        </div>

        <div class="cv-sec"><h3>Computer Literacy</h3>
            @if($a->computerSkills->count())
            <table><thead><tr><th style="width:44px">#</th><th>Skill Name</th><th>Level</th></tr></thead><tbody>
                @foreach($a->computerSkills as $i => $c)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $c->skill }}</td><td><span class="cv-tag"><i class="bi bi-display"></i> {{ $c->level }}</span></td></tr>
                @endforeach
            </tbody></table>
            @else<div class="cv-empty">None added.</div>@endif
        </div>

        <div class="cv-sec"><h3>Language Proficiency</h3>
            @if($a->languageProficiencies->count())
            <table><thead><tr><th style="width:44px">#</th><th>Name</th><th>Proficiency</th></tr></thead><tbody>
                @foreach($a->languageProficiencies as $i => $l)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $l->language }}</td>
                        <td><span class="cv-tag l"><i class="bi bi-book"></i> Read: {{ $l->reading }}</span><span class="cv-tag l"><i class="bi bi-pencil"></i> Write: {{ $l->writing }}</span>
                            <span class="cv-tag l"><i class="bi bi-mic"></i> Speak: {{ $l->speaking }}</span><span class="cv-tag l"><i class="bi bi-ear"></i> Understand: {{ $l->understanding }}</span></td></tr>
                @endforeach
            </tbody></table>
            @else<div class="cv-empty">None added.</div>@endif
        </div>

        <div class="cv-sec"><h3>Referees</h3>
            @if($a->referees->count())
            <table><thead><tr><th style="width:44px">#</th><th>Name</th><th>Institution/Organization and Title</th><th>Contacts</th></tr></thead><tbody>
                @foreach($a->referees as $i => $r)
                    <tr><td>{{ $i + 1 }}</td><td><b>{{ $r->name }}</b></td>
                        <td>{{ $r->organization }}<div class="st-sub">{{ strtoupper((string) $r->position) }}</div></td>
                        <td>@if($r->email)<span class="cv-tag l"><i class="bi bi-envelope"></i> {{ $r->email }}</span><br>@endif<span class="cv-tag l"><i class="bi bi-telephone"></i> {{ $r->phone }}</span></td></tr>
                @endforeach
            </tbody></table>
            @else<div class="cv-empty">None added.</div>@endif
        </div>

        <div class="cv-sec"><h3>Other Attachments</h3>
            @if($a->attachments->count())
            <table><thead><tr><th style="width:44px">#</th><th>Type</th><th>File</th></tr></thead><tbody>
                @foreach($a->attachments as $i => $at)
                    <tr><td>{{ $i + 1 }}</td><td>{{ $at->type }}</td><td><a class="cv-tag" target="_blank" href="{{ $f($at->attachment_path) }}"><i class="bi bi-paperclip"></i> {{ basename((string) $at->attachment_path) }}</a>{!! $del('attachment', $at->id) !!}</td></tr>
                @endforeach
            </tbody></table>
            @else<div class="cv-empty">None uploaded.</div>@endif
        </div>
    </div>
</div>
@endsection
