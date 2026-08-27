<?php

namespace App\Http\Controllers;

use App\Models\StockLedger;
use Illuminate\Http\Request;

class StockLedgerController extends Controller
{
    public function index(Request $request)
    {
        // First paint (fast): just load last 50. After that, UI will use AJAX.
        $rows = StockLedger::query()
            ->select(['id','created_at','product_batch_id','location_id','direction','qty','ref_type','ref_id','line_id','meta'])
            ->latest()
            ->paginate(50);

        return view('stock-ledger.index', compact('rows'));
    }

    /**
     * AJAX: server-side search + filters + pagination
     * Query params:
     *  q, batch_id, location_id, direction, date_from, date_to, min_qty, max_qty, ref_type, ref_id, page, per_page
     */
    public function ajaxIndex(Request $request)
    {
        $q         = trim((string)$request->query('q', ''));
        $batchId   = (int)$request->query('batch_id', 0);
        $locId     = (int)$request->query('location_id', 0);
        $direction = strtoupper(trim((string)$request->query('direction', '')));
        $refType   = trim((string)$request->query('ref_type', ''));
        $refIdRaw  = trim((string)$request->query('ref_id', ''));
        $dateFrom  = trim((string)$request->query('date_from', '')); // YYYY-MM-DD
        $dateTo    = trim((string)$request->query('date_to', ''));   // YYYY-MM-DD
        $minQtyRaw = trim((string)$request->query('min_qty', ''));
        $maxQtyRaw = trim((string)$request->query('max_qty', ''));
        $perPage   = (int)$request->query('per_page', 50);
        $perPage   = max(10, min(200, $perPage));

        $qb = StockLedger::query()
            ->select(['id','created_at','product_batch_id','location_id','direction','qty','ref_type','ref_id','line_id','meta'])
            ->when($batchId > 0, fn($qq) => $qq->where('product_batch_id', $batchId))
            ->when($locId > 0, fn($qq) => $qq->where('location_id', $locId))
            ->when(in_array($direction, ['IN','OUT'], true), fn($qq) => $qq->where('direction', $direction))
            ->when($refType !== '', fn($qq) => $qq->where('ref_type', 'like', "%{$refType}%"))
            ->when($refIdRaw !== '' && ctype_digit($refIdRaw), fn($qq) => $qq->where('ref_id', (int)$refIdRaw))
            ->when($dateFrom !== '', fn($qq) => $qq->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn($qq) => $qq->whereDate('created_at', '<=', $dateTo))
            ->when($minQtyRaw !== '' && is_numeric($minQtyRaw), fn($qq) => $qq->where('qty', '>=', (float)$minQtyRaw))
            ->when($maxQtyRaw !== '' && is_numeric($maxQtyRaw), fn($qq) => $qq->where('qty', '<=', (float)$maxQtyRaw))
            ->when($q !== '', function ($qq) use ($q) {
                // NOTE: meta JSON search is DB-specific.
                // We'll use LIKE on meta text which works in MySQL if meta is JSON/text.
                $qq->where(function ($w) use ($q) {
                    $w->where('ref_type', 'like', "%{$q}%")
                      ->orWhere('direction', 'like', "%{$q}%")
                      ->orWhere('product_batch_id', 'like', "%{$q}%")
                      ->orWhere('location_id', 'like', "%{$q}%")
                      ->orWhere('ref_id', 'like', "%{$q}%")
                      ->orWhere('line_id', 'like', "%{$q}%")
                      ->orWhere('meta', 'like', "%{$q}%");
                });
            })
            ->latest('id');

        $rows = $qb->paginate($perPage);

        $payload = $rows->getCollection()->map(function ($r) {
            $metaJson = json_encode($r->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
            return [
                'id' => (int)$r->id,
                'created_at' => (string)$r->created_at,
                'product_batch_id' => (int)$r->product_batch_id,
                'location_id' => (int)$r->location_id,
                'direction' => (string)$r->direction,
                'qty' => (float)$r->qty,
                'ref_type' => (string)($r->ref_type ?? ''),
                'ref_id' => $r->ref_id === null ? null : (int)$r->ref_id,
                'line_id' => $r->line_id === null ? null : (int)$r->line_id,
                'meta_json' => $metaJson,
            ];
        })->values();

        return response()->json([
            'rows' => $payload,
            'meta' => [
                'count_on_page' => $payload->count(),
                'total' => $rows->total(),
                'per_page' => $rows->perPage(),
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
            ],
            // Useful if you want Laravel pagination UI (optional)
            'pagination_html' => $rows->links()->render(),
        ]);
    }
}
