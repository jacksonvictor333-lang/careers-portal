@extends('staff.layout')
@section('title', 'Academic Levels')

@section('content')
<div class="st-head"><div><h1>Academic Levels</h1><p>Education levels from lowest to highest.</p></div></div>
<section class="st-card"><div class="st-wrap"><table class="st-t">
    <thead><tr><th style="width:80px">Rank</th><th>Level</th><th>Used for</th></tr></thead>
    <tbody>
    @foreach($levels as $name => $rank)
        <tr><td><b>{{ $rank }}</b></td><td>{{ $name }}</td><td>{{ $rank <= 2 ? 'Exam number (no programme)' : 'Programme and institution' }}</td></tr>
    @endforeach
    </tbody>
</table></div></section>
<div class="st-hint" style="margin:0 4px">These levels are fixed because the eligibility check compares them by rank (a Bachelor Degree is above a Diploma). They are shown here for reference.</div>
@endsection
