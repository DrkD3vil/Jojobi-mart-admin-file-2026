<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $customerId = Auth::guard('customer')->id();

        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:150',
            'comment' => 'nullable|string|max:2000',
        ]);

        $bought = OrderItem::where('product_id', $product->id)
            ->whereHas('order', fn ($q) => $q->where('customer_id', $customerId))
            ->exists();

        abort_unless($bought, 403, 'You can review a product after purchasing it.');

        ProductReview::updateOrCreate(
            ['product_id' => $product->id, 'customer_id' => $customerId],
            $data
        );

        return back()->with('success', 'Thanks for your review!');
    }
}
