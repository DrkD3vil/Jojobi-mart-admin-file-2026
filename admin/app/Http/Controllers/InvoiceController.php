<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;


class InvoiceController extends Controller
{
    //



    public function show(Order $order)
    {
        $order->load([
            'customer:id,name,phone,due_balance,advance_balance,reward_points',
            'items:id,order_id,product_name,barcode,price_type,unit_price,quantity,discount_amount,total_price',
            'payments:id,order_id,channel,method,trx_id,account_label,amount,status,created_at',
        ]);

        return view('invoices.show', compact('order'));
    }
}
