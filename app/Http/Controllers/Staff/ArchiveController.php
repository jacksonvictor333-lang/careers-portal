<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use Illuminate\Http\Request;

/** Archive: matangazo ambayo tarehe ya kufunga imepita */
class ArchiveController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $vacancies = Vacancy::query()->withCount('applications')
            ->where('closes_at', '<', Vacancy::today())
            ->when($q !== '', fn ($w) => $w->where(fn ($x) => $x->where('title', 'like', "%{$q}%")->orWhere('employer', 'like', "%{$q}%")->orWhere('reference_number', 'like', "%{$q}%")))
            ->orderByDesc('closes_at')->paginate(15)->withQueryString();

        return view('staff.archive.index', compact('vacancies', 'q'));
    }
}
