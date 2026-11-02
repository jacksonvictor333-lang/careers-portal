@extends('staff.layout')
@section('title', 'Account History')

@section('content')
@php $title = 'Account History'; $cur = 'history'; @endphp
@include('staff.applicants.partials-cv-head')

<section class="st-card"><div class="st-ch">Account history <span class="st-pill b">{{ $events->count() }}</span></div>
    <div class="st-wrap"><table class="st-t">
        <thead><tr><th style="width:170px">When</th><th>Event</th><th>Note</th><th>By</th></tr></thead>
        <tbody>
        @foreach($events as $e)
            <tr><td>{{ $e['at']->format('d M Y H:i') }}</td>
                <td><i class="bi {{ $e['icon'] }}" style="color:var(--navy)"></i> {{ $e['title'] }}</td>
                <td>{{ $e['note'] ?: '—' }}</td><td>{{ $e['by'] ?: '—' }}</td></tr>
        @endforeach
        </tbody>
    </table></div>
</section>
@endsection
