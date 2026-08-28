<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()->whereNull('parent_id')->withCount('products')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Request $request, Category $category)
    {
        abort_unless($category->is_active, 404);

        $childIds = $category->children()->pluck('id')->push($category->id);

        $products = Product::active()->online()
            ->whereIn('category_id', $childIds)
            ->with(['images', 'brand', 'batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true)])
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::active()->whereNull('parent_id')->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();

        $customerId = Auth::guard('customer')->id();
        $wishedIds = $customerId ? Wishlist::where('customer_id', $customerId)->pluck('product_id')->all() : [];

        return view('categories.show', compact('category', 'products', 'categories', 'brands', 'wishedIds'));
    }
}
