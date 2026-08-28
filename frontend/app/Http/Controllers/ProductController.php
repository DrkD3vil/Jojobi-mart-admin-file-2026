<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = $this->filtered($request)->paginate(12)->withQueryString();

        $categories = Category::active()->whereNull('parent_id')->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'wishedIds' => $this->wishedIds(),
            'pageTitle' => $request->filled('q') ? 'Search: "' . $request->q . '"' : 'All products',
        ]);
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['images', 'category', 'brand']);

        $batch = $product->onlineBatch(config('store.location_id'));
        abort_if(!$batch, 404);

        $available = $batch->availableAt(config('store.location_id'));

        $reviews = $product->reviews()->with('customer')->latest()->take(20)->get();

        $related = Product::active()->online()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with(['images', 'batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true)])
            ->take(8)
            ->get();

        if ($related->isEmpty()) {
            $related = Product::active()->online()
                ->where('id', '!=', $product->id)
                ->with(['images', 'batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true)])
                ->inRandomOrder()
                ->take(8)
                ->get();
        }

        $canReview = false;
        if ($customerId = Auth::guard('customer')->id()) {
            $bought = \App\Models\OrderItem::where('product_id', $product->id)
                ->whereHas('order', fn ($q) => $q->where('customer_id', $customerId))
                ->exists();
            $alreadyReviewed = $product->reviews()->where('customer_id', $customerId)->exists();
            $canReview = $bought && !$alreadyReviewed;
        }

        return view('products.show', [
            'product' => $product,
            'batch' => $batch,
            'available' => $available,
            'reviews' => $reviews,
            'related' => $related,
            'wishedIds' => $this->wishedIds(),
            'canReview' => $canReview,
        ]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $products = Product::active()->online()
            ->where('name', 'like', "%{$q}%")
            ->with(['images', 'batches' => fn ($query) => $query->where('is_active', true)->where('is_online', true)])
            ->take(8)
            ->get();

        $locationId = config('store.location_id');

        return response()->json($products->map(function (Product $p) use ($locationId) {
            $batch = $p->onlineBatch($locationId);
            $image = $p->images->firstWhere('is_primary', true) ?? $p->images->first();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'url' => route('products.show', $p),
                'image' => $image ? asset('storage/' . $image->image_path) : null,
                'price' => $batch?->displayPrice(),
            ];
        }));
    }

    private function filtered(Request $request)
    {
        $query = Product::active()->online()->with([
            'images', 'brand', 'category',
            'batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true),
        ]);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->integer('brand'));
        }

        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->whereHas('batches', function ($q) use ($request) {
                $q->where('is_active', true)->where('is_online', true);
                if ($request->filled('min_price')) {
                    $q->where('sell_price', '>=', (float) $request->min_price);
                }
                if ($request->filled('max_price')) {
                    $q->where('sell_price', '<=', (float) $request->max_price);
                }
            });
        }

        match ($request->query('sort')) {
            'price_asc' => $query->orderBy(
                \App\Models\ProductBatch::select('sell_price')
                    ->whereColumn('product_id', 'products.id')
                    ->where('is_active', true)->where('is_online', true)
                    ->orderBy('sell_price')->limit(1)
            ),
            'price_desc' => $query->orderByDesc(
                \App\Models\ProductBatch::select('sell_price')
                    ->whereColumn('product_id', 'products.id')
                    ->where('is_active', true)->where('is_online', true)
                    ->orderBy('sell_price')->limit(1)
            ),
            'name' => $query->orderBy('name'),
            default => $query->latest('id'),
        };

        return $query;
    }

    private function wishedIds(): array
    {
        $customerId = Auth::guard('customer')->id();
        if (!$customerId) {
            return [];
        }

        return Wishlist::where('customer_id', $customerId)->pluck('product_id')->all();
    }
}
