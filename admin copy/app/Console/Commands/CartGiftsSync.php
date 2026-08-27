<?php

namespace App\Console\Commands;

use App\Models\Cart;
use App\Services\CartGiftService;
use Illuminate\Console\Command;

class CartGiftsSync extends Command
{

    protected $signature = 'cart:gifts-sync {cart_id}';
    protected $description = 'Sync batch free-offer gifts for a cart';

    public function handle(CartGiftService $service): int
    {
        $cartId = (int) $this->argument('cart_id');
        $cart = Cart::find($cartId);

        if (!$cart) {
            $this->error("Cart not found: {$cartId}");
            return self::FAILURE;
        }

        $result = $service->sync($cart);
        $this->info("Synced gifts for cart #{$cart->id}");
        if (!empty($result['hints'])) {
            foreach ($result['hints'] as $h) $this->line("- {$h}");
        }

        return self::SUCCESS;
    }
}
