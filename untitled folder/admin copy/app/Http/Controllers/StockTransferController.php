<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Models\Location;
use App\Models\StockTransaction;
use App\Models\StockTransactionLine;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockTransferController extends Controller
{
    public function create()
    {
        $locations = Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id','name']);

        return view('transfers.create', compact('locations'));
    }

    /**
     * AJAX: Search batches by product name / product barcode / product sku / batch id
     * NOTE: product_batches.barcode removed (your db doesn't have it)
     */


public function ajaxBatches(Request $request)
{
    $q = trim((string)$request->query('q', ''));
    $fromLocationId = (int)$request->query('from_location_id', 0);

    if ($q === '' || mb_strlen($q) < 2) {
        return response()->json(['data' => []]);
    }

    $qb = DB::table('product_batches as pb')
        ->join('products as p', 'p.id', '=', 'pb.product_id')
        ->leftJoin('batch_stocks as bs', function ($j) use ($fromLocationId) {
            $j->on('bs.product_batch_id', '=', 'pb.id');
            if ($fromLocationId) {
                $j->where('bs.location_id', $fromLocationId);
            }
        })
        ->where(function ($w) use ($q) {
            $w->where('p.name', 'like', "%{$q}%")
              ->orWhere('p.barcode', 'like', "%{$q}%")
              ->orWhere('pb.batch_no', 'like', "%{$q}%")
              ->orWhere('pb.batch_sku', 'like', "%{$q}%");

            if (ctype_digit($q)) {
                $w->orWhere('pb.id', (int)$q);
            }
        });

    $rows = $qb->orderBy('p.name')
        ->limit(20)
        ->select([
            'p.id as product_id',
            'p.name as product_name',
            'p.barcode as product_barcode',
            'pb.id as product_batch_id',
            'pb.batch_no',
            'pb.batch_sku',
            DB::raw('COALESCE(bs.on_hand, 0) as on_hand'),
            DB::raw('COALESCE(bs.reserved, 0) as reserved'),
            DB::raw('(COALESCE(bs.on_hand,0) - COALESCE(bs.reserved,0)) as available'),
        ])
        ->get();

    return response()->json(['data' => $rows]);
}

public function ajaxAvailability(Request $request)
{
    $batchId = (int)$request->query('product_batch_id', 0);
    $locId   = (int)$request->query('location_id', 0);

    if (!$batchId || !$locId) {
        return response()->json(['on_hand' => 0, 'reserved' => 0, 'available' => 0]);
    }

    $row = DB::table('batch_stocks')
        ->where('product_batch_id', $batchId)
        ->where('location_id', $locId)
        ->first(['on_hand','reserved']);

    $on = (float)($row->on_hand ?? 0);
    $rs = (float)($row->reserved ?? 0);

    return response()->json([
        'on_hand' => $on,
        'reserved' => $rs,
        'available' => $on - $rs,
    ]);
}

    public function store(StoreTransferRequest $request, InventoryService $inventory)
    {
        $data = $request->validated();
        $idemKey = $request->header('X-Idempotency-Key') ?? (string) Str::uuid();

        $existing = StockTransaction::where('idempotency_key', $idemKey)->first();
        if ($existing) {
            return redirect()->back()->with('ok', "Already processed transfer tx #{$existing->id}");
        }

        return DB::transaction(function () use ($data, $inventory, $idemKey) {
            $tx = StockTransaction::create([
                'type' => 'TRANSFER',
                'status' => 'DRAFT',
                'from_location_id' => (int)$data['from_location_id'],
                'to_location_id' => (int)$data['to_location_id'],
                'ref_type' => 'manual',
                'ref_id' => null,
                'idempotency_key' => $idemKey,
                'note' => $data['note'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $now = now();
            $lines = [];
            foreach ($data['lines'] as $l) {
                $lines[] = [
                    'stock_transaction_id' => $tx->id,
                    'product_id' => (int)$l['product_id'],
                    'product_batch_id' => (int)$l['product_batch_id'],
                    'qty' => (float)$l['qty'],
                    'unit' => $l['unit'] ?? 'pcs',
                    'meta' => json_encode(['reason' => 'transfer']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            StockTransactionLine::insert($lines);
            $inventory->post($tx);

            return redirect()->back()->with('ok', "Transfer posted. Tx ID={$tx->id}");
        });
    }
}
