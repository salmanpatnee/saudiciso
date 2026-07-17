<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductsAdminController extends Controller
{
    public function index()
    {
        $products = Product::select('id', 'title')->latest()->paginate(20);

        return view('process/products/index', compact('products'));
    }

    public function create()
    {
        $item = null;

        return view('process/products/create', compact('item'));
    }

    public function store(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'title' => 'required|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'body' => 'nullable|string',
        ]);

        Product::create([
            'title' => $attributes['title'],
            'slug' => $this->generateUniqueSlug($attributes['title']),
            'body' => $attributes['body'] ?? null,
            'featured_image_path' => $request->file('featured_image')?->store('products/images', 'public'),
        ]);

        return redirect(route('admin.products.index'))
            ->with('success', 'Product saved successfully.');
    }

    public function edit(Product $product)
    {
        $item = $product;

        return view('process/products/create', compact('item'));
    }

    public function update(Request $request, Product $product): RedirectResponse
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

        if ($attributes['title'] !== $product->title) {
            $data['slug'] = $this->generateUniqueSlug($attributes['title'], $product->id);
        }

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image_path) {
                Storage::disk('public')->delete($product->featured_image_path);
            }
            $data['featured_image_path'] = $request->file('featured_image')->store('products/images', 'public');
        }

        $product->update($data);

        return redirect(route('admin.products.index'))
            ->with('success', 'Product saved successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->featured_image_path) {
            Storage::disk('public')->delete($product->featured_image_path);
        }

        $product->delete();

        return redirect(route('admin.products.index'))
            ->with('success', 'Product deleted successfully.');
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $suffix = 1;

        while (
            Product::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $original.'-'.++$suffix;
        }

        return $slug;
    }
}
