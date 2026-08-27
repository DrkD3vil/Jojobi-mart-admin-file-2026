<?php

namespace App\Observers;

use App\Models\BatchStock;
use App\Services\RealtimeMetricsService;

class BatchStockObserver
{
    public function __construct(private RealtimeMetricsService $rt) {}

    public function updated(BatchStock $s): void
    {
        $loc = (int)($s->location_id ?? 0);

        $oldAvail = (float)($s->getOriginal('on_hand') ?? 0) - (float)($s->getOriginal('reserved') ?? 0);
        $newAvail = (float)($s->on_hand ?? 0) - (float)($s->reserved ?? 0);

        $wasLow = $oldAvail < 10;
        $isLow  = $newAvail < 10;

        if ($wasLow && !$isLow) {
            $this->rt->incLowStock($loc, -1);
            $this->rt->incLowStock(0, -1);
        } elseif (!$wasLow && $isLow) {
            $this->rt->incLowStock($loc, +1);
            $this->rt->incLowStock(0, +1);
        }
    }
}
