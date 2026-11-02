@extends('staff.layout')
@section('title', $def['title'])

@section('content')
@php $e = $edit; $cols = $def['columns']; @endphp
<div class="st-head"><div><h1>{{ $def['title'] }}</h1><p>{{ $def['note'] }}</p></div></div>

<section class="st-card"><div class="st-ch">{{ $e ? 'Edit entry' : 'Add new' }}</div><div class="st-cb">
    <form method="POST" action="{{ $e ? route('staff.settings.lookup.update', [$def['key'], $e->id]) : route('staff.settings.lookup.store', $def['key']) }}">
        @csrf @if($e) @method('PUT') @endif
        <div class="st-form st-f" style="grid-template-columns:repeat({{ min(max(count($def['fields']), 1), 3) }},minmax(0,1fr))">
            @foreach($def['fields'] as $f)
                @php $val = old($f['name'], $e->{$f['name']} ?? ''); @endphp
                <div class="{{ $errors->has($f['name']) ? 'err' : '' }}"><label class="t">{{ $f['label'] }} @if(! empty($f['required']))<span class="req">*</span>@endif</label>
                    @if(($f['type'] ?? 'text') === 'select')
                        <select name="{{ $f['name'] }}"><option value="">{{ empty($f['required']) ? '— none —' : 'Select...' }}</option>
                            @foreach($options[$f['name']] as $ov => $ol)<option value="{{ $ov }}" {{ (string) $val === (string) $ov ? 'selected' : '' }}>{{ $ol }}</option>@endforeach
                        </select>
                    @else
                        <input type="text" name="{{ $f['name'] }}" value="{{ $val }}" maxlength="{{ $f['max'] ?? 255 }}" {{ ! empty($f['required']) ? 'required' : '' }}>
                    @endif
                </div>
            @endforeach
        </div>
        <div style="margin-top:14px"><button class="st-btn" type="submit"><i class="bi bi-check2"></i> {{ $e ? 'Save changes' : 'Add' }}</button>
            @if($e)<a class="st-btn ghost" href="{{ route('staff.settings.lookup', $def['key']) }}">Cancel</a>@endif</div>
    </form>
</div></section>

@if(! empty($def['bulk']))
<section class="st-card"><details><summary class="st-ch" style="cursor:pointer;list-style:none"><span><i class="bi bi-list-ul"></i> Add many at once</span><i class="bi bi-chevron-down" style="font-size:12px"></i></summary><div class="st-cb">
    <form method="POST" action="{{ route('staff.settings.lookup.bulk', $def['key']) }}" class="st-f">
        @csrf
        <label class="t">One comment per line</label>
        <textarea name="lines" style="min-height:150px" placeholder="Lacks relevant experience as per advert&#10;Already Shortlisted in the same cadre | Normal" required></textarea>
        <div class="st-hint">Add <b>| Normal</b> at the end of a line for the Normal type. Lines without it are type All. Comments that already exist are skipped.</div>
        <div style="margin-top:12px"><button class="st-btn" type="submit"><i class="bi bi-check2-all"></i> Add all</button></div>
    </form>
</div></details></section>
@endif

<form class="st-search" method="GET" action="{{ route('staff.settings.lookup', $def['key']) }}"><input type="text" name="q" value="{{ $q }}" placeholder="Search..."><button class="st-btn" type="submit"><i class="bi bi-search"></i> Search</button></form>

<section class="st-card"><div class="st-wrap"><table class="st-t">
    <thead><tr><th style="width:56px">#</th>@foreach($cols as $c)<th>{{ $c['label'] }}</th>@endforeach<th>Status</th><th class="r">Action</th></tr></thead>
    <tbody>
    @forelse($rows as $i => $r)
        <tr><td>{{ $rows->firstItem() + $i }}</td>
            @foreach($cols as $c)<td>{{ $r->{$c['field']} ?? '—' }}</td>@endforeach
            <td><span class="st-pill {{ $r->is_active ? 'g' : 'n' }}">{{ $r->is_active ? 'Active' : 'Disabled' }}</span></td>
            <td class="r"><a class="st-btn ghost sm" href="{{ route('staff.settings.lookup.edit', [$def['key'], $r->id]) }}"><i class="bi bi-pencil"></i> Edit</a>
                <form method="POST" action="{{ route('staff.settings.lookup.toggle', [$def['key'], $r->id]) }}" style="display:inline">@csrf<button class="st-btn ghost sm" type="submit">{{ $r->is_active ? 'Disable' : 'Enable' }}</button></form></td></tr>
    @empty
        <tr><td colspan="{{ count($cols) + 3 }}" class="st-empty">Nothing here yet.</td></tr>
    @endforelse
    </tbody>
</table></div>
@if($rows->hasPages())<div class="st-pager">@if($rows->onFirstPage())<span class="dis">Previous</span>@else<a href="{{ $rows->previousPageUrl() }}">Previous</a>@endif<span class="cur">{{ $rows->currentPage() }} / {{ $rows->lastPage() }}</span>@if($rows->hasMorePages())<a href="{{ $rows->nextPageUrl() }}">Next</a>@else<span class="dis">Next</span>@endif</div>@endif
</section>
@endsection
