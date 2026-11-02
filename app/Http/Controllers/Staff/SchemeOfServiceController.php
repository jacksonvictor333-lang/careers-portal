<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ProgrammeCategory;
use App\Models\SalaryScale;
use App\Models\SchemeOfService;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Scheme of Service: cheo, sifa na majukumu; tangazo jipya linaweza kujazwa kutoka hapa */
class SchemeOfServiceController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $schemes = SchemeOfService::with('salaryScale')->withCount('vacancies')
            ->when($q !== '', fn ($w) => $w->where('title', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%"))
            ->orderBy('title')->paginate(15)->withQueryString();

        return view('staff.schemes.index', compact('schemes', 'q'));
    }

    public function create()
    {
        return view('staff.schemes.form', $this->formData(new SchemeOfService(['min_experience_years' => 0, 'is_active' => true])));
    }

    public function store(Request $request)
    {
        SchemeOfService::create($this->payload($request));

        return redirect()->route('staff.schemes')->with('success', 'Scheme of service saved.');
    }

    public function edit(int $id)
    {
        return view('staff.schemes.form', $this->formData(SchemeOfService::findOrFail($id)));
    }

    public function update(Request $request, int $id)
    {
        SchemeOfService::findOrFail($id)->update($this->payload($request, $id));

        return redirect()->route('staff.schemes')->with('success', 'Scheme of service updated.');
    }

    private function formData(SchemeOfService $scheme): array
    {
        return [
            'scheme'     => $scheme,
            'scales'     => SalaryScale::where('is_active', true)->orderBy('code')->get(['id', 'code']),
            'levels'     => array_keys(Vacancy::EDUCATION_RANK),
            'categories' => ProgrammeCategory::names(),
            'chosen'     => old('required_categories', $scheme->categories_list),
        ];
    }

    private function payload(Request $request, ?int $ignore = null): array
    {
        $levels = array_keys(Vacancy::EDUCATION_RANK);

        $d = $request->validate([
            'code'                  => ['required', 'string', 'max:60', Rule::unique('scheme_of_services', 'code')->ignore($ignore)],
            'title'                 => ['required', 'string', 'max:200'],
            'salary_scale_id'       => ['nullable', Rule::exists('salary_scales', 'id')],
            'min_education_level'   => ['nullable', Rule::in($levels)],
            'max_education_level'   => ['nullable', Rule::in($levels)],
            'required_categories'   => ['nullable', 'array'],
            'required_categories.*' => [Rule::in(ProgrammeCategory::names())],
            'min_experience_years'  => ['required', 'integer', 'min:0', 'max:50'],
            'duties'                => ['nullable', 'string', 'max:5000'],
            'requirements'          => ['nullable', 'string', 'max:5000'],
        ]);

        $min = $d['min_education_level'] ?? null;
        $max = $d['max_education_level'] ?? null;
        if ($min && $max && Vacancy::EDUCATION_RANK[$max] < Vacancy::EDUCATION_RANK[$min]) {
            back()->withInput()->withErrors(['max_education_level' => 'The highest level cannot be below the lowest level.'])->throwResponse();
        }

        $d['code'] = strtoupper(trim($d['code']));
        $d['required_categories'] = ! empty($d['required_categories']) ? implode("\n", $d['required_categories']) : null;
        $d['is_active'] = $request->boolean('is_active');

        return $d;
    }
}
