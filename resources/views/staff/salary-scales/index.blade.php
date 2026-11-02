@extends('staff.layout')
@section('title', 'Salary Scales')

@section('content')
@php $manage = auth('staff')->user()->canManage(); $e = $edit; @endphp
<div class="st-head"><div><h1>Salary Scales</h1><p>Scales such as TGS D, used by schemes of service and adverts.</p></div></div>

@if($manage)
<section class="st-card"><div class="st-ch">{{ $e ? 'Edit ' . $e->code : 'Add salary scale' }}</div><div class="st-cb">
    <form method="POST" action="{{ $e ? route('staff.salary-scales.update', $e->id) : route('staff.salary-scales.store') }}">
        @csrf @if($e) @method('PUT') @endif
        <div class="st-form st-f" style="grid-template-columns:repeat(4,minmax(0,1fr))">
            <div><label class="t">Code <span class="req">*</span></label><input type="text" name="code" value="{{ old('code', $e->code ?? '') }}" placeholder="TGS D" required></div>
            <div><label class="t">Name</label><input type="text" name="name" value="{{ old('name', $e->name ?? '') }}"></div>
            <div><label class="t">Minimum salary (TZS)</label><input type="number" step="0.01" min="0" name="min_salary" value="{{ old('min_salary', $e->min_salary ?? '') }}"></div>
            <div><label class="t">Maximum salary (TZS)</label><input type="number" step="0.01" min="0" name="max_salary" value="{{ old('max_salary', $e->max_salary ?? '') }}"></div>
            <div class="full"><label style="display:flex;align-items:center;gap:8px;font-size:13.5px"><input type="checkbox" name="is_active" value="1" style="width:auto;height:auto" {{ old('is_active', $e->is_active ?? true) ? 'checked' : '' }}> Active</label></div>
        </div>
        <div style="margin-top:12px"><button class="st-btn" type="submit"><i class="bi bi-check2"></i> {{ $e ? 'Save changes' : 'Add scale' }}</button>
            @if($e)<a class="st-btn ghost" href="{{ route('staff.salary-scales') }}">Cancel</a>@endif</div>
    </form>
</div></section>
@endif

<section class="st-card"><div class="st-wrap"><table class="st-t">
    <thead><tr><th>Code</th><th>Name</th><th>Minimum</th><th>Maximum</th><th>Schemes</th><th>Status</th><th class="r"></th></tr></thead>
    <tbody>
    @foreach($scales as $s)
        <tr><td><b>{{ $s->code }}</b></td><td>{{ $s->name ?: '—' }}</td>
            <td>{{ $s->min_salary !== null ? number_format((float) $s->min_salary) : '—' }}</td><td>{{ $s->max_salary !== null ? number_format((float) $s->max_salary) : '—' }}</td>
            <td>{{ $s->schemes_count }}</td><td><span class="st-pill {{ $s->is_active ? 'g' : 'n' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="r">@if($manage)<a class="st-btn ghost sm" href="{{ route('staff.salary-scales.edit', $s->id) }}"><i class="bi bi-pencil"></i> Edit</a>@endif</td></tr>
    @endforeach
    </tbody>
</table></div></section>
@endsection
