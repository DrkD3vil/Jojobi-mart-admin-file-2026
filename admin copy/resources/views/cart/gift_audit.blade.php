@extends('layouts.app')

@section('content')
<div class="page" style="max-width:1200px;margin:0 auto;padding:18px;">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:14px;">
        <div>
            <div class="subtle">Gift Analyzer</div>
            <h2 style="margin:0;">Cart Free Offer Report</h2>
            <div class="subtle" style="margin-top:4px;">
                Main product → free product calculation + location stock visibility
            </div>
        </div>

        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;justify-content:flex-end;">
            <form method="GET" action="{{ route('cart.gifts.audit') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <input class="inputx" name="cart_id" placeholder="Cart ID (optional)" style="width:160px;height:42px;"
                       value="{{ request('cart_id') }}">
                <input class="inputx" name="location_id" placeholder="Location ID" style="width:140px;height:42px;"
                       value="{{ request('location_id', $locationId) }}">
                <button class="btnx" type="submit">View</button>
                <a class="btnx btnx-ghost" href="{{ route('cart.index') }}">Back to Cart</a>
            </form>

            @if($cart)
                <form method="POST" action="{{ route('cart.gifts.audit.sync') }}">
                    @csrf
                    <input type="hidden" name="cart_id" value="{{ $cart->id }}">
                    <input type="hidden" name="location_id" value="{{ $locationId }}">
                    <button class="btnx btnx-ghost" type="submit">↻ Sync Gifts</button>
                </form>
            @endif
        </div>
    </div>

    @if(session('ok'))
        <div class="cardx" style="padding:10px 12px;margin-bottom:12px;border:1px solid var(--border);">
            ✅ {{ session('ok') }}
        </div>
    @endif

    @if(!$cart)
        <div class="cardx" style="padding:18px;">
            <div class="strong">No cart found</div>
            <div class="subtle" style="margin-top:6px;">Pass a cart_id in the URL or open cart first.</div>
        </div>
        @return
    @endif

    <div class="cardx" style="margin-bottom:12px;">
        <div class="cardx-hd">
            <div>
                <div class="strong">Cart #{{ $cart->id }}</div>
                <div class="subtle">
                    Location #{{ $locationId }} •
                    Customer: {{ $cart->customer?->name ?? 'Guest' }} {{ $cart->customer?->phone ? '('.$cart->customer->phone.')' : '' }}
                </div>
            </div>
            <div class="subtle">
                Items: {{ $cart->items->count() }} • Total: {{ number_format((float)$cart->total,2) }}
            </div>
        </div>
    </div>

    <div class="cardx">
        <div class="cardx-hd">
            <div class="strong">Offer Rows</div>
            <div class="subtle">Click “Details” to expand batch-level free stock & offer math.</div>
        </div>

        <div class="table-wrap">
            <table class="tablex">
                <thead>
                    <tr>
                        <th style="width:54px;">#</th>
                        <th>Main Product</th>
                        <th style="width:130px;">Main Qty</th>
                        <th>Free Product</th>
                        <th style="width:140px;">Gift Qty (Should)</th>
                        <th style="width:140px;">Gift Qty (Added)</th>
                        <th style="width:160px;">Total Free Available</th>
                        <th style="width:90px;"></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $idx => $r)
                    @php
                        $active = (bool)$r['offer']['active'];
                        $should = (float)$r['should_gift_qty'];
                        $added  = (float)$r['added_gift_qty'];
                        $avail  = (float)$r['free_stock']['total_available'];

                        $status = '—';
                        $pill = 'pill';

                        if (!$active) { $status='No offer'; $pill='pill warning'; }
                        else if ($should <= 0) { $status='Not eligible'; $pill='pill warning'; }
                        else if ($added <= 0) { $status='Missing gift'; $pill='pill danger'; }
                        else if (abs($added - $should) < 0.0001) { $status='OK'; $pill='pill success'; }
                        else { $status='Partial/Adjusted'; $pill='pill warning'; }

                        $rowId = 'row_' . $r['parent_item_id'];
                    @endphp

                    <tr>
                        <td>{{ $idx+1 }}</td>

                        <td>
                            <div class="strong">{{ $r['main_product']['name'] }}</div>
                            <div class="subtle">
                                {{ $r['main_product']['barcode'] ?? '—' }} • Batch: {{ $r['main_product']['batch_sku'] ?? '—' }}
                            </div>
                            <div style="margin-top:6px;"><span class="{{ $pill }}">{{ $status }}</span></div>
                        </td>

                        <td>
                            <div class="strong">{{ number_format((float)$r['main_qty_sale'], 4) }} {{ $r['main_unit_sale'] }}</div>
                            <div class="subtle">Batch qty: {{ number_format((float)$r['main_qty_batch'], 4) }} {{ $r['main_product']['batch_unit'] }}</div>
                        </td>

                        <td>
                            <div class="strong">{{ $r['free_product_name'] ?? ('#'.$r['free_product_id']) }}</div>
                            <div class="subtle">ID: {{ $r['free_product_id'] ?? '—' }}</div>
                        </td>

                        <td class="money">
                            <div class="strong">{{ number_format($should, 4) }}</div>
                            <div class="subtle">
                                Buy {{ number_format((float)$r['offer']['buy_qty'],4) }} → Free {{ number_format((float)$r['offer']['free_qty'],4) }}
                                • x{{ (int)$r['offer']['times'] }}
                            </div>
                        </td>

                        <td class="money">
                            <div class="strong">{{ number_format($added, 4) }}</div>
                            <div class="subtle">Auto gift rows sum</div>
                        </td>

                        <td class="money">
                            <div class="strong">{{ number_format($avail, 4) }}</div>
                            <div class="subtle">
                                On hand {{ number_format((float)$r['free_stock']['total_on_hand'],4) }}
                                • Reserved {{ number_format((float)$r['free_stock']['total_reserved'],4) }}
                            </div>
                        </td>

                        <td style="text-align:right;">
                            <button class="btnx btnx-ghost" type="button" data-toggle="{{ $rowId }}">Details</button>
                        </td>
                    </tr>

                    <tr id="{{ $rowId }}" style="display:none;background:rgba(0,0,0,.02);">
                        <td colspan="8" style="padding:12px 14px;">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                <div class="cardx" style="padding:12px;">
                                    <div class="strong">Offer Math</div>
                                    <div class="subtle" style="margin-top:8px;line-height:1.7;">
                                        Main batch unit qty:
                                        <b>{{ number_format((float)$r['main_qty_batch'],4) }}</b>
                                        / Buy qty:
                                        <b>{{ number_format((float)$r['offer']['buy_qty'],4) }}</b>
                                        = Times:
                                        <b>{{ (int)$r['offer']['times'] }}</b>
                                        <br>
                                        Should gift = Times × Free qty =
                                        <b>{{ number_format((float)$r['should_gift_qty'],4) }}</b>
                                        <br>
                                        Added gift = <b>{{ number_format((float)$r['added_gift_qty'],4) }}</b>
                                    </div>
                                </div>

                                <div class="cardx" style="padding:12px;">
                                    <div class="strong">Free Product Stock (Location #{{ $locationId }})</div>
                                    <div class="subtle" style="margin-top:8px;">FIFO by expiry • shows each batch</div>

                                    <div class="table-wrap" style="margin-top:10px;">
                                        <table class="tablex">
                                            <thead>
                                                <tr>
                                                    <th style="width:120px;">Batch</th>
                                                    <th style="width:120px;">Expiry</th>
                                                    <th style="width:90px;">Unit</th>
                                                    <th class="money">On hand</th>
                                                    <th class="money">Reserved</th>
                                                    <th class="money">Available</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($r['free_stock']['batches'] as $b)
                                                <tr>
                                                    <td>{{ $b['batch_sku'] }}</td>
                                                    <td>{{ $b['expiry_date'] ? \Carbon\Carbon::parse($b['expiry_date'])->format('Y-m-d') : '—' }}</td>
                                                    <td>{{ $b['unit'] }}</td>
                                                    <td class="money">{{ number_format((float)$b['on_hand'],4) }}</td>
                                                    <td class="money">{{ number_format((float)$b['reserved'],4) }}</td>
                                                    <td class="money">{{ number_format((float)$b['available'],4) }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="6" class="subtle">No active free batches found</td></tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">No paid items found in this cart.</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-toggle]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.getAttribute('data-toggle');
                    const row = document.getElementById(id);
                    if (!row) return;
                    const open = row.style.display !== 'none';
                    row.style.display = open ? 'none' : 'table-row';
                    btn.textContent = open ? 'Details' : 'Hide';
                });
            });
        });
    </script>
</div>
@endsection
