<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerBalance;
use App\Models\CustomerRewardLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    //

    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        $customers = Customer::query()
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:190',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:190',
            'type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'type' => $data['type'] ?? 'regular',
            'is_active' => $data['is_active'] ?? true,
            'address' => $data['address'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json(['success' => true, 'customer' => $customer]);
    }

    public function show(Customer $customer)
    {
        $customer->load([
            'balanceLedgers' => fn($q) => $q->latest()->limit(50),
            'rewardLedgers' => fn($q) => $q->latest()->limit(50),
        ]);

        return response()->json(['success' => true, 'customer' => $customer]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:190',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:190',
            'type' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer->update($data);

        return response()->json(['success' => true, 'customer' => $customer->fresh()]);
    }

    /**
     * ✅ Add Due or Advance (ledger) – supports online/offline POS
     * kind: due|advance
     * direction: debit|credit
     *
     * Examples:
     * - Customer buys on credit => kind=due, direction=debit, amount=100  (due_balance +100)
     * - Customer pays due => kind=due, direction=credit, amount=50       (due_balance -50)
     * - Customer prepays => kind=advance, direction=debit, amount=200    (advance_balance +200)
     * - Use advance => kind=advance, direction=credit, amount=40         (advance_balance -40)
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

            // idempotency safe
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

            CustomerBalance::create([
                'customer_id' => $customer->id,
                'kind' => $data['kind'],
                'direction' => $data['direction'],
                'amount' => $amount,
                'ref_type' => $data['ref_type'] ?? null,
                'ref_id' => $data['ref_id'] ?? null,
                'channel' => $data['channel'] ?? 'pos',
                'terminal_id' => $data['terminal_id'] ?? null,
                'created_by' => $data['created_by'] ?? null,
                'idempotency_key' => $data['idempotency_key'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            // update fast balances
            $customer->refresh(); // ensure we have latest row lock

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
                'customer' => $customer->fresh(),
            ]);
        });
    }

    /**
     * ✅ Rewards (ledger)
     * action: earn|redeem|adjust
     * direction: add|subtract
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

            // prevent negative points
            if ($data['direction'] === 'subtract' && (float)$customer->reward_points < $points) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough reward points.',
                ], 422);
            }

            CustomerRewardLedger::create([
                'customer_id' => $customer->id,
                'action' => $data['action'],
                'direction' => $data['direction'],
                'points' => $points,

                'ref_type' => $data['ref_type'] ?? null,
                'ref_id' => $data['ref_id'] ?? null,

                'channel' => $data['channel'] ?? 'pos',
                'terminal_id' => $data['terminal_id'] ?? null,
                'created_by' => $data['created_by'] ?? null,

                'idempotency_key' => $data['idempotency_key'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            $delta = ($data['direction'] === 'add') ? $points : -$points;
            $customer->reward_points = max(0, (float)$customer->reward_points + $delta);
            $customer->save();

            return response()->json([
                'success' => true,
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
        if (mb_strlen($q) < 2) return response()->json([]);

        $rows = Customer::query()
            ->select(['id','uuid','name','phone','email','type','due_balance','advance_balance','reward_points'])
            ->where('is_active', true)
            ->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(15)
            ->get();

        return response()->json($rows);
    }


    // App\Http\Controllers\CustomerController.php


}
