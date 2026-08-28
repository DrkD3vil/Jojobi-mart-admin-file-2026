<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::active()->withCount('products')->orderBy('name')->get();

        return view('brands.index', compact('brands'));
    }

    public function show(Brand $brand)
    {
        abort_unless($brand->is_active, 404);

        $products = Product::active()->online()
            ->where('brand_id', $brand->id)
            ->with(['images', 'batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true)])
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::active()->whereNull('parent_id')->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();

        $customerId = Auth::guard('customer')->id();
        $wishedIds = $customerId ? Wishlist::where('customer_id', $customerId)->pluck('product_id')->all() : [];

        return view('brands.show', compact('brand', 'products', 'categories', 'brands', 'wishedIds'));
    }
}
