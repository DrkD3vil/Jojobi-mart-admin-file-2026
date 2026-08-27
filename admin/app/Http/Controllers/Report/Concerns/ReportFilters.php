<?php

namespace App\Http\Controllers\Report\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Shared date-range resolution and order/refund/COGS query building blocks
 * for the financial report controllers. Centralizing these keeps the
 * "Today" dashboard, the "Analysis" dashboard, and the Reports & Analytics
 * section computing profit the same way instead of drifting apart.
 */
trait ReportFilters
{
    private function resolveDateRange(string $range, ?string $startDate, ?string $endDate): array
    {
        $now = Carbon::now();

        return match ($range) {
            'today'      => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'yesterday'  => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()],
            'this_week'  => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'last_week'  => [$now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()],
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_year'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'last_year'  => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            'custom'     => [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    /**
     * The period immediately preceding the given range, same length, for
     * period-over-period comparison (e.g. "this_month" -> previous month).
     */
    private function precedingRange(Carbon $start, Carbon $end): array
    {
        $lengthDays = $start->diffInDays($end) + 1;

        return [
            $start->copy()->subDays($lengthDays)->startOfDay(),
            $start->copy()->subDay()->endOfDay(),
        ];
    }

    /**
     * Split-child (sub-order) rows never carry their own financial identity
     * -- their totals already belong to the parent order. Every report must
     * exclude them, unconditionally (no filter re-includes them).
     */
    private function excludeSplitChildren($q, string $alias = 'o')
    {
        return $q->where(function ($qq) use ($alias) {
            $qq->where("{$alias}.is_split_child", false)
                ->orWhereNull("{$alias}.is_split_child");
        });
    }

    /**
     * Cancelled/void orders never happened financially and are excluded by
     * default. Only call this when the caller hasn't let the user explicitly
     * ask to see one of these statuses (e.g. a "Cancelled" filter option).
     */
    private function excludeInvalidStatuses($q, string $alias = 'o')
    {
        return $q->whereNotIn("{$alias}.status", ['cancelled', 'canceled', 'void']);
    }

    /**
     * Line-level refund base query: returns joined to return_items (the
     * granular source of truth) with product_batches left-joined so COGS
     * reversal never silently drops a row over a missing/deleted batch.
     * Callers add their own date/location scoping on top.
     */
    private function baseReturnsQuery()
    {
        return DB::table('returns as r')
            ->join('return_items as ri', 'ri.return_id', '=', 'r.id')
            ->leftJoin('product_batches as pb', 'ri.product_batch_id', '=', 'pb.id');
    }

    /**
     * Order-item COGS base query. Always left-joins product_batches so an
     * item with a missing/deleted batch counts its cost as 0 instead of
     * disappearing from COGS entirely (which would inflate profit).
     */
    private function baseCogsQuery()
    {
        return DB::table('order_items as oi')
            ->join('orders as o', 'oi.order_id', '=', 'o.id')
            ->leftJoin('product_batches as pb', 'oi.product_batch_id', '=', 'pb.id');
    }
}
