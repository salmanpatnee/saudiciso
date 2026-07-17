<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function index(): View
    {
        $productsData = Product::orderBy('id')->get();

        return view('ciso/products/index', compact('productsData'));
    }

    public function show(Product $product): View
    {
        return view('ciso/products/show', compact('product'));
    }
}
