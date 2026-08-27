@extends('layouts.app')

@section('content')
<div class="container py-3">

<style>
    .page {
        color: var(--foreground, oklch(0.985 0 0));
    }

    .cardx {
        background: var(--card, oklch(0.205 0 0));
        color: var(--card-foreground, oklch(0.985 0 0));
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: var(--radius, 0.625rem);
        box-shadow: var(--card-shadow, 0 2px 4px 0 rgb(0 0 0 / 0.25));
        overflow: hidden;
        transition: box-shadow var(--transition-normal, 250ms) ease,
                    transform var(--transition-normal, 250ms) ease;
    }

    .cardx:hover {
        box-shadow: var(--card-shadow-hover, 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25));
    }

    .cardx-hd {
        padding: 12px 14px;
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        background: var(--accent, oklch(0.269 0 0));
    }

    .title {
        font-size: 18px;
        font-weight: 950;
        margin: 0;
        letter-spacing: .2px;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .subtle {
        font-size: 12px;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    }

    .strong {
        font-weight: 900;
    }

    .inputx {
        width: 100%;
        background: var(--input, oklch(1 0 0 / 15%));
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        color: var(--foreground, oklch(0.985 0 0));
        border-radius: calc(var(--radius, 0.625rem) - 6px);
        padding: 10px 12px;
        outline: none;
        transition: box-shadow var(--transition-fast, 150ms) ease,
                    border-color var(--transition-fast, 150ms) ease;
        font-weight: 500;
    }

    .inputx:focus {
        border-color: var(--accent-color, oklch(0.488 0.243 264.376));
        box-shadow: 0 0 0 4px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
    }

    .btnx {
        border: 1px solid transparent;
        background: var(--accent-color, oklch(0.488 0.243 264.376));
        color: var(--sidebar-primary-foreground, #fff);
        border-radius: calc(var(--radius, 0.625rem) - 6px);
        padding: 8px 12px;
        font-weight: 900;
        user-select: none;
        transition: transform var(--transition-fast, 150ms) ease,
                    background var(--transition-fast, 150ms) ease,
                    opacity var(--transition-fast, 150ms) ease;
    }

    .btnx:hover {
        background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
        transform: translateY(-1px);
    }

    .btnx:disabled {
        opacity: .65;
        cursor: not-allowed;
        transform: none;
    }

    .btnx-ghost {
        background: transparent;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        color: var(--foreground, oklch(0.985 0 0));
    }

    .btnx-ghost:hover {
        background: var(--accent, oklch(0.269 0 0));
    }

    .btnx.icon {
        padding: 6px 10px;
        border-radius: 12px;
    }

    .result-list {
        max-height: 420px;
        overflow: auto;
    }

    .result-row {
        display: flex;
        gap: 12px;
        align-items: center;
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        cursor: pointer;
        transition: background var(--transition-fast, 150ms) ease,
                    transform var(--transition-fast, 150ms) ease;
    }

    .result-row:hover {
        background: var(--accent-glow, rgba(37, 99, 235, 0.35));
        transform: translateY(-1px);
    }

    .thumb {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: var(--accent, oklch(0.269 0 0));
        overflow: hidden;
        flex: 0 0 auto;
    }

    .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .r-title {
        font-weight: 950;
        line-height: 1.2;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .r-meta {
        font-size: 12px;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    }

    .pill {
        font-size: 12px;
        padding: 3px 9px;
        border-radius: 999px;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        background: var(--accent, oklch(0.269 0 0));
        color: var(--foreground, oklch(0.985 0 0));
        font-weight: 900;
        white-space: nowrap;
    }

    .pill.success {
        border-color: var(--success, oklch(0.696 0.17 162.48));
        background: color-mix(in oklch, var(--success, oklch(0.696 0.17 162.48)) 15%, transparent 85%);
        color: var(--success, oklch(0.696 0.17 162.48));
    }

    .pill.warning {
        border-color: var(--warning, oklch(0.769 0.188 70.08));
        background: color-mix(in oklch, var(--warning, oklch(0.769 0.188 70.08)) 15%, transparent 85%);
        color: var(--warning, oklch(0.769 0.188 70.08));
    }

    .pill.danger {
        border-color: var(--danger, oklch(0.704 0.191 22.216));
        background: color-mix(in oklch, var(--danger, oklch(0.704 0.191 22.216)) 15%, transparent 85%);
        color: var(--danger, oklch(0.704 0.191 22.216));
    }

    .actions {
        margin-left: auto;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    select.selectx,
    input.qtyx,
    input.subx {
        height: 34px;
        padding: 0 10px;
        border-radius: calc(var(--radius, 0.625rem) - 8px);
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        background: var(--input, oklch(1 0 0 / 15%));
        color: var(--foreground, oklch(0.985 0 0));
        outline: none;
        font-size: 13px;
        transition: box-shadow var(--transition-fast, 150ms) ease,
                    border-color var(--transition-fast, 150ms) ease;
        font-weight: 500;
    }

    select.selectx:focus,
    input.qtyx:focus,
    input.subx:focus {
        border-color: var(--accent-color, oklch(0.488 0.243 264.376));
        box-shadow: 0 0 0 4px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
    }

    input.qtyx {
        width: 96px;
    }

    input.subx {
        width: 120px;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .table-wrap {
        overflow: auto;
    }

    .tablex {
        width: 100%;
        min-width: 1040px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .tablex th,
    .tablex td {
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        vertical-align: top;
        color: var(--foreground, oklch(0.985 0 0));
    }

    .tablex thead th {
        position: sticky;
        top: 0;
        background: var(--accent, oklch(0.269 0 0));
        z-index: 2;
        font-size: 12px;
        letter-spacing: .25px;
        text-transform: uppercase;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        font-weight: 900;
    }

    .money {
        text-align: right;
        font-variant-numeric: tabular-nums;
        font-weight: 800;
    }

    .mini-img {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        overflow: hidden;
        background: var(--accent, oklch(0.269 0 0));
    }

    .mini-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .namecell .nm {
        font-weight: 950;
        line-height: 1.15;
    }

    .namecell .bc,
    .namecell .sku {
        font-size: 12px;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    }

    .price-highlight {
        display: inline-block;
        padding: 2px 9px;
        border-radius: 999px;
        border: 1px solid var(--accent-color, oklch(0.488 0.243 264.376));
        background: var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
        color: var(--accent-color, oklch(0.488 0.243 264.376));
        font-weight: 800;
        font-size: 0.9em;
    }

    .qty-msg {
        display: none;
        margin-top: 4px;
        font-size: 12px;
        color: var(--warning, oklch(0.769 0.188 70.08));
        font-weight: 700;
    }

    .rowNo {
        display: inline-flex;
        width: 28px;
        height: 28px;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        background: var(--accent, oklch(0.269 0 0));
        font-weight: 950;
        font-variant-numeric: tabular-nums;
        color: var(--foreground, oklch(0.985 0 0));
    }

    .empty-state {
        padding: 18px 14px;
        text-align: center;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    }

    tr.gift-row {
        background: var(--accent-glow, rgba(37, 99, 235, 0.25));
    }

    .giftTag {
        margin-left: 6px;
    }

    .spin {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 999px;
        border: 2px solid var(--border-color, oklch(0.9 0 0));
        border-top-color: var(--accent-color, oklch(0.488 0.243 264.376));
        animation: sp 800ms linear infinite;
        vertical-align: -2px;
        margin-right: 6px;
    }

    @keyframes sp {
        to {
            transform: rotate(360deg);
        }
    }

    .pos-shell {
        display: grid;
        grid-template-columns: 1.2fr .8fr;
        gap: 14px;
        align-items: start;
    }

    @media (max-width: 992px) {
        .pos-shell {
            grid-template-columns: 1fr;
        }
    }

    .cart-panel {
        position: sticky;
        top: 14px;
        height: calc(100vh - 28px);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    @media (max-width: 992px) {
        .cart-panel {
            position: relative;
            top: 0;
            height: auto;
        }
    }

    .cart-panel .cardx-hd {
        position: sticky;
        top: 0;
        z-index: 5;
        background: var(--glass-base, rgba(255, 255, 255, 0.85));
        backdrop-filter: blur(8px);
    }

    .cart-scroll {
        flex: 1;
        overflow: auto;
        scroll-behavior: smooth;
    }

    .cart-footer {
        position: sticky;
        bottom: 0;
        z-index: 5;
        background: var(--glass-base, rgba(255, 255, 255, 0.85));
        border-top: 1px solid var(--border-color, oklch(0.9 0 0));
        backdrop-filter: blur(8px);
    }

    .hintbar {
        margin-top: 12px;
        padding: 10px 14px;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: var(--radius, 0.625rem);
        background: var(--input, oklch(1 0 0 / 15%));
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: space-between;
    }

    .toast-stack {
        position: fixed;
        right: 16px;
        top: 16px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 12000;
        width: min(420px, calc(100vw - 32px));
    }

    .toastx {
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        background: var(--card, oklch(0.205 0 0));
        color: var(--foreground, oklch(0.985 0 0));
        border-radius: 16px;
        box-shadow: var(--dropdown-shadow, 0 10px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3));
        overflow: hidden;
        transform: translateY(-10px);
        opacity: 0;
        animation: toastIn 180ms ease forwards;
        backdrop-filter: blur(10px);
    }

    @keyframes toastIn {
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .toastx-hd {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 10px 12px;
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        background: var(--accent, oklch(0.269 0 0));
    }

    .toastx-title {
        font-weight: 950;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .toastx-body {
        padding: 10px 12px 12px;
        font-size: 13px;
        line-height: 1.35;
        white-space: pre-line;
        color: var(--foreground, oklch(0.985 0 0));
    }

    .toastx-close {
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        background: transparent;
        color: var(--foreground, oklch(0.985 0 0));
        border-radius: 12px;
        padding: 6px 10px;
        font-weight: 950;
        cursor: pointer;
        transition: background var(--transition-fast, 150ms) ease;
    }

    .toastx-close:hover {
        background: var(--accent, oklch(0.269 0 0));
    }

    .toastx-progress {
        height: 3px;
        background: var(--border-color, oklch(0.9 0 0));
    }

    .toastx-progress > div {
        height: 100%;
        width: 100%;
        transform-origin: left;
        animation: toastProg linear forwards;
    }

    @keyframes toastProg {
        to {
            transform: scaleX(0);
        }
    }

    .toastx[data-type="success"] {
        border-color: var(--success, oklch(0.696 0.17 162.48));
    }

    .toastx[data-type="warning"] {
        border-color: var(--warning, oklch(0.769 0.188 70.08));
    }

    .toastx[data-type="danger"] {
        border-color: var(--danger, oklch(0.704 0.191 22.216));
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: var(--accent-color, oklch(0.488 0.243 264.376));
        box-shadow: 0 0 0 6px var(--accent-glow, oklch(0.488 0.243 264.376 / 0.2));
    }

    .toastx[data-type="success"] .dot {
        background: var(--success, oklch(0.696 0.17 162.48));
        box-shadow: 0 0 0 6px color-mix(in oklch, var(--success, oklch(0.696 0.17 162.48)) 20%, transparent 80%);
    }

    .toastx[data-type="warning"] .dot {
        background: var(--warning, oklch(0.769 0.188 70.08));
        box-shadow: 0 0 0 6px color-mix(in oklch, var(--warning, oklch(0.769 0.188 70.08)) 20%, transparent 80%);
    }

    .toastx[data-type="danger"] .dot {
        background: var(--danger, oklch(0.704 0.191 22.216));
        box-shadow: 0 0 0 6px color-mix(in oklch, var(--danger, oklch(0.704 0.191 22.216)) 20%, transparent 80%);
    }

    .overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgb(0 0 0 / .55);
        backdrop-filter: blur(6px);
        z-index: 9990;
    }

    .modalwrap {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        padding: 14px;
        place-items: center;
    }

    .modalwrap.show {
        display: grid;
    }

    .modalx {
        width: min(720px, 100%);
        transform: none !important;
        background: var(--card, oklch(0.205 0 0));
        border: 1px solid var(--border-color, oklch(0.9 0 0));
    }

    .modalx.cardx:hover {
        transform: none !important;
        box-shadow: var(--dropdown-shadow, 0 10px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3)) !important;
    }

    @media (max-width: 576px) {
        .modalwrap.show {
            align-items: end;
        }

        .modalx {
            width: 100%;
        }
    }

    /* Focus styles for accessibility */
    .btnx:focus,
    .inputx:focus,
    select.selectx:focus,
    input.qtyx:focus,
    input.subx:focus,
    .toastx-close:focus {
        outline: 2px solid var(--ring, oklch(0.556 0 0));
        outline-offset: 2px;
    }

    /* Additional utility classes */
    .text-success {
        color: var(--success, oklch(0.696 0.17 162.48));
    }

    .text-warning {
        color: var(--warning, oklch(0.769 0.188 70.08));
    }

    .text-danger {
        color: var(--danger, oklch(0.704 0.191 22.216));
    }

    .text-info {
        color: var(--info, oklch(0.488 0.243 264.376));
    }
</style>

    @php
        $currentLocationId = (int) session('location_id', 1);

        // helper: decide unit group for cart initial render
        $unitGroup = function($u){
            $u = strtolower(trim((string)$u));
            if (in_array($u, ['kg','kilogram','kilograms'])) return 'weight';
            if (in_array($u, ['g','gm','gram','grams'])) return 'weight';
            if (in_array($u, ['l','lt','liter','litre','liters','litres'])) return 'volume';
            if (in_array($u, ['ml','milliliter','millilitre','milliliters','millilitres'])) return 'volume';
            return 'count';
        };
    @endphp

    <div class="page">
        <div class="d-flex align-items-end justify-content-between mb-3">
            <div>
                <div class="subtle">Modern POS / Cart</div>
                <h3 class="title m-0">Shopping Cart</h3>
            </div>

            <div style="display:flex; gap:10px; align-items:end;">
                <div style="min-width:240px;">
                    <div class="subtle">Location</div>
                    <select class="selectx" id="locationSelect" style="height:42px; width:100%;">
                        @if(isset($locations) && count($locations))
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ (int)$loc->id === $currentLocationId ? 'selected' : '' }}>
                                    {{ $loc->name ?? ('Location #' . $loc->id) }}
                                </option>
                            @endforeach
                        @else
                            <option value="{{ $currentLocationId }}" selected>Location #{{ $currentLocationId }}</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>
        <div class="overlay" id="overlay"></div>

        <!-- ================= CUSTOMER ================= -->
        <div class="cardx mb-3">
            <div class="cardx-hd">
                <div class="strong">Customer</div>
                <button class="btnx btnx-ghost" id="addCustomerBtn" type="button">+ New</button>
            </div>

            <div style="padding:12px">
                <input class="inputx" id="customerSearch" placeholder="Search name / phone" autocomplete="off">
                <div id="customerResults" class="result-list"></div>
                <div id="selectedCustomer" class="subtle mt-2">👤 Guest customer</div>
            </div>
        </div>

        <!-- ================= NEW CUSTOMER MODAL ================= -->
        <div class="modalwrap" id="customerModalWrap" aria-hidden="true">
            <div id="customerModal" class="cardx modalx">
                <div class="cardx-hd">
                    <div class="strong">New Customer</div>
                    <button type="button" class="btnx btnx-ghost" id="closeCustomerModalBtn">Close</button>
                </div>

                <div style="padding:12px">
                    <input class="inputx mb-2" id="newCustomerName" placeholder="Name">
                    <input class="inputx mb-2" id="newCustomerPhone" placeholder="Phone">
                    <div style="display:flex; gap:10px; justify-content:flex-end;">
                        <button class="btnx btnx-ghost" id="cancelCustomerBtn" type="button">Cancel</button>
                        <button class="btnx" id="saveCustomerBtn" type="button">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= GRID ================= -->
        <div class="pos-shell">
            <!-- LEFT: SEARCH (unchanged, no unit select) -->
            <div class="cardx">
                <div class="cardx-hd">
                    <div>
                        <div class="strong">Search Products</div>
                        <div class="subtle">Name / Barcode — FIFO batches in stock (Location Based)</div>
                    </div>
                    <button class="btnx btnx-ghost" id="clearSearchBtn" type="button">Clear</button>
                </div>

                <div style="padding:12px 14px;">
                    <input class="inputx" type="text" id="cartSearch" placeholder="Type 2+ characters...">
                </div>

                <div id="searchResults" class="result-list"></div>
            </div>

            <!-- RIGHT: CART -->
            <div class="cardx cart-panel">
                <div class="cardx-hd">
                    <div>
                        <div class="strong">Cart Items</div>
                        <div class="subtle">Location #<span id="locBadge">{{ $currentLocationId }}</span> • Gifts auto-added • Sticky total</div>
                    </div>
                    <div style="display:flex; gap:8px;">
                        <button class="btnx btnx-ghost" type="button" id="clearCartBtn">Clear</button>
                        <a class="btnx btnx-ghost" href="{{ route('cart.index') }}">↻</a>
                    </div>
                </div>

                <!-- Manual Gift trigger -->
                <div style="padding:12px 14px; border-bottom:1px solid var(--border); display:flex; gap:10px; align-items:center; justify-content:space-between;">
                    <div>
                        <div class="strong">Manual Gift</div>
                        <div class="subtle">Search and add gift for selected customer.</div>
                    </div>
                    <button class="btnx btnx-ghost" type="button" id="openGiftModalBtn">🎁 Choose Gift</button>
                </div>

                <div class="cart-scroll">
                    <div class="table-wrap">
                        <table class="tablex">
                            <thead>
                            <tr>
                                <th style="width:54px;">#</th>
                                <th style="width:64px;">Img</th>
                                <th>Name</th>
                                <th style="width:140px;">Type</th>
                                <th style="width:120px;">Unit</th>
                                <th class="money" style="width:110px;">Price</th>
                                <th style="width:130px;">Qty</th>
                                <th class="money" style="width:160px;">Discount</th>
                                <th class="money" style="width:150px;">Sub (Editable)</th>
                                <th style="width:70px;"></th>
                            </tr>
                            </thead>

                            <tbody id="cartBody">
                            @forelse($cart->items as $item)
                                @php
                                    $isGift = (bool) ($item->is_gift ?? false);
                                    $giftSource = $item->gift_source ?? null;

                                    $batchUnit = $item->batch?->unit ?? ($item->batch_unit ?? 'pcs');
                                    $grp = $unitGroup($batchUnit);

                                    $saleUnit = strtolower($item->unit ?? $batchUnit ?? 'pcs');

                                    // normalize gram spelling for UI
                                    if (in_array($saleUnit, ['gm','gram','grams'])) $saleUnit = 'g';
                                    if (in_array($saleUnit, ['kilogram','kilograms'])) $saleUnit = 'kg';
                                    if (in_array($saleUnit, ['liter','litre','liters','litres','lt'])) $saleUnit = 'l';
                                    if (in_array($saleUnit, ['milliliter','millilitre','milliliters','millilitres'])) $saleUnit = 'ml';
                                @endphp

                                <tr data-item-id="{{ $item->id }}"
                                    data-batch-unit="{{ strtolower($batchUnit ?? 'pcs') }}"
                                    class="{{ $isGift ? 'gift-row' : '' }}">
                                    <td><span class="rowNo"></span></td>

                                    <td>
                                        <div class="mini-img">
                                            @if ($item->image)
                                                <img src="{{ asset('storage/' .$item->image->image_path) }}" alt="">
                                            @endif
                                        </div>
                                    </td>

                                    <td class="namecell">
                                        <div class="nm">
                                            {{ $item->product?->name }}
                                            @if ($isGift)
                                                <span class="pill success giftTag">GIFT • {{ $giftSource }}</span>
                                            @endif
                                        </div>
                                        <div class="bc">{{ $item->product?->barcode }}</div>
                                        <div class="sku">Batch: {{ $item->batch?->batch_sku }}</div>
                                    </td>

                                    <td>
                                        @if($isGift)
                                            <span class="pill">gift</span>
                                        @else
                                            <select class="selectx priceTypeSelect" data-item-id="{{ $item->id }}">
                                                <option value="retail" {{ $item->price_type === 'retail' ? 'selected' : '' }}>Retail</option>
                                                <option value="whole" {{ $item->price_type === 'whole' ? 'selected' : '' }}>Whole</option>
                                                <option value="customer_whole" {{ $item->price_type === 'customer_whole' ? 'selected' : '' }}>Customer</option>
                                            </select>
                                        @endif
                                    </td>

                                    <td>
                                        @if($isGift)
                                            <span class="pill">{{ $saleUnit }}</span>
                                            <div class="subtle">Fixed</div>
                                        @else
                                            @if($grp === 'weight')
                                                <select class="selectx unitSelect" data-item-id="{{ $item->id }}">
                                                    <option value="kg" {{ $saleUnit==='kg'?'selected':'' }}>kg</option>
                                                    <option value="g"  {{ $saleUnit==='g'?'selected':'' }}>g</option>
                                                </select>
                                                <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                            @elseif($grp === 'volume')
                                                <select class="selectx unitSelect" data-item-id="{{ $item->id }}">
                                                    <option value="l"  {{ $saleUnit==='l'?'selected':'' }}>L</option>
                                                    <option value="ml" {{ $saleUnit==='ml'?'selected':'' }}>ml</option>
                                                </select>
                                                <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                            @else
                                                <select class="selectx unitSelect" data-item-id="{{ $item->id }}">
                                                    <option value="pcs"   {{ $saleUnit==='pcs'?'selected':'' }}>pcs</option>
                                                    <option value="dozen" {{ $saleUnit==='dozen'?'selected':'' }}>dozen</option>
                                                    <option value="box"   {{ $saleUnit==='box'?'selected':'' }}>box</option>
                                                </select>
                                                <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                            @endif
                                        @endif
                                    </td>

                                    <td class="money">
                                        <span class="price-highlight unitPrice">{{ number_format((float) $item->unit_price, 2) }}</span>
                                        <div class="subtle">per <span class="unitLabel">{{ $saleUnit }}</span></div>
                                    </td>

                                    <td>
                                        @if($isGift)
                                            <span class="strong">{{ number_format((float) $item->quantity, 3) }}</span>
                                            <div class="subtle">Gift qty</div>
                                        @else
                                            <input class="qtyx qtyInput" type="number" min="0.0001" step="0.0001"
                                                   value="{{ (float) $item->quantity }}"
                                                   data-item-id="{{ $item->id }}">
                                            <div class="qty-msg" data-msg-for="{{ $item->id }}"></div>
                                        @endif
                                    </td>

                                    <td class="money">
                                        @php
                                            $amt = (float) ($item->discount_amount ?? 0);
                                            $pct = $item->discount_percent !== null ? (float) $item->discount_percent : null;
                                        @endphp

                                        @if ($isGift)
                                            <span class="subtle">—</span>
                                        @elseif ($amt > 0)
                                            <span class="pill success">{{ number_format($amt, 2) }}</span>
                                            <div class="subtle" style="margin-top:4px;">{{ $item->discount_label ?? '' }}</div>
                                        @elseif($pct !== null && $pct > 0)
                                            <span class="pill warning">{{ number_format($pct, 2) }}%</span>
                                            @if (!empty($item->discount_label))
                                                <div class="subtle" style="margin-top:4px;">{{ $item->discount_label }}</div>
                                            @endif
                                        @else
                                            <span class="subtle">—</span>
                                        @endif
                                    </td>

                                    <td class="money">
                                        @if($isGift)
                                            <span class="strong lineSubtotal">{{ number_format((float) $item->total_price, 2) }}</span>
                                        @else
                                            <input class="subx subInput" type="number" min="0" step="0.01"
                                                   value="{{ number_format((float) $item->total_price, 2, '.', '') }}"
                                                   data-item-id="{{ $item->id }}"
                                                   title="Type subtotal to auto-calc qty + unit">
                                            <div class="subtle">sweet: type Sub</div>
                                        @endif
                                    </td>

                                    <td class="money">
                                        @if($isGift && $giftSource === 'batch_offer')
                                            <button class="btnx btnx-ghost icon" type="button" disabled title="Auto gift can't be removed directly">✕</button>
                                        @elseif($isGift && $giftSource === 'manual')
                                            <button class="btnx btnx-ghost icon removeManualGiftBtn" type="button"
                                                    data-item-id="{{ $item->id }}">✕</button>
                                        @else
                                            <button class="btnx btnx-ghost icon removeBtn" type="button"
                                                    data-item-id="{{ $item->id }}">✕</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr id="emptyRow">
                                    <td colspan="10">
                                        <div class="empty-state">🧺 Cart is empty — search and add products</div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="cart-footer">
                    <div style="padding:10px 14px; display:flex; align-items:center; justify-content:space-between; gap:10px;">
                        <div>
                            <div class="subtle">Total</div>
                            <div class="strong" style="font-size:18px;">
                                <span id="cartTotalFoot">{{ number_format($cart->total ?? 0, 2) }}</span>
                            </div>
                            <div class="subtle" id="autoAdjustNote" style="margin-top:4px;"></div>
                            <div class="subtle" id="giftHintLine" style="margin-top:6px;"></div>
                        </div>
                        <div style="display:flex; gap:8px; align-items:center;">
                            <button class="btnx btnx-ghost" type="button" id="togglePaymentsBtn">Payments</button>
                            <button class="btnx" type="button" id="checkoutPayBtn">Checkout</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ================= ORDER SECTION ================= -->
        <div class="cardx mb-3 mt-3" id="orderCard">
            <div class="cardx-hd">
                <div>
                    <div class="strong">Order</div>
                    <div class="subtle">Reward + Discount + Auto Balance</div>
                </div>
                <button class="btnx btnx-ghost" type="button" id="openGiftModalBtn2">🎁 Manual Gift</button>
            </div>

            <div style="padding:12px; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div>
                    <div class="subtle">Customer</div>
                    <div class="strong" id="orderCustomerName">Guest</div>
                    <div class="subtle" id="orderCustomerPhone">—</div>
                </div>

                <div>
                    <div class="subtle">Due / Advance</div>
                    <div class="strong" id="orderCustomerBalance">Due: 0.00 | Advance: 0.00</div>
                </div>

                <div>
                    <div class="subtle">Reward Points Available</div>
                    <div class="strong" id="rewardAvailable">0.00</div>
                </div>

                <div>
                    <div class="subtle">Reward Points Use (amount auto = points × 1)</div>
                    <input class="inputx" id="rewardPointsUse" type="number" min="0" step="0.01" value="0">
                </div>

                <div>
                    <div class="subtle">Order Discount</div>
                    <input class="inputx" id="orderDiscount" type="number" min="0" step="0.01" value="0">
                </div>

                <div>
                    <div class="subtle">Auto Apply Balance</div>
                    <select class="selectx" id="autoBalanceMode" style="height:42px;">
                        <option value="auto" selected>Auto (Advance reduce, Due add)</option>
                        <option value="none">Do not apply</option>
                    </select>
                </div>

                <div style="grid-column:1 / -1; border-top:1px solid var(--border); padding-top:10px;">
                    <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">
                        <div>
                            <div class="subtle">Cart Total</div>
                            <div class="strong" id="cartTotalLive">{{ number_format($cart->total ?? 0, 2) }}</div>
                        </div>
                        <div>
                            <div class="subtle">Payable</div>
                            <div class="strong" id="payableTotalLive">{{ number_format($cart->total ?? 0, 2) }}</div>
                        </div>
                        <div>
                            <div class="subtle">Net After Balance</div>
                            <div class="strong" id="netAfterBalanceLive">{{ number_format($cart->total ?? 0, 2) }}</div>
                        </div>
                    </div>

                    <div class="subtle" id="orderHint" style="margin-top:8px;"></div>
                </div>
            </div>
        </div>

        <!-- ================= PAYMENTS (COLLAPSIBLE) ================= -->
        <div class="cardx mb-3" id="paymentsCard" style="display:none;">
            <div class="cardx-hd">
                <div>
                    <div class="strong">Payments</div>
                    <div class="subtle">Fast checkout: 1 row auto-created</div>
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                    <button type="button" class="btnx btnx-ghost" id="fullPaymentBtn">Full Payment</button>
                    <button type="button" class="btnx btnx-ghost" id="addPaymentRowBtn">+ Add Payment</button>
                </div>
            </div>

            <div style="padding:12px;">
                <div class="subtle" style="margin-bottom:8px;">
                    Tip: If Net After Balance is 0.00, you can checkout without adding payment.
                </div>

                <div id="paymentRows"></div>

                <div style="margin-top:10px; display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px;">
                    <div>
                        <div class="subtle">Paid Total</div>
                        <div class="strong" id="paidTotalLive">0.00</div>
                    </div>
                    <div>
                        <div class="subtle">Due Total</div>
                        <div class="strong" id="dueTotalLive">0.00</div>
                    </div>
                    <div>
                        <div class="subtle">Change</div>
                        <div class="strong" id="changeTotalLive">0.00</div>
                    </div>
                </div>

                <div style="margin-top:10px;">
                    <div class="subtle">Payment Note (optional)</div>
                    <input class="inputx" id="paymentNote" placeholder="Example: Cash + bKash, partial paid, etc.">
                </div>

                <div class="subtle" id="paymentHint" style="margin-top:8px;"></div>
            </div>
        </div>

        <div class="hintbar">
            <div class="subtle" id="sweetHint">
                💡 Tip: You can type subtotal in cart row to auto-calc Qty & Unit.
            </div>
            <button type="button" class="btnx btnx-ghost" id="dismissHintBtn">OK</button>
        </div>
    </div>

    <!-- ================= MANUAL GIFT MODAL ================= -->
    <div class="modalwrap" id="giftModalWrap" aria-hidden="true">
        <div id="giftModal" class="cardx modalx">
            <div class="cardx-hd">
                <div>
                    <div class="strong">Manual Gift</div>
                    <div class="subtle" id="giftModalCustomerLine">Customer: Guest</div>
                    <div class="subtle">Location #<span id="giftLocBadge">{{ $currentLocationId }}</span></div>
                </div>
                <button type="button" class="btnx btnx-ghost" id="closeGiftModalBtn">Close</button>
            </div>

            <div style="padding:12px;">
                <div class="subtle" style="margin-bottom:8px;">
                    Search gift product by name / barcode then click <b>Add Gift</b> (price 0.00).
                </div>

                <input class="inputx" id="giftSearchInput" placeholder="Type 2+ characters..." autocomplete="off">
                <div id="giftSearchResults" class="result-list" style="margin-top:10px;"></div>

                <div class="subtle" id="giftModalHint" style="margin-top:10px;"></div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){

            /* ===========================
                LOCATION (GLOBAL)
            =========================== */
            const locationSelect = document.getElementById('locationSelect');
            const locBadge = document.getElementById('locBadge');
            const giftLocBadge = document.getElementById('giftLocBadge');

            let LOCATION_ID = Number(locationSelect?.value || {{ $currentLocationId }});

            function setLocationBadges(){
                if (locBadge) locBadge.textContent = String(LOCATION_ID);
                if (giftLocBadge) giftLocBadge.textContent = String(LOCATION_ID);
            }
            setLocationBadges();

            /* ===========================
                TOAST
            =========================== */
            const toastStack = document.getElementById('toastStack');

            function escapeHtml(str){
                return String(str ?? '')
                    .replaceAll('&','&amp;')
                    .replaceAll('<','&lt;')
                    .replaceAll('>','&gt;')
                    .replaceAll('"','&quot;')
                    .replaceAll("'",'&#039;');
            }

            function pushToast({ type='warning', title='Notice', messages=[], timeout=3200 } = {}){
                if (!toastStack) return;

                const msgText = (Array.isArray(messages) ? messages : [messages])
                    .filter(Boolean).map(m => String(m)).join('\n');

                const el = document.createElement('div');
                el.className = 'toastx';
                el.dataset.type = type;

                el.innerHTML = `
                    <div class="toastx-hd">
                        <div class="toastx-title">
                            <span class="dot"></span>
                            <span>${escapeHtml(title)}</span>
                        </div>
                        <button class="toastx-close" type="button" aria-label="Close">✕</button>
                    </div>
                    <div class="toastx-body">${escapeHtml(msgText || 'Something happened.')}</div>
                    <div class="toastx-progress"><div style="animation-duration:${timeout}ms"></div></div>
                `;

                const remove = () => {
                    clearTimeout(el._t);
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-10px)';
                    setTimeout(() => el.remove(), 160);
                };

                el.querySelector('.toastx-close').addEventListener('click', remove);
                toastStack.appendChild(el);

                const max = 4;
                while (toastStack.children.length > max){
                    toastStack.removeChild(toastStack.lastChild);
                }

                if (timeout > 0) el._t = setTimeout(remove, timeout);
            }

            function extractMessages(data){
                const msgs = [];
                if (data && data.errors && typeof data.errors === 'object'){
                    Object.values(data.errors).forEach(arr => {
                        if (Array.isArray(arr)) arr.forEach(m => msgs.push(String(m)));
                    });
                }
                if (data && data.message) msgs.push(String(data.message));
                if (!msgs.length) msgs.push('Something went wrong.');
                return [...new Set(msgs)];
            }

            function guessTypeFromStatus(status){
                if (status >= 500) return 'danger';
                if ([422,409,403].includes(status)) return 'warning';
                if (status >= 400) return 'danger';
                return 'success';
            }

            function toastAll(res, data, fallbackTitle='Error'){
                const status = res?.status || 0;
                const type = guessTypeFromStatus(status);
                const messages = extractMessages(data);

                let title = fallbackTitle;
                if (status === 422) title = 'Validation';
                else if (status === 409) title = 'Duplicate';
                else if (status >= 500) title = 'Server Error';

                pushToast({ type, title, messages, timeout: 3800 });
            }

            window.pushToast = pushToast;
            window.toastAll = toastAll;

            async function pingSetLocationAndReload(newLocId){
                try{
                    await fetch(`{{ route('cart.search') }}?q=__&location_id=${encodeURIComponent(newLocId)}`, {
                        headers: { 'Accept':'application/json' }
                    });
                } catch (e) {}
                window.location.reload();
            }

            locationSelect?.addEventListener('change', () => {
                LOCATION_ID = Number(locationSelect.value || 1);
                setLocationBadges();
                pushToast({ type:'success', title:'Location', messages:[`Switched to location #${LOCATION_ID}`], timeout:1800 });
                pingSetLocationAndReload(LOCATION_ID);
            });

            /* ===========================
                MODALS
            =========================== */
            const overlay = document.getElementById('overlay');
            const customerModalWrap = document.getElementById('customerModalWrap');
            const giftModalWrap = document.getElementById('giftModalWrap');

            function openWrap(wrap){
                if (!wrap) return;
                overlay.style.display = 'block';
                wrap.classList.add('show');
                wrap.setAttribute('aria-hidden','false');
                document.body.style.overflow = 'hidden';
            }
            function closeWrap(wrap){
                if (!wrap) return;
                wrap.classList.remove('show');
                wrap.setAttribute('aria-hidden','true');
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
            function closeAllModals(){
                closeWrap(customerModalWrap);
                closeWrap(giftModalWrap);
            }

            overlay.addEventListener('click', closeAllModals);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAllModals(); });

            customerModalWrap.addEventListener('click', (e) => {
                if (e.target === customerModalWrap) closeWrap(customerModalWrap);
            });
            giftModalWrap.addEventListener('click', (e) => {
                if (e.target === giftModalWrap) closeWrap(giftModalWrap);
            });

            document.getElementById('addCustomerBtn')?.addEventListener('click', () => openWrap(customerModalWrap));
            document.getElementById('closeCustomerModalBtn')?.addEventListener('click', () => closeWrap(customerModalWrap));
            document.getElementById('cancelCustomerBtn')?.addEventListener('click', () => closeWrap(customerModalWrap));
            document.getElementById('closeGiftModalBtn')?.addEventListener('click', () => closeWrap(giftModalWrap));

            /* ===========================
                DOM (CART)
            =========================== */
            const searchInput = document.getElementById('cartSearch');
            const searchResults = document.getElementById('searchResults');
            const clearSearchBtn = document.getElementById('clearSearchBtn');

            const cartBody = document.getElementById('cartBody');
            const cartTotalFoot = document.getElementById('cartTotalFoot');
            const clearCartBtn = document.getElementById('clearCartBtn');

            const hintEl = document.getElementById('sweetHint');
            const dismissHintBtn = document.getElementById('dismissHintBtn');

            const togglePaymentsBtn = document.getElementById('togglePaymentsBtn');
            const paymentsCard = document.getElementById('paymentsCard');

            const giftHintLineEl = document.getElementById('giftHintLine');

            const openGiftModalBtn = document.getElementById('openGiftModalBtn');
            const openGiftModalBtn2 = document.getElementById('openGiftModalBtn2');
            const giftSearchInput = document.getElementById('giftSearchInput');
            const giftSearchResults = document.getElementById('giftSearchResults');
            const giftModalCustomerLine = document.getElementById('giftModalCustomerLine');
            const giftModalHint = document.getElementById('giftModalHint');

            let debounceTimer = null;
            let searching = false;
            let updating = new Set();

            // ✅ delays
            const QTY_DELAY_MS = 450;     // typing quantity
            const SUB_DELAY_MS = 550;     // typing subtotal (sweet logic)
            const BOX_PCS = 24;           // ⚠️ change if your box size is different

            /* ===========================
                HELPERS (UNIT GROUP + FACTORS)
            =========================== */
            function money(n){ return Number(n || 0).toFixed(2); }
            function num(v){ return Math.max(0, Number(v || 0)); }

            function setHint(msg){
                hintEl.textContent = msg || "💡 Tip: You can type subtotal in cart row to auto-calc Qty & Unit.";
            }

            function normalizeUnit(u){
                u = String(u || '').trim().toLowerCase();
                if (['gm','gram','grams'].includes(u)) return 'g';
                if (['kilogram','kilograms'].includes(u)) return 'kg';
                if (['lt','liter','litre','liters','litres'].includes(u)) return 'l';
                if (['milliliter','millilitre','milliliters','millilitres'].includes(u)) return 'ml';
                if (!u) return 'pcs';
                return u;
            }

            function unitGroupFromBatchUnit(batchUnit){
                const u = normalizeUnit(batchUnit);
                if (u === 'kg' || u === 'g') return 'weight';
                if (u === 'l' || u === 'ml') return 'volume';
                return 'count';
            }

            // convert selected unit to "base unit" factor:
            // weight base = g, volume base = ml, count base = pcs
            function factorToBase(group, unit){
                unit = normalizeUnit(unit);
                if (group === 'weight'){
                    if (unit === 'kg') return 1000;
                    return 1; // g
                }
                if (group === 'volume'){
                    if (unit === 'l') return 1000;
                    return 1; // ml
                }
                // count
                if (unit === 'dozen') return 12;
                if (unit === 'box') return BOX_PCS;
                return 1; // pcs
            }

            function bestUnitForBaseQty(group, baseQty){
                baseQty = Number(baseQty || 0);

                if (group === 'weight'){
                    if (baseQty >= 1000) return { unit:'kg', qty: baseQty / 1000 };
                    return { unit:'g', qty: baseQty };
                }
                if (group === 'volume'){
                    if (baseQty >= 1000) return { unit:'l', qty: baseQty / 1000 };
                    return { unit:'ml', qty: baseQty };
                }

                // count: choose biggest if >= 1
                if (baseQty >= BOX_PCS) return { unit:'box', qty: baseQty / BOX_PCS };
                if (baseQty >= 12) return { unit:'dozen', qty: baseQty / 12 };
                return { unit:'pcs', qty: baseQty };
            }

            function updateRowNumbers(){
                const rows = Array.from(cartBody.querySelectorAll('tr[data-item-id]'));
                const total = rows.length;
                rows.forEach((tr, idx) => {
                    const noEl = tr.querySelector('.rowNo');
                    if (noEl) noEl.textContent = String(total - idx);
                });
            }

            function clearQtyMessages(){
                document.querySelectorAll('.qty-msg').forEach(el => {
                    el.style.display = 'none';
                    el.textContent = '';
                });
            }

            function showQtyMessage(itemId, msg){
                clearQtyMessages();
                const el = document.querySelector(`.qty-msg[data-msg-for="${itemId}"]`);
                if (!el) return;
                el.textContent = msg;
                el.style.display = 'block';
            }

            function renderDiscountCell(item){
                const isGift = !!item.is_gift;
                if (isGift) return `<span class="subtle">—</span>`;

                const amt = Number(item.discount_amount || 0);
                const pct = (item.discount_percent === null || typeof item.discount_percent === 'undefined') ? null : Number(item.discount_percent);

                if (amt > 0){
                    return `<span class="pill success">${money(amt)}</span>
                            <div class="subtle" style="margin-top:4px;">${escapeHtml(item.discount_label ?? '')}</div>`;
                }
                if (pct !== null && pct > 0){
                    return `<span class="pill warning">${pct.toFixed(2)}%</span>
                            ${item.discount_label ? `<div class="subtle" style="margin-top:4px;">${escapeHtml(item.discount_label)}</div>` : ``}`;
                }
                return `<span class="subtle">—</span>`;
            }

            /* ===========================
                ORDER DOM (unchanged)
            =========================== */
            let selectedCustomer = null;

            const orderCustomerNameEl = document.getElementById('orderCustomerName');
            const orderCustomerPhoneEl = document.getElementById('orderCustomerPhone');
            const orderCustomerBalanceEl = document.getElementById('orderCustomerBalance');

            const rewardAvailableEl = document.getElementById('rewardAvailable');
            const rewardPointsUseEl = document.getElementById('rewardPointsUse');
            const orderDiscountEl = document.getElementById('orderDiscount');
            const autoBalanceModeEl = document.getElementById('autoBalanceMode');

            const cartTotalLiveEl = document.getElementById('cartTotalLive');
            const payableTotalLiveEl = document.getElementById('payableTotalLive');
            const netAfterBalanceLiveEl = document.getElementById('netAfterBalanceLive');
            const orderHintEl = document.getElementById('orderHint');
            const autoAdjustNoteEl = document.getElementById('autoAdjustNote');

            const POINT_RATE = 1;

            const paymentRowsEl = document.getElementById('paymentRows');
            const addPaymentRowBtn = document.getElementById('addPaymentRowBtn');
            const fullPaymentBtn = document.getElementById('fullPaymentBtn');

            const paidTotalLiveEl = document.getElementById('paidTotalLive');
            const dueTotalLiveEl = document.getElementById('dueTotalLive');
            const changeTotalLiveEl = document.getElementById('changeTotalLive');

            const paymentNoteEl = document.getElementById('paymentNote');
            const paymentHintEl = document.getElementById('paymentHint');

            const checkoutPayBtn = document.getElementById('checkoutPayBtn');

            const METHODS = {
                offline: ['cash', 'card', 'bank', 'cheque'],
                online: ['bkash', 'nagad', 'rocket', 'upay', 'stripe', 'paypal', 'sslcommerz'],
            };

            function cartTotalNow(){
                return num(cartTotalFoot?.textContent || cartTotalLiveEl?.textContent || 0);
            }

            function calcPayable(){
                const cartT = cartTotalNow();

                const availablePts = num(selectedCustomer?.reward_points || 0);
                rewardAvailableEl.textContent = money(availablePts);

                let ptsUse = num(rewardPointsUseEl.value);
                if (!selectedCustomer) ptsUse = 0;
                if (ptsUse > availablePts) ptsUse = availablePts;

                const rewardAmount = ptsUse * POINT_RATE;

                let disc = num(orderDiscountEl.value);
                if (disc > cartT) disc = cartT;

                let payable = cartT - disc - rewardAmount;
                if (payable < 0) payable = 0;

                const mode = autoBalanceModeEl.value || 'auto';
                const dueBal = num(selectedCustomer?.due_balance || 0);
                const advBal = num(selectedCustomer?.advance_balance || 0);

                let net = payable;
                let note = '';

                if (selectedCustomer && mode === 'auto'){
                    if (advBal > 0){
                        const usedAdv = Math.min(advBal, net);
                        net = net - usedAdv;
                        note += usedAdv > 0 ? `Advance used: ${money(usedAdv)}. ` : '';
                    }
                    if (dueBal > 0){
                        net = net + dueBal;
                        note += `Due added: ${money(dueBal)}. `;
                    }
                }

                rewardPointsUseEl.value = money(ptsUse);

                cartTotalLiveEl.textContent = money(cartT);
                payableTotalLiveEl.textContent = money(payable);
                netAfterBalanceLiveEl.textContent = money(net);

                autoAdjustNoteEl.textContent = (selectedCustomer && mode === 'auto')
                    ? `Auto: ${note.trim() || 'No balance applied.'}`
                    : (mode === 'none' ? 'Auto balance disabled.' : '');

                orderHintEl.textContent = `Reward: ${money(ptsUse)} pts (${money(rewardAmount)}), Discount: ${money(disc)}.`;

                recalcPaymentSummary(net);
                renderPaymentHint(net);

                return { cartT, ptsUse, rewardAmount, disc, payable, net };
            }

            [rewardPointsUseEl, orderDiscountEl, autoBalanceModeEl].forEach(el => {
                el.addEventListener('input', calcPayable);
                el.addEventListener('change', calcPayable);
            });

            function renderOrderCustomer(customer){
                selectedCustomer = customer || null;

                if (!selectedCustomer){
                    orderCustomerNameEl.textContent = 'Guest';
                    orderCustomerPhoneEl.textContent = '—';
                    orderCustomerBalanceEl.textContent = `Due: 0.00 | Advance: 0.00`;
                    rewardAvailableEl.textContent = '0.00';
                    rewardPointsUseEl.value = '0.00';
                    calcPayable();
                    return;
                }

                orderCustomerNameEl.textContent = selectedCustomer.name || 'Customer';
                orderCustomerPhoneEl.textContent = selectedCustomer.phone || '—';
                orderCustomerBalanceEl.textContent =
                    `Due: ${money(selectedCustomer.due_balance)} | Advance: ${money(selectedCustomer.advance_balance)}`;

                rewardAvailableEl.textContent = money(selectedCustomer.reward_points);
                rewardPointsUseEl.value = '0.00';
                calcPayable();
            }

            /* ===========================
                PAYMENTS (unchanged)
            =========================== */
            function methodOptions(channel){
                const list = METHODS[channel] || [];
                return list.map(m => `<option value="${m}">${m}</option>`).join('');
            }

            function createPaymentRow(defaults = {}){
                const row = document.createElement('div');
                row.className = 'cardx';
                row.style.marginBottom = '10px';

                const channel = defaults.channel || 'offline';
                const method = defaults.method || (METHODS[channel]?.[0] || '');
                const amount = defaults.amount || '';
                const trx_id = defaults.trx_id || '';
                const account = defaults.account_label || '';

                row.innerHTML = `
                    <div class="cardx-hd">
                        <div class="strong">Payment</div>
                        <button type="button" class="btnx btnx-ghost payRowRemoveBtn">Remove</button>
                    </div>

                    <div style="padding:12px; display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <div>
                            <div class="subtle">Channel</div>
                            <select class="selectx payChannel">
                                <option value="offline" ${channel==='offline'?'selected':''}>offline</option>
                                <option value="online"  ${channel==='online'?'selected':''}>online</option>
                            </select>
                        </div>

                        <div>
                            <div class="subtle">Method</div>
                            <select class="selectx payMethod">${methodOptions(channel)}</select>
                        </div>

                        <div>
                            <div class="subtle">Amount</div>
                            <input class="inputx payAmount" type="number" min="0" step="0.0001" placeholder="0.00" value="${escapeHtml(amount)}">
                        </div>

                        <div>
                            <div class="subtle">Account Label (optional)</div>
                            <input class="inputx payAccount" placeholder="Example: Cash Drawer / bKash Personal" value="${escapeHtml(account)}">
                        </div>

                        <div class="payTrxWrap" style="grid-column:1 / -1; display:${channel==='online'?'block':'none'};">
                            <div class="subtle">Transaction ID (Required for online)</div>
                            <input class="inputx payTrx" placeholder="Example: BK123456789" value="${escapeHtml(trx_id)}">
                        </div>

                        <div class="subtle payRowHint" style="grid-column:1 / -1;"></div>
                    </div>
                `;

                const methodSelect = row.querySelector('.payMethod');
                methodSelect.value = method;

                const channelSelect = row.querySelector('.payChannel');
                const trxWrap = row.querySelector('.payTrxWrap');
                const trxInput = row.querySelector('.payTrx');

                function refresh(){
                    const net = num(netAfterBalanceLiveEl.textContent || 0);
                    recalcPaymentSummary(net);
                    renderPaymentHint(net);
                    renderPaymentHintRow(row);
                }

                channelSelect.addEventListener('change', () => {
                    const ch = channelSelect.value;
                    methodSelect.innerHTML = methodOptions(ch);
                    methodSelect.value = METHODS[ch]?.[0] || '';
                    trxWrap.style.display = (ch === 'online') ? 'block' : 'none';
                    if (ch !== 'online') trxInput.value = '';
                    refresh();
                });

                row.querySelector('.payAmount').addEventListener('input', refresh);
                trxInput.addEventListener('input', refresh);
                row.querySelector('.payAccount').addEventListener('input', refresh);

                row.querySelector('.payRowRemoveBtn').addEventListener('click', () => {
                    row.remove();
                    refresh();
                });

                renderPaymentHintRow(row);
                return row;
            }

            function ensureOnePaymentRow(){
                const rows = paymentRowsEl.querySelectorAll('.cardx');
                if (rows.length === 0){
                    paymentRowsEl.appendChild(createPaymentRow());
                }
            }

            function getPaymentRowsData(){
                const rows = Array.from(paymentRowsEl.querySelectorAll('.cardx'));
                return rows.map(r => {
                    const channel = r.querySelector('.payChannel')?.value || 'offline';
                    const method = r.querySelector('.payMethod')?.value || '';
                    const amount = num(r.querySelector('.payAmount')?.value);
                    const trx_id = (r.querySelector('.payTrx')?.value || '').trim();
                    const account_label = (r.querySelector('.payAccount')?.value || '').trim();
                    return { channel, method, amount, trx_id, account_label, _rowEl: r };
                });
            }

            function renderPaymentHintRow(rowEl){
                const ch = rowEl.querySelector('.payChannel')?.value;
                const m  = rowEl.querySelector('.payMethod')?.value;
                const amt= rowEl.querySelector('.payAmount')?.value;
                const trx= (rowEl.querySelector('.payTrx')?.value || '').trim();
                const hint = rowEl.querySelector('.payRowHint');
                if (!hint) return;

                const a = num(amt);
                if (!m) { hint.textContent = 'Please select a method.'; return; }
                if (a <= 0) { hint.textContent = 'Enter an amount greater than 0.'; return; }
                if (ch === 'online' && !trx) { hint.textContent = 'Online payment needs Transaction ID.'; return; }
                hint.textContent = `OK: ${ch} / ${m} / ${money(a)}`;
            }

            function recalcPaymentSummary(net){
                const rows = getPaymentRowsData();
                const paid = rows.reduce((s, p) => s + num(p.amount), 0);

                let due = 0, change = 0;
                if (paid <= 0) { due = net; change = 0; }
                else if (paid < net) { due = net - paid; change = 0; }
                else { due = 0; change = paid - net; }

                paidTotalLiveEl.textContent = money(paid);
                dueTotalLiveEl.textContent = money(due);
                changeTotalLiveEl.textContent = money(change);
            }

            function renderPaymentHint(net){
                const rows = getPaymentRowsData();
                rows.forEach(p => renderPaymentHintRow(p._rowEl));

                const paid = rows.reduce((s, p) => s + num(p.amount), 0);

                if (net <= 0) { paymentHintEl.textContent = 'Net is 0.00. You can checkout without payment.'; return; }
                if (rows.length === 0) { paymentHintEl.textContent = 'Add payment(s) if customer pays now. Otherwise leave empty and checkout as due.'; return; }
                if (paid <= 0) { paymentHintEl.textContent = 'Payment rows added but total is 0.00. Enter amounts.'; return; }
                if (paid < net) { paymentHintEl.textContent = `Partial payment. Due will be ${money(net - paid)}.`; return; }
                if (paid === net) { paymentHintEl.textContent = 'Full payment. Order will be completed.'; return; }
                paymentHintEl.textContent = `Over payment. Change will be ${money(paid - net)}.`;
            }

            togglePaymentsBtn.addEventListener('click', () => {
                const show = paymentsCard.style.display === 'none';
                paymentsCard.style.display = show ? 'block' : 'none';
                if (show){
                    ensureOnePaymentRow();
                    pushToast({ type:'success', title:'Payments', messages:['Payments opened'], timeout:1800 });
                    const net = num(netAfterBalanceLiveEl.textContent || 0);
                    recalcPaymentSummary(net);
                    renderPaymentHint(net);
                } else {
                    pushToast({ type:'warning', title:'Payments', messages:['Payments hidden'], timeout:1600 });
                }
            });

            addPaymentRowBtn.addEventListener('click', () => {
                paymentRowsEl.appendChild(createPaymentRow());
                const net = num(netAfterBalanceLiveEl.textContent || 0);
                recalcPaymentSummary(net);
                renderPaymentHint(net);
                pushToast({ type:'success', title:'Payment', messages:['Added new payment row'], timeout:1600 });
            });

            fullPaymentBtn.addEventListener('click', () => {
                const { net } = calcPayable();
                paymentsCard.style.display = 'block';
                ensureOnePaymentRow();

                const firstRow = paymentRowsEl.querySelector('.cardx');
                const amountInput = firstRow?.querySelector('.payAmount');
                if (amountInput){
                    amountInput.value = money(net);
                    amountInput.dispatchEvent(new Event('input', { bubbles:true }));
                }

                recalcPaymentSummary(num(net));
                renderPaymentHint(num(net));

                pushToast({ type:'success', title:'Full Payment', messages:[`Filled ${money(net)}`], timeout:2200 });
            });

            /* ===========================
                CART AJAX
            =========================== */
            async function jsonFetch(url, method, payload){
                const body = payload ? { ...payload, location_id: LOCATION_ID } : { location_id: LOCATION_ID };

                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                });

                const data = await res.json().catch(() => ({}));
                return { res, data };
            }

            function giftBadge(item){
                if (!item.is_gift) return '';
                return `<span class="pill success giftTag">GIFT • ${escapeHtml(item.gift_source || 'gift')}</span>`;
            }

            function typeCell(item){
                if (item.is_gift){
                    return `<span class="pill">gift</span>`;
                }
                return `
                    <select class="selectx priceTypeSelect" data-item-id="${item.id}">
                        <option value="retail" ${item.price_type==='retail'?'selected':''}>Retail</option>
                        <option value="whole" ${item.price_type==='whole'?'selected':''}>Whole</option>
                        <option value="customer_whole" ${item.price_type==='customer_whole'?'selected':''}>Customer</option>
                    </select>
                `;
            }

            function unitCell(item){
                const batchUnit = normalizeUnit(item.batch_unit || 'pcs');
                const group = unitGroupFromBatchUnit(batchUnit);
                const currentUnit = normalizeUnit(item.unit || batchUnit);

                if (item.is_gift){
                    return `<span class="pill">${escapeHtml(currentUnit)}</span><div class="subtle">Fixed</div>`;
                }

                if (group === 'weight'){
                    return `
                        <select class="selectx unitSelect" data-item-id="${item.id}">
                            <option value="kg" ${currentUnit==='kg'?'selected':''}>kg</option>
                            <option value="g"  ${currentUnit==='g'?'selected':''}>g</option>
                        </select>
                        <div class="subtle">Batch: ${escapeHtml(batchUnit)}</div>
                    `;
                }
                if (group === 'volume'){
                    return `
                        <select class="selectx unitSelect" data-item-id="${item.id}">
                            <option value="l"  ${currentUnit==='l'?'selected':''}>L</option>
                            <option value="ml" ${currentUnit==='ml'?'selected':''}>ml</option>
                        </select>
                        <div class="subtle">Batch: ${escapeHtml(batchUnit)}</div>
                    `;
                }

                return `
                    <select class="selectx unitSelect" data-item-id="${item.id}">
                        <option value="pcs" ${currentUnit==='pcs'?'selected':''}>pcs</option>
                        <option value="dozen" ${currentUnit==='dozen'?'selected':''}>dozen</option>
                        <option value="box" ${currentUnit==='box'?'selected':''}>box</option>
                    </select>
                    <div class="subtle">Batch: ${escapeHtml(batchUnit)}</div>
                `;
            }

            function qtyCell(item){
                if (item.is_gift){
                    return `<span class="strong">${Number(item.quantity||0).toFixed(3)}</span><div class="subtle">Gift qty</div>`;
                }
                return `
                    <input class="qtyx qtyInput" type="number" min="0.0001" step="0.0001" value="${Number(item.quantity||0)}" data-item-id="${item.id}">
                    <div class="qty-msg" data-msg-for="${item.id}"></div>
                `;
            }

            function subCell(item){
                if (item.is_gift){
                    return `<span class="strong lineSubtotal">${money(item.total_price)}</span>`;
                }
                return `
                    <input class="subx subInput" type="number" min="0" step="0.01"
                           value="${money(item.total_price)}"
                           data-item-id="${item.id}"
                           title="Type subtotal to auto-calc qty + unit">
                    <div class="subtle">sweet: type Sub</div>
                `;
            }

            function removeCell(item){
                if (item.is_gift && item.gift_source === 'batch_offer'){
                    return `<button class="btnx btnx-ghost icon" type="button" disabled title="Auto gift can't be removed directly">✕</button>`;
                }
                if (item.is_gift && item.gift_source === 'manual'){
                    return `<button class="btnx btnx-ghost icon removeManualGiftBtn" type="button" data-item-id="${item.id}">✕</button>`;
                }
                return `<button class="btnx btnx-ghost icon removeBtn" type="button" data-item-id="${item.id}">✕</button>`;
            }

            function renderCart(cart){
                cartBody.innerHTML = '';
                const items = [...(cart.items || [])].sort((a, b) => Number(b.id) - Number(a.id));

                if (!items.length){
                    cartBody.innerHTML = `<tr id="emptyRow"><td colspan="10"><div class="empty-state">🧺 Cart is empty — search and add products</div></td></tr>`;
                    cartTotalFoot.textContent = money(cart.total);
                    calcPayable();
                    bindCartRowEvents();
                    updateRowNumbers();
                    return;
                }

                items.forEach(item => {
                    const imgHtml = item.image ? `<img src="${item.image}" alt="">` : ``;
                    const trClass = item.is_gift ? 'gift-row' : '';
                    const batchUnit = normalizeUnit(item.batch_unit || 'pcs');
                    const currentUnit = normalizeUnit(item.unit || batchUnit);

                    cartBody.insertAdjacentHTML('beforeend', `
                        <tr data-item-id="${item.id}" data-batch-unit="${escapeHtml(batchUnit)}" class="${trClass}">
                            <td><span class="rowNo"></span></td>
                            <td><div class="mini-img">${imgHtml}</div></td>
                            <td class="namecell">
                                <div class="nm">${escapeHtml(item.name ?? '')} ${giftBadge(item)}</div>
                                <div class="bc">${escapeHtml(item.barcode ?? '')}</div>
                                <div class="sku">Batch: ${escapeHtml(item.batch_sku ?? '')}</div>
                            </td>
                            <td>${typeCell(item)}</td>
                            <td>${unitCell(item)}</td>
                            <td class="money">
                                <span class="price-highlight unitPrice">${money(item.unit_price)}</span>
                                <div class="subtle">per <span class="unitLabel">${escapeHtml(currentUnit)}</span></div>
                            </td>
                            <td>${qtyCell(item)}</td>
                            <td class="money">${renderDiscountCell(item)}</td>
                            <td class="money">${subCell(item)}</td>
                            <td class="money">${removeCell(item)}</td>
                        </tr>
                    `);
                });

                cartTotalFoot.textContent = money(cart.total);
                calcPayable();
                bindCartRowEvents();
                updateRowNumbers();
            }

            function showGiftHints(data){
                const hints = data?.gift_hints || [];
                giftHintLineEl.textContent = hints.length ? ('🎁 ' + hints.join(' • ')) : '';
            }

            async function addToCart(batchId, qty, priceType, btn){
                clearQtyMessages();

                if (btn){
                    btn.disabled = true;
                    btn._old = btn.innerHTML;
                    btn.innerHTML = `<span class="spin"></span>Adding`;
                }

                const { res, data } = await jsonFetch(`{{ route('cart.add') }}`, 'POST', {
                    batch_id: batchId,
                    quantity: qty,
                    price_type: priceType
                    // ✅ unit not sent from search (as requested)
                });

                if (btn){
                    btn.disabled = false;
                    btn.innerHTML = btn._old || 'Add';
                }

                if (!res.ok || !data.success){
                    toastAll(res, data, 'Add failed');
                    return;
                }

                renderCart(data.cart);
                showGiftHints(data);
                setHint(data.hint || null);

                document.querySelector('.cart-scroll')?.scrollTo({ top:0, behavior:'smooth' });
                pushToast({ type:'success', title:'Cart', messages:[`Added to cart (Location #${LOCATION_ID})`], timeout:1600 });
            }

            async function updateCartItem(itemId, priceType, quantity, unit){
                clearQtyMessages();
                if (updating.has(itemId)) return;
                updating.add(itemId);

                const { res, data } = await jsonFetch(`{{ route('cart.item.update') }}`, 'POST', {
                    item_id: itemId,
                    price_type: priceType,
                    quantity,
                    unit: unit || null
                });

                updating.delete(itemId);

                if (!res.ok && res.status === 422 && data?.invalid_item_id){
                    if (data?.cart) renderCart(data.cart);

                    const min = data.required_min;
                    const max = data.required_max;
                    const t = (data.required_type || '').replace('_',' ');

                    let range = '';
                    if (min !== null && max !== null) range = `Min ${min} & Max ${max}`;
                    else if (min !== null) range = `Min ${min}`;
                    else if (max !== null) range = `Max ${max}`;
                    else range = `required quantity`;

                    showQtyMessage(data.invalid_item_id, `❗ Need ${range} to use ${t} price.`);
                    pushToast({ type:'warning', title:'Qty rule', messages:[data.message || 'Qty condition not met'], timeout:3500 });
                    setHint(`✨ ${range} required for ${t}. Increase quantity to unlock.`);
                    return;
                }

                if (!res.ok || !data.success){
                    toastAll(res, data, 'Update failed');
                    return;
                }

                renderCart(data.cart);
                showGiftHints(data);
                pushToast({ type:'success', title:'Cart', messages:[`Updated (Location #${LOCATION_ID})`], timeout:1500 });
            }

            async function removeCartItem(itemId){
                clearQtyMessages();
                const { res, data } = await jsonFetch(`{{ url('/cart/item') }}/${itemId}`, 'DELETE', {});
                if (!res.ok || !data.success){
                    toastAll(res, data, 'Remove failed');
                    return;
                }
                renderCart(data.cart);
                showGiftHints(data);
                pushToast({ type:'success', title:'Cart', messages:['Removed'], timeout:1500 });
            }

            async function removeManualGift(itemId){
                clearQtyMessages();
                const { res, data } = await jsonFetch(`{{ url('/cart/gift/manual') }}/${itemId}`, 'DELETE', {});
                if (!res.ok || !data.success){
                    toastAll(res, data, 'Remove gift failed');
                    return;
                }
                renderCart(data.cart);
                pushToast({ type:'success', title:'Gift', messages:['Gift removed'], timeout:1600 });
            }

            async function clearCart(){
                clearQtyMessages();
                const { res, data } = await jsonFetch(`{{ route('cart.clear') }}`, 'DELETE', {});
                if (!res.ok || !data.success){
                    toastAll(res, data, 'Clear failed');
                    return;
                }
                renderCart(data.cart);
                pushToast({ type:'success', title:'Cart', messages:['Cleared'], timeout:1600 });
            }

            /* ===========================
                ✅ CART ROW EVENTS
                - qty/type/unit => debounce update
                - subtotal => "sweet logic" auto qty+unit
            =========================== */
            function scheduleRowUpdate(tr){
                if (!tr) return;
                clearTimeout(tr._uT);

                tr._uT = setTimeout(async () => {
                    const itemId = tr.dataset.itemId;
                    const selType = tr.querySelector('.priceTypeSelect');
                    const selUnit = tr.querySelector('.unitSelect');
                    const qtyInput = tr.querySelector('.qtyInput');

                    const priceType = selType?.value || 'retail';
                    const unit = normalizeUnit(selUnit?.value || tr.dataset.batchUnit || 'pcs');

                    let qty = Number(qtyInput?.value || 0);
                    if (!isFinite(qty) || qty <= 0) qty = 0.0001;

                    await updateCartItem(itemId, priceType, qty, unit);
                }, QTY_DELAY_MS);
            }

            function scheduleSubUpdate(tr){
                if (!tr) return;
                clearTimeout(tr._sT);

                tr._sT = setTimeout(async () => {
                    const itemId = tr.dataset.itemId;
                    const selType = tr.querySelector('.priceTypeSelect');
                    const selUnit = tr.querySelector('.unitSelect');
                    const qtyInput = tr.querySelector('.qtyInput');
                    const subInput = tr.querySelector('.subInput');
                    const unitPriceEl = tr.querySelector('.unitPrice');

                    const priceType = selType?.value || 'retail';

                    const batchUnit = normalizeUnit(tr.dataset.batchUnit || 'pcs');
                    const group = unitGroupFromBatchUnit(batchUnit);

                    const currentUnit = normalizeUnit(selUnit?.value || batchUnit);
                    const unitPrice = Number(unitPriceEl?.textContent || 0);

                    let targetSub = Number(subInput?.value || 0);
                    if (!isFinite(targetSub) || targetSub <= 0 || unitPrice <= 0){
                        // fallback: do nothing
                        return;
                    }

                    // price per base-unit (g/ml/pcs)
                    const currentFactor = factorToBase(group, currentUnit);
                    const pricePerBase = unitPrice / currentFactor;

                    if (pricePerBase <= 0) return;

                    // required base quantity
                    const baseQty = targetSub / pricePerBase;

                    // choose best unit and qty for that base qty
                    const pick = bestUnitForBaseQty(group, baseQty);

                    // safety small qty
                    let q = Number(pick.qty);
                    if (!isFinite(q) || q <= 0) q = 0.0001;

                    // round nicer
                    if (group === 'count') q = Math.max(0.0001, Math.round(q * 100) / 100);
                    else q = Math.max(0.0001, Math.round(q * 10000) / 10000);

                    // set UI instantly (nice feel) before server render
                    if (selUnit) selUnit.value = pick.unit;
                    if (qtyInput) qtyInput.value = String(q);

                    setHint(`✨ Sweet: ${money(targetSub)} → ${q} ${pick.unit} (batch ${batchUnit})`);

                    await updateCartItem(itemId, priceType, q, pick.unit);
                }, SUB_DELAY_MS);
            }

            function bindCartRowEvents(){
                document.querySelectorAll('#cartBody tr[data-item-id]').forEach(tr => {
                    const selType = tr.querySelector('.priceTypeSelect');
                    const selUnit = tr.querySelector('.unitSelect');
                    const qtyInput = tr.querySelector('.qtyInput');
                    const subInput = tr.querySelector('.subInput');

                    if (selType){
                        selType.onchange = () => scheduleRowUpdate(tr);
                    }
                    if (selUnit){
                        selUnit.onchange = () => scheduleRowUpdate(tr);
                    }
                    if (qtyInput){
                        qtyInput.oninput = () => scheduleRowUpdate(tr);
                        qtyInput.onchange = () => scheduleRowUpdate(tr);
                    }
                    if (subInput){
                        subInput.oninput = () => scheduleSubUpdate(tr);
                        subInput.onchange = () => scheduleSubUpdate(tr);
                    }
                });

                document.querySelectorAll('.removeBtn').forEach(btn => {
                    btn.onclick = async () => await removeCartItem(btn.dataset.itemId);
                });

                document.querySelectorAll('.removeManualGiftBtn').forEach(btn => {
                    btn.onclick = async () => await removeManualGift(btn.dataset.itemId);
                });
            }

            /* ===========================
                SEARCH (simple, no unit select)
            =========================== */
            function pickPrimary(images){
                if (!Array.isArray(images) || images.length === 0) return '';
                const p = images.find(x => Number(x.is_primary) === 1);
                return (p?.image_path) || images[0].image_path || '';
            }

            async function doSearch(term){
                if (term.length < 2){
                    searchResults.innerHTML = '';
                    return;
                }
                if (searching) return;
                searching = true;

                searchResults.innerHTML = `<div style="padding:12px 14px;" class="subtle"><span class="spin"></span>Searching...</div>`;

                const res = await fetch(`{{ route('cart.search') }}?q=${encodeURIComponent(term)}&location_id=${encodeURIComponent(LOCATION_ID)}`, {
                    headers: { 'Accept':'application/json' }
                });
                const data = await res.json().catch(() => []);
                searching = false;

                searchResults.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0){
                    searchResults.innerHTML = `<div style="padding:12px 14px;" class="subtle">No in-stock FIFO batches found (this location)</div>`;
                    return;
                }

                data.forEach(item => {
                    const img = pickPrimary(item.images);

                    const retail = Number(item.sell_price || 0);
                    const whole = Number(item.whole_sell_price || 0);
                    const customer = Number(item.customer_whole_price || 0);

                    const row = document.createElement('div');
                    row.className = 'result-row';

                    row.innerHTML = `
                        <div class="thumb">${img ? `<img src="${img}" alt="">` : ''}</div>
                        <div style="flex:1;">
                            <div class="r-title">${escapeHtml(item.name ?? '')} <span class="subtle" style="font-weight:900;">(${escapeHtml(item.barcode ?? '')})</span></div>
                            <div class="r-meta" style="margin-top:2px;">
                                Batch: <b>${escapeHtml(item.batch_sku ?? '')}</b> •
                                Stock: <span class="pill ${Number(item.quantity)>5?'success':'warning'}">${escapeHtml(item.quantity ?? 0)}</span>
                                <span class="pill" style="margin-left:6px;">Loc #${LOCATION_ID}</span>
                            </div>
                            <div class="r-meta" style="margin-top:6px; display:flex; gap:8px; flex-wrap:wrap;">
                                <span class="pill">Retail: <b>${money(retail)}</b></span>
                                <span class="pill">Whole: <b>${money(whole)}</b></span>
                                <span class="pill">Customer: <b>${money(customer)}</b></span>
                            </div>
                        </div>
                        <div class="actions">
                            <select class="selectx">
                                <option value="retail" selected>Retail</option>
                                <option value="whole">Whole</option>
                                <option value="customer_whole">Customer</option>
                            </select>
                            <input class="qtyx" type="number" min="0.0001" step="0.0001" value="1">
                            <button class="btnx" type="button">Add</button>
                        </div>
                    `;

                    const sel = row.querySelector('select');
                    const qty = row.querySelector('input');
                    const btn = row.querySelector('button');

                    row.addEventListener('click', (e) => {
                        const tag = (e.target.tagName || '').toLowerCase();
                        if (['select','option','input','button'].includes(tag)) return;
                        btn.click();
                    });

                    btn.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        await addToCart(item.batch_id, Math.max(0.0001, Number(qty.value || 1)), sel.value, btn);
                    });

                    searchResults.appendChild(row);
                });
            }

            searchInput.addEventListener('input', function(){
                clearTimeout(debounceTimer);
                const term = this.value.trim();
                debounceTimer = setTimeout(() => doSearch(term), 220);
            });

            clearSearchBtn.addEventListener('click', () => {
                searchInput.value = '';
                searchResults.innerHTML = '';
                searchInput.focus();
            });

            clearCartBtn.addEventListener('click', async () => await clearCart());
            dismissHintBtn.addEventListener('click', () => setHint("✅ Ready."));

            /* ===========================
                GIFT MODAL (unchanged)
            =========================== */
            let giftDebounce = null;
            let giftSearching = false;

            function showGiftModalHint(msg){
                giftModalHint.textContent = msg || '';
            }

            function setGiftModalCustomerLine(){
                if (!selectedCustomer){
                    giftModalCustomerLine.textContent = 'Customer: Guest';
                    showGiftModalHint('Select a customer first.');
                    return;
                }
                giftModalCustomerLine.textContent = `Customer: ${selectedCustomer.name || 'Customer'} (${selectedCustomer.phone || '-'})`;
                showGiftModalHint('');
            }

            function openGiftModal(){
                if (!selectedCustomer){
                    pushToast({ type:'warning', title:'Customer required', messages:['Please select a customer first.'], timeout:2800 });
                    return;
                }
                setGiftModalCustomerLine();
                openWrap(giftModalWrap);
                giftSearchInput.value = '';
                giftSearchResults.innerHTML = '';
                giftSearchInput.focus();
            }

            openGiftModalBtn?.addEventListener('click', openGiftModal);
            openGiftModalBtn2?.addEventListener('click', openGiftModal);

            async function giftSearch(term){
                if (!term || term.length < 2){
                    giftSearchResults.innerHTML = '';
                    return;
                }
                if (giftSearching) return;
                giftSearching = true;

                giftSearchResults.innerHTML = `<div style="padding:12px 14px;" class="subtle"><span class="spin"></span>Searching gifts...</div>`;

                const res = await fetch(`{{ route('products.quick.search') }}?q=${encodeURIComponent(term)}&location_id=${encodeURIComponent(LOCATION_ID)}`, {
                    headers: { 'Accept':'application/json' }
                });
                const rows = await res.json().catch(() => []);
                giftSearching = false;

                giftSearchResults.innerHTML = '';

                if (!Array.isArray(rows) || rows.length === 0){
                    giftSearchResults.innerHTML = `<div style="padding:12px 14px;" class="subtle">No products found</div>`;
                    return;
                }

                rows.forEach(p => {
                    const img = (p.images && p.images.length) ? pickPrimary(p.images) : '';

                    const row = document.createElement('div');
                    row.className = 'result-row';

                    row.innerHTML = `
                        <div class="thumb">${img ? `<img src="${img}" alt="">` : ''}</div>
                        <div style="flex:1;">
                            <div class="r-title">${escapeHtml(p.name ?? '')} <span class="subtle" style="font-weight:900;">(${escapeHtml(p.barcode ?? '')})</span></div>
                            <div class="r-meta" style="margin-top:2px;">Tap to add as gift (Loc #${LOCATION_ID})</div>
                        </div>
                        <div class="actions">
                            <input class="qtyx" type="number" min="0.0001" step="0.0001" value="1" style="width:88px;">
                            <button class="btnx" type="button">Add Gift</button>
                        </div>
                    `;

                    const qtyInput = row.querySelector('input');
                    const btn = row.querySelector('button');

                    row.addEventListener('click', (e) => {
                        const tag = (e.target.tagName || '').toLowerCase();
                        if (['input','button'].includes(tag)) return;
                        btn.click();
                    });

                    btn.addEventListener('click', async (e) => {
                        e.stopPropagation();

                        btn.disabled = true;
                        const old = btn.innerHTML;
                        btn.innerHTML = `<span class="spin"></span>Adding`;

                        const { res: r, data } = await jsonFetch(`{{ route('cart.manual.gift.add') }}`, 'POST', {
                            product_id: p.id,
                            quantity: Math.max(0.0001, Number(qtyInput.value || 1))
                        });

                        btn.disabled = false;
                        btn.innerHTML = old;

                        if (!r.ok || !data.success){
                            toastAll(r, data, 'Gift add failed');
                            return;
                        }

                        renderCart(data.cart);
                        pushToast({ type:'success', title:'Gift', messages:[`Gift added (Loc #${LOCATION_ID})`], timeout:1600 });
                        closeWrap(giftModalWrap);
                    });

                    giftSearchResults.appendChild(row);
                });
            }

            giftSearchInput.addEventListener('input', () => {
                clearTimeout(giftDebounce);
                const term = giftSearchInput.value.trim();
                giftDebounce = setTimeout(() => giftSearch(term), 220);
            });

            /* ===========================
                CHECKOUT (unchanged)
            =========================== */
            function validatePaymentsBeforeSubmit(net){
                const rows = getPaymentRowsData();
                if (rows.length === 0) return { ok:true, payments:[] };

                const payments = [];
                for (const p of rows){
                    delete p._rowEl;

                    if (!p.method) return { ok:false, msg:'Please select payment method.' };
                    if (p.amount <= 0) return { ok:false, msg:'Payment amount must be greater than 0.' };
                    if (p.channel === 'online' && !p.trx_id) return { ok:false, msg:'Transaction ID is required for online payment.' };

                    payments.push({
                        channel: p.channel,
                        method: p.method,
                        amount: p.amount,
                        trx_id: p.trx_id || null,
                        account_label: p.account_label || null,
                    });
                }
                return { ok:true, payments };
            }

            checkoutPayBtn.addEventListener('click', async () => {
                const { ptsUse, rewardAmount, disc, net } = calcPayable();

                const valid = validatePaymentsBeforeSubmit(net);
                if (!valid.ok){
                    pushToast({ type:'warning', title:'Payment', messages:[valid.msg || 'Payment invalid'], timeout:3200 });
                    return;
                }

                checkoutPayBtn.disabled = true;
                const oldText = checkoutPayBtn.innerHTML;
                checkoutPayBtn.innerHTML = `<span class="spin"></span>Processing`;

                const payload = {
                    location_id: LOCATION_ID,
                    rewards_points_used: ptsUse,
                    rewards_amount_used: rewardAmount,
                    order_discount: disc,
                    payment_note: (paymentNoteEl.value || '').trim() || null,
                    apply_balance_mode: autoBalanceModeEl.value || 'auto',
                };
                if (valid.payments.length > 0) payload.payments = valid.payments;

                const res = await fetch(`{{ route('cart.checkout') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json().catch(() => ({}));

                checkoutPayBtn.disabled = false;
                checkoutPayBtn.innerHTML = oldText;

                if (!res.ok || !data.success){
                    toastAll(res, data, 'Checkout failed');
                    return;
                }

                pushToast({ type:'success', title:'Checkout', messages:[`Checkout complete • Order: ${data.order?.order_no || ''}`], timeout:2800 });

                if (data.invoice_url){
                    window.location.href = data.invoice_url;
                    return;
                }
                window.location.reload();
            });

            /* ===========================
                CUSTOMER SEARCH + SET + CREATE (unchanged)
            =========================== */
            const customerSearchInput = document.getElementById('customerSearch');
            const customerResultsBox = document.getElementById('customerResults');
            const selectedCustomerBox = document.getElementById('selectedCustomer');

            const saveCustomerBtn = document.getElementById('saveCustomerBtn');
            const nameInput = document.getElementById('newCustomerName');
            const phoneInput = document.getElementById('newCustomerPhone');

            let cDebounce = null;
            let activeQuery = '';

            async function setCustomer(customer){
                const res = await fetch(`{{ route('cart.customer.set') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':'{{ csrf_token() }}',
                        'Accept':'application/json'
                    },
                    body: JSON.stringify({ customer_id: customer ? customer.id : null })
                });

                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success){
                    toastAll(res, data, 'Customer');
                    return;
                }

                if (!customer){
                    selectedCustomerBox.textContent = '👤 Guest customer';
                    customerResultsBox.innerHTML = '';
                    customerSearchInput.value = '';
                    renderOrderCustomer(null);
                    pushToast({ type:'success', title:'Customer', messages:['Guest selected'], timeout:1600 });
                    return;
                }

                const c = data.customer;
                selectedCustomerBox.innerHTML = `✅ <strong>${escapeHtml(c.name)}</strong> — Points: ${money(c.reward_points||0)}`;
                customerResultsBox.innerHTML = '';
                customerSearchInput.value = '';
                renderOrderCustomer(c);

                pushToast({ type:'success', title:'Customer', messages:[`${c.name} selected`], timeout:1600 });
            }

            customerSearchInput.addEventListener('input', () => {
                clearTimeout(cDebounce);

                const q = customerSearchInput.value.trim();
                if (q.length < 2){
                    customerResultsBox.innerHTML = '';
                    return;
                }

                cDebounce = setTimeout(async () => {
                    activeQuery = q;
                    const res = await fetch(`{{ route('customers.quick.search') }}?q=${encodeURIComponent(q)}`);
                    const rows = await res.json().catch(() => []);
                    if (activeQuery !== q) return;

                    customerResultsBox.innerHTML = '';

                    const guestRow = document.createElement('div');
                    guestRow.className = 'result-row';
                    guestRow.innerHTML = `<div><strong>Guest</strong><div class="subtle">No customer selected</div></div>`;
                    guestRow.onclick = () => setCustomer(null);
                    customerResultsBox.appendChild(guestRow);

                    if (!rows.length){
                        customerResultsBox.insertAdjacentHTML('beforeend', `<div class="subtle" style="padding:8px">No customers found</div>`);
                        return;
                    }

                    rows.forEach(c => {
                        const row = document.createElement('div');
                        row.className = 'result-row';
                        row.innerHTML = `
                            <div>
                                <strong>${escapeHtml(c.name)}</strong> (${escapeHtml(c.phone ?? '-')})
                                <div class="subtle">
                                    Due: ${money(c.due_balance||0)} |
                                    Advance: ${money(c.advance_balance||0)} |
                                    Points: ${money(c.reward_points||0)}
                                </div>
                            </div>
                        `;
                        row.onclick = () => setCustomer(c);
                        customerResultsBox.appendChild(row);
                    });
                }, 250);
            });

            saveCustomerBtn.addEventListener('click', async () => {
                const name = nameInput.value.trim();
                if (!name){
                    pushToast({ type:'warning', title:'Customer', messages:['Name required'], timeout:2500 });
                    return;
                }
                const phone = phoneInput.value.trim();

                const res = await fetch(`{{ route('customers.store') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type':'application/json',
                        'X-CSRF-TOKEN':'{{ csrf_token() }}',
                        'Accept':'application/json'
                    },
                    body: JSON.stringify({ name, phone })
                });

                const data = await res.json().catch(() => ({}));
                if (!res.ok || (!data.success && !data.customer)){
                    toastAll(res, data, 'Create customer');
                    return;
                }

                await setCustomer(data.customer);
                nameInput.value = '';
                phoneInput.value = '';
                closeWrap(customerModalWrap);
            });

            /* init */
            bindCartRowEvents();
            updateRowNumbers();
            calcPayable();
        });
    </script>

</div>
@endsection
