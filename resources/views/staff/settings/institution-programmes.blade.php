@extends('staff.layout')
@section('title', 'Manage Institution/Programme')

@section('content')
<div class="st-head"><div><h1>Manage Institution/Programme</h1><p>Choose an institution, then tick the programmes it offers.</p></div></div>

<section class="st-card"><div class="st-cb">
    <form method="GET" action="{{ route('staff.settings.institution-programmes') }}" class="st-f" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap">
        <div style="flex:1;min-width:280px;max-width:560px"><label class="t">Institution</label>
            <select name="institution" onchange="this.form.submit()"><option value="">Select institution...</option>
                @foreach($institutions as $i)<option value="{{ $i->id }}" {{ $selected === $i->id ? 'selected' : '' }}>{{ $i->name }}</option>@endforeach
            </select></div>
        <noscript><button class="st-btn" type="submit">Open</button></noscript>
    </form>
</div></section>

@if($selected)
<form method="POST" action="{{ route('staff.settings.institution-programmes.save') }}">
    @csrf <input type="hidden" name="institution" value="{{ $selected }}">
    <section class="st-card"><div class="st-ch">Programmes offered <span class="st-pill b" id="cnt">{{ count($linked) }}</span>
        <input type="text" id="filter" placeholder="Filter programmes..." style="height:32px;padding:0 10px;font:inherit;font-size:13px;border:1px solid #D1D5DB;border-radius:8px;width:240px"></div>
        <div class="st-cb">
            @foreach($programmes as $level => $list)
                <div style="margin:10px 0 6px;font-weight:700;font-size:13px">{{ $level }}</div>
                <div class="st-checks" style="max-height:none">
                    @foreach($list as $p)<label data-t="{{ strtolower($p->name) }}"><input type="checkbox" name="programmes[]" value="{{ $p->id }}" {{ in_array($p->id, $linked) ? 'checked' : '' }}> {{ $p->name }}</label>@endforeach
                </div>
            @endforeach
        </div>
    </section>
    <button class="st-btn" type="submit"><i class="bi bi-check2"></i> Save</button>
</form>
@push('scripts')
<script>
    var f = document.getElementById('filter'), cnt = document.getElementById('cnt');
    f.addEventListener('input', function () { var t = f.value.toLowerCase(); document.querySelectorAll('.st-checks label').forEach(function (l) { l.style.display = l.dataset.t.indexOf(t) === -1 ? 'none' : ''; }); });
    document.addEventListener('change', function () { cnt.textContent = document.querySelectorAll('input[name="programmes[]"]:checked').length; });
</script>
@endpush
@endif
@endsection
