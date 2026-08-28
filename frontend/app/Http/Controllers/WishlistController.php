<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::where('customer_id', Auth::guard('customer')->id())
            ->with(['product.images', 'product.brand', 'product.batches' => fn ($q) => $q->where('is_active', true)->where('is_online', true)])
            ->latest()
            ->get();

        return view('account.wishlist', compact('items'));
    }

    public function toggle(Request $request, Product $product)
    {
        $customerId = Auth::guard('customer')->id();

        $existing = Wishlist::where('customer_id', $customerId)->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $wished = false;
        } else {
            Wishlist::create(['customer_id' => $customerId, 'product_id' => $product->id]);
            $wished = true;
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'wished' => $wished]);
        }

        return back()->with('success', $wished ? 'Added to your wishlist.' : 'Removed from your wishlist.');
    }
}
