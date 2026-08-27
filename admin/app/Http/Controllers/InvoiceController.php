<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;


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

    /**
     * Standalone, print-ready receipt (no app chrome -- this view has its
     * own <html> document, not the admin layout). Embeds a barcode of the
     * order number and a QR code linking to the public, signed order
     * status page so a customer can scan the paper receipt to track it.
     */
    public function print(Order $order)
    {
        $order->load([
            'customer:id,name,phone,due_balance,advance_balance',
            'items:id,order_id,product_name,barcode,price_type,unit_price,quantity,discount_amount,total_price',
            'payments:id,order_id,channel,method,trx_id,account_label,amount,status,created_at',
            'location:id,name,address',
        ]);

        $trackingUrl = URL::signedRoute('public.order.show', ['order' => $order->id]);

        $barcodeSvg = \DNS1D::getBarcodeSVG($order->order_no, 'C128', 2, 60);
        $qrSvg = \DNS2D::getBarcodeSVG($trackingUrl, 'QRCODE', 4, 4);

        $cashierName = $order->timeline()->where('event', 'created')->first()?->causer?->name;

        return view('invoices.print', [
            'order' => $order,
            'cashierName' => $cashierName,
            'logoImage' => $this->logoDataUri(),
            'barcodeImage' => 'data:image/svg+xml;base64,' . base64_encode($barcodeSvg),
            'qrImage' => 'data:image/svg+xml;base64,' . base64_encode($qrSvg),
        ]);
    }

    /**
     * Reuses the same logo the login page shows (config('tyro-login.branding.logo'),
     * a remote URL by default), fetched once and cached as a data URI so the
     * printed receipt never depends on an external request at print time.
     */
    private function logoDataUri(): ?string
    {
        $logoUrl = config('tyro-login.branding.logo');

        if (!$logoUrl) {
            return null;
        }

        return Cache::remember('invoice_logo_data_uri:' . md5($logoUrl), now()->addDay(), function () use ($logoUrl) {
            try {
                $response = Http::timeout(5)->get($logoUrl);

                if (!$response->successful()) {
                    return null;
                }

                $mime = $response->header('Content-Type') ?: 'image/png';

                return 'data:' . $mime . ';base64,' . base64_encode($response->body());
            } catch (\Throwable $e) {
                return null;
            }
        });
    }
}
