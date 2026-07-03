<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CMSController extends Controller
{
    public function index()
    {
        $process = Process::select('id', 'process_id', 'title')->paginate(20);

        return view('process/cms/process/index', compact('process'));
    }

    public function show(Process $cm)
    {
        $process = $cm;
        $process->load('resources');

        return view('process/cms/process/show', compact('process'));
    }

    public function create()
    {
        $cm = null;

        return view('process/cms/process/create', compact('cm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'process_id' => ['required', 'unique:cms_process'],
            'title' => 'required',
            'title_ar' => 'nullable',
            'description' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        Process::create([
            'process_id' => $attributes['process_id'],
            'title' => $attributes['title'],
            'title_ar' => $attributes['title_ar'] ?? null,
            'description' => $attributes['description'] ?? null,
            'featured_image_path' => $request->file('featured_image')?->store('cms/images', 'public'),
        ]);

        return redirect(route('cms.index'))
            ->with('success', 'Process saved successfully.');
    }

    public function edit(Request $request, Process $cm)
    {
        return view('process/cms/process/create', compact('cm'));
    }

    public function update(Request $request, Process $cm): RedirectResponse
    {
        $attributes = $request->validate([
            'process_id' => ['required', 'unique:cms_process,process_id,'.$cm->id],
            'title' => 'required',
            'title_ar' => 'nullable',
            'description' => 'nullable',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $data = [
            'process_id' => $attributes['process_id'],
            'title' => $attributes['title'],
            'title_ar' => $attributes['title_ar'] ?? null,
            'description' => $attributes['description'] ?? null,
        ];

        if ($request->hasFile('featured_image')) {
            if ($cm->featured_image_path) {
                Storage::disk('public')->delete($cm->featured_image_path);
            }
            $data['featured_image_path'] = $request->file('featured_image')->store('cms/images', 'public');
        }

        $cm->update($data);

        return redirect(route('cms.index'))
            ->with('success', 'Process saved successfully.');
    }

    public function destroy(Process $cm): RedirectResponse
    {
        $cm->load('resources');
        if ($cm->resources()->count() > 0) {
            return redirect(route('cms.index'))
                ->with('error', 'Process cannot be deleted as it has resources attached to it.');
        }

        if ($cm->featured_image_path) {
            Storage::disk('public')->delete($cm->featured_image_path);
        }

        $cm->delete();

        return redirect(route('cms.index'))
            ->with('success', 'Process deleted successfully.');
    }
}
