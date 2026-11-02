@extends('staff.layout')
@section('title', $title)

@section('content')
<div class="st-head"><div><h1>{{ $title }}</h1></div></div>
<section class="st-card"><div class="st-cb" style="text-align:center;padding:48px 20px;color:var(--muted)">
    <i class="bi {{ $icon }}" style="font-size:44px;color:var(--navy)"></i>
    <p style="margin:12px 0 0;font-size:14px">{{ $text }}</p>
    <p style="margin:4px 0 0;font-size:12.5px">This page is a placeholder in the menu for now.</p>
</div></section>
@endsection
