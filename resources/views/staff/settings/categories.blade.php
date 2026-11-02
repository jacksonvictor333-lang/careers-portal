@extends('staff.layout')
@section('title', 'Programme categories')

@section('content')
<div class="st-head"><div><h1>Programme categories</h1><p>Fields of study used for academic qualifications and advert requirements.</p></div></div>
<section class="st-card"><div class="st-cb">
    <form method="POST" action="{{ route('staff.settings.categories.store') }}" style="display:flex;gap:8px;align-items:flex-end" class="st-f">
        @csrf
        <div style="flex:1;max-width:380px"><label class="t">New category</label><input type="text" name="name" required maxlength="120" placeholder="e.g. Library and Information Science"></div>
        <button class="st-btn" type="submit"><i class="bi bi-plus-lg"></i> Add</button>
    </form>
</div></section>
<section class="st-card"><div class="st-wrap"><table class="st-t">
    <thead><tr><th>Category</th><th>Programmes</th><th>Status</th><th class="r"></th></tr></thead>
    <tbody>
    @foreach($categories as $c)
        <tr><td><b>{{ $c->name }}</b></td><td>{{ $c->programmes_count }}</td><td><span class="st-pill {{ $c->is_active ? 'g' : 'n' }}">{{ $c->is_active ? 'Active' : 'Disabled' }}</span></td>
            <td class="r"><form method="POST" action="{{ route('staff.settings.categories.toggle', $c->id) }}" style="display:inline">@csrf<button class="st-btn ghost sm" type="submit">{{ $c->is_active ? 'Disable' : 'Enable' }}</button></form></td></tr>
    @endforeach
    </tbody>
</table></div></section>
@endsection
