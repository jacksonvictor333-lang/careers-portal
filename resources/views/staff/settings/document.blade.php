@extends('staff.layout')
@section('title', $title)

@section('content')
<div class="st-center">
<div class="st-head"><div><h1>{{ $title }}</h1><p>{{ $note }}</p></div></div>
<section class="st-card"><div class="st-cb">
    <form method="POST" action="{{ route('staff.settings.document.save', $key) }}" class="st-f">
        @csrf @method('PUT')
        <label class="t">Text</label>
        <textarea name="body" style="min-height:220px" required>{{ old('body', $body) }}</textarea>
        <div style="margin-top:14px"><button class="st-btn" type="submit"><i class="bi bi-check2"></i> Save</button></div>
    </form>
</div></section>
</div>
@endsection
