<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Msingi wa sehemu zote za orodha (Academic, Professional, Work, n.k.).
 * Kila sehemu inaandika config() yake tu: fields, columns, jina, route.
 */
abstract class SectionController extends Controller
{
    /** Maelezo ya sehemu: angalia AcademicQualificationController kama mfano. */
    abstract protected function config(): array;

    protected function applicant(): Applicant
    {
        return Auth::guard('applicant')->user();
    }

    /** Rekodi za applicant huyu tu: hawezi kuona wala kugusa za wengine */
    protected function query(array $c)
    {
        [$col, $dir] = $c['order'] ?? ['id', 'asc'];

        return $c['model']::query()
            ->where('applicant_id', $this->applicant()->id)
            ->orderBy($col, $dir);
    }

    protected function findOwned(array $c, $id)
    {
        return $c['model']::query()
            ->where('applicant_id', $this->applicant()->id)
            ->findOrFail($id);
    }

    public function index()
    {
        return $this->page($this->config(), null);
    }

    public function edit($id)
    {
        $c = $this->config();

        return $this->page($c, $this->findOwned($c, $id));
    }

    protected function page(array $c, $editing)
    {
        return view($c['view'], [
            'applicant' => $this->applicant(),
            'config'    => $c,
            'items'     => $this->query($c)->get(),
            'editing'   => $editing,
        ]);
    }

    public function store(Request $request)
    {
        $c    = $this->config();
        $data = $this->prepare($request->validate($this->rules($c['fields'], false)));
        $data = $this->storeFiles($request, $c, $data, null);

        $item = new $c['model']($data);
        $item->applicant_id = $this->applicant()->id;
        $item->save();

        $this->applicant()->touch();   // "Last updated" kwenye sidebar

        return redirect()->route($c['route'])->with('success', $c['singular'] . ' added.');
    }

    public function update(Request $request, $id)
    {
        $c    = $this->config();
        $item = $this->findOwned($c, $id);
        $data = $this->prepare($request->validate($this->rules($c['fields'], true)));
        $data = $this->storeFiles($request, $c, $data, $item);

        $item->fill($data)->save();
        $this->applicant()->touch();

        return redirect()->route($c['route'])->with('success', $c['singular'] . ' updated.');
    }

    public function destroy($id)
    {
        $c    = $this->config();
        $item = $this->findOwned($c, $id);

        foreach ($c['fields'] as $name => $f) {
            if (($f['type'] ?? 'text') === 'file' && $item->{$name}) {
                if (! $this->keepOldFiles()) {
                    Storage::disk('public')->delete($item->{$name});
                }
            }
        }

        $item->delete();
        $this->applicant()->touch();

        return redirect()->route($c['route'])->with('success', $c['singular'] . ' deleted.');
    }

    /** Sehemu zinazotaka historia ya nyaraka (mf. elimu) zibadilishe iwe true ili faili za zamani zihifadhiwe. */
    protected function keepOldFiles(): bool
    {
        return false;
    }

    /** Sehemu ikihitaji kurekebisha data kabla ya kuhifadhi, ibadilishe method hii. */
    protected function prepare(array $data): array
    {
        return $data;
    }

    protected function rules(array $fields, bool $isUpdate): array
    {
        $rules = [];

        foreach ($fields as $name => $f) {
            $r = $f['rules'] ?? ['nullable'];

            // Wakati wa kuhariri, faili si lazima ipakiwe tena
            if ($isUpdate && ($f['type'] ?? 'text') === 'file') {
                $r = array_map(fn ($x) => $x === 'required' ? 'nullable' : $x, $r);
            }

            $rules[$name] = $r;
        }

        return $rules;
    }

    /** Hifadhi faili zilizopakiwa na weka njia yake kwenye $data */
    protected function storeFiles(Request $request, array $c, array $data, $existing): array
    {
        foreach ($c['fields'] as $name => $f) {
            if (($f['type'] ?? 'text') !== 'file') {
                continue;
            }

            unset($data[$name]);   // validate() inarudisha UploadedFile; tunaiweka njia badala yake

            if ($request->hasFile($name)) {
                $path = $request->file($name)->store(
                    'applicants/' . $this->applicant()->id . '/' . $c['slug'],
                    'public'
                );

                if ($existing && $existing->{$name} && ! $this->keepOldFiles()) {
                    Storage::disk('public')->delete($existing->{$name});
                }

                $data[$name] = $path;
            }
        }

        return $data;
    }
}
