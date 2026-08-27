<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\CartGiftService;
use Illuminate\Http\Request;

class ManualGiftController extends Controller
{
    //

    public function add(Request $request, Cart $cart, CartGiftService $giftService)
    {
        $data = $request->validate([
            'product_id' => ['required','integer'],
            'quantity' => ['nullable','numeric','min:1'],
        ]);

        // $giftService->addManualGift($cart, (int)$data['product_id'], (float)($data['quantity'] ?? 1));

        // return redirect()->route('cart.show', $cart);
    }
}
