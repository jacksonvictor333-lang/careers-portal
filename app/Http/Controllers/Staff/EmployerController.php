<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Staff: waajiri na anuani zao za barua za maombi */
class EmployerController extends Controller
{
    public function index()
    {
        $employers = Employer::withCount('vacancies')->orderBy('name')->get();

        return view('staff.employers.index', compact('employers'));
    }

    public function create()
    {
        return view('staff.employers.form', ['employer' => new Employer(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        Employer::create($this->payload($request));

        return redirect()->route('staff.employers')->with('success', 'Employer saved.');
    }

    public function edit(int $id)
    {
        return view('staff.employers.form', ['employer' => Employer::findOrFail($id)]);
    }

    public function update(Request $request, int $id)
    {
        $employer = Employer::findOrFail($id);
        $payload  = $this->payload($request, $employer->id);
        $oldName  = $employer->name;

        $employer->update($payload);

        // Jina likibadilika, matangazo yake yaendane nalo
        if ($oldName !== $employer->name) {
            $employer->vacancies()->update(['employer' => $employer->name]);
        }

        return redirect()->route('staff.employers')->with('success', 'Employer updated.');
    }

    private function payload(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255', Rule::unique('employers', 'name')->ignore($ignoreId)],
            'addressee'     => ['nullable', 'string', 'max:255'],
            'address_lines' => ['nullable', 'string', 'max:1000'],
            'email'         => ['nullable', 'email', 'max:255'],
            'phone'         => ['nullable', 'string', 'max:50'],
        ]);

        return [
            'name'          => trim($data['name']),
            'addressee'     => $data['addressee'] ?? null,
            'address_lines' => $data['address_lines'] ?? null,
            'email'         => $data['email'] ?? null,
            'phone'         => $data['phone'] ?? null,
            'is_active'     => $request->boolean('is_active'),
        ];
    }
}
