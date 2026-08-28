<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\CartService;
use App\Services\RewardRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CartController extends Controller
{
    // Keep this in sync with admin/app/Http/Controllers/CartController.php::POINT_RATE
    // -- both apps redeem against the same `reward_points` balance on the
    // shared `customers` table, so a rate change made in only one place
    // would let a customer redeem more (or fewer) points than intended
    // depending on which storefront they checked out from.
    private const POINT_RATE = 1.0;

    public function __construct(
        private CartService $cartService,
        private RewardRecommendationService $rewardRecommendationService,
    ) {
    }

    public function index()
    {
        $cart = $this->cartService->peek();
        $cart?->load(['items.product.images', 'items.batch', 'items.image']);

        $customer = Auth::guard('customer')->user();
        $recommendations = ($customer && (float) $customer->reward_points > 0)
            ? $this->rewardRecommendationService->forCustomer($customer)
            : collect();

        return view('cart.index', ['cart' => $cart, 'recommendations' => $recommendations]);
    }

    public function mini()
    {
        $cart = $this->cartService->peek();
        $cart?->load(['items.product', 'items.image']);

        $items = $cart ? $cart->items->map(fn ($i) => [
            'id' => $i->id,
            'name' => $i->product?->name,
            'url' => $i->product ? route('products.show', $i->product) : '#',
            'image' => $i->image ? asset('storage/' . $i->image->image_path) : null,
            'quantity' => (float) $i->quantity,
            'unit' => $i->unit,
            'unit_price' => (float) $i->unit_price,
            'total_price' => (float) $i->total_price,
        ]) : collect();

        return response()->json([
            'items' => $items,
            'count' => (int) $items->sum('quantity'),
            'total' => (float) $items->sum('total_price'),
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'batch_id' => 'required|integer|exists:product_batches,id',
            'quantity' => 'nullable|numeric|min:0.0001',
        ]);

        try {
            $cart = $this->cartService->addItem((int) $data['batch_id'], (float) ($data['quantity'] ?? 1));
        } catch (RuntimeException $e) {
            return $this->fail($request, $e->getMessage());
        }

        return $this->ok($request, $cart, 'Added to your cart.');
    }

    public function update(Request $request, int $item)
    {
        $data = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        try {
            $cart = $this->cartService->updateItem($item, (float) $data['quantity']);
        } catch (RuntimeException $e) {
            return $this->fail($request, $e->getMessage());
        }

        return $this->ok($request, $cart, 'Cart updated.');
    }

    public function remove(Request $request, int $item)
    {
        $cart = $this->cartService->removeItem($item);

        return $this->ok($request, $cart, 'Item removed.');
    }

    /**
     * Previews/applies a reward-point redemption against the active cart.
     * Guests have no reward_points balance to redeem from -- they never get
     * this far (the cart-building routes stay open to them, but this one
     * requires a logged-in customer). Mirrors admin's
     * CartController::applyRewards() -- OrderService::placeOrder()
     * independently re-validates and re-applies these fields from the
     * (locked) cart at checkout time, so this can never under/over-apply
     * points on its own.
     */
    public function applyRewards(Request $request): JsonResponse
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['success' => false, 'message' => 'Log in to redeem your reward points.'], 422);
        }

        $data = $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($data) {
            $cart = $this->cartService->lockCurrent();

            $customer = Customer::whereKey(Auth::guard('customer')->id())->lockForUpdate()->first();
            if (!$customer) {
                return response()->json(['success' => false, 'message' => 'Customer not found.'], 422);
            }

            $points = (int) $data['points'];
            if ($points > (float) $customer->reward_points) {
                return response()->json(['success' => false, 'message' => 'Not enough reward points.'], 422);
            }

            $rewardAmount = min($points * self::POINT_RATE, (float) $cart->total);

            $cart->rewards_points_used = $points;
            $cart->rewards_amount_used = $rewardAmount;
            $cart->save();

            return response()->json([
                'success' => true,
                'rewards_points_used' => $points,
                'rewards_amount_used' => $rewardAmount,
            ]);
        });
    }

    public function clearRewards(Request $request): JsonResponse
    {
        $cart = $this->cartService->lockCurrent();
        $cart->rewards_points_used = 0;
        $cart->rewards_amount_used = 0;
        $cart->save();

        return response()->json(['success' => true]);
    }

    private function ok(Request $request, $cart, string $message)
    {
        $cart->load('items');
        $count = (int) $cart->items->sum('quantity');
        $total = (float) $cart->items->sum('total_price');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
                'total' => $total,
                'total_formatted' => number_format($total, 2),
            ]);
        }

        return back()->with('success', $message);
    }

    private function fail(Request $request, string $message)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return back()->withErrors(['cart' => $message]);
    }
}
