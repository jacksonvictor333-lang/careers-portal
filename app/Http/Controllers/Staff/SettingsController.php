<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\ProgrammeCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Settings (admin): programme categories zinazotumika kwenye sifa za elimu na matangazo */
class SettingsController extends Controller
{
    public function categories()
    {
        return view('staff.settings.categories', ['categories' => ProgrammeCategory::withCount('programmes')->orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function storeCategory(Request $request)
    {
        $d = $request->validate(['name' => ['required', 'string', 'max:120', Rule::unique('programme_categories', 'name')]]);
        ProgrammeCategory::create([
            'name' => trim($d['name']), 'is_active' => true,
            'sort_order' => (int) ProgrammeCategory::max('sort_order') + 1,
        ]);

        return back()->with('success', 'Category added.');
    }

    public function toggleCategory(int $id)
    {
        $c = ProgrammeCategory::findOrFail($id);
        $c->update(['is_active' => ! $c->is_active]);

        return back()->with('success', $c->name . ($c->is_active ? ' enabled.' : ' disabled.'));
    }
}
