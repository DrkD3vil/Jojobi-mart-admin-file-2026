<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\BatchStock;
use App\Models\Location;
use Illuminate\Http\Request;

class ProductGiftController extends Controller
{
    public function index(Request $request)
    {
        $locationId = $request->query('location_id', null);
        $searchTerm = $request->query('search', '');
        $productId = $request->query('product_id', null);
        $status = $request->query('status', null);
        $perPage = $request->query('per_page', 10);

        // Get all locations for filtering
        $locations = Location::orderBy('name')->get();

        // Get all active products with free offers for dropdown
        $allProducts = Product::whereHas('batches', function($query) {
            $query->whereNotNull('free_product_id')
                  ->where('is_free_offer_active', 1);
        })->orderBy('name')->get();

        // Main query for product gifts
        $query = ProductBatch::with(['product', 'freeProduct'])
            ->whereNotNull('free_product_id')
            ->where('is_free_offer_active', 1)
            ->where('free_qty', '>', 0);

        // Apply search filter
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('batch_sku', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('product', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('sku', 'like', '%' . $searchTerm . '%');
                  })
                  ->orWhereHas('freeProduct', function ($q) use ($searchTerm) {
                      $q->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('sku', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Apply product filter
        if ($productId) {
            $query->where('product_id', $productId);
        }

        // Apply location filter
        if ($locationId) {
            $query->whereHas('batchStocks', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }

        // Get paginated results
        $productBatches = $query->paginate($perPage);

        // Prepare data for view
        $productGiftDetails = [];
        $stats = [
            'total_offers' => 0,
            'total_free_qty' => 0,
            'active_locations' => 0,
            'avg_free_per_offer' => 0,
        ];

        foreach ($productBatches as $batch) {
            // Get stock for all locations or specific location
            $batchStocksQuery = BatchStock::where('product_batch_id', $batch->id);

            if ($locationId) {
                $batchStocksQuery->where('location_id', $locationId);
            }

            $batchStocks = $batchStocksQuery->with('location')->get();

            // Calculate available free quantity across filtered locations
            $availableFreeQty = $batchStocks->sum('available');

            // If we want to show specific location info
            if ($locationId) {
                $locationStock = $batchStocks->firstWhere('location_id', $locationId);
                $locationName = $locationStock ? $locationStock->location->name : 'Unknown';
                $stockAtLocation = $locationStock ? $locationStock->available : 0;
            } else {
                // Show aggregated data across all locations
                $locationName = 'Multiple Locations';
                $stockAtLocation = $availableFreeQty;
            }

            // Calculate status based on stock ratio
            $stockRatio = $availableFreeQty > 0 ? ($availableFreeQty / max($batch->free_qty, 1)) : 0;

            if ($stockRatio >= 0.7) {
                $statusClass = 'high';
                $statusText = 'High Stock';
            } elseif ($stockRatio >= 0.3) {
                $statusClass = 'medium';
                $statusText = 'Medium Stock';
            } else {
                $statusClass = 'low';
                $statusText = 'Low Stock';
            }

            // Apply status filter if set
            if ($status && $status !== $statusClass) {
                continue;
            }

            $productGiftDetails[] = [
                'product_id' => $batch->product_id,
                'product_name' => $batch->product->name,
                'product_sku' => $batch->product->sku,
                'batch_id' => $batch->id,
                'batch_sku' => $batch->batch_sku,
                'free_product_id' => $batch->free_product_id,
                'free_product_name' => $batch->freeProduct->name,
                'free_product_sku' => $batch->freeProduct->sku,
                'free_qty_per_offer' => $batch->free_qty,
                'location_name' => $locationName,
                'available_free_qty' => $availableFreeQty,
                'stock_at_location' => $stockAtLocation,
                'status_class' => $statusClass,
                'status_text' => $statusText,
                'stock_ratio' => $stockRatio,
                'start_date' => $batch->free_offer_start_date,
                'end_date' => $batch->free_offer_end_date,
                'is_active' => $batch->is_free_offer_active,
            ];

            // Update statistics
            $stats['total_offers']++;
            $stats['total_free_qty'] += $availableFreeQty;
        }

        // Calculate statistics
        $stats['active_locations'] = $locationId ? 1 : Location::count();
        $stats['avg_free_per_offer'] = $stats['total_offers'] > 0
            ? round($stats['total_free_qty'] / $stats['total_offers'], 1)
            : 0;

        return view('products.gift_audit', [
            'productGiftDetails' => $productGiftDetails,
            'locations' => $locations,
            'allProducts' => $allProducts,
            'productBatches' => $productBatches,
            'searchTerm' => $searchTerm,
            'selectedLocation' => $locationId,
            'selectedProduct' => $productId,
            'selectedStatus' => $status,
            'stats' => $stats,
            'perPage' => $perPage,
        ]);
    }

    public function export(Request $request)
    {
        // Similar to the index function but for exporting CSV
    }

    public function updateStatus(Request $request, $batchId)
    {
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $batch = ProductBatch::findOrFail($batchId);
        $batch->is_free_offer_active = $request->is_active;
        $batch->save();

        return response()->json([
            'success' => true,
            'message' => 'Offer status updated successfully',
            'is_active' => $batch->is_free_offer_active
        ]);
    }
}
