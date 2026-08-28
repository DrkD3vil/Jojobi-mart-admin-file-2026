<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $locationId = config('store.location_id');

        $featured = Product::active()->online()
            ->with(['images', 'brand', 'batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true)])
            ->latest('id')
            ->take(8)
            ->get();

        $categories = Category::active()->whereNull('parent_id')->orderBy('name')->take(6)->get();
        $brands = Brand::active()->orderBy('name')->take(10)->get();

        $onSale = Product::active()->online()
            ->with(['images', 'brand', 'batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true)])
            ->whereHas('batches', fn ($q) => $q->whereNotNull('discounted_price'))
            ->take(8)
            ->get();

        $productCount = Product::active()->online()->count();

        return view('home', compact('featured', 'categories', 'brands', 'onSale', 'locationId', 'productCount'));
    }
}
