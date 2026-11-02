<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\ProgrammeCategory;
use App\Models\SchemeOfService;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Staff: kuweka na kuhariri matangazo ya kazi, na kuona waliomba */
class VacancyController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $vacancies = Vacancy::query()
            ->withCount('applications')
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('employer', 'like', "%{$q}%")
                  ->orWhere('reference_number', 'like', "%{$q}%");
            }))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('staff.vacancies.index', compact('vacancies', 'q'));
    }

    public function show(int $id)
    {
        $vacancy = Vacancy::with(['applications.applicant', 'applications.letter'])->findOrFail($id);

        return view('staff.vacancies.show', compact('vacancy'));
    }

    public function create()
    {
        return view('staff.vacancies.form', $this->formData(new Vacancy([
            'min_experience_years' => 0,
            'number_of_posts'      => 1,
            'is_published'         => true,
            'posted_at'            => now('Africa/Dar_es_Salaam')->toDateString(),
        ])));
    }

    public function store(Request $request)
    {
        $vacancy = Vacancy::create($this->payload($request));

        return redirect()->route('staff.vacancies.show', $vacancy->id)->with('success', 'Vacancy saved.');
    }

    public function edit(int $id)
    {
        return view('staff.vacancies.form', $this->formData(Vacancy::findOrFail($id)));
    }

    public function update(Request $request, int $id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $vacancy->update($this->payload($request, $vacancy->id));

        return redirect()->route('staff.vacancies.show', $vacancy->id)->with('success', 'Vacancy updated.');
    }

    /** Chapisha / ficha tangazo */
    public function toggle(int $id)
    {
        $vacancy = Vacancy::findOrFail($id);
        $vacancy->update(['is_published' => ! $vacancy->is_published]);

        return back()->with('success', $vacancy->is_published ? 'Vacancy published.' : 'Vacancy unpublished.');
    }

    // ---------- Msaada ----------

    private function formData(Vacancy $vacancy): array
    {
        return [
            'vacancy'    => $vacancy,
            'employers'  => Employer::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'levels'     => array_keys(Vacancy::EDUCATION_RANK),
            'categories' => ProgrammeCategory::names(),
            'schemes'    => SchemeOfService::with('salaryScale')->where('is_active', true)->orderBy('title')->get(),
            'chosen'     => old('required_categories', $vacancy->required_categories_list),
        ];
    }

    private function payload(Request $request, ?int $ignoreId = null): array
    {
        $levels = array_keys(Vacancy::EDUCATION_RANK);

        $data = $request->validate([
            'reference_number'     => ['required', 'string', 'max:100', Rule::unique('vacancies', 'reference_number')->ignore($ignoreId)],
            'title'                => ['required', 'string', 'max:255'],
            'employer_id'          => ['required', Rule::exists('employers', 'id')],
            'scheme_of_service_id' => ['nullable', Rule::exists('scheme_of_services', 'id')],
            'location'             => ['nullable', 'string', 'max:150'],
            'salary_scale'         => ['nullable', 'string', 'max:100'],
            'number_of_posts'      => ['required', 'integer', 'min:1', 'max:9999'],
            'description'          => ['nullable', 'string', 'max:3000'],
            'duties'               => ['nullable', 'string', 'max:5000'],
            'requirements'         => ['nullable', 'string', 'max:5000'],
            'min_education_level'  => ['nullable', Rule::in($levels)],
            'max_education_level'  => ['nullable', Rule::in($levels)],
            'required_categories'  => ['nullable', 'array'],
            'required_categories.*' => [Rule::in(ProgrammeCategory::names())],
            'min_experience_years' => ['required', 'integer', 'min:0', 'max:50'],
            'posted_at'            => ['required', 'date'],
            'closes_at'            => ['required', 'date', 'after_or_equal:posted_at'],
            'letter_address'       => ['nullable', 'string', 'max:1000'],
            'is_published'         => ['nullable', 'boolean'],
        ], [
            'closes_at.after_or_equal' => 'The closing date must be on or after the posting date.',
        ]);

        // Kiwango cha juu hakiwezi kuwa chini ya cha chini
        $min = $data['min_education_level'] ?? null;
        $max = $data['max_education_level'] ?? null;
        if ($min && $max && (Vacancy::EDUCATION_RANK[$max] < Vacancy::EDUCATION_RANK[$min])) {
            back()->withInput()->withErrors(['max_education_level' => 'The highest level cannot be below the lowest level.'])->throwResponse();
        }

        $employer = Employer::findOrFail($data['employer_id']);

        return [
            'reference_number'     => trim($data['reference_number']),
            'title'                => trim($data['title']),
            'employer_id'          => $employer->id,
            'employer'             => $employer->name,
            'scheme_of_service_id' => $data['scheme_of_service_id'] ?? null,
            'location'             => $data['location'] ?? null,
            'salary_scale'         => $data['salary_scale'] ?? null,
            'number_of_posts'      => $data['number_of_posts'],
            'description'          => $data['description'] ?? null,
            'duties'               => $data['duties'] ?? null,
            'requirements'         => $data['requirements'] ?? null,
            'min_education_level'  => $min,
            'max_education_level'  => $max,
            'required_categories'  => $data['required_categories'] ? implode("\n", $data['required_categories']) : null,
            'min_experience_years' => $data['min_experience_years'],
            'posted_at'            => $data['posted_at'],
            'closes_at'            => $data['closes_at'],
            'letter_address'       => filled($data['letter_address'] ?? null) ? $data['letter_address'] : null,
            'is_published'         => $request->boolean('is_published'),
        ];
    }
}
