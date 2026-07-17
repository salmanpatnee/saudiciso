<?php

namespace App\Http\Controllers;

use App\Models\CisoEducation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CisoEducationAdminController extends Controller
{
    public function index()
    {
        $cisoEducations = CisoEducation::select('id', 'title')->latest()->paginate(20);

        return view('process/ciso-education/index', compact('cisoEducations'));
    }

    public function create()
    {
        $item = null;

        return view('process/ciso-education/create', compact('item'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'title' => 'required|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'body' => 'nullable|string',
        ]);

        CisoEducation::create([
            'title' => $attributes['title'],
            'slug' => $this->generateUniqueSlug($attributes['title']),
            'body' => $attributes['body'] ?? null,
            'featured_image_path' => $request->file('featured_image')?->store('ciso-education/images', 'public'),
        ]);

        return redirect(route('admin.ciso-education.index'))
            ->with('success', 'CISO Education saved successfully.');
    }

    public function edit(CisoEducation $cisoEducation)
    {
        $item = $cisoEducation;

        return view('process/ciso-education/create', compact('item'));
    }

    public function update(Request $request, CisoEducation $cisoEducation): RedirectResponse
    {
        $attributes = $request->validate([
            'title' => 'required|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'body' => 'nullable|string',
        ]);

        $data = [
            'title' => $attributes['title'],
            'body' => $attributes['body'] ?? null,
        ];

        if ($attributes['title'] !== $cisoEducation->title) {
            $data['slug'] = $this->generateUniqueSlug($attributes['title'], $cisoEducation->id);
        }

        if ($request->hasFile('featured_image')) {
            if ($cisoEducation->featured_image_path) {
                Storage::disk('public')->delete($cisoEducation->featured_image_path);
            }
            $data['featured_image_path'] = $request->file('featured_image')->store('ciso-education/images', 'public');
        }

        $cisoEducation->update($data);

        return redirect(route('admin.ciso-education.index'))
            ->with('success', 'CISO Education saved successfully.');
    }

    public function destroy(CisoEducation $cisoEducation): RedirectResponse
    {
        if ($cisoEducation->featured_image_path) {
            Storage::disk('public')->delete($cisoEducation->featured_image_path);
        }

        $cisoEducation->delete();

        return redirect(route('admin.ciso-education.index'))
            ->with('success', 'CISO Education deleted successfully.');
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $suffix = 1;

        while (
            CisoEducation::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.++$suffix;
        }

        return $slug;
    }
}
