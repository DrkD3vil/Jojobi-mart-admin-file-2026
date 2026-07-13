<?php

namespace App\Services;

use App\Models\RealtimeMetric;
use Illuminate\Support\Facades\DB;

class RealtimeMetricsService
{
    // location_id: 0 = global
    public function snapshot(int $locationId = 0): RealtimeMetric
    {
        return RealtimeMetric::firstOrCreate(['location_id' => $locationId]);
    }

    public function bumpLastOrderId(int $locationId, int $orderId): void
    {
        $this->upsertRow($locationId, [
            'last_order_id' => $orderId,
        ], [
            'last_order_id' => DB::raw("GREATEST(last_order_id, VALUES(last_order_id))"),
            'updated_at' => now(),
        ]);
    }

    public function incPending(int $locationId, int $delta): void
    {
        // delta can be +1 or -1
        $delta = (int)$delta;

        $this->upsertRow($locationId, [
            'pending_orders' => max(0, $delta), // insert value (doesn't matter much)
        ], [
            'pending_orders' => DB::raw("GREATEST(0, pending_orders + ($delta))"),
            'updated_at' => now(),
        ]);
    }

    public function incLowStock(int $locationId, int $delta): void
    {
        $delta = (int)$delta;

        $this->upsertRow($locationId, [
            'low_stock_items' => max(0, $delta),
        ], [
            'low_stock_items' => DB::raw("GREATEST(0, low_stock_items + ($delta))"),
            'updated_at' => now(),
        ]);
    }

    public function setAbandoned(int $locationId, int $count): void
    {
        $count = max(0, (int)$count);

        $this->upsertRow($locationId, [
            'abandoned_carts' => $count,
        ], [
            'abandoned_carts' => $count,
            'updated_at' => now(),
        ]);
    }

    /**
     * Internal helper (MySQL)
     */
    private function upsertRow(int $locationId, array $insert, array $update): void
    {
        $insert = array_merge([
            'location_id' => $locationId,
            'created_at' => now(),
            'updated_at' => now(),
        ], $insert);

        // Build raw SQL for ON DUPLICATE KEY UPDATE
        $columns = array_keys($insert);
        $placeholders = implode(',', array_fill(0, count($columns), '?'));

        $updates = [];
        $bindings = array_values($insert);

        foreach ($update as $col => $val) {
            if ($val instanceof \Illuminate\Database\Query\Expression) {
                $updates[] = "`$col` = {$val->getValue(DB::connection()->getQueryGrammar())}";
            } else {
                $updates[] = "`$col` = ?";
                $bindings[] = $val;
            }
        }

        $sql = "INSERT INTO `realtime_metrics` (`" . implode('`,`', $columns) . "`)
                VALUES ($placeholders)
                ON DUPLICATE KEY UPDATE " . implode(',', $updates);

        DB::statement($sql, $bindings);
    }
}
