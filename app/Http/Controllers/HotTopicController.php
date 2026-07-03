<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Models\HotTopic;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HotTopicController extends Controller
{
    public function index(): View
    {
        $hotTopics = HotTopic::withCount('resources')->latest()->paginate(20);

        return view('process/hot-topics/index', compact('hotTopics'));
    }

    public function create(): View
    {
        $item = null;
        $categories = array_column(Category::cases(), 'value');

        return view('process/hot-topics/create', compact('item', 'categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::enum(Category::class)],
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'body' => 'nullable|string',
            'resources' => 'nullable|array',
            'resources.*' => 'file|extensions:pdf,doc,docx|max:20480',
        ]);

        $hotTopic = HotTopic::create([
            'title' => $attributes['title'],
            'category' => $attributes['category'],
            'body' => $attributes['body'] ?? null,
            'featured_image_path' => $request->file('featured_image')?->store('hot-topics/images', 'public'),
        ]);

        $this->storeResources($request, $hotTopic);

        return redirect(route('admin.hot-topics.index'))
            ->with('success', 'Hot Topic saved successfully.');
    }

    public function edit(HotTopic $hotTopic): View
    {
        $item = $hotTopic->load('resources');
        $categories = array_column(Category::cases(), 'value');

        return view('process/hot-topics/create', compact('item', 'categories'));
    }

    public function update(Request $request, HotTopic $hotTopic): RedirectResponse
    {
        $attributes = $request->validate([
            'title' => 'required|string|max:255',
            'category' => ['required', Rule::enum(Category::class)],
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'body' => 'nullable|string',
            'resources' => 'nullable|array',
            'resources.*' => 'file|extensions:pdf,doc,docx|max:20480',
        ]);

        $data = [
            'title' => $attributes['title'],
            'category' => $attributes['category'],
            'body' => $attributes['body'] ?? null,
        ];

        if ($request->hasFile('featured_image')) {
            if ($hotTopic->featured_image_path) {
                Storage::disk('public')->delete($hotTopic->featured_image_path);
            }

            $data['featured_image_path'] = $request->file('featured_image')->store('hot-topics/images', 'public');
        }

        $hotTopic->update($data);

        $this->storeResources($request, $hotTopic);

        return redirect(route('admin.hot-topics.index'))
            ->with('success', 'Hot Topic saved successfully.');
    }

    public function destroy(HotTopic $hotTopic): RedirectResponse
    {
        if ($hotTopic->featured_image_path) {
            Storage::disk('public')->delete($hotTopic->featured_image_path);
        }

        foreach ($hotTopic->resources as $resource) {
            Storage::disk('public')->delete($resource->file_path);
            $resource->delete();
        }

        $hotTopic->delete();

        return redirect(route('admin.hot-topics.index'))
            ->with('success', 'Hot Topic deleted successfully.');
    }

    public function destroyResource(Resource $resource): RedirectResponse
    {
        Storage::disk('public')->delete($resource->file_path);
        $resource->delete();

        return redirect()->back()->with('success', 'Attachment deleted successfully.');
    }

    private function storeResources(Request $request, HotTopic $hotTopic): void
    {
        foreach ($request->file('resources', []) as $file) {
            $hotTopic->resources()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $file->store('hot-topics/resources', 'public'),
                'file_type' => $file->getClientMimeType(),
                'resource_type' => 'document',
            ]);
        }
    }
}
