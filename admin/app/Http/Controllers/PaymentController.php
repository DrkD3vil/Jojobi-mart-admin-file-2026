<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
class PaymentController extends Controller
{
    //



    private const METHODS = [
        'offline' => ['cash', 'card', 'bank'],
        'online'  => ['bkash', 'nagad', 'rocket'],
    ];

    public function create(Order $order)
    {
        $order->load('payments');

        $methods = self::METHODS;

        return view('payments.create', compact('order', 'methods'));
    }

    public function index(Order $order)
    {
        $order->load('payments');

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'order_no' => $order->order_no,
                'payable_total' => (float) $order->payable_total,
                'paid_total' => (float) ($order->paid_total ?? 0),
                'due_total' => (float) ($order->due_total ?? 0),
                'change_total' => (float) ($order->change_total ?? 0),
                'payment_status' => $order->payment_status ?? 'unpaid',
            ],
            'payments' => $order->payments->map(fn($p) => [
                'id' => $p->id,
                'channel' => $p->channel,
                'method' => $p->method,
                'trx_id' => $p->trx_id,
                'account_label' => $p->account_label,
                'amount' => (float) $p->amount,
                'status' => $p->status,
                'created_at' => $p->created_at?->toDateTimeString(),
            ])->values(),
        ]);
    }

    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'payment_note' => 'nullable|string|max:2000',

            'payments' => 'required|array|min:1',
            'payments.*.channel' => 'required|string|in:offline,online',
            'payments.*.method'  => 'required|string',
            'payments.*.amount'  => 'required|numeric|min:0.0001',

            'payments.*.trx_id' => 'nullable|string|max:80',
            'payments.*.account_label' => 'nullable|string|max:120',
        ]);

        return DB::transaction(function () use ($order, $data) {

            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            // Only orders that are still open and awaiting payment can take one.
            // 'void' was never a real order status (the status column's enum
            // never included it), so this check previously never fired.
            if (!in_array($order->status, ['pending', 'processing'])) {
                return response()->json(['success' => false, 'message' => 'Order is not in a payable state (status: ' . $order->status . ').'], 422);
            }
            if ((float)$order->payable_total <= 0) {
                return response()->json(['success' => false, 'message' => 'Invalid payable total'], 422);
            }

            $alreadyPaid = (float) Payment::where('order_id', $order->id)
                ->where('status', 'captured')
                ->sum('amount');
            $payable = (float) $order->payable_total;

            // Guard against a double-click/duplicate submit: once the order is
            // already fully settled, don't silently accept more money against it.
            if ($alreadyPaid >= $payable) {
                return response()->json(['success' => false, 'message' => 'Order is already fully paid.'], 422);
            }

            $remaining = $payable - $alreadyPaid;
            $requestedTotal = 0.0;
            $hasNonCash = false;

            // validate methods + online trx_id
            foreach ($data['payments'] as $p) {
                $channel = $p['channel'];
                $method  = $p['method'];

                $allowed = self::METHODS[$channel] ?? [];
                if (!in_array($method, $allowed, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid method '{$method}' for '{$channel}'"
                    ], 422);
                }

                if ($channel === 'online' && empty($p['trx_id'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Trx ID required for online payment ({$method})"
                    ], 422);
                }

                $requestedTotal += (float) $p['amount'];
                if ($method !== 'cash') {
                    $hasNonCash = true;
                }
            }

            // Only cash is allowed to overpay (real-world "give change" case);
            // any other method overshooting the remaining balance is rejected.
            if ($hasNonCash && $requestedTotal > $remaining + 0.0001) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount exceeds the remaining balance due (' . number_format($remaining, 2) . '). Only cash payments may include change.'
                ], 422);
            }

            // save payments
            foreach ($data['payments'] as $p) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'channel' => $p['channel'],
                    'method' => $p['method'],
                    'trx_id' => $p['trx_id'] ?? null,
                    'account_label' => $p['account_label'] ?? null,
                    'amount' => (float)$p['amount'],
                    'status' => 'captured',
                    'meta' => null,
                ]);

                $payment->recordTimeline(
                    'captured',
                    'Payment Captured',
                    currency_bdt($payment->amount) . " via {$payment->method} for order #{$order->order_no}.",
                    null,
                    'captured',
                    'credit-card'
                );
            }

            // recalc totals from DB
            $paid = (float) Payment::where('order_id', $order->id)
                ->where('status', 'captured')
                ->sum('amount');

            $payable = (float) $order->payable_total;

            $change = 0.0;
            $due = 0.0;
            $paymentStatus = 'unpaid';

            if ($paid <= 0) {
                $due = $payable;
                $paymentStatus = 'unpaid';
            } elseif ($paid < $payable) {
                $due = $payable - $paid;
                $paymentStatus = 'partial';
            } else {
                $change = $paid - $payable;
                $due = 0;
                $paymentStatus = 'paid';
            }

            $order->paid_total = $paid;
            $order->due_total = $due;
            $order->change_total = $change;
            $order->payment_status = $paymentStatus;
            $order->payment_note = $data['payment_note'] ?? null;
            if ($paymentStatus === 'paid') {
                $order->status = 'completed';
            }
            $order->save();

            $order->recordTimeline(
                'payment_received',
                'Payment Received',
                currency_bdt($requestedTotal) . " received — order is now {$paymentStatus}.",
                null,
                $paymentStatus,
                'credit-card'
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment saved',
                'order' => [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'payable_total' => (float) $order->payable_total,
                    'paid_total' => (float) $order->paid_total,
                    'due_total' => (float) $order->due_total,
                    'change_total' => (float) $order->change_total,
                    'payment_status' => $order->payment_status,
                ],
            ]);
        });
    }
}
