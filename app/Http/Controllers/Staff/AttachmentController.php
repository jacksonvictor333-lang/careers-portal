<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use Illuminate\Http\Request;

/** Attachments: viambatisho vyote vilivyopakiwa na waombaji (Other Attachments) */
class AttachmentController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $attachments = Attachment::with('applicant')
            ->when($q !== '', fn ($w) => $w->where(function ($x) use ($q) {
                $x->where('title', 'like', "%{$q}%")->orWhere('type', 'like', "%{$q}%")
                  ->orWhereHas('applicant', fn ($a) => $a->where('first_name', 'like', "%{$q}%")->orWhere('last_name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
            }))
            ->orderByDesc('id')->paginate(20)->withQueryString();

        return view('staff.attachments.index', compact('attachments', 'q'));
    }
}
