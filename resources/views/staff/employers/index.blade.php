@extends('staff.layout')
@section('title', 'Employers')

@section('content')
<div class="st-head"><div><h1>Employers</h1><p>The address shown to applicants for the application letter comes from the employer.</p></div>
    @if(auth('staff')->user()->canManage())<a class="st-btn" href="{{ route('staff.employers.create') }}"><i class="bi bi-plus-lg"></i> New employer</a>@endif</div>

<section class="st-card"><div class="st-wrap">
    <table class="st-t">
        <thead><tr><th>Employer</th><th>Letter address</th><th>Vacancies</th><th>Status</th><th class="r">Actions</th></tr></thead>
        <tbody>
            @forelse($employers as $e)
                <tr>
                    <td><b>{{ $e->name }}</b></td>
                    <td>{!! $e->full_address_lines ? nl2br(e(implode("\n", $e->full_address_lines))) : '<span class="st-sub">Not set (default address is used)</span>' !!}</td>
                    <td>{{ $e->vacancies_count }}</td>
                    <td><span class="st-pill {{ $e->is_active ? 'g' : 'n' }}">{{ $e->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="r">@if(auth('staff')->user()->canManage())<a class="st-btn ghost sm" href="{{ route('staff.employers.edit', $e->id) }}"><i class="bi bi-pencil"></i> Edit</a>@endif</td>
                </tr>
            @empty
                <tr><td colspan="5" class="st-empty">No employers yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div></section>
@endsection
