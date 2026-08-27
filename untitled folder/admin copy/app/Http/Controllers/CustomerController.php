<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerBalance;
use App\Models\CustomerRewardLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with filters
     */
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search filter
        if ($q = $request->get('q')) {
            $query->where(function($qry) use ($q) {
                $qry->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('phone', 'LIKE', "%{$q}%")
                    ->orWhere('email', 'LIKE', "%{$q}%")
                    ->orWhere('uuid', 'LIKE', "%{$q}%");
            });
        }

        // Type filter
        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('is_active', $status === 'active');
        }

        // Sort
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');

        // Allow only specific fields for sorting
        $allowedSortFields = ['id', 'name', 'type', 'phone', 'due_balance', 'advance_balance', 'reward_points', 'is_active'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $customers = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('is_active', true)->count(),
            'inactive' => Customer::where('is_active', false)->count(),
            'total_due' => Customer::sum('due_balance') ?? 0,
            'total_advance' => Customer::sum('advance_balance') ?? 0,
            'total_rewards' => Customer::sum('reward_points') ?? 0,
        ];

        // Get customer types for filter
        $types = Customer::whereNotNull('type')->distinct()->pluck('type')->filter()->values();

        return view('customers.index', compact('customers', 'stats', 'types'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'phone' => 'nullable|string|max:30|unique:customers,phone',
            'email' => 'nullable|email|max:190|unique:customers,email',
            'type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $customer = Customer::create([
                'name' => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'type' => $data['type'] ?? 'regular',
                'is_active' => $data['is_active'] ?? true,
                'address' => $data['address'] ?? null,
                'notes' => $data['notes'] ?? null,
                'due_balance' => 0,
                'advance_balance' => 0,
                'reward_points' => 0,
            ]);

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer created successfully!',
                    'customer' => $customer
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create customer', ['error' => $e->getMessage()]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create customer: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to create customer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified customer
     */
    public function show(Customer $customer)
    {
        $customer->load([
            'orders' => function($q) {
                $q->latest()->limit(10);
            },
            'balanceLedgers' => function($q) {
                $q->latest()->limit(50);
            },
            'rewardLedgers' => function($q) {
                $q->latest()->limit(50);
            },
        ]);

        // Get order statistics
        $orderStats = [
            'total' => $customer->orders()->count(),
            'total_amount' => $customer->orders()->sum('payable_total') ?? 0,
            'pending' => $customer->orders()->where('status', 'pending')->count(),
            'completed' => $customer->orders()->where('status', 'completed')->count(),
            'cancelled' => $customer->orders()->where('status', 'cancelled')->count(),
        ];

        return view('customers.show', compact('customer', 'orderStats'));
    }

    /**
     * Show the form for editing the specified customer
     */
    public function edit(Customer $customer)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'customer' => $customer
            ]);
        }

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'phone' => 'nullable|string|max:30|unique:customers,phone,' . $customer->id,
            'email' => 'nullable|email|max:190|unique:customers,email,' . $customer->id,
            'type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            $customer->update($data);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer updated successfully!',
                    'customer' => $customer->fresh()
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to update customer', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update customer: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to update customer: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        try {
            // Check if customer has orders
            if ($customer->orders()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete customer with existing orders.'
                ], 422);
            }

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete customer', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete customer: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Post balance transaction
     */
    public function postBalance(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'kind' => 'required|in:due,advance',
            'direction' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0.01',
            'ref_type' => 'nullable|string|max:50',
            'ref_id' => 'nullable|integer',
            'channel' => 'nullable|in:pos,online,offline',
            'terminal_id' => 'nullable|string|max:100',
            'created_by' => 'nullable|string|max:100',
            'idempotency_key' => 'nullable|string|max:120',
            'note' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($customer, $data) {
            // Idempotency check
            if (!empty($data['idempotency_key'])) {
                $exists = CustomerBalance::where('customer_id', $customer->id)
                    ->where('idempotency_key', $data['idempotency_key'])
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Already processed (idempotent).',
                        'customer' => $customer->fresh(),
                    ]);
                }
            }

            $amount = (float)$data['amount'];

            // Create balance ledger
            CustomerBalance::create([
                'customer_id' => $customer->id,
                'kind' => $data['kind'],
                'direction' => $data['direction'],
                'amount' => $amount,
                'ref_type' => $data['ref_type'] ?? null,
                'ref_id' => $data['ref_id'] ?? null,
                'channel' => $data['channel'] ?? 'pos',
                'terminal_id' => $data['terminal_id'] ?? null,
                'created_by' => $data['created_by'] ?? Auth::id(),
                'idempotency_key' => $data['idempotency_key'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            // Update balance
            $customer->refresh();
            $delta = ($data['direction'] === 'debit') ? $amount : -$amount;

            if ($data['kind'] === 'due') {
                $newDue = max(0, (float)$customer->due_balance + $delta);
                $customer->due_balance = $newDue;
            } else {
                $newAdv = max(0, (float)$customer->advance_balance + $delta);
                $customer->advance_balance = $newAdv;
            }

            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Balance updated successfully!',
                'customer' => $customer->fresh(),
            ]);
        });
    }

    /**
     * Post rewards transaction
     */
    public function postRewards(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'action' => 'required|in:earn,redeem,adjust',
            'direction' => 'required|in:add,subtract',
            'points' => 'required|numeric|min:0.01',
            'ref_type' => 'nullable|string|max:50',
            'ref_id' => 'nullable|integer',
            'channel' => 'nullable|in:pos,online,offline',
            'terminal_id' => 'nullable|string|max:100',
            'created_by' => 'nullable|string|max:100',
            'idempotency_key' => 'nullable|string|max:120',
            'note' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($customer, $data) {
            // Idempotency check
            if (!empty($data['idempotency_key'])) {
                $exists = CustomerRewardLedger::where('customer_id', $customer->id)
                    ->where('idempotency_key', $data['idempotency_key'])
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Already processed (idempotent).',
                        'customer' => $customer->fresh(),
                    ]);
                }
            }

            $points = (float)$data['points'];

            // Prevent negative points
            if ($data['direction'] === 'subtract' && (float)$customer->reward_points < $points) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough reward points.',
                ], 422);
            }

            // Create reward ledger
            CustomerRewardLedger::create([
                'customer_id' => $customer->id,
                'action' => $data['action'],
                'direction' => $data['direction'],
                'points' => $points,
                'ref_type' => $data['ref_type'] ?? null,
                'ref_id' => $data['ref_id'] ?? null,
                'channel' => $data['channel'] ?? 'pos',
                'terminal_id' => $data['terminal_id'] ?? null,
                'created_by' => $data['created_by'] ?? Auth::id(),
                'idempotency_key' => $data['idempotency_key'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            // Update reward points
            $delta = ($data['direction'] === 'add') ? $points : -$points;
            $customer->reward_points = max(0, (float)$customer->reward_points + $delta);
            $customer->save();

            return response()->json([
                'success' => true,
                'message' => 'Rewards updated successfully!',
                'customer' => $customer->fresh(),
            ]);
        });
    }

    /**
     * Quick search for POS dropdown
     */
    public function quickSearch(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        if (mb_strlen($q) < 1) {
            return response()->json([]);
        }

        $customers = Customer::query()
            ->select(['id', 'uuid', 'name', 'phone', 'email', 'type',
                     'due_balance', 'advance_balance', 'reward_points'])
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('phone', 'LIKE', "%{$q}%")
                    ->orWhere('email', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json($customers);
    }

    /**
     * Get customer statistics for dashboard
     */
    public function getStats()
    {
        $stats = [
            'total' => Customer::count(),
            'active' => Customer::where('is_active', true)->count(),
            'inactive' => Customer::where('is_active', false)->count(),
            'total_due' => Customer::sum('due_balance') ?? 0,
            'total_advance' => Customer::sum('advance_balance') ?? 0,
            'total_rewards' => Customer::sum('reward_points') ?? 0,
            'new_today' => Customer::whereDate('created_at', today())->count(),
            'new_this_week' => Customer::whereBetween('created_at', [now()->startOfWeek(), now()])->count(),
            'new_this_month' => Customer::whereMonth('created_at', now()->month)->count(),
        ];

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($stats);
        }

        return $stats;
    }

    /**
     * Export customers to CSV
     */
    public function export()
    {
        $customers = Customer::all();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, ['ID', 'Name', 'Phone', 'Email', 'Type', 'Status', 'Due Balance', 'Advance Balance', 'Reward Points', 'Created At']);

            // Add data
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->id,
                    $customer->name,
                    $customer->phone,
                    $customer->email,
                    $customer->type ?? 'regular',
                    $customer->is_active ? 'Active' : 'Inactive',
                    $customer->due_balance ?? 0,
                    $customer->advance_balance ?? 0,
                    $customer->reward_points ?? 0,
                    $customer->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
