<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $q = Expense::query()
            ->with(['category:id,name', 'location:id,name', 'creator:id,name']);

        // Filters
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $q->where(function ($x) use ($kw) {
                $x->where('title', 'like', "%{$kw}%")
                  ->orWhere('vendor_name', 'like', "%{$kw}%")
                  ->orWhere('reference_no', 'like', "%{$kw}%")
                  ->orWhere('expense_no', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('category_id')) $q->where('expense_category_id', $request->category_id);
        if ($request->filled('location_id')) $q->where('location_id', $request->location_id);
        if ($request->filled('payment_method')) $q->where('payment_method', $request->payment_method);

        if ($request->filled('date_from')) $q->whereDate('expense_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $q->whereDate('expense_date', '<=', $request->date_to);

        if ($request->filled('min_amount')) $q->where('amount', '>=', $request->min_amount);
        if ($request->filled('max_amount')) $q->where('amount', '<=', $request->max_amount);

        // Sorting
        $sort = $request->get('sort', 'expense_date');
        $dir  = $request->get('dir', 'desc');
        if (!in_array($sort, ['expense_date','amount','created_at','expense_no'])) $sort = 'expense_date';
        if (!in_array($dir, ['asc','desc'])) $dir = 'desc';
        $q->orderBy($sort, $dir)->orderBy('id', 'desc');

        $expenses = $q->paginate(20)->appends($request->query());

        // Totals (for filtered result)
        $filteredTotal = (clone $q)->toBase()->sum('amount');

        // Chart data: monthly sum for current year (or filtered date range if provided)
        $from = $request->filled('date_from') ? Carbon::parse($request->date_from) : Carbon::now()->startOfYear();
        $to   = $request->filled('date_to') ? Carbon::parse($request->date_to) : Carbon::now()->endOfYear();

        $monthly = Expense::query()
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as ym, SUM(amount) as total")
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total','ym');

        $chartLabels = $monthly->keys()->values();
        $chartValues = $monthly->values();

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get(['id','name']);
        $locations = Location::where('is_active', true)->orderBy('name')->get(['id','name']);

        $paymentMethods = Expense::query()
            ->whereNotNull('payment_method')
            ->distinct()
            ->orderBy('payment_method')
            ->pluck('payment_method');

        return view('expenses.index', compact(
            'expenses','filteredTotal','categories','locations','paymentMethods',
            'chartLabels','chartValues'
        ));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get(['id','name']);
        $locations = Location::where('is_active', true)->orderBy('name')->get(['id','name']);
        return view('expenses.create', compact('categories','locations'));
    }

    public function store(ExpenseStoreRequest $request)
    {
        $data = $request->validated();

        $data['expense_no'] = $this->generateExpenseNo();
        $data['created_by'] = auth()->id();

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request->file('receipt_image')->store('expenses/receipts', 'public');
        }

        Expense::create($data);

        return redirect()->route('expenses.index')->with('success', 'Expense created.');
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get(['id','name']);
        $locations = Location::where('is_active', true)->orderBy('name')->get(['id','name']);
        return view('expenses.edit', compact('expense','categories','locations'));
    }

    public function update(ExpenseUpdateRequest $request, Expense $expense)
    {
        $data = $request->validated();

        if (($data['remove_receipt'] ?? false) && $expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
            $data['receipt_image'] = null;
        }

        if ($request->hasFile('receipt_image')) {
            if ($expense->receipt_image) Storage::disk('public')->delete($expense->receipt_image);
            $data['receipt_image'] = $request->file('receipt_image')->store('expenses/receipts', 'public');
        }

        unset($data['remove_receipt']);
        $expense->update($data);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense moved to trash.');
    }

    // Trash
    public function trash()
    {
        $expenses = Expense::onlyTrashed()->orderBy('deleted_at','desc')->paginate(20);
        return view('expenses.trash', compact('expenses'));
    }

    public function restore($id)
    {
        $e = Expense::onlyTrashed()->findOrFail($id);
        $e->restore();
        return back()->with('success', 'Expense restored.');
    }

    public function forceDelete($id)
    {
        $e = Expense::onlyTrashed()->findOrFail($id);
        if ($e->receipt_image) Storage::disk('public')->delete($e->receipt_image);
        $e->forceDelete();
        return back()->with('success', 'Expense permanently deleted.');
    }

    // Add this new method
public function emptyTrash()
{
    $trashedExpenses = Expense::onlyTrashed()->get();
    
    foreach ($trashedExpenses as $expense) {
        // Delete receipt images if they exist
        if ($expense->receipt_image) {
            Storage::disk('public')->delete($expense->receipt_image);
        }
        // Permanently delete the expense
        $expense->forceDelete();
    }
    
    $count = $trashedExpenses->count();
    return back()->with('success', "Successfully emptied trash. {$count} expense(s) permanently deleted.");
}

    // Optional: Export CSV (uses current filters)
    public function exportCsv(Request $request)
    {
        $q = Expense::query();

        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $q->where(function ($x) use ($kw) {
                $x->where('title', 'like', "%{$kw}%")
                  ->orWhere('vendor_name', 'like', "%{$kw}%")
                  ->orWhere('reference_no', 'like', "%{$kw}%")
                  ->orWhere('expense_no', 'like', "%{$kw}%");
            });
        }
        if ($request->filled('category_id')) $q->where('expense_category_id', $request->category_id);
        if ($request->filled('location_id')) $q->where('location_id', $request->location_id);
        if ($request->filled('payment_method')) $q->where('payment_method', $request->payment_method);
        if ($request->filled('date_from')) $q->whereDate('expense_date', '>=', $request->date_from);
        if ($request->filled('date_to')) $q->whereDate('expense_date', '<=', $request->date_to);

        $rows = $q->with(['category:id,name','location:id,name'])
            ->orderBy('expense_date','desc')
            ->get();

        $filename = 'expenses_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Expense No','Date','Title','Category','Location','Vendor','Payment','Reference','Amount','Currency']);
            foreach ($rows as $e) {
                fputcsv($out, [
                    $e->expense_no,
                    $e->expense_date?->format('Y-m-d'),
                    $e->title,
                    $e->category?->name,
                    $e->location?->name,
                    $e->vendor_name,
                    $e->payment_method,
                    $e->reference_no,
                    $e->amount,
                    $e->currency,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateExpenseNo(): string
    {
        $prefix = 'EXP-' . now()->format('Ymd') . '-';
        $last = Expense::where('expense_no', 'like', $prefix.'%')
            ->orderBy('id','desc')
            ->value('expense_no');

        $next = 1;
        if ($last) {
            $parts = explode('-', $last);
            $seq = (int) end($parts);
            $next = $seq + 1;
        }
        return $prefix . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }
}
