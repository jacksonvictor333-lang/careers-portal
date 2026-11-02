<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB
        ]);

        $applicant = Auth::guard('applicant')->user();

        $path = $request->file('photo')->store('applicants', 'public');

        // Futa picha ya zamani
        if ($applicant->photo_path) {
            Storage::disk('public')->delete($applicant->photo_path);
        }

        $applicant->update(['photo_path' => $path]);

        return back()->with('success', 'Profile picture updated.');
    }
}