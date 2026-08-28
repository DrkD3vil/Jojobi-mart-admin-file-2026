<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\BatchStock;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin-side queue for channel=online orders only. Deliberately thin: it
 * only lists/filters. Advancing a status (process/complete/cancel) reuses
 * OrderController's existing routes/logic rather than duplicating the
 * stock-restore and timeline handling those already do correctly.
 *
 * The one exception is the "packaged" fulfillment step (package()/queue()
 * below) -- it's orthogonal to `orders.status` (see the
 * add_packaged_fields_to_orders_table migration) so it lives here instead
 * of in OrderController's status state machine.
 */
class EcommerceOrderController extends Controller
{
    /**
     * A batch is considered "scarce" for pick-queue prioritization purposes
     * when its total on-hand quantity (summed across locations) is at or
     * below this. Not tied to any existing low-stock concept in the
     * codebase -- FinancialDashboardController/FinancialTodayDashboardController
     * use a `(on_hand - reserved) < 10` threshold for their own dashboard
     * tiles, and ProductBatchController uses `<= 10` on raw quantity, but
     * neither is exposed as a shared/reusable constant, and Setting.php is
     * just a generic key/value store with nothing low-stock-specific in it.
     * Kept local and named so it's easy to tune independently later.
     */
    private const LOW_STOCK_THRESHOLD = 5;

    public function index(Request $request)
    {
        $status = $request->query('status', '');

        $orders = Order::onlineChannel()
            ->with(['customer:id,name,phone', 'location:id,name'])
            ->withCount('items')
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'pending' => Order::onlineChannel()->where('status', 'pending')->count(),
            'processing' => Order::onlineChannel()->where('status', 'processing')->count(),
            'completed' => Order::onlineChannel()->where('status', 'completed')->count(),
            'cancelled' => Order::onlineChannel()->where('status', 'cancelled')->count(),
        ];

        return view('ecommerce.orders.index', compact('orders', 'counts', 'status'));
    }

    /**
     * Mark a processing online order as packaged -- items picked, packed,
     * and ready for delivery. Orthogonal to `status`: does not advance it.
     */
    public function package(Order $order)
    {
        return DB::transaction(function () use ($order) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->channel !== 'online') {
                return back()->with('error', 'Only online orders go through packaging.');
            }
            if ($order->status !== 'processing') {
                return back()->with('error', 'Only processing orders can be marked packaged.');
            }
            if ($order->packaged_at) {
                return back()->with('error', 'Already packaged.');
            }

            $order->update(['packaged_at' => now(), 'packaged_by' => auth()->id()]);
            $order->recordTimeline('packaged', 'Order Packaged', 'Items picked, packed, and ready for delivery.', 'processing', 'processing', 'package');

            return back()->with('success', 'Order marked as packaged.');
        });
    }

    /**
     * Staff fulfillment priority queue: online orders that are processing
     * but not yet packaged, ranked by a score computed on the fly (no new
     * DB fields beyond packaged_at) --
     *   score = ageHours + (prepaid ? 4 : 0) + (has a scarce-stock item ? 6 : 0)
     * so older orders, prepaid orders, and orders holding scarce stock (that
     * should be picked before it runs out from under them) bubble up first.
     */
    public function queue()
    {
        $orders = Order::awaitingPackaging()
            ->with(['customer:id,name,phone', 'items:id,order_id,product_batch_id,product_name,quantity'])
            ->get();

        $batchIds = $orders->pluck('items')->flatten()->pluck('product_batch_id')->filter()->unique();

        $scarceBatchIds = BatchStock::whereIn('product_batch_id', $batchIds)
            ->selectRaw('product_batch_id, SUM(on_hand) as total_on_hand')
            ->groupBy('product_batch_id')
            ->havingRaw('SUM(on_hand) <= ?', [self::LOW_STOCK_THRESHOLD])
            ->pluck('product_batch_id')
            ->all();

        $orders = $orders->map(function (Order $order) use ($scarceBatchIds) {
            $ageHours = $order->created_at->diffInHours(now());
            $hasScarceItem = $order->items->contains(
                fn ($item) => in_array($item->product_batch_id, $scarceBatchIds, true)
            );

            $order->setAttribute('queue_age_hours', $ageHours);
            $order->setAttribute('queue_has_scarce_item', $hasScarceItem);
            $order->setAttribute('queue_score', $ageHours * 1.0
                + ($order->payment_status === 'paid' ? 4 : 0)
                + ($hasScarceItem ? 6 : 0));

            return $order;
        })
            ->sortByDesc('queue_score')
            ->values()
            ->take(50);

        return view('ecommerce.orders.queue', ['orders' => $orders]);
    }
}
