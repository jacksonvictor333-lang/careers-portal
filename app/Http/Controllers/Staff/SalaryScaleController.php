<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\SalaryScale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalaryScaleController extends Controller
{
    public function index()
    {
        return view('staff.salary-scales.index', ['scales' => SalaryScale::withCount('schemes')->orderBy('code')->get(), 'edit' => null]);
    }

    public function edit(int $id)
    {
        return view('staff.salary-scales.index', ['scales' => SalaryScale::withCount('schemes')->orderBy('code')->get(), 'edit' => SalaryScale::findOrFail($id)]);
    }

    public function store(Request $request)
    {
        SalaryScale::create($this->data($request));

        return redirect()->route('staff.salary-scales')->with('success', 'Salary scale added.');
    }

    public function update(Request $request, int $id)
    {
        SalaryScale::findOrFail($id)->update($this->data($request, $id));

        return redirect()->route('staff.salary-scales')->with('success', 'Salary scale updated.');
    }

    private function data(Request $request, ?int $ignore = null): array
    {
        $d = $request->validate([
            'code'       => ['required', 'string', 'max:40', Rule::unique('salary_scales', 'code')->ignore($ignore)],
            'name'       => ['nullable', 'string', 'max:150'],
            'min_salary' => ['nullable', 'numeric', 'min:0'],
            'max_salary' => ['nullable', 'numeric', 'gte:min_salary'],
        ]);
        $d['code'] = strtoupper(trim($d['code']));
        $d['is_active'] = $request->boolean('is_active', true);

        return $d;
    }
}
