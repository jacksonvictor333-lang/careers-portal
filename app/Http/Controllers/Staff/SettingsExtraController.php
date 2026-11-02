<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\DocumentContent;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Settings (admin): Academic Levels (kusoma tu), Institution/Programme, Document Contents */
class SettingsExtraController extends Controller
{
    private const DOCS = [
        'declaration'       => ['Declaration statement', 'Shown to the applicant on the Declaration page. One statement per line.'],
        'activation_notice' => ['Account activation notice', 'Shown at the top of every page while an applicant account is not yet activated.'],
    ];

    public function levels()
    {
        return view('staff.settings.levels', ['levels' => Vacancy::EDUCATION_RANK]);
    }

    public function institutionProgrammes(Request $request)
    {
        $institutions = DB::table('institutions')->orderBy('name')->get(['id', 'name']);
        $selected = $request->integer('institution') ?: null;
        $linked = $selected ? DB::table('institution_programme')->where('institution_id', $selected)->pluck('programme_id')->all() : [];
        $programmes = $selected ? DB::table('programmes')->where('is_active', true)->orderBy('level')->orderBy('name')->get(['id', 'level', 'name'])->groupBy('level') : collect();

        return view('staff.settings.institution-programmes', compact('institutions', 'selected', 'linked', 'programmes'));
    }

    public function saveInstitutionProgrammes(Request $request)
    {
        $data = $request->validate(['institution' => ['required', 'integer', 'exists:institutions,id'], 'programmes' => ['nullable', 'array'], 'programmes.*' => ['integer', 'exists:programmes,id']]);

        DB::transaction(function () use ($data) {
            DB::table('institution_programme')->where('institution_id', $data['institution'])->delete();
            foreach (array_unique($data['programmes'] ?? []) as $pid) {
                DB::table('institution_programme')->insert(['institution_id' => $data['institution'], 'programme_id' => $pid]);
            }
        });

        return redirect()->route('staff.settings.institution-programmes', ['institution' => $data['institution']])->with('success', 'Programmes saved for this institution.');
    }

    public function document(string $key)
    {
        abort_unless(isset(self::DOCS[$key]), 404);

        return view('staff.settings.document', ['key' => $key, 'title' => self::DOCS[$key][0], 'note' => self::DOCS[$key][1], 'body' => DocumentContent::text($key)]);
    }

    public function saveDocument(Request $request, string $key)
    {
        abort_unless(isset(self::DOCS[$key]), 404);
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        DocumentContent::updateOrCreate(['key' => $key], ['title' => self::DOCS[$key][0], 'body' => $data['body']]);

        return back()->with('success', 'Saved.');
    }
}
