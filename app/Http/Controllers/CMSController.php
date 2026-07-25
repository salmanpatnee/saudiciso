<?php

namespace App\Http\Controllers;

use App\Models\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CMSController extends Controller
{
    public function index()
    {
        $process = Process::select('id', 'process_id', 'title', 'order')->orderBy('order')->paginate(20);

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
            'order' => 'nullable|integer',
        ]);

        Process::create([
            'process_id' => $attributes['process_id'],
            'slug' => $this->generateUniqueSlug($attributes['title']),
            'title' => $attributes['title'],
            'title_ar' => $attributes['title_ar'] ?? null,
            'description' => $attributes['description'] ?? null,
            'featured_image_path' => $request->file('featured_image')?->store('cms/images', 'public'),
            'order' => $attributes['order'] ?? null,
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
            'order' => 'nullable|integer',
        ]);

        $data = [
            'process_id' => $attributes['process_id'],
            'title' => $attributes['title'],
            'title_ar' => $attributes['title_ar'] ?? null,
            'description' => $attributes['description'] ?? null,
            'order' => $attributes['order'] ?? null,
        ];

        if ($attributes['title'] !== $cm->title) {
            $data['slug'] = $this->generateUniqueSlug($attributes['title'], $cm->id);
        }

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

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $suffix = 1;

        while (
            Process::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.++$suffix;
        }

        return $slug;
    }
}
