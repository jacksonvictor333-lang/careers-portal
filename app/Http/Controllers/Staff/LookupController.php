<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Support\StaffLookups;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Settings (admin): orodha za msingi zinazoendeshwa na App\Support\StaffLookups */
class LookupController extends Controller
{
    public function index(Request $request, string $key, ?int $id = null)
    {
        $def = StaffLookups::find($key);
        $q   = trim((string) $request->query('q'));

        $query = $def['query']();
        if ($q !== '' && ! empty($def['search'])) {
            $query->where(function ($w) use ($def, $q) {
                foreach ($def['search'] as $col) {
                    $w->orWhere($col, 'like', "%{$q}%");
                }
            });
        }

        $rows = $query->paginate(20)->withQueryString();
        $edit = $id ? DB::table($def['table'])->where('id', $id)->first() : null;
        abort_if($id && ! $edit, 404);

        $options = [];
        foreach ($def['fields'] as $f) {
            if (($f['type'] ?? 'text') === 'select') {
                $options[$f['name']] = ($f['options'])();
            }
        }

        return view('staff.settings.lookup', compact('def', 'rows', 'edit', 'q', 'options'));
    }

    public function store(Request $request, string $key)
    {
        $def  = StaffLookups::find($key);
        $data = $this->validated($request, $def, null);

        DB::table($def['table'])->insert($data + ($def['fixed'] ?? []) + ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]);

        return redirect()->route('staff.settings.lookup', $key)->with('success', 'Saved.');
    }

    /** Ongeza nyingi kwa pamoja: mstari mmoja = kipengele kimoja; ongeza " | Normal" mwishoni kwa aina ya Normal */
    public function bulk(Request $request, string $key)
    {
        $def = StaffLookups::find($key);
        abort_unless(! empty($def['bulk']), 404);
        $request->validate(['lines' => ['required', 'string', 'max:100000']]);

        $added = 0; $skipped = 0;
        foreach (preg_split('/\R/', (string) $request->input('lines')) as $line) {
            $line = trim($line);
            if ($line === '') { continue; }
            $type = 'All';
            if (preg_match('/\s*\|\s*(All|Normal)\s*$/i', $line, $m)) {
                $type = ucfirst(strtolower($m[1]));
                $line = trim(substr($line, 0, -strlen($m[0])));
            }
            $line = mb_substr($line, 0, 250);
            $dup = DB::table($def['table'])->where($def['fixed'])->where('name', $line)->exists();
            if ($dup) { $skipped++; continue; }
            DB::table($def['table'])->insert(['name' => $line, 'applies_to' => $type, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()] + $def['fixed']);
            $added++;
        }

        return redirect()->route('staff.settings.lookup', $key)->with('success', "$added added" . ($skipped ? ", $skipped already existed" : '') . '.');
    }

    public function update(Request $request, string $key, int $id)
    {
        $def  = StaffLookups::find($key);
        abort_unless(DB::table($def['table'])->where('id', $id)->exists(), 404);
        $data = $this->validated($request, $def, $id);

        DB::table($def['table'])->where('id', $id)->update($data + ['updated_at' => now()]);

        return redirect()->route('staff.settings.lookup', $key)->with('success', 'Updated.');
    }

    public function toggle(string $key, int $id)
    {
        $def = StaffLookups::find($key);
        $row = DB::table($def['table'])->where('id', $id)->first();
        abort_unless($row, 404);
        DB::table($def['table'])->where('id', $id)->update(['is_active' => ! $row->is_active, 'updated_at' => now()]);

        return back()->with('success', $row->is_active ? 'Disabled.' : 'Enabled.');
    }

    private function validated(Request $request, array $def, ?int $ignoreId): array
    {
        $rules = [];
        foreach ($def['fields'] as $f) {
            $r = [! empty($f['required']) ? 'required' : 'nullable'];
            if (($f['type'] ?? 'text') === 'select') {
                $r[] = \Illuminate\Validation\Rule::in(array_map('strval', array_keys(($f['options'])())));
            } else {
                $r[] = 'string';
                $r[] = 'max:' . ($f['max'] ?? 255);
            }
            $rules[$f['name']] = $r;
        }
        $data = $request->validate($rules);
        foreach ($data as $k => $v) {
            $data[$k] = is_string($v) ? trim($v) : $v;
            if ($data[$k] === '') { $data[$k] = null; }
        }

        if (array_key_exists('applies_to', $data) && $data['applies_to'] === null) { $data['applies_to'] = 'All'; }

        if (! empty($def['unique'])) {
            $exists = DB::table($def['table'])->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId));
            foreach ($def['unique'] as $col) {
                $exists->where($col, $data[$col] ?? ($def['fixed'][$col] ?? null));
            }
            if ($exists->exists()) {
                back()->withInput()->withErrors(['name' => 'This entry already exists.'])->throwResponse();
            }
        }

        return $data;
    }
}
