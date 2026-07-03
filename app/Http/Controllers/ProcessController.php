<?php

namespace App\Http\Controllers;

use App\Models\Process;

class ProcessController extends Controller
{
    public function index()
    {
        $allProcess = Process::all();

        return view('ciso/process/index', compact('allProcess'));
    }

    public function show(Process $process)
    {
        if ($process->featured_image_path) {
            $slideImage = asset('storage/'.$process->featured_image_path);
        } else {
            $slideNumber = Process::where('id', '<=', $process->id)->count();
            $slideImage = "/Images/process/Slide{$slideNumber}.JPG";
        }

        return view('ciso/process/show', compact('process', 'slideImage'));
    }
}
