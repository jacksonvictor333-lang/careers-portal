@extends('staff.layout')
@section('title', 'Staff users')

@section('content')
<div class="st-head">
    <div><h1>Staff users</h1><p>Create staff accounts and choose what each person may do.</p></div>
    <a class="st-btn" href="{{ route('staff.users.create') }}"><i class="bi bi-person-plus"></i> Add staff user</a>
</div>

<section class="st-card"><div class="st-wrap"><table class="st-t">
    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th class="r">Action</th></tr></thead>
    <tbody>
        @foreach($users as $u)
            <tr>
                <td><b>{{ $u->name }}</b>@if($u->id === auth('staff')->id()) <span class="st-sub">(you)</span>@endif</td>
                <td>{{ $u->email }}</td>
                <td><span class="st-pill {{ $u->role === 'admin' ? 'r' : ($u->role === 'officer' ? 'b' : 'n') }}">{{ $u->role_label }}</span></td>
                <td><span class="st-pill {{ $u->is_active ? 'g' : 'n' }}">{{ $u->is_active ? 'Active' : 'Disabled' }}</span></td>
                <td>{{ $u->last_login_at ? $u->last_login_at->format('d M Y H:i') : 'Never' }}</td>
                <td class="r"><a class="st-btn ghost sm" href="{{ route('staff.users.edit', $u->id) }}"><i class="bi bi-pencil"></i> Edit</a></td>
            </tr>
        @endforeach
    </tbody>
</table></div></section>

<section class="st-card"><div class="st-ch">What each role can do</div><div class="st-cb">
    <dl class="st-kv">
        <div><dt>Viewer</dt><dd>View vacancies, applicants and letters only.</dd></div>
        <div><dt>Officer</dt><dd>Viewer + manage vacancies, employers and change application statuses.</dd></div>
        <div><dt>Admin</dt><dd>Everything, including staff users.</dd></div>
    </dl>
</div></section>
@endsection
