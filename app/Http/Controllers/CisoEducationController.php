<?php

namespace App\Http\Controllers;

use App\Models\CisoEducation;
use Illuminate\View\View;

class CisoEducationController extends Controller
{
    public function index(): View
    {
        $data = CisoEducation::orderBy('id')->get();

        return view('ciso/ciso-education/index', compact('data'));
    }

    public function show(CisoEducation $cisoEducation): View
    {
        return view('ciso/ciso-education/show', compact('cisoEducation'));
    }
}
