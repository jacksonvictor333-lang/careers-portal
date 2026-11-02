<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;

/** Kurasa ambazo bado hazijaundwa (Job Permit, Tools) - zimewekwa kwenye menyu ili muundo ukamilike */
class PageController extends Controller
{
    public function permits()
    {
        return view('staff.pages.soon', ['title' => 'Job Permit', 'icon' => 'bi-file-earmark-check', 'text' => 'Job permit management will be added here.']);
    }

    public function tools()
    {
        return view('staff.pages.soon', ['title' => 'Tools', 'icon' => 'bi-tools', 'text' => 'Administrative tools will be added here.']);
    }
}
