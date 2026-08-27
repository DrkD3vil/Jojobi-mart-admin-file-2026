<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\RealtimeMetricsService;

class OrderObserver
{
    public function __construct(private RealtimeMetricsService $rt) {}

    public function created(Order $order): void
    {
        $loc = (int)($order->location_id ?? 0);

        // last order id (per location + global)
        $this->rt->bumpLastOrderId($loc, $order->id);
        $this->rt->bumpLastOrderId(0, $order->id);

        if ($order->status === 'pending') {
            $this->rt->incPending($loc, +1);
            $this->rt->incPending(0, +1);
        }
    }

    public function updated(Order $order): void
    {
        $loc = (int)($order->location_id ?? 0);

        $this->rt->bumpLastOrderId($loc, $order->id);
        $this->rt->bumpLastOrderId(0, $order->id);

        if ($order->wasChanged('status')) {
            $from = (string)$order->getOriginal('status');
            $to   = (string)$order->status;

            if ($from === 'pending' && $to !== 'pending') {
                $this->rt->incPending($loc, -1);
                $this->rt->incPending(0, -1);
            }

            if ($from !== 'pending' && $to === 'pending') {
                $this->rt->incPending($loc, +1);
                $this->rt->incPending(0, +1);
            }
        }
    }
}
