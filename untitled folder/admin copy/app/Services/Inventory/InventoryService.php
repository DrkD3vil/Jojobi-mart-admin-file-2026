<?php

namespace App\Services\Inventory;

use App\Models\BatchStock;
use App\Models\StockLedger;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Post a stock transaction (DRAFT -> POSTED).
     * Strong logic:
     * - DB transaction (atomic)
     * - Row lock on BatchStock rows
     * - Validates availability for OUT
     * - Writes StockLedger audit lines
     */
    public function post(StockTransaction $tx): StockTransaction
    {
        return DB::transaction(function () use ($tx) {

            // ✅ idempotent inside app (avoid double posting)
            if ($tx->status === 'POSTED') {
                return $tx->fresh('lines');
            }

            $tx->load('lines');

            if ($tx->lines->isEmpty()) {
                throw new \RuntimeException("Transaction has no lines.");
            }

            foreach ($tx->lines as $line) {

                // Determine which location this line affects
                // - For TRANSFER we will affect both locations
                // - For other types we affect to_location_id (in) or from_location_id (out)
                if ($tx->type === 'TRANSFER') {
                    $this->applyTransferLine($tx, $line);
                    continue;
                }

                // For normal types decide location:
                // SALE_OUT / REPLACEMENT_OUT / ADJUSTMENT_OUT => from_location_id required
                // RETURN_IN / REPLACEMENT_IN / ADJUSTMENT_IN => to_location_id required
                $locationId = null;

                if (in_array($tx->type, ['SALE_OUT','REPLACEMENT_OUT','ADJUSTMENT_OUT'])) {
                    $locationId = $tx->from_location_id;
                } else {
                    $locationId = $tx->to_location_id;
                }

                if (!$locationId) {
                    throw new \RuntimeException("Missing location for tx type={$tx->type}");
                }

                // Lock (or create) BatchStock row
                $stock = $this->lockStockRow($line->product_batch_id, $locationId);

                if (in_array($tx->type, ['SALE_OUT','REPLACEMENT_OUT','ADJUSTMENT_OUT'])) {
                    // ✅ Prevent negative stock
                    if ((float)$stock->available < (float)$line->qty) {
                        throw new \RuntimeException("Insufficient stock for batch {$line->product_batch_id} at location {$locationId}");
                    }

                    $stock->on_hand = (float)$stock->on_hand - (float)$line->qty;
                    $stock->save();

                    $this->writeLedger(
                        productBatchId: $line->product_batch_id,
                        locationId: $locationId,
                        refType: $tx->ref_type,
                        refId: $tx->ref_id,
                        lineId: $line->id,
                        direction: 'OUT',
                        qty: (float)$line->qty,
                        unit: $line->unit,
                        meta: $line->meta,
                        createdBy: $tx->created_by
                    );
                } else {
                    // IN
                    $stock->on_hand = (float)$stock->on_hand + (float)$line->qty;
                    $stock->save();

                    $this->writeLedger(
                        productBatchId: $line->product_batch_id,
                        locationId: $locationId,
                        refType: $tx->ref_type,
                        refId: $tx->ref_id,
                        lineId: $line->id,
                        direction: 'IN',
                        qty: (float)$line->qty,
                        unit: $line->unit,
                        meta: $line->meta,
                        createdBy: $tx->created_by
                    );
                }
            }

            $tx->status = 'POSTED';
            $tx->save();

            return $tx->fresh('lines');
        });
    }

    /**
     * Transfer line affects two locations:
     * from_location OUT and to_location IN.
     */
    protected function applyTransferLine($tx, $line): void
    {
        if (!$tx->from_location_id || !$tx->to_location_id) {
            throw new \RuntimeException("Transfer requires from_location_id and to_location_id.");
        }

        // Lock from stock
        $fromStock = $this->lockStockRow($line->product_batch_id, $tx->from_location_id);

        if ((float)$fromStock->available < (float)$line->qty) {
            throw new \RuntimeException("Insufficient stock for transfer batch {$line->product_batch_id}");
        }

        $fromStock->on_hand = (float)$fromStock->on_hand - (float)$line->qty;
        $fromStock->save();

        $this->writeLedger(
            productBatchId: $line->product_batch_id,
            locationId: $tx->from_location_id,
            refType: $tx->ref_type,
            refId: $tx->ref_id,
            lineId: $line->id,
            direction: 'OUT',
            qty: (float)$line->qty,
            unit: $line->unit,
            meta: array_merge($line->meta ?? [], ['to' => $tx->to_location_id]),
            createdBy: $tx->created_by
        );

        // Lock to stock
        $toStock = $this->lockStockRow($line->product_batch_id, $tx->to_location_id);

        $toStock->on_hand = (float)$toStock->on_hand + (float)$line->qty;
        $toStock->save();

        $this->writeLedger(
            productBatchId: $line->product_batch_id,
            locationId: $tx->to_location_id,
            refType: $tx->ref_type,
            refId: $tx->ref_id,
            lineId: $line->id,
            direction: 'IN',
            qty: (float)$line->qty,
            unit: $line->unit,
            meta: array_merge($line->meta ?? [], ['from' => $tx->from_location_id]),
            createdBy: $tx->created_by
        );
    }

    /**
     * Lock stock row or create if not exist (for IN operations).
     */
    protected function lockStockRow(int $batchId, int $locationId): BatchStock
    {
        $stock = BatchStock::query()
            ->where('product_batch_id', $batchId)
            ->where('location_id', $locationId)
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            // Create then re-lock it (safe in transaction)
            BatchStock::create([
                'product_batch_id' => $batchId,
                'location_id' => $locationId,
                'on_hand' => 0,
                'reserved' => 0,
            ]);

            $stock = BatchStock::query()
                ->where('product_batch_id', $batchId)
                ->where('location_id', $locationId)
                ->lockForUpdate()
                ->first();
        }

        return $stock;
    }

    protected function writeLedger(
        int $productBatchId,
        int $locationId,
        ?string $refType,
        ?int $refId,
        ?int $lineId,
        string $direction,
        float $qty,
        ?string $unit,
        ?array $meta,
        ?int $createdBy
    ): void {
        StockLedger::create([
            'product_batch_id' => $productBatchId,
            'location_id' => $locationId,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'line_id' => $lineId,
            'direction' => $direction,
            'qty' => $qty,
            'unit' => $unit,
            'meta' => $meta,
            'created_by' => $createdBy,
        ]);
    }
}
