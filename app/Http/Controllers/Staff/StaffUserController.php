<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\StaffUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Admin tu: kusimamia watumiaji wa staff na majukumu yao */
class StaffUserController extends Controller
{
    public function index()
    {
        return view('staff.users.index', ['users' => StaffUser::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('staff.users.form', ['user' => new StaffUser(['role' => 'viewer', 'is_active' => true]), 'roles' => StaffUser::ROLES]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name'   => ['required', 'string', 'max:80'],
            'phone'       => ['nullable', 'string', 'regex:/^\+?[0-9]{9,15}$/'],
            'email'     => ['required', 'email', 'max:150', 'unique:staff_users,email'],
            'role'      => ['required', Rule::in(array_keys(StaffUser::ROLES))],
            'password'  => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = new StaffUser();
        $user->forceFill([
            'first_name' => $data['first_name'], 'middle_name' => $data['middle_name'] ?? null, 'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null, 'email' => $data['email'], 'role' => $data['role'],
            'password' => $data['password'], 'is_active' => $request->boolean('is_active', true),
        ])->save();

        return redirect()->route('staff.users')->with('success', 'Staff user created.');
    }

    public function edit(int $id)
    {
        return view('staff.users.form', ['user' => StaffUser::findOrFail($id), 'roles' => StaffUser::ROLES]);
    }

    public function update(Request $request, int $id)
    {
        $user = StaffUser::findOrFail($id);
        $me   = Auth::guard('staff')->user();

        $data = $request->validate([
            'first_name'  => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name'   => ['required', 'string', 'max:80'],
            'phone'       => ['nullable', 'string', 'regex:/^\+?[0-9]{9,15}$/'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('staff_users', 'email')->ignore($user->id)],
            'role'     => ['required', Rule::in(array_keys(StaffUser::ROLES))],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $active = $request->boolean('is_active');

        // Ulinzi: usijifungie mwenyewe, na acha admin mmoja hai angalau
        $losesAdmin = $user->role === 'admin' && $user->is_active && ($data['role'] !== 'admin' || ! $active);
        if ($losesAdmin) {
            if ($user->id === $me->id) {
                return back()->withInput()->with('error', 'You cannot remove your own admin role or disable your own account.');
            }
            $others = StaffUser::where('role', 'admin')->where('is_active', true)->where('id', '!=', $user->id)->count();
            if ($others === 0) {
                return back()->withInput()->with('error', 'At least one active admin must remain.');
            }
        }

        $user->forceFill([
            'first_name' => $data['first_name'], 'middle_name' => $data['middle_name'] ?? null, 'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null, 'email' => $data['email'], 'role' => $data['role'], 'is_active' => $active,
        ]);
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        return redirect()->route('staff.users')->with('success', 'Staff user updated.');
    }
}
