@php
    $staff = auth('staff')->user();
    $manage = $staff->canManage(); $admin = $staff->isAdmin();
    $page = isset($requests) ? $requests : $applicants;
@endphp
<div class="st-bar">
    <form method="GET" action="{{ route('staff.applicants') }}">Show
        <input type="hidden" name="tab" value="{{ $tab }}"><input type="hidden" name="q" value="{{ $q }}">
        <select name="per" onchange="this.form.submit()">@foreach([10, 25, 50, 100] as $n)<option value="{{ $n }}" {{ $per === $n ? 'selected' : '' }}>{{ $n }}</option>@endforeach</select> entries</form>
    <span>Showing {{ $page->firstItem() ?? 0 }} to {{ $page->lastItem() ?? 0 }} of {{ $page->total() }} entries</span>
</div>

<section class="st-card" style="overflow:visible">
@if($tab === 'requests')
    <div class="st-wrap" style="overflow:visible"><table class="st-t">
        <thead><tr><th>#</th><th>Applicant</th><th>Email</th><th>Request</th><th>Requested by</th><th>Date</th><th>Status</th><th class="r">Action</th></tr></thead>
        <tbody>
        @forelse($requests as $i => $r)
            <tr>
                <td>{{ $requests->firstItem() + $i }}</td>
                <td><a href="{{ route('staff.applicants.show', $r->applicant_id) }}">{{ optional($r->applicant)->full_name ?: 'N/A' }}</a></td>
                <td>{{ optional($r->applicant)->email }}</td>
                <td>{{ $r->type_label }}@if($r->reason)<div class="st-sub">{{ $r->reason }}</div>@endif</td>
                <td>{{ optional($r->requester)->name ?: '—' }}</td>
                <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                <td><span class="st-pill {{ $r->status === 'pending' ? 'a' : ($r->status === 'approved' ? 'g' : 'r') }}">{{ ucfirst($r->status) }}</span></td>
                <td class="r">
                    @if($r->status === 'pending' && $admin)
                        <form method="POST" action="{{ route('staff.applicants.decide', $r->id) }}" style="display:inline">@csrf<input type="hidden" name="decision" value="approve"><button class="st-btn sm" type="submit">Approve</button></form>
                        <form method="POST" action="{{ route('staff.applicants.decide', $r->id) }}" style="display:inline">@csrf<input type="hidden" name="decision" value="reject"><button class="st-btn ghost sm" type="submit">Reject</button></form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="st-empty">No requests.</td></tr>
        @endforelse
        </tbody>
    </table></div>
@else
    <div class="st-wrap" style="overflow:visible"><table class="st-t">
        <thead><tr><th>#</th><th>Applicant Name</th><th>NIN</th><th>Email Address</th><th>Gender</th>
            @if($tab === 'nida')<th>Date of birth</th>@else<th>Physical Disability</th>@endif
            <th>Profile</th>
            <th style="text-align:center">Status</th><th style="text-align:center">Action</th></tr></thead>
        <tbody>
        @forelse($applicants as $i => $a)
            @php $disabled = strcasecmp((string) $a->disability, 'Yes') === 0; @endphp
            <tr>
                <td>{{ $applicants->firstItem() + $i }}</td>
                <td class="nm">{{ trim((string) $a->full_name) !== '' ? mb_strtoupper($a->full_name) : 'N/A' }}</td>
                <td>{{ $a->nida_number ?: 'N/A' }}</td>
                <td>{{ $a->email }}</td>
                <td>{{ $a->gender ? strtoupper($a->gender) : 'N/A' }}</td>
                @if($tab === 'nida')
                    <td>{{ $a->date_of_birth ? $a->date_of_birth->format('d M Y') : 'N/A' }}</td>
                @else
                    <td>{{ $disabled ? 'YES' : 'NO' }}@if($disabled && $a->disability_confirmed_at) <span class="st-pill g">Confirmed</span>@endif</td>
                @endif
                @php [$pct] = $a->profileCompleteness(); $pc = $pct >= 90 ? '#12B76A' : ($pct >= 50 ? '#1D4E89' : '#F79009'); @endphp
                <td title="Profile completeness"><span class="pbar"><i style="width:{{ $pct }}%;background:{{ $pc }}"></i></span><span class="ptxt">{{ $pct }}%</span></td>
                <td style="text-align:center">
                    @if($a->is_locked)<span class="st-pill s a">Locked</span>
                    @elseif($a->is_active)<span class="st-pill s g">Active</span>
                    @else<span class="st-pill s r">Not Active</span>@endif
                </td>
                <td style="text-align:center">
                    <details class="st-dd"><summary><i class="bi bi-list"></i></summary>
                        <div class="st-pop">
                            <a href="{{ route('staff.applicants.show', $a->id) }}"><i class="bi bi-clipboard2-data"></i> View Profile</a>
                            @if($manage)
                                <form method="POST" action="{{ route('staff.applicants.reset-link', $a->id) }}">@csrf<button type="submit"><i class="bi bi-shield-check"></i> Send Reset Password Link</button></form>
                                <form method="POST" action="{{ route('staff.applicants.disability', $a->id) }}">@csrf<button type="submit" {{ $disabled ? '' : 'disabled' }}><i class="bi bi-person-check"></i> Confirm Disability</button></form>
                            @endif
                            @if($admin)
                                <form method="POST" action="{{ route('staff.applicants.password', $a->id) }}" onsubmit="return confirm('Generate a new temporary password for this applicant? The old password will stop working.')">@csrf<button type="submit"><i class="bi bi-shield-lock"></i> Change Password</button></form>
                                <button type="button" onclick="openEmail({{ $a->id }}, @js($a->email), @js(trim((string) $a->full_name)))"><i class="bi bi-pencil"></i> Change Email</button>
                            @endif
                            @if($manage)
                                <hr>
                                <form method="POST" action="{{ route('staff.applicants.active', $a->id) }}">@csrf<button type="submit"><i class="bi bi-person-gear"></i> {{ $a->is_active ? 'Deactivate Applicant' : 'Activate Applicant' }}</button></form>
                                <form method="POST" action="{{ route('staff.applicants.lock', $a->id) }}">@csrf<button type="submit"><i class="bi bi-{{ $a->is_locked ? 'unlock' : 'lock' }}"></i> {{ $a->is_locked ? 'Unlock Applicant' : 'Lock Applicant' }}</button></form>
                            @endif
                            @if(! $a->is_active || $a->is_locked)
                                <form method="POST" action="{{ route('staff.applicants.request', $a->id) }}">@csrf<button type="submit"><i class="bi bi-hand-index-thumb"></i> Request {{ $a->is_locked ? 'Unlock' : 'Activation' }}</button></form>
                            @endif
                        </div>
                    </details>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="st-empty">No applicants found.</td></tr>
        @endforelse
        </tbody>
    </table></div>
@endif
    @if($page->hasPages())
        <div class="st-pager">
            @if($page->onFirstPage())<span class="dis">Previous</span>@else<a href="{{ $page->previousPageUrl() }}">Previous</a>@endif
            <span class="cur">{{ $page->currentPage() }} / {{ $page->lastPage() }}</span>
            @if($page->hasMorePages())<a href="{{ $page->nextPageUrl() }}">Next</a>@else<span class="dis">Next</span>@endif
        </div>
    @endif
</section>
