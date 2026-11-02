<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Personal Details: sehemu tatu
 *  1) Personal Information  (NIN, jina, jinsia, kuzaliwa, uraia) - baadaye itatoka NIDA; kwa sasa inahaririwa
 *  2) Other Personal Details (hali ya ndoa, ulemavu)
 *  3) Contact Details       (makazi ya sasa, simu, anwani ya posta)
 */
class PersonalDetailsController extends Controller
{
    private function regions(): array
    {
        return Region::map();   // kutoka table `regions` na `districts`
    }

    private function districtsOf(?string $region): array
    {
        return $this->regions()[$region] ?? [];
    }

    private function applicant()
    {
        return Auth::guard('applicant')->user();
    }

    public function show()
    {
        return view('applicant.personal-details', [
            'applicant' => $this->applicant(),
            'regions'   => $this->regions(),
            'countries' => config('locations.countries', []),
        ]);
    }

    // ---------- 1) Personal Information ----------

    public function updateIdentity(Request $request)
    {
        $data = $request->validate([
            'first_name'     => ['required', 'string', 'max:100'],
            'middle_name'    => ['nullable', 'string', 'max:100'],
            'last_name'      => ['required', 'string', 'max:100'],
            'nida_number'    => ['nullable', 'string', 'max:30'],
            'gender'         => ['required', Rule::in(['Male', 'Female'])],
            'date_of_birth'  => ['required', 'date', 'before:today'],
            'birth_region'   => ['required', Rule::in(array_keys($this->regions()))],
            'birth_district' => ['required', Rule::in($this->districtsOf($request->input('birth_region')))],
            'nationality'    => ['required', 'string', 'max:100'],
        ], [
            'birth_district.in' => 'Select a district that belongs to the chosen region.',
        ]);

        // CV na sehemu nyingine bado zinasoma place_of_birth
        $data['place_of_birth'] = $data['birth_district'] . ', ' . $data['birth_region'];

        $this->applicant()->update($data);

        return redirect()->route('applicant.personal-details')->with('success', 'Personal information saved.');
    }

    // ---------- 2) Other Personal Details ----------

    public function updateOther(Request $request)
    {
        $data = $request->validate([
            'marital_status'     => ['required', Rule::in(['Single', 'Married', 'Divorced', 'Widowed'])],
            'disability'         => ['required', Rule::in(['Yes', 'No'])],
            'disability_details' => [Rule::requiredIf($request->input('disability') === 'Yes'), 'nullable', 'string', 'max:200'],
        ]);

        if ($data['disability'] === 'No') {
            $data['disability_details'] = null;
        }

        $this->applicant()->update($data);

        return redirect()->route('applicant.personal-details')->with('success', 'Other personal details saved.');
    }

    // ---------- 3) Contact Details ----------

    public function updateContact(Request $request)
    {
        $home = $request->input('residence_country') === config('locations.countries.0');

        // Nambari ya simu: tarakimu 9 baada ya +255 (0 ya mwanzo inaondolewa)
        $digits = ltrim(preg_replace('/\D+/', '', (string) $request->input('phone')), '0');
        if (str_starts_with($digits, '255') && strlen($digits) > 9) {
            $digits = substr($digits, 3);
        }
        $request->merge(['phone_digits' => $digits]);

        $data = $request->validate([
            'residence_country' => ['required', Rule::in(config('locations.countries', []))],
            'region'            => [Rule::requiredIf($home), 'nullable', Rule::in(array_keys($this->regions()))],
            'district'          => [Rule::requiredIf($home), 'nullable', Rule::in($this->districtsOf($request->input('region')))],
            'phone_digits'      => ['required', 'digits:9'],
            'postal_address'    => ['required', 'string', 'max:100'],
            'postal_region'     => ['required', Rule::in(array_keys($this->regions()))],
        ], [
            'phone_digits.required' => 'Enter your mobile number.',
            'phone_digits.digits'   => 'The mobile number must have 9 digits after +255, for example 653621675.',
            'district.in'           => 'Select a district that belongs to the chosen region.',
        ]);

        $postal = trim($data['postal_address']);
        if (preg_match('/^\d+$/', $postal)) {
            $postal = 'P.O BOX ' . $postal;   // namba tu => "P.O BOX 34"
        }

        $this->applicant()->update([
            'residence_country' => $data['residence_country'],
            'region'            => $data['region'] ?? null,
            'district'          => $data['district'] ?? null,
            'phone'             => '+255' . $data['phone_digits'],
            'postal_address'    => $postal,
            'postal_region'     => $data['postal_region'],
            'address'           => $postal,        // CV inasoma address
        ]);

        return redirect()->route('applicant.personal-details')->with('success', 'Contact details saved.');
    }
}
