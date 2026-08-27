@extends('layouts.app')

@section('content')
    <div class="container py-3">

        <style>
            /* ===== DESIGN SYSTEM ===== */
            :root {
                --background: oklch(0.145 0 0);
                --foreground: oklch(0.985 0 0);
                --card: oklch(0.205 0 0);
                --card-foreground: oklch(0.985 0 0);
                --popover: oklch(0.205 0 0);
                --popover-foreground: oklch(0.985 0 0);
                --primary: oklch(0.488 0.243 264.376);
                --primary-foreground: oklch(0.985 0 0);
                --secondary: oklch(0.269 0 0);
                --secondary-foreground: oklch(0.985 0 0);
                --muted: oklch(0.269 0 0);
                --muted-foreground: oklch(0.708 0 0);
                --accent: oklch(0.269 0 0);
                --accent-foreground: oklch(0.985 0 0);
                --destructive: oklch(0.704 0.191 22.216);
                --destructive-foreground: oklch(0.985 0 0);
                --border: oklch(0.269 0 0);
                --input: oklch(0.269 0 0);
                --ring: oklch(0.488 0.243 264.376);
                --radius: 0.625rem;
                --success: oklch(0.696 0.17 162.48);
                --warning: oklch(0.769 0.188 70.08);
                --danger: oklch(0.704 0.191 22.216);
                --info: oklch(0.488 0.243 264.376);
            }

            .page {
                color: var(--foreground);
                max-width: 1600px;
                margin: 0 auto;
            }

            /* ===== CARDS ===== */
            .cardx {
                background: var(--card);
                color: var(--card-foreground);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                box-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
                overflow: hidden;
                transition: box-shadow 250ms ease, transform 250ms ease;
            }

            .cardx:hover {
                box-shadow: 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25);
            }

            .cardx-hd {
                padding: 12px 14px;
                border-bottom: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
                background: var(--secondary);
            }

            .title {
                font-size: 18px;
                font-weight: 950;
                margin: 0;
                letter-spacing: .2px;
                color: var(--foreground);
            }

            .subtle {
                font-size: 12px;
                color: var(--muted-foreground);
            }

            .strong {
                font-weight: 900;
            }

            /* ===== INPUTS ===== */
            .inputx {
                width: 100%;
                background: var(--input);
                border: 1px solid var(--border);
                color: var(--foreground);
                border-radius: calc(var(--radius) - 6px);
                padding: 10px 12px;
                outline: none;
                transition: box-shadow 150ms ease, border-color 150ms ease;
                font-weight: 500;
            }

            .inputx:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 4px color-mix(in oklch, var(--primary) 20%, transparent 80%);
            }

            /* ===== BUTTONS ===== */
            .btnx {
                border: 1px solid transparent;
                background: var(--primary);
                color: #fff;
                border-radius: calc(var(--radius) - 6px);
                padding: 8px 12px;
                font-weight: 900;
                user-select: none;
                cursor: pointer;
                transition: transform 150ms ease, background 150ms ease, opacity 150ms ease;
                font-size: 13px;
            }

            .btnx:hover {
                background: color-mix(in oklch, var(--primary) 80%, transparent 20%);
                transform: translateY(-1px);
            }

            .btnx:disabled {
                opacity: .65;
                cursor: not-allowed;
                transform: none;
            }

            .btnx-ghost {
                background: transparent;
                border: 1px solid var(--border);
                color: var(--foreground);
            }

            .btnx-ghost:hover {
                background: var(--secondary);
                transform: none;
            }

            .btnx-sm {
                padding: 4px 10px;
                font-size: 12px;
            }

            .btnx.icon {
                padding: 6px 10px;
                border-radius: 12px;
                font-size: 14px;
                line-height: 1;
            }

            /* ===== SELECTS ===== */
            .selectx {
                height: 34px;
                padding: 0 10px;
                border-radius: calc(var(--radius) - 8px);
                border: 1px solid var(--border);
                background: var(--input);
                color: var(--foreground);
                outline: none;
                font-size: 13px;
                transition: box-shadow 150ms ease, border-color 150ms ease;
                font-weight: 500;
                cursor: pointer;
                min-width: 60px;
            }

            .selectx:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 4px color-mix(in oklch, var(--primary) 20%, transparent 80%);
            }

            /* ===== RESULTS / LISTS ===== */
            .result-list {
                max-height: 420px;
                overflow: auto;
            }

            .result-row {
                display: flex;
                gap: 12px;
                align-items: center;
                padding: 10px 12px;
                border-bottom: 1px solid var(--border);
                cursor: pointer;
                transition: background 150ms ease, transform 150ms ease;
            }

            .result-row:hover {
                background: color-mix(in oklch, var(--primary) 25%, transparent 75%);
                transform: translateY(-1px);
            }

            .thumb {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                background: var(--secondary);
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
                color: var(--foreground);
            }

            .r-meta {
                font-size: 12px;
                color: var(--muted-foreground);
            }

            /* ===== PILLS ===== */
            .pill {
                font-size: 11px;
                padding: 2px 9px;
                border-radius: 999px;
                border: 1px solid var(--border);
                background: var(--secondary);
                color: var(--foreground);
                font-weight: 900;
                white-space: nowrap;
                display: inline-block;
            }

            .pill.success {
                border-color: var(--success);
                background: color-mix(in oklch, var(--success) 15%, transparent 85%);
                color: var(--success);
            }

            .pill.warning {
                border-color: var(--warning);
                background: color-mix(in oklch, var(--warning) 15%, transparent 85%);
                color: var(--warning);
            }

            .pill.danger {
                border-color: var(--danger);
                background: color-mix(in oklch, var(--danger) 15%, transparent 85%);
                color: var(--danger);
            }

            /* ===== TABLE ===== */
            .table-wrap {
                overflow: auto;
            }

            .tablex {
                width: 100%;
                min-width: 1040px;
                border-collapse: separate;
                border-spacing: 0;
                font-size: 13px;
            }

            .tablex th,
            .tablex td {
                padding: 10px 12px;
                border-bottom: 1px solid var(--border);
                vertical-align: middle;
                color: var(--foreground);
            }

            .tablex thead th {
                position: sticky;
                top: 0;
                background: var(--secondary);
                z-index: 2;
                font-size: 11px;
                letter-spacing: .25px;
                text-transform: uppercase;
                color: var(--muted-foreground);
                font-weight: 900;
                white-space: nowrap;
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
                background: var(--secondary);
                flex: 0 0 auto;
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
                font-size: 11px;
                color: var(--muted-foreground);
            }

            .price-highlight {
                display: inline-block;
                padding: 2px 9px;
                border-radius: 999px;
                border: 1px solid var(--primary);
                background: color-mix(in oklch, var(--primary) 15%, transparent 85%);
                color: var(--primary);
                font-weight: 800;
                font-size: 0.95em;
            }

            .rowNo {
                display: inline-flex;
                width: 28px;
                height: 28px;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                border: 1px solid var(--border);
                background: var(--secondary);
                font-weight: 950;
                font-variant-numeric: tabular-nums;
                color: var(--foreground);
                font-size: 12px;
            }

            .empty-state {
                padding: 18px 14px;
                text-align: center;
                color: var(--muted-foreground);
            }

            tr.gift-row {
                background: color-mix(in oklch, var(--primary) 12%, transparent 88%);
            }

            .giftTag {
                margin-left: 6px;
            }

            /* ===== SPINNER ===== */
            .spin {
                display: inline-block;
                width: 14px;
                height: 14px;
                border-radius: 999px;
                border: 2px solid var(--border);
                border-top-color: var(--primary);
                animation: sp 800ms linear infinite;
                vertical-align: -2px;
                margin-right: 6px;
            }

            @keyframes sp {
                to {
                    transform: rotate(360deg);
                }
            }

            /* ===== LAYOUT ===== */
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
                    max-height: 600px;
                }
            }

            .cart-panel .cardx-hd {
                position: sticky;
                top: 0;
                z-index: 5;
                background: var(--card);
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
                background: var(--card);
                border-top: 1px solid var(--border);
                padding: 10px 14px;
            }

            .hintbar {
                margin-top: 12px;
                padding: 10px 14px;
                border: 1px solid var(--border);
                border-radius: var(--radius);
                background: var(--input);
                display: flex;
                gap: 10px;
                align-items: center;
                justify-content: space-between;
            }

            /* ===== TOASTS ===== */
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
                border: 1px solid var(--border);
                background: var(--card);
                color: var(--foreground);
                border-radius: 16px;
                box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.4);
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
                border-bottom: 1px solid var(--border);
                background: var(--secondary);
            }

            .toastx-title {
                font-weight: 950;
                font-size: 13px;
                display: flex;
                align-items: center;
                gap: 8px;
                color: var(--foreground);
            }

            .toastx-body {
                padding: 10px 12px 12px;
                font-size: 13px;
                line-height: 1.35;
                white-space: pre-line;
                color: var(--foreground);
            }

            .toastx-close {
                border: 1px solid var(--border);
                background: transparent;
                color: var(--foreground);
                border-radius: 12px;
                padding: 4px 10px;
                font-weight: 950;
                cursor: pointer;
                transition: background 150ms ease;
            }

            .toastx-close:hover {
                background: var(--secondary);
            }

            .toastx-progress {
                height: 3px;
                background: var(--border);
            }

            .toastx-progress>div {
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
                border-color: var(--success);
            }

            .toastx[data-type="warning"] {
                border-color: var(--warning);
            }

            .toastx[data-type="danger"] {
                border-color: var(--danger);
            }

            .dot {
                width: 10px;
                height: 10px;
                border-radius: 999px;
                background: var(--primary);
                box-shadow: 0 0 0 6px color-mix(in oklch, var(--primary) 20%, transparent 80%);
            }

            .toastx[data-type="success"] .dot {
                background: var(--success);
                box-shadow: 0 0 0 6px color-mix(in oklch, var(--success) 20%, transparent 80%);
            }

            .toastx[data-type="warning"] .dot {
                background: var(--warning);
                box-shadow: 0 0 0 6px color-mix(in oklch, var(--warning) 20%, transparent 80%);
            }

            .toastx[data-type="danger"] .dot {
                background: var(--danger);
                box-shadow: 0 0 0 6px color-mix(in oklch, var(--danger) 20%, transparent 80%);
            }

            /* ===== MODALS ===== */
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
                background: var(--card);
                border: 1px solid var(--border);
                max-height: 90vh;
                overflow: auto;
            }

            @media (max-width: 576px) {
                .modalwrap.show {
                    align-items: end;
                }

                .modalx {
                    width: 100%;
                    max-height: 95vh;
                    border-radius: var(--radius) var(--radius) 0 0;
                }
            }

            /* ===== PAYMENTS ===== */
            .payment-row {
                margin-bottom: 10px;
                padding: 10px 14px;
                border: 1px solid var(--border);
                border-radius: calc(var(--radius) - 6px);
                background: var(--input);
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 8px 12px;
                align-items: end;
            }

            .payment-row .label {
                font-size: 11px;
                color: var(--muted-foreground);
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .3px;
                display: block;
                margin-bottom: 3px;
            }

            .payment-row .full-width {
                grid-column: 1 / -1;
            }

            .payment-row .remove-payment {
                justify-self: end;
                align-self: center;
            }

            .payment-row .pay-hint {
                grid-column: 1 / -1;
                font-size: 12px;
                color: var(--muted-foreground);
            }

            /* ===== UTILITY ===== */
            .gap-2 {
                gap: 8px;
            }

            .gap-3 {
                gap: 12px;
            }

            .mt-1 {
                margin-top: 4px;
            }

            .mt-2 {
                margin-top: 8px;
            }

            .mb-1 {
                margin-bottom: 4px;
            }

            .mb-2 {
                margin-bottom: 8px;
            }

            .mb-3 {
                margin-bottom: 12px;
            }

            .flex {
                display: flex;
            }

            .flex-between {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .flex-wrap {
                flex-wrap: wrap;
            }

            .items-center {
                align-items: center;
            }

            .w-full {
                width: 100%;
            }

            .text-center {
                text-align: center;
            }

            .text-right {
                text-align: right;
            }

            .text-success {
                color: var(--success);
            }

            .text-warning {
                color: var(--warning);
            }

            .text-danger {
                color: var(--danger);
            }

            .text-info {
                color: var(--info);
            }

            .grid-2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .grid-3 {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 10px;
            }

            @media (max-width: 576px) {

                .grid-2,
                .grid-3 {
                    grid-template-columns: 1fr;
                }
            }

            /* ===== EDITABLE DATA HIGHLIGHT ===== */
            .editable {
                position: relative;
                transition: background 150ms ease;
            }

            .editable:hover {
                background: color-mix(in oklch, var(--primary) 8%, transparent 92%);
                border-radius: 4px;
            }

            .editable-input {
                background: transparent;
                border: 1px dashed var(--border);
                color: var(--foreground);
                padding: 2px 6px;
                border-radius: 4px;
                font-weight: 800;
                font-size: inherit;
                width: 80px;
                text-align: right;
                font-variant-numeric: tabular-nums;
                transition: border-color 150ms ease, background 150ms ease;
            }

            .editable-input:focus {
                border-color: var(--primary);
                background: var(--input);
                outline: none;
                box-shadow: 0 0 0 3px color-mix(in oklch, var(--primary) 20%, transparent 80%);
            }

            .editable-input:hover {
                border-color: var(--primary);
                background: color-mix(in oklch, var(--primary) 10%, transparent 90%);
            }

            .editable-label {
                font-size: 10px;
                color: var(--muted-foreground);
                display: block;
                margin-top: 2px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .3px;
            }

            .copy-btn {
                background: transparent;
                border: 1px solid var(--border);
                color: var(--muted-foreground);
                border-radius: 6px;
                padding: 2px 8px;
                font-size: 11px;
                cursor: pointer;
                transition: all 150ms ease;
                font-weight: 600;
            }

            .copy-btn:hover {
                border-color: var(--primary);
                color: var(--primary);
                background: color-mix(in oklch, var(--primary) 10%, transparent 90%);
            }

            .copy-btn.copied {
                border-color: var(--success);
                color: var(--success);
                background: color-mix(in oklch, var(--success) 10%, transparent 90%);
            }

            /* Quantity message */
            .qty-msg {
                display: none;
                margin-top: 4px;
                font-size: 11px;
                color: var(--warning);
                font-weight: 700;
            }

            /* Scrollbar styling */
            .result-list::-webkit-scrollbar,
            .cart-scroll::-webkit-scrollbar,
            .table-wrap::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            .result-list::-webkit-scrollbar-track,
            .cart-scroll::-webkit-scrollbar-track,
            .table-wrap::-webkit-scrollbar-track {
                background: var(--secondary);
                border-radius: 3px;
            }

            .result-list::-webkit-scrollbar-thumb,
            .cart-scroll::-webkit-scrollbar-thumb,
            .table-wrap::-webkit-scrollbar-thumb {
                background: var(--primary);
                border-radius: 3px;
            }

            .result-list::-webkit-scrollbar-thumb:hover,
            .cart-scroll::-webkit-scrollbar-thumb:hover,
            .table-wrap::-webkit-scrollbar-thumb:hover {
                background: color-mix(in oklch, var(--primary) 70%, transparent 30%);
            }
        </style>

        @php
            $currentLocationId = (int) session('location_id', 1);

            // Helper: determine unit group
            $unitGroup = function ($u) {
                $u = strtolower(trim((string) $u));
                if (in_array($u, ['kg', 'kilogram', 'kilograms'])) {
                    return 'weight';
                }
                if (in_array($u, ['g', 'gm', 'gram', 'grams'])) {
                    return 'weight';
                }
                if (in_array($u, ['l', 'lt', 'liter', 'litre', 'liters', 'litres'])) {
                    return 'volume';
                }
                if (in_array($u, ['ml', 'milliliter', 'millilitre', 'milliliters', 'millilitres'])) {
                    return 'volume';
                }
                return 'count';
            };

            // Helper: format money
            function fm($n)
            {
                return number_format((float) $n, 2);
            }
        @endphp

        <div class="page">

            <!-- ===== HEADER ===== -->
            <div class="flex-between mb-3 flex-wrap">
                <div>
                    <div class="subtle">Modern POS / Cart</div>
                    <h3 class="title m-0">Shopping Cart</h3>
                </div>
                <div class="flex gap-2 items-center" style="flex-wrap:wrap;">
                    <div style="min-width:200px;">
                        <div class="subtle">Location</div>
                        <select class="selectx" id="locationSelect" style="height:42px; width:100%;">
                            @if (isset($locations) && count($locations))
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}"
                                        {{ (int) $loc->id === $currentLocationId ? 'selected' : '' }}>
                                        {{ $loc->name ?? 'Location #' . $loc->id }}
                                    </option>
                                @endforeach
                            @else
                                <option value="{{ $currentLocationId }}" selected>Location #{{ $currentLocationId }}
                                </option>
                            @endif
                        </select>
                    </div>
                    <button class="btnx btnx-ghost" id="refreshBtn" type="button" title="Refresh cart">⟳</button>
                </div>
            </div>

            <!-- ===== TOASTS ===== -->
            <div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>
            <div class="overlay" id="overlay"></div>

            <!-- ============================================================ -->
            <!-- ===== CUSTOMER SECTION ===== -->
            <!-- ============================================================ -->
            <div class="cardx mb-3">
                <div class="cardx-hd">
                    <div class="strong">Customer</div>
                    <div class="flex gap-2">
                        <button class="btnx btnx-ghost btnx-sm" id="addCustomerBtn" type="button">+ New</button>
                        <button class="btnx btnx-ghost btnx-sm" id="clearCustomerBtn" type="button">✕ Clear</button>
                    </div>
                </div>

                <div style="padding:12px;">
                    <input class="inputx" id="customerSearch" placeholder="Search name / phone" autocomplete="off">
                    <div id="customerResults" class="result-list" style="margin-top:8px;"></div>
                    <div id="selectedCustomer" class="subtle mt-2">👤 Guest customer</div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- ===== NEW CUSTOMER MODAL ===== -->
            <!-- ============================================================ -->
            <div class="modalwrap" id="customerModalWrap" aria-hidden="true">
                <div id="customerModal" class="cardx modalx">
                    <div class="cardx-hd">
                        <div class="strong">New Customer</div>
                        <button type="button" class="btnx btnx-ghost btnx-sm" id="closeCustomerModalBtn">Close</button>
                    </div>
                    <div style="padding:12px;">
                        <input class="inputx mb-2" id="newCustomerName" placeholder="Name *" autofocus>
                        <input class="inputx mb-2" id="newCustomerPhone" placeholder="Phone">
                        <div class="flex flex-wrap" style="justify-content:flex-end; gap:8px;">
                            <button class="btnx btnx-ghost" id="cancelCustomerBtn" type="button">Cancel</button>
                            <button class="btnx" id="saveCustomerBtn" type="button">Save</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- ===== MAIN GRID ===== -->
            <!-- ============================================================ -->
            <div class="pos-shell">

                <!-- ===== LEFT: SEARCH ===== -->
                <div class="cardx">
                    <div class="cardx-hd">
                        <div>
                            <div class="strong">Search Products</div>
                            <div class="subtle">Name / Barcode — FIFO batches in stock (Location Based)</div>
                        </div>
                        <button class="btnx btnx-ghost btnx-sm" id="clearSearchBtn" type="button">Clear</button>
                    </div>

                    <div style="padding:12px 14px;">
                        <input class="inputx" type="text" id="cartSearch" placeholder="Type 2+ characters...">
                    </div>

                    <div id="searchResults" class="result-list"></div>
                </div>

                <!-- ===== RIGHT: CART ===== -->
                <div class="cardx cart-panel">
                    <div class="cardx-hd">
                        <div>
                            <div class="strong">Cart Items</div>
                            <div class="subtle">Location #<span id="locBadge">{{ $currentLocationId }}</span> • Gifts
                                auto-added</div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btnx btnx-ghost btnx-sm" type="button" id="clearCartBtn">Clear</button>
                            <button class="btnx btnx-ghost btnx-sm" type="button" id="openGiftModalBtn">🎁 Gift</button>
                        </div>
                    </div>

                    <div class="cart-scroll">
                        <div class="table-wrap">
                            <table class="tablex">
                                <thead>
                                    <tr>
                                        <th style="width:44px;">#</th>
                                        <th style="width:60px;">Img</th>
                                        <th>Name</th>
                                        <th style="width:120px;">Type</th>
                                        <th style="width:100px;">Unit</th>
                                        <th class="money" style="width:110px;">Price</th>
                                        <th style="width:120px;">Qty</th>
                                        <th class="money" style="width:140px;">Discount</th>
                                        <th class="money" style="width:140px;">Subtotal</th>
                                        <th style="width:60px;"></th>
                                    </tr>
                                </thead>

                                <tbody id="cartBody">
                                    @forelse($cart->items as $item)
                                        @php
                                            $isGift = (bool) ($item->is_gift ?? false);
                                            $giftSource = $item->gift_source ?? null;

                                            $batchUnit = $item->batch?->unit ?? ($item->batch_unit ?? 'pcs');
                                            $grp = $unitGroup($batchUnit);

                                            $saleUnit = strtolower($item->unit ?? ($batchUnit ?? 'pcs'));
                                            // normalize
                                            if (in_array($saleUnit, ['gm', 'gram', 'grams'])) {
                                                $saleUnit = 'g';
                                            }
                                            if (in_array($saleUnit, ['kilogram', 'kilograms'])) {
                                                $saleUnit = 'kg';
                                            }
                                            if (in_array($saleUnit, ['liter', 'litre', 'liters', 'litres', 'lt'])) {
                                                $saleUnit = 'l';
                                            }
                                            if (
                                                in_array($saleUnit, [
                                                    'milliliter',
                                                    'millilitre',
                                                    'milliliters',
                                                    'millilitres',
                                                ])
                                            ) {
                                                $saleUnit = 'ml';
                                            }
                                        @endphp

                                        <tr data-item-id="{{ $item->id }}"
                                            data-batch-unit="{{ strtolower($batchUnit ?? 'pcs') }}"
                                            class="{{ $isGift ? 'gift-row' : '' }}">
                                            <td><span class="rowNo"></span></td>

                                            <td>
                                                <div class="mini-img">
                                                    @if ($item->image)
                                                        <img src="{{ asset('storage/' . $item->image->image_path) }}"
                                                            alt="{{ $item->product?->name ?? 'Product' }}" width="80"
                                                            style="border-radius: 10px;">
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="namecell">
                                                <div class="nm">
                                                    {{ $item->product?->name }}
                                                    @if ($isGift)
                                                        <span class="pill success giftTag">GIFT •
                                                            {{ $giftSource }}</span>
                                                    @endif
                                                </div>
                                                <div class="bc">{{ $item->product?->barcode }}</div>
                                                <div class="sku">Batch: {{ $item->batch?->batch_sku }}</div>
                                            </td>

                                            <td>
                                                @if ($isGift)
                                                    <span class="pill">gift</span>
                                                @else
                                                    <select class="selectx priceTypeSelect"
                                                        data-item-id="{{ $item->id }}">
                                                        <option value="retail"
                                                            {{ $item->price_type === 'retail' ? 'selected' : '' }}>Retail
                                                        </option>
                                                        <option value="whole"
                                                            {{ $item->price_type === 'whole' ? 'selected' : '' }}>Whole
                                                        </option>
                                                        <option value="customer_whole"
                                                            {{ $item->price_type === 'customer_whole' ? 'selected' : '' }}>
                                                            Customer</option>
                                                    </select>
                                                @endif
                                            </td>

                                            <td>
                                                @if ($isGift)
                                                    <span class="pill">{{ $saleUnit }}</span>
                                                    <div class="subtle">Fixed</div>
                                                @else
                                                    @if ($grp === 'weight')
                                                        <select class="selectx unitSelect"
                                                            data-item-id="{{ $item->id }}">
                                                            <option value="kg"
                                                                {{ $saleUnit === 'kg' ? 'selected' : '' }}>kg
                                                            </option>
                                                            <option value="g"
                                                                {{ $saleUnit === 'g' ? 'selected' : '' }}>g
                                                            </option>
                                                        </select>
                                                        <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                                    @elseif($grp === 'volume')
                                                        <select class="selectx unitSelect"
                                                            data-item-id="{{ $item->id }}">
                                                            <option value="l"
                                                                {{ $saleUnit === 'l' ? 'selected' : '' }}>L
                                                            </option>
                                                            <option value="ml"
                                                                {{ $saleUnit === 'ml' ? 'selected' : '' }}>ml
                                                            </option>
                                                        </select>
                                                        <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                                    @else
                                                        <select class="selectx unitSelect"
                                                            data-item-id="{{ $item->id }}">
                                                            <option value="pcs"
                                                                {{ $saleUnit === 'pcs' ? 'selected' : '' }}>
                                                                pcs</option>
                                                            <option value="dozen"
                                                                {{ $saleUnit === 'dozen' ? 'selected' : '' }}>dozen
                                                            </option>
                                                            <option value="box"
                                                                {{ $saleUnit === 'box' ? 'selected' : '' }}>
                                                                box</option>
                                                        </select>
                                                        <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                                    @endif
                                                @endif
                                            </td>

                                            <td class="money">
                                                <span class="price-highlight unitPrice">{{ fm($item->unit_price) }}</span>
                                                <div class="subtle">per <span
                                                        class="unitLabel">{{ $saleUnit }}</span></div>
                                            </td>

                                            <td>
                                                @if ($isGift)
                                                    <span
                                                        class="strong">{{ number_format((float) $item->quantity, 3) }}</span>
                                                    <div class="subtle">Gift qty</div>
                                                @else
                                                    <input class="editable-input qtyInput" type="number" min="0.0001"
                                                        step="0.0001" value="{{ (float) $item->quantity }}"
                                                        data-item-id="{{ $item->id }}">
                                                    <div class="qty-msg" data-msg-for="{{ $item->id }}"></div>
                                                @endif
                                            </td>

                                            <td class="money">
                                                @php
                                                    $amt = (float) ($item->discount_amount ?? 0);
                                                    $pct =
                                                        $item->discount_percent !== null
                                                            ? (float) $item->discount_percent
                                                            : null;
                                                @endphp

                                                @if ($isGift)
                                                    <span class="subtle">—</span>
                                                @elseif ($amt > 0)
                                                    <span class="pill success">{{ fm($amt) }}</span>
                                                    <div class="subtle">{{ $item->discount_label ?? '' }}</div>
                                                @elseif($pct !== null && $pct > 0)
                                                    <span class="pill warning">{{ number_format($pct, 2) }}%</span>
                                                    @if (!empty($item->discount_label))
                                                        <div class="subtle">{{ $item->discount_label }}</div>
                                                    @endif
                                                @else
                                                    <span class="subtle">—</span>
                                                @endif
                                            </td>

                                            <td class="money editable">
                                                @if ($isGift)
                                                    <span class="strong lineSubtotal">{{ fm($item->total_price) }}</span>
                                                @else
                                                    <input class="editable-input subInput" type="number" min="0"
                                                        step="0.01" value="{{ fm($item->total_price) }}"
                                                        data-item-id="{{ $item->id }}"
                                                        title="Type subtotal to auto-calc qty + unit">
                                                    <span class="editable-label">Click to edit</span>
                                                    <button class="copy-btn subCopyBtn"
                                                        data-item-id="{{ $item->id }}"
                                                        title="Copy subtotal">📋</button>
                                                @endif
                                            </td>

                                            <td class="money">
                                                @if ($isGift && $giftSource === 'batch_offer')
                                                    <button class="btnx btnx-ghost icon" type="button" disabled
                                                        title="Auto gift can't be removed">✕</button>
                                                @elseif($isGift && $giftSource === 'manual')
                                                    <button class="btnx btnx-ghost icon removeManualGiftBtn"
                                                        type="button" data-item-id="{{ $item->id }}">✕</button>
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

                    <!-- ===== CART FOOTER ===== -->
                    <div class="cart-footer">
                        <div class="flex-between" style="flex-wrap:wrap; gap:8px;">
                            <div>
                                <div class="subtle">Total</div>
                                <div class="strong" style="font-size:18px;">
                                    <span id="cartTotalFoot">{{ fm($cart->total ?? 0) }}</span>
                                </div>
                                <div class="subtle" id="autoAdjustNote" style="margin-top:4px;"></div>
                                <div class="subtle" id="giftHintLine" style="margin-top:6px;"></div>
                            </div>
                            <div class="flex gap-2" style="flex-wrap:wrap;">
                                <button class="btnx btnx-ghost btnx-sm" type="button"
                                    id="togglePaymentsBtn">Payments</button>
                                <button type="button" class="btnx btnx-ghost btnx-sm" id="fullPaymentBtn">Full
                                    Payment</button>

                                <button class="btnx" type="button" id="checkoutPayBtn">Checkout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- ===== ORDER SECTION ===== -->
            <!-- ============================================================ -->
            <div class="cardx mb-3 mt-3" id="orderCard">
                <div class="cardx-hd">
                    <div>
                        <div class="strong">Order</div>
                        <div class="subtle">Reward + Discount + Auto Balance</div>
                    </div>
                    <button class="btnx btnx-ghost btnx-sm" type="button" id="openGiftModalBtn2">🎁 Manual Gift</button>
                </div>

                <div style="padding:12px;">
                    <div class="grid-2">
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
                            <input class="inputx" id="rewardPointsUse" type="number" min="0" step="0.01"
                                value="0">
                        </div>

                        <div>
                            <div class="subtle">Order Discount</div>
                            <input class="inputx" id="orderDiscount" type="number" min="0" step="0.01"
                                value="0">
                        </div>

                        <div>
                            <div class="subtle">Auto Apply Balance</div>
                            <select class="selectx" id="autoBalanceMode" style="height:42px;">
                                <option value="auto" selected>Auto (Advance reduce, Due add)</option>
                                <option value="none">Do not apply</option>
                            </select>
                        </div>
                    </div>

                    <div style="border-top:1px solid var(--border); padding-top:10px; margin-top:8px;">
                        <div class="grid-3">
                            <div>
                                <div class="subtle">Cart Total</div>
                                <div class="strong" id="cartTotalLive">{{ fm($cart->total ?? 0) }}</div>
                            </div>
                            <div>
                                <div class="subtle">Payable</div>
                                <div class="strong" id="payableTotalLive">{{ fm($cart->total ?? 0) }}</div>
                            </div>
                            <div>
                                <div class="subtle">Net After Balance</div>
                                <div class="strong" id="netAfterBalanceLive">{{ fm($cart->total ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="subtle" id="orderHint" style="margin-top:8px;"></div>
                    </div>
                </div>
            </div>

            <!-- ============================================================ -->
            <!-- ===== PAYMENTS ===== -->
            <!-- ============================================================ -->
            <div class="cardx mb-3" id="paymentsCard" style="display:none;">
                <div class="cardx-hd">
                    <div>
                        <div class="strong">Payments</div>
                        <div class="subtle">Fast checkout: 1 row auto-created</div>
                    </div>
                    <div class="flex gap-2" style="flex-wrap:wrap;">
                        <button type="button" class="btnx btnx-ghost btnx-sm" id="fullPaymentBtn">Full Payment</button>
                        <button type="button" class="btnx btnx-ghost btnx-sm" id="addPaymentRowBtn">+ Add
                            Payment</button>
                    </div>
                </div>

                <div style="padding:12px;">
                    <div class="subtle" style="margin-bottom:8px;">
                        Tip: If Net After Balance is 0.00, you can checkout without adding payment.
                    </div>

                    <div id="paymentRows"></div>

                    <div class="grid-3" style="margin-top:10px;">
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

            <!-- ===== HINT BAR ===== -->
            <div class="hintbar">
                <div class="subtle" id="sweetHint">
                    💡 Tip: You can type subtotal in cart row to auto-calc Qty & Unit. Click any editable value to modify.
                </div>
                <button type="button" class="btnx btnx-ghost btnx-sm" id="dismissHintBtn">OK</button>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- ===== MANUAL GIFT MODAL ===== -->
        <!-- ============================================================ -->
        <div class="modalwrap" id="giftModalWrap" aria-hidden="true">
            <div id="giftModal" class="cardx modalx">
                <div class="cardx-hd">
                    <div>
                        <div class="strong">Manual Gift</div>
                        <div class="subtle" id="giftModalCustomerLine">Customer: Guest</div>
                        <div class="subtle">Location #<span id="giftLocBadge">{{ $currentLocationId }}</span></div>
                    </div>
                    <button type="button" class="btnx btnx-ghost btnx-sm" id="closeGiftModalBtn">Close</button>
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

        <!-- ============================================================ -->
        <!-- ===== JAVASCRIPT ===== -->
        <!-- ============================================================ -->

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /* ================================================================
           CONFIG
        ================================================================ */
        const QTY_DELAY_MS = 450;
        const SUB_DELAY_MS = 550;
        const BOX_PCS = 24;
        const POINT_RATE = 1;

        let LOCATION_ID = Number(document.getElementById('locationSelect')?.value || 1);
        let selectedCustomer = null;
        let updating = new Set();
        let isProcessing = false;

        const METHODS = {
            offline: ['cash', 'card', 'bank', 'cheque'],
            online: ['bkash', 'nagad', 'rocket', 'upay', 'stripe', 'paypal', 'sslcommerz'],
        };

        /* ================================================================
           IMAGE HELPER - FIXED FOR STORAGE PATH
        ================================================================ */
        function getImageUrl(imagePath) {
            if (!imagePath) {
                return null;
            }

            // If it's already a full URL (http or https)
            if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
                return imagePath;
            }

            // If it starts with /storage/ or /products/
            if (imagePath.startsWith('/storage/') || imagePath.startsWith('/products/')) {
                return imagePath;
            }

            // If it starts with storage/ (without leading slash)
            if (imagePath.startsWith('storage/')) {
                return '/' + imagePath;
            }

            // For paths like "products/filename.webp"
            // Use the Laravel asset helper equivalent
            const baseUrl = window.location.origin;
            return baseUrl + '/' + imagePath;
        }

        function getImageHtml(imagePath, alt = 'Product', className = '') {
            const url = getImageUrl(imagePath);
            if (url) {
                return `<img src="${url}" alt="${escapeHtml(alt)}" class="${className}" loading="lazy" onerror="this.style.display='none'">`;
            }
            return `<div class="placeholder-img">📷</div>`;
        }

        /* ================================================================
           DOM REFS - Cache all selectors
        ================================================================ */
        const $ = id => document.getElementById(id);
        const qs = (sel, ctx) => (ctx || document).querySelector(sel);
        const qsa = (sel, ctx) => (ctx || document).querySelectorAll(sel);

        // Cache DOM elements
        const dom = {
            locationSelect: $('locationSelect'),
            locBadge: $('locBadge'),
            giftLocBadge: $('giftLocBadge'),
            toastStack: $('toastStack'),
            overlay: $('overlay'),

            customerSearch: $('customerSearch'),
            customerResults: $('customerResults'),
            selectedCustomer: $('selectedCustomer'),
            addCustomerBtn: $('addCustomerBtn'),
            clearCustomerBtn: $('clearCustomerBtn'),
            customerModalWrap: $('customerModalWrap'),
            closeCustomerModal: $('closeCustomerModalBtn'),
            cancelCustomer: $('cancelCustomerBtn'),
            saveCustomer: $('saveCustomerBtn'),
            newCustomerName: $('newCustomerName'),
            newCustomerPhone: $('newCustomerPhone'),

            cartSearch: $('cartSearch'),
            searchResults: $('searchResults'),
            clearSearchBtn: $('clearSearchBtn'),

            cartBody: $('cartBody'),
            cartTotalFoot: $('cartTotalFoot'),
            clearCartBtn: $('clearCartBtn'),

            togglePayments: $('togglePaymentsBtn'),
            paymentsCard: $('paymentsCard'),
            paymentRows: $('paymentRows'),
            addPaymentRow: $('addPaymentRowBtn'),
            fullPaymentBtn: $('fullPaymentBtn'),
            fullPaymentBtn2: $('fullPaymentBtn2'),
            paidTotalLive: $('paidTotalLive'),
            dueTotalLive: $('dueTotalLive'),
            changeTotalLive: $('changeTotalLive'),
            paymentNote: $('paymentNote'),
            paymentHint: $('paymentHint'),

            checkoutBtn: $('checkoutPayBtn'),

            orderCustomerName: $('orderCustomerName'),
            orderCustomerPhone: $('orderCustomerPhone'),
            orderCustomerBalance: $('orderCustomerBalance'),
            rewardAvailable: $('rewardAvailable'),
            rewardPointsUse: $('rewardPointsUse'),
            orderDiscount: $('orderDiscount'),
            autoBalanceMode: $('autoBalanceMode'),
            cartTotalLive: $('cartTotalLive'),
            payableTotalLive: $('payableTotalLive'),
            netAfterBalanceLive: $('netAfterBalanceLive'),
            orderHint: $('orderHint'),
            autoAdjustNote: $('autoAdjustNote'),

            giftHintLine: $('giftHintLine'),
            openGiftModal: $('openGiftModalBtn'),
            openGiftModal2: $('openGiftModalBtn2'),
            giftModalWrap: $('giftModalWrap'),
            closeGiftModal: $('closeGiftModalBtn'),
            giftSearchInput: $('giftSearchInput'),
            giftSearchResults: $('giftSearchResults'),
            giftModalCustomerLine: $('giftModalCustomerLine'),
            giftModalHint: $('giftModalHint'),

            sweetHint: $('sweetHint'),
            dismissHint: $('dismissHintBtn'),
            refreshBtn: $('refreshBtn'),
        };

        /* ================================================================
           HELPERS
        ================================================================ */
        function money(n) {
            return Number(n || 0).toFixed(2);
        }

        function num(v) {
            return Math.max(0, Number(v || 0));
        }

        function escapeHtml(str) {
            if (!str) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(str).replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        function normalizeUnit(u) {
            u = String(u || '').trim().toLowerCase();
            const map = {
                'gm': 'g',
                'gram': 'g',
                'grams': 'g',
                'kilogram': 'kg',
                'kilograms': 'kg',
                'lt': 'l',
                'liter': 'l',
                'litre': 'l',
                'liters': 'l',
                'litres': 'l',
                'milliliter': 'ml',
                'millilitre': 'ml',
                'milliliters': 'ml',
                'millilitres': 'ml'
            };
            return map[u] || u || 'pcs';
        }

        function unitGroupFromBatchUnit(batchUnit) {
            const u = normalizeUnit(batchUnit);
            if (u === 'kg' || u === 'g') return 'weight';
            if (u === 'l' || u === 'ml') return 'volume';
            return 'count';
        }

        function factorToBase(group, unit) {
            unit = normalizeUnit(unit);
            if (group === 'weight') {
                return unit === 'kg' ? 1000 : 1;
            }
            if (group === 'volume') {
                return unit === 'l' ? 1000 : 1;
            }
            if (unit === 'dozen') return 12;
            if (unit === 'box') return BOX_PCS;
            return 1;
        }

        function bestUnitForBaseQty(group, baseQty) {
            baseQty = Number(baseQty || 0);
            if (group === 'weight') {
                if (baseQty >= 1000) return { unit: 'kg', qty: baseQty / 1000 };
                return { unit: 'g', qty: baseQty };
            }
            if (group === 'volume') {
                if (baseQty >= 1000) return { unit: 'l', qty: baseQty / 1000 };
                return { unit: 'ml', qty: baseQty };
            }
            if (baseQty >= BOX_PCS) return { unit: 'box', qty: baseQty / BOX_PCS };
            if (baseQty >= 12) return { unit: 'dozen', qty: baseQty / 12 };
            return { unit: 'pcs', qty: baseQty };
        }

        function setHint(msg) {
            if (dom.sweetHint) {
                dom.sweetHint.textContent = msg || "💡 Tip: You can type subtotal in cart row to auto-calc Qty & Unit.";
            }
        }

        function setLocationBadges() {
            if (dom.locBadge) dom.locBadge.textContent = String(LOCATION_ID);
            if (dom.giftLocBadge) dom.giftLocBadge.textContent = String(LOCATION_ID);
        }

        function cartTotalNow() {
            return num(dom.cartTotalFoot?.textContent || dom.cartTotalLive?.textContent || 0);
        }

        /* ================================================================
           TOAST SYSTEM
        ================================================================ */
        function pushToast({ type = 'warning', title = 'Notice', messages = [], timeout = 3200 } = {}) {
            if (!dom.toastStack) return;

            const msgText = (Array.isArray(messages) ? messages : [messages])
                .filter(Boolean).map(m => String(m)).join('\n');

            if (!msgText) return;

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
                <div class="toastx-body">${escapeHtml(msgText)}</div>
                <div class="toastx-progress"><div style="animation-duration:${timeout}ms"></div></div>
            `;

            const remove = () => {
                clearTimeout(el._t);
                el.classList.add('removing');
                setTimeout(() => {
                    if (el.parentNode) el.remove();
                }, 200);
            };

            el.querySelector('.toastx-close').addEventListener('click', remove);
            dom.toastStack.appendChild(el);

            while (dom.toastStack.children.length > 4) {
                const first = dom.toastStack.firstChild;
                if (first) first.remove();
            }

            if (timeout > 0) {
                el._t = setTimeout(remove, timeout);
            }
        }

        function extractMessages(data) {
            const msgs = [];
            if (data?.errors && typeof data.errors === 'object') {
                Object.values(data.errors).forEach(arr => {
                    if (Array.isArray(arr)) {
                        arr.forEach(m => msgs.push(String(m)));
                    }
                });
            }
            if (data?.message) msgs.push(String(data.message));
            if (!msgs.length) msgs.push('Something went wrong.');
            return [...new Set(msgs)];
        }

        function guessTypeFromStatus(status) {
            if (status >= 500) return 'danger';
            if ([422, 409, 403].includes(status)) return 'warning';
            if (status >= 400) return 'danger';
            return 'success';
        }

        function toastAll(res, data, fallbackTitle = 'Error') {
            const status = res?.status || 0;
            const type = guessTypeFromStatus(status);
            const messages = extractMessages(data);
            let title = fallbackTitle;
            if (status === 422) title = 'Validation';
            else if (status === 409) title = 'Duplicate';
            else if (status >= 500) title = 'Server Error';
            pushToast({ type, title, messages, timeout: 3800 });
        }

        /* ================================================================
           FETCH HELPERS
        ================================================================ */
        async function jsonFetch(url, method, payload) {
            const body = payload ? { ...payload, location_id: LOCATION_ID } : { location_id: LOCATION_ID };

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(body)
                });
                const data = await res.json().catch(() => ({}));
                return { res, data };
            } catch (error) {
                console.error('Fetch error:', error);
                return {
                    res: { status: 0, ok: false },
                    data: { message: 'Network error. Please check your connection.' }
                };
            }
        }

        /* ================================================================
           MODALS
        ================================================================ */
        function openWrap(wrap) {
            if (!wrap) return;
            if (dom.overlay) dom.overlay.style.display = 'block';
            wrap.classList.add('show');
            wrap.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
        }

        function closeWrap(wrap) {
            if (!wrap) return;
            wrap.classList.remove('show');
            wrap.setAttribute('aria-hidden', 'true');
            if (dom.overlay) dom.overlay.style.display = 'none';
            document.body.style.overflow = '';
        }

        function closeAllModals() {
            closeWrap(dom.customerModalWrap);
            closeWrap(dom.giftModalWrap);
        }

        if (dom.overlay) {
            dom.overlay.addEventListener('click', closeAllModals);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAllModals();
        });

        [dom.customerModalWrap, dom.giftModalWrap].forEach(wrap => {
            if (wrap) {
                wrap.addEventListener('click', (e) => {
                    if (e.target === wrap) closeWrap(wrap);
                });
            }
        });

        if (dom.addCustomerBtn) {
            dom.addCustomerBtn.addEventListener('click', () => openWrap(dom.customerModalWrap));
        }
        if (dom.closeCustomerModal) {
            dom.closeCustomerModal.addEventListener('click', () => closeWrap(dom.customerModalWrap));
        }
        if (dom.cancelCustomer) {
            dom.cancelCustomer.addEventListener('click', () => closeWrap(dom.customerModalWrap));
        }
        if (dom.closeGiftModal) {
            dom.closeGiftModal.addEventListener('click', () => closeWrap(dom.giftModalWrap));
        }

        /* ================================================================
           LOCATION
        ================================================================ */
        if (dom.locationSelect) {
            dom.locationSelect.addEventListener('change', function() {
                LOCATION_ID = Number(this.value || 1);
                setLocationBadges();
                pushToast({
                    type: 'success',
                    title: 'Location',
                    messages: [`Switched to location #${LOCATION_ID}`],
                    timeout: 1800
                });
                window.location.reload();
            });
        }

        if (dom.refreshBtn) {
            dom.refreshBtn.addEventListener('click', () => window.location.reload());
        }

        /* ================================================================
           CUSTOMER
        ================================================================ */
        let cDebounce = null;
        let activeQuery = '';

        function renderOrderCustomer(customer) {
            selectedCustomer = customer || null;

            if (!selectedCustomer) {
                if (dom.orderCustomerName) dom.orderCustomerName.textContent = 'Guest';
                if (dom.orderCustomerPhone) dom.orderCustomerPhone.textContent = '—';
                if (dom.orderCustomerBalance) dom.orderCustomerBalance.textContent = 'Due: 0.00 | Advance: 0.00';
                if (dom.rewardAvailable) dom.rewardAvailable.textContent = '0.00';
                if (dom.rewardPointsUse) dom.rewardPointsUse.value = '0.00';
                if (dom.selectedCustomer) dom.selectedCustomer.textContent = '👤 Guest customer';
                calcPayable();
                return;
            }

            if (dom.orderCustomerName) dom.orderCustomerName.textContent = selectedCustomer.name || 'Customer';
            if (dom.orderCustomerPhone) dom.orderCustomerPhone.textContent = selectedCustomer.phone || '—';
            if (dom.orderCustomerBalance) {
                dom.orderCustomerBalance.textContent =
                    `Due: ${money(selectedCustomer.due_balance)} | Advance: ${money(selectedCustomer.advance_balance)}`;
            }
            if (dom.rewardAvailable) dom.rewardAvailable.textContent = money(selectedCustomer.reward_points);
            if (dom.rewardPointsUse) dom.rewardPointsUse.value = '0.00';
            if (dom.selectedCustomer) {
                dom.selectedCustomer.innerHTML =
                    `✅ <strong>${escapeHtml(selectedCustomer.name)}</strong> — Points: ${money(selectedCustomer.reward_points || 0)}`;
            }
            calcPayable();
        }

        async function setCustomer(customer) {
            try {
                const { res, data } = await jsonFetch(
                    '{{ route('cart.customer.set') }}',
                    'POST',
                    { customer_id: customer ? customer.id : null }
                );

                if (!res.ok || !data.success) {
                    toastAll(res, data, 'Customer');
                    return;
                }

                const c = customer ? data.customer : null;
                renderOrderCustomer(c);
                if (dom.customerResults) dom.customerResults.innerHTML = '';
                if (dom.customerSearch) dom.customerSearch.value = '';

                pushToast({
                    type: 'success',
                    title: 'Customer',
                    messages: [c ? `${c.name} selected` : 'Guest selected'],
                    timeout: 1600
                });
            } catch (error) {
                console.error('Set customer error:', error);
                pushToast({
                    type: 'danger',
                    title: 'Error',
                    messages: ['Failed to set customer. Please try again.'],
                    timeout: 3000
                });
            }
        }

        if (dom.customerSearch) {
            dom.customerSearch.addEventListener('input', function() {
                clearTimeout(cDebounce);
                const q = this.value.trim();
                if (q.length < 2) {
                    if (dom.customerResults) dom.customerResults.innerHTML = '';
                    return;
                }
                cDebounce = setTimeout(async () => {
                    activeQuery = q;
                    try {
                        const res = await fetch(`{{ route('customers.quick.search') }}?q=${encodeURIComponent(q)}`);
                        const rows = await res.json().catch(() => []);
                        if (activeQuery !== q) return;
                        if (!dom.customerResults) return;

                        dom.customerResults.innerHTML = '';

                        const guestRow = document.createElement('div');
                        guestRow.className = 'result-row';
                        guestRow.innerHTML =
                            `<div><strong>Guest</strong><div class="subtle">No customer selected</div></div>`;
                        guestRow.onclick = () => setCustomer(null);
                        dom.customerResults.appendChild(guestRow);

                        if (!rows.length) {
                            dom.customerResults.insertAdjacentHTML('beforeend',
                                `<div class="subtle" style="padding:8px">No customers found</div>`
                            );
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
                            dom.customerResults.appendChild(row);
                        });
                    } catch (error) {
                        console.error('Customer search error:', error);
                    }
                }, 250);
            });
        }

        if (dom.clearCustomerBtn) {
            dom.clearCustomerBtn.addEventListener('click', () => setCustomer(null));
        }

        if (dom.saveCustomer) {
            dom.saveCustomer.addEventListener('click', async () => {
                const name = dom.newCustomerName?.value?.trim() || '';
                if (!name) {
                    pushToast({ type: 'warning', title: 'Customer', messages: ['Name required'], timeout: 2500 });
                    return;
                }
                const phone = dom.newCustomerPhone?.value?.trim() || '';

                try {
                    const { res, data } = await jsonFetch('{{ route('customers.store') }}', 'POST', { name, phone });

                    if (!res.ok || (!data.success && !data.customer)) {
                        toastAll(res, data, 'Create customer');
                        return;
                    }

                    await setCustomer(data.customer);
                    if (dom.newCustomerName) dom.newCustomerName.value = '';
                    if (dom.newCustomerPhone) dom.newCustomerPhone.value = '';
                    closeWrap(dom.customerModalWrap);
                } catch (error) {
                    console.error('Save customer error:', error);
                    pushToast({
                        type: 'danger',
                        title: 'Error',
                        messages: ['Failed to create customer. Please try again.'],
                        timeout: 3000
                    });
                }
            });
        }

        /* ================================================================
           CALCULATE PAYABLE
        ================================================================ */
        function calcPayable() {
            const cartT = cartTotalNow();
            const availablePts = num(selectedCustomer?.reward_points || 0);
            if (dom.rewardAvailable) dom.rewardAvailable.textContent = money(availablePts);

            let ptsUse = num(dom.rewardPointsUse?.value || 0);
            if (!selectedCustomer) ptsUse = 0;
            if (ptsUse > availablePts) ptsUse = availablePts;
            const rewardAmount = ptsUse * POINT_RATE;

            let disc = num(dom.orderDiscount?.value || 0);
            if (disc > cartT) disc = cartT;

            let payable = cartT - disc - rewardAmount;
            if (payable < 0) payable = 0;

            const mode = dom.autoBalanceMode?.value || 'auto';
            const dueBal = num(selectedCustomer?.due_balance || 0);
            const advBal = num(selectedCustomer?.advance_balance || 0);

            let net = payable;
            let note = '';
            if (selectedCustomer && mode === 'auto') {
                if (advBal > 0) {
                    const usedAdv = Math.min(advBal, net);
                    net = net - usedAdv;
                    note += usedAdv > 0 ? `Advance used: ${money(usedAdv)}. ` : '';
                }
                if (dueBal > 0) {
                    net = net + dueBal;
                    note += `Due added: ${money(dueBal)}. `;
                }
            }

            if (dom.rewardPointsUse) dom.rewardPointsUse.value = money(ptsUse);
            if (dom.cartTotalLive) dom.cartTotalLive.textContent = money(cartT);
            if (dom.payableTotalLive) dom.payableTotalLive.textContent = money(payable);
            if (dom.netAfterBalanceLive) dom.netAfterBalanceLive.textContent = money(net);
            if (dom.autoAdjustNote) {
                dom.autoAdjustNote.textContent = (selectedCustomer && mode === 'auto') ?
                    `Auto: ${note.trim() || 'No balance applied.'}` :
                    (mode === 'none' ? 'Auto balance disabled.' : '');
            }
            if (dom.orderHint) {
                dom.orderHint.textContent =
                    `Reward: ${money(ptsUse)} pts (${money(rewardAmount)}), Discount: ${money(disc)}.`;
            }

            recalcPaymentSummary(net);
            renderPaymentHint(net);
            return { cartT, ptsUse, rewardAmount, disc, payable, net };
        }

        [dom.rewardPointsUse, dom.orderDiscount, dom.autoBalanceMode].forEach(el => {
            if (el) {
                el.addEventListener('input', calcPayable);
                el.addEventListener('change', calcPayable);
            }
        });

        /* ================================================================
           PAYMENTS
        ================================================================ */
        function methodOptions(channel) {
            const list = METHODS[channel] || [];
            return list.map(m => `<option value="${m}">${m}</option>`).join('');
        }

        function createPaymentRow(defaults = {}) {
            const row = document.createElement('div');
            row.className = 'payment-row';

            const channel = defaults.channel || 'offline';
            const method = defaults.method || (METHODS[channel]?.[0] || '');
            const amount = defaults.amount || '';
            const trx_id = defaults.trx_id || '';
            const account = defaults.account_label || '';

            row.innerHTML = `
                <div>
                    <span class="label">Channel</span>
                    <select class="selectx payChannel" style="width:100%;">
                        <option value="offline" ${channel==='offline'?'selected':''}>offline</option>
                        <option value="online" ${channel==='online'?'selected':''}>online</option>
                    </select>
                </div>
                <div>
                    <span class="label">Method</span>
                    <select class="selectx payMethod" style="width:100%;">${methodOptions(channel)}</select>
                </div>
                <div>
                    <span class="label">Amount</span>
                    <input class="inputx payAmount" type="number" min="0" step="0.0001" placeholder="0.00" value="${escapeHtml(amount)}" style="height:34px;">
                </div>
                <div>
                    <span class="label">Account Label</span>
                    <input class="inputx payAccount" placeholder="Optional" value="${escapeHtml(account)}" style="height:34px;">
                </div>
                <div class="payTrxWrap full-width" style="display:${channel==='online'?'block':'none'};">
                    <span class="label">Transaction ID (Required for online)</span>
                    <input class="inputx payTrx" placeholder="Example: BK123456789" value="${escapeHtml(trx_id)}" style="height:34px;">
                </div>
                <div class="full-width" style="display:flex; justify-content:space-between; align-items:center;">
                    <span class="pay-hint"></span>
                    <button type="button" class="btnx btnx-ghost btnx-sm remove-payment">✕ Remove</button>
                </div>
            `;

            const methodSelect = row.querySelector('.payMethod');
            if (methodSelect) methodSelect.value = method;

            const channelSelect = row.querySelector('.payChannel');
            const trxWrap = row.querySelector('.payTrxWrap');

            function refresh() {
                const net = num(dom.netAfterBalanceLive?.textContent || 0);
                recalcPaymentSummary(net);
                renderPaymentHint(net);
                renderPaymentHintRow(row);
            }

            if (channelSelect) {
                channelSelect.addEventListener('change', function() {
                    const ch = this.value;
                    if (methodSelect) {
                        methodSelect.innerHTML = methodOptions(ch);
                        methodSelect.value = METHODS[ch]?.[0] || '';
                    }
                    if (trxWrap) {
                        trxWrap.style.display = (ch === 'online') ? 'block' : 'none';
                    }
                    const trxInput = row.querySelector('.payTrx');
                    if (trxInput && ch !== 'online') trxInput.value = '';
                    refresh();
                });
            }

            const amountInput = row.querySelector('.payAmount');
            const trxInput = row.querySelector('.payTrx');
            const accountInput = row.querySelector('.payAccount');

            if (amountInput) amountInput.addEventListener('input', refresh);
            if (trxInput) trxInput.addEventListener('input', refresh);
            if (accountInput) accountInput.addEventListener('input', refresh);

            const removeBtn = row.querySelector('.remove-payment');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    row.remove();
                    refresh();
                });
            }

            renderPaymentHintRow(row);
            return row;
        }

        function getPaymentRowsData() {
            if (!dom.paymentRows) return [];
            const rows = Array.from(dom.paymentRows.querySelectorAll('.payment-row'));
            return rows.map(r => {
                const channel = r.querySelector('.payChannel')?.value || 'offline';
                const method = r.querySelector('.payMethod')?.value || '';
                const amount = num(r.querySelector('.payAmount')?.value);
                const trx_id = (r.querySelector('.payTrx')?.value || '').trim();
                const account_label = (r.querySelector('.payAccount')?.value || '').trim();
                return { channel, method, amount, trx_id, account_label, _rowEl: r };
            });
        }

        function renderPaymentHintRow(rowEl) {
            const ch = rowEl.querySelector('.payChannel')?.value;
            const m = rowEl.querySelector('.payMethod')?.value;
            const amt = rowEl.querySelector('.payAmount')?.value;
            const trx = (rowEl.querySelector('.payTrx')?.value || '').trim();
            const hint = rowEl.querySelector('.pay-hint');
            if (!hint) return;

            const a = num(amt);
            if (!m) {
                hint.textContent = 'Please select a method.';
                return;
            }
            if (a <= 0) {
                hint.textContent = 'Enter an amount greater than 0.';
                return;
            }
            if (ch === 'online' && !trx) {
                hint.textContent = 'Online payment needs Transaction ID.';
                return;
            }
            hint.textContent = `✓ ${ch} / ${m} / ${money(a)}`;
        }

        function recalcPaymentSummary(net) {
            const rows = getPaymentRowsData();
            const paid = rows.reduce((s, p) => s + num(p.amount), 0);

            let due = 0, change = 0;
            if (net <= 0) {
                // Net is zero, no payment needed
            } else if (paid < net) {
                due = net - paid;
            } else {
                change = paid - net;
            }

            if (dom.paidTotalLive) dom.paidTotalLive.textContent = money(paid);
            if (dom.dueTotalLive) dom.dueTotalLive.textContent = money(due);
            if (dom.changeTotalLive) dom.changeTotalLive.textContent = money(change);

            return { paid, due, change };
        }

        function renderPaymentHint(net) {
            const rows = getPaymentRowsData();
            rows.forEach(p => renderPaymentHintRow(p._rowEl));

            const paid = rows.reduce((s, p) => s + num(p.amount), 0);

            if (!dom.paymentHint) return;

            if (net <= 0) {
                dom.paymentHint.textContent = 'Net is 0.00. You can checkout without payment.';
                return;
            }
            if (rows.length === 0) {
                dom.paymentHint.textContent =
                    'Add payment(s) if customer pays now. Otherwise leave empty and checkout as due.';
                return;
            }
            if (paid <= 0) {
                dom.paymentHint.textContent = 'Payment rows added but total is 0.00. Enter amounts.';
                return;
            }
            if (paid < net) {
                dom.paymentHint.textContent = `Partial payment. Due will be ${money(net - paid)}.`;
                return;
            }
            if (paid === net) {
                dom.paymentHint.textContent = 'Full payment. Order will be completed.';
                return;
            }
            dom.paymentHint.textContent = `Over payment. Change will be ${money(paid - net)}.`;
        }

        function ensureOnePaymentRow() {
            if (!dom.paymentRows) return;
            if (dom.paymentRows.querySelectorAll('.payment-row').length === 0) {
                dom.paymentRows.appendChild(createPaymentRow());
            }
        }

        if (dom.togglePayments) {
            dom.togglePayments.addEventListener('click', function() {
                if (!dom.paymentsCard) return;
                const show = dom.paymentsCard.style.display === 'none';
                dom.paymentsCard.style.display = show ? 'block' : 'none';
                if (show) {
                    ensureOnePaymentRow();
                    pushToast({ type: 'success', title: 'Payments', messages: ['Payments opened'], timeout: 1800 });
                    const net = num(dom.netAfterBalanceLive?.textContent || 0);
                    recalcPaymentSummary(net);
                    renderPaymentHint(net);
                } else {
                    pushToast({ type: 'warning', title: 'Payments', messages: ['Payments hidden'], timeout: 1600 });
                }
            });
        }

        if (dom.addPaymentRow) {
            dom.addPaymentRow.addEventListener('click', function() {
                if (!dom.paymentRows) return;
                dom.paymentRows.appendChild(createPaymentRow());
                const net = num(dom.netAfterBalanceLive?.textContent || 0);
                recalcPaymentSummary(net);
                renderPaymentHint(net);
                pushToast({ type: 'success', title: 'Payment', messages: ['Added new payment row'], timeout: 1600 });
            });
        }

        // Both Full Payment buttons
        const fullPaymentHandler = function() {
            const { net } = calcPayable();
            if (!dom.paymentsCard) return;
            dom.paymentsCard.style.display = 'block';
            ensureOnePaymentRow();

            const firstRow = dom.paymentRows?.querySelector('.payment-row');
            const amountInput = firstRow?.querySelector('.payAmount');
            if (amountInput) {
                amountInput.value = money(net);
                amountInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            recalcPaymentSummary(num(net));
            renderPaymentHint(num(net));
            pushToast({ type: 'success', title: 'Full Payment', messages: [`Filled ${money(net)}`], timeout: 2200 });
        };

        if (dom.fullPaymentBtn) {
            dom.fullPaymentBtn.addEventListener('click', fullPaymentHandler);
        }
        if (dom.fullPaymentBtn2) {
            dom.fullPaymentBtn2.addEventListener('click', fullPaymentHandler);
        }

        /* ================================================================
           RENDER CART - FIXED IMAGE HANDLING
        ================================================================ */
        function giftBadge(item) {
            if (!item.is_gift) return '';
            return `<span class="pill success giftTag">GIFT • ${escapeHtml(item.gift_source || 'gift')}</span>`;
        }

        function typeCell(item) {
            if (item.is_gift) return `<span class="pill">gift</span>`;
            return `
                <select class="selectx priceTypeSelect" data-item-id="${item.id}">
                    <option value="retail" ${item.price_type==='retail'?'selected':''}>Retail</option>
                    <option value="whole" ${item.price_type==='whole'?'selected':''}>Whole</option>
                    <option value="customer_whole" ${item.price_type==='customer_whole'?'selected':''}>Customer</option>
                </select>
            `;
        }

        function unitCell(item) {
            const batchUnit = normalizeUnit(item.batch_unit || 'pcs');
            const group = unitGroupFromBatchUnit(batchUnit);
            const currentUnit = normalizeUnit(item.unit || batchUnit);

            if (item.is_gift) {
                return `<span class="pill">${escapeHtml(currentUnit)}</span><div class="subtle">Fixed</div>`;
            }

            if (group === 'weight') {
                return `
                    <select class="selectx unitSelect" data-item-id="${item.id}">
                        <option value="kg" ${currentUnit==='kg'?'selected':''}>kg</option>
                        <option value="g" ${currentUnit==='g'?'selected':''}>g</option>
                    </select>
                    <div class="subtle">Batch: ${escapeHtml(batchUnit)}</div>
                `;
            }
            if (group === 'volume') {
                return `
                    <select class="selectx unitSelect" data-item-id="${item.id}">
                        <option value="l" ${currentUnit==='l'?'selected':''}>L</option>
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

        function qtyCell(item) {
            if (item.is_gift) {
                return `<span class="strong">${Number(item.quantity||0).toFixed(3)}</span><div class="subtle">Gift qty</div>`;
            }
            return `
                <input class="editable-input qtyInput" type="number" min="0.0001" step="0.0001"
                       value="${Number(item.quantity||0)}" data-item-id="${item.id}">
                <div class="qty-msg" data-msg-for="${item.id}"></div>
            `;
        }

        function discountCell(item) {
            if (item.is_gift) return `<span class="subtle">—</span>`;
            const amt = Number(item.discount_amount || 0);
            const pct = (item.discount_percent === null || typeof item.discount_percent === 'undefined') ?
                null : Number(item.discount_percent);

            if (amt > 0) {
                return `<span class="pill success">${money(amt)}</span>
                        <div class="subtle">${escapeHtml(item.discount_label ?? '')}</div>`;
            }
            if (pct !== null && pct > 0) {
                return `<span class="pill warning">${pct.toFixed(2)}%</span>
                        ${item.discount_label ? `<div class="subtle">${escapeHtml(item.discount_label)}</div>` : ''}`;
            }
            return `<span class="subtle">—</span>`;
        }

        function subCell(item) {
            if (item.is_gift) {
                return `<span class="strong lineSubtotal">${money(item.total_price)}</span>`;
            }
            return `
                <input class="editable-input subInput" type="number" min="0" step="0.01"
                       value="${money(item.total_price)}" data-item-id="${item.id}"
                       title="Type subtotal to auto-calc qty + unit">
                <span class="editable-label">Click to edit</span>
                <button class="copy-btn subCopyBtn" data-item-id="${item.id}" title="Copy subtotal">📋</button>
            `;
        }

        function removeCell(item) {
            if (item.is_gift && item.gift_source === 'batch_offer') {
                return `<button class="btnx btnx-ghost icon" type="button" disabled title="Auto gift can't be removed">✕</button>`;
            }
            if (item.is_gift && item.gift_source === 'manual') {
                return `<button class="btnx btnx-ghost icon removeManualGiftBtn" type="button" data-item-id="${item.id}">✕</button>`;
            }
            return `<button class="btnx btnx-ghost icon removeBtn" type="button" data-item-id="${item.id}">✕</button>`;
        }

        function renderCart(cart) {
            if (!dom.cartBody) return;

            dom.cartBody.innerHTML = '';
            const items = [...(cart.items || [])].sort((a, b) => Number(b.id) - Number(a.id));

            if (!items.length) {
                dom.cartBody.innerHTML =
                    `<tr id="emptyRow"><td colspan="10"><div class="empty-state">🧺 Cart is empty — search and add products</div></td></tr>`;
                if (dom.cartTotalFoot) dom.cartTotalFoot.textContent = money(cart.total);
                calcPayable();
                bindCartRowEvents();
                updateRowNumbers();
                return;
            }

            items.forEach((item) => {
                // Proper image handling
                let imgHtml = '';
                if (item.image) {
                    const imageUrl = getImageUrl(item.image);
                    if (imageUrl) {
                        imgHtml =
                            `<img src="${imageUrl}" alt="${escapeHtml(item.name || 'Product')}" loading="lazy" onerror="this.style.display='none'">`;
                    }
                }

                const trClass = item.is_gift ? 'gift-row' : '';
                const batchUnit = normalizeUnit(item.batch_unit || 'pcs');
                const currentUnit = normalizeUnit(item.unit || batchUnit);

                dom.cartBody.insertAdjacentHTML('beforeend', `
                    <tr data-item-id="${item.id}" data-batch-unit="${escapeHtml(batchUnit)}" class="${trClass}">
                        <td><span class="rowNo"></span></td>
                        <td><div class="mini-img">${imgHtml || '<div class="placeholder-img">📷</div>'}</div></td>
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
                        <td class="money">${discountCell(item)}</td>
                        <td class="money editable">${subCell(item)}</td>
                        <td class="money">${removeCell(item)}</td>
                    </tr>
                `);
            });

            if (dom.cartTotalFoot) dom.cartTotalFoot.textContent = money(cart.total);
            calcPayable();
            bindCartRowEvents();
            updateRowNumbers();
        }

        function updateRowNumbers() {
            if (!dom.cartBody) return;
            const rows = Array.from(dom.cartBody.querySelectorAll('tr[data-item-id]'));
            rows.forEach((tr, idx) => {
                const noEl = tr.querySelector('.rowNo');
                if (noEl) noEl.textContent = String(rows.length - idx);
            });
        }

        function clearQtyMessages() {
            document.querySelectorAll('.qty-msg').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });
        }

        function showQtyMessage(itemId, msg) {
            clearQtyMessages();
            const el = document.querySelector(`.qty-msg[data-msg-for="${itemId}"]`);
            if (!el) return;
            el.textContent = msg;
            el.style.display = 'block';
        }

        function showGiftHints(data) {
            if (!dom.giftHintLine) return;
            const hints = data?.gift_hints || [];
            dom.giftHintLine.textContent = hints.length ? ('🎁 ' + hints.join(' • ')) : '';
        }

        /* ================================================================
           CART ACTIONS
        ================================================================ */
        async function addToCart(batchId, qty, priceType, btn) {
            if (isProcessing) return;

            clearQtyMessages();
            if (btn) {
                btn.disabled = true;
                btn._old = btn.innerHTML;
                btn.innerHTML = `<span class="spinner"></span>Adding`;
            }

            try {
                const { res, data } = await jsonFetch('{{ route('cart.add') }}', 'POST', {
                    batch_id: batchId,
                    quantity: qty,
                    price_type: priceType
                });

                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = btn._old || 'Add';
                }

                if (!res.ok || !data.success) {
                    toastAll(res, data, 'Add failed');
                    return;
                }

                renderCart(data.cart);
                showGiftHints(data);
                if (data.hint) setHint(data.hint);

                const cartScroll = document.querySelector('.cart-scroll');
                if (cartScroll) cartScroll.scrollTo({ top: 0, behavior: 'smooth' });

                pushToast({ type: 'success', title: 'Cart', messages: [`Added to cart (Location #${LOCATION_ID})`], timeout: 1600 });
            } catch (error) {
                console.error('Add to cart error:', error);
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = btn._old || 'Add';
                }
                pushToast({ type: 'danger', title: 'Error', messages: ['Failed to add item. Please try again.'], timeout: 3000 });
            }
        }

        async function updateCartItem(itemId, priceType, quantity, unit) {
            if (isProcessing || updating.has(itemId)) return;

            clearQtyMessages();
            updating.add(itemId);

            try {
                const { res, data } = await jsonFetch('{{ route('cart.item.update') }}', 'POST', {
                    item_id: itemId,
                    price_type: priceType,
                    quantity,
                    unit: unit || null
                });

                updating.delete(itemId);

                if (!res.ok && res.status === 422 && data?.invalid_item_id) {
                    if (data?.cart) renderCart(data.cart);
                    const min = data.required_min;
                    const max = data.required_max;
                    const t = (data.required_type || '').replace('_', ' ');
                    let range = '';
                    if (min !== null && max !== null) range = `Min ${min} & Max ${max}`;
                    else if (min !== null) range = `Min ${min}`;
                    else if (max !== null) range = `Max ${max}`;
                    else range = 'required quantity';
                    showQtyMessage(data.invalid_item_id, `❗ Need ${range} to use ${t} price.`);
                    pushToast({ type: 'warning', title: 'Qty rule', messages: [data.message || 'Qty condition not met'], timeout: 3500 });
                    setHint(`✨ ${range} required for ${t}. Increase quantity to unlock.`);
                    return;
                }

                if (!res.ok || !data.success) {
                    toastAll(res, data, 'Update failed');
                    return;
                }

                renderCart(data.cart);
                showGiftHints(data);
                pushToast({ type: 'success', title: 'Cart', messages: [`Updated (Location #${LOCATION_ID})`], timeout: 1500 });
            } catch (error) {
                console.error('Update cart error:', error);
                updating.delete(itemId);
                pushToast({ type: 'danger', title: 'Error', messages: ['Failed to update item. Please try again.'], timeout: 3000 });
            }
        }

        async function removeCartItem(itemId) {
            if (isProcessing) return;

            clearQtyMessages();
            try {
                const { res, data } = await jsonFetch(`{{ url('/cart/item') }}/${itemId}`, 'DELETE', {});
                if (!res.ok || !data.success) {
                    toastAll(res, data, 'Remove failed');
                    return;
                }
                renderCart(data.cart);
                showGiftHints(data);
                pushToast({ type: 'success', title: 'Cart', messages: ['Removed'], timeout: 1500 });
            } catch (error) {
                console.error('Remove cart error:', error);
                pushToast({ type: 'danger', title: 'Error', messages: ['Failed to remove item. Please try again.'], timeout: 3000 });
            }
        }

        async function removeManualGift(itemId) {
            if (isProcessing) return;

            clearQtyMessages();
            try {
                const { res, data } = await jsonFetch(`{{ url('/cart/gift/manual') }}/${itemId}`, 'DELETE', {});
                if (!res.ok || !data.success) {
                    toastAll(res, data, 'Remove gift failed');
                    return;
                }
                renderCart(data.cart);
                pushToast({ type: 'success', title: 'Gift', messages: ['Gift removed'], timeout: 1600 });
            } catch (error) {
                console.error('Remove gift error:', error);
                pushToast({ type: 'danger', title: 'Error', messages: ['Failed to remove gift. Please try again.'], timeout: 3000 });
            }
        }

        async function clearCart() {
            if (isProcessing) return;

            clearQtyMessages();
            try {
                const { res, data } = await jsonFetch('{{ route('cart.clear') }}', 'DELETE', {});
                if (!res.ok || !data.success) {
                    toastAll(res, data, 'Clear failed');
                    return;
                }
                renderCart(data.cart);
                pushToast({ type: 'success', title: 'Cart', messages: ['Cleared'], timeout: 1600 });
            } catch (error) {
                console.error('Clear cart error:', error);
                pushToast({ type: 'danger', title: 'Error', messages: ['Failed to clear cart. Please try again.'], timeout: 3000 });
            }
        }

        /* ================================================================
           BIND CART ROW EVENTS
        ================================================================ */
        function scheduleRowUpdate(tr) {
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

        function scheduleSubUpdate(tr) {
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
                if (!isFinite(targetSub) || targetSub <= 0 || unitPrice <= 0) return;

                const currentFactor = factorToBase(group, currentUnit);
                const pricePerBase = unitPrice / currentFactor;
                if (pricePerBase <= 0) return;

                const baseQty = targetSub / pricePerBase;
                const pick = bestUnitForBaseQty(group, baseQty);
                let q = Number(pick.qty);
                if (!isFinite(q) || q <= 0) q = 0.0001;
                if (group === 'count') q = Math.max(0.0001, Math.round(q * 100) / 100);
                else q = Math.max(0.0001, Math.round(q * 10000) / 10000);

                if (selUnit) selUnit.value = pick.unit;
                if (qtyInput) qtyInput.value = String(q);
                setHint(`✨ Sweet: ${money(targetSub)} → ${q} ${pick.unit} (batch ${batchUnit})`);

                await updateCartItem(itemId, priceType, q, pick.unit);
            }, SUB_DELAY_MS);
        }

        function bindCartRowEvents() {
            const rows = document.querySelectorAll('#cartBody tr[data-item-id]');

            rows.forEach(tr => {
                const selType = tr.querySelector('.priceTypeSelect');
                const selUnit = tr.querySelector('.unitSelect');
                const qtyInput = tr.querySelector('.qtyInput');
                const subInput = tr.querySelector('.subInput');

                if (selType) {
                    const newSel = selType.cloneNode(true);
                    selType.parentNode.replaceChild(newSel, selType);
                    newSel.onchange = () => scheduleRowUpdate(tr);
                }

                if (selUnit) {
                    const newUnit = selUnit.cloneNode(true);
                    selUnit.parentNode.replaceChild(newUnit, selUnit);
                    newUnit.onchange = () => scheduleRowUpdate(tr);
                }

                if (qtyInput) {
                    const newQty = qtyInput.cloneNode(true);
                    qtyInput.parentNode.replaceChild(newQty, qtyInput);
                    newQty.oninput = () => scheduleRowUpdate(tr);
                    newQty.onchange = () => scheduleRowUpdate(tr);
                }

                if (subInput) {
                    const newSub = subInput.cloneNode(true);
                    subInput.parentNode.replaceChild(newSub, subInput);
                    newSub.oninput = () => scheduleSubUpdate(tr);
                    newSub.onchange = () => scheduleSubUpdate(tr);
                }

                const copyBtn = tr.querySelector('.subCopyBtn');
                if (copyBtn) {
                    const newCopy = copyBtn.cloneNode(true);
                    copyBtn.parentNode.replaceChild(newCopy, copyBtn);
                    const currentSubInput = tr.querySelector('.subInput');
                    newCopy.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const val = currentSubInput?.value || '';
                        if (!val) return;
                        if (navigator.clipboard) {
                            navigator.clipboard.writeText(val).then(() => {
                                newCopy.textContent = '✓';
                                newCopy.classList.add('copied');
                                setTimeout(() => {
                                    newCopy.textContent = '📋';
                                    newCopy.classList.remove('copied');
                                }, 1200);
                            }).catch(() => {});
                        } else {
                            const textarea = document.createElement('textarea');
                            textarea.value = val;
                            document.body.appendChild(textarea);
                            textarea.select();
                            document.execCommand('copy');
                            textarea.remove();
                            newCopy.textContent = '✓';
                            newCopy.classList.add('copied');
                            setTimeout(() => {
                                newCopy.textContent = '📋';
                                newCopy.classList.remove('copied');
                            }, 1200);
                        }
                    });
                }
            });

            document.querySelectorAll('.removeBtn').forEach(btn => {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                newBtn.onclick = async function() {
                    await removeCartItem(this.dataset.itemId);
                };
            });

            document.querySelectorAll('.removeManualGiftBtn').forEach(btn => {
                const newBtn = btn.cloneNode(true);
                btn.parentNode.replaceChild(newBtn, btn);
                newBtn.onclick = async function() {
                    await removeManualGift(this.dataset.itemId);
                };
            });

            document.querySelectorAll('.editable-input').forEach(input => {
                input.addEventListener('focus', function() {
                    this.select();
                });
            });
        }

        /* ================================================================
           SEARCH - FIXED IMAGE HANDLING
        ================================================================ */
        function pickPrimary(images) {
            if (!Array.isArray(images) || images.length === 0) {
                return null;
            }
            const primary = images.find(x => Number(x.is_primary) === 1);
            const imgData = primary || images[0];
            return imgData?.image_path || null;
        }

        let searchDebounce = null;
        let searching = false;

        async function doSearch(term) {
            if (!dom.searchResults) return;

            if (term.length < 2) {
                dom.searchResults.innerHTML = '';
                return;
            }
            if (searching) return;
            searching = true;
            dom.searchResults.innerHTML =
                `<div style="padding:12px 14px;" class="subtle"><span class="spinner"></span>Searching...</div>`;

            try {
                const res = await fetch(
                    `{{ route('cart.search') }}?q=${encodeURIComponent(term)}&location_id=${encodeURIComponent(LOCATION_ID)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                const data = await res.json().catch(() => []);
                searching = false;
                dom.searchResults.innerHTML = '';

                if (!Array.isArray(data) || data.length === 0) {
                    dom.searchResults.innerHTML =
                        `<div style="padding:12px 14px;" class="subtle">No in-stock FIFO batches found (this location)</div>`;
                    return;
                }

                data.forEach(item => {
                    const imgPath = pickPrimary(item.images);
                    const imgUrl = getImageUrl(imgPath);
                    const imgHtml = imgUrl ?
                        `<img src="${imgUrl}" alt="${escapeHtml(item.name ?? '')}" loading="lazy">` :
                        '';
                    const retail = Number(item.sell_price || 0);
                    const whole = Number(item.whole_sell_price || 0);
                    const customer = Number(item.customer_whole_price || 0);

                    const row = document.createElement('div');
                    row.className = 'result-row';
                    row.innerHTML = `
                        <div class="thumb">${imgHtml || '<div class="placeholder-img">📷</div>'}</div>
                        <div style="flex:1;">
                            <div class="r-title">${escapeHtml(item.name ?? '')} <span class="subtle" style="font-weight:900;">(${escapeHtml(item.barcode ?? '')})</span></div>
                            <div class="r-meta" style="margin-top:2px;">
                                Batch: <b>${escapeHtml(item.batch_sku ?? '')}</b> •
                                Stock: <span class="pill ${Number(item.quantity)>5?'success':'warning'}">${escapeHtml(item.quantity ?? 0)}</span>
                                <span class="pill" style="margin-left:6px;">Loc #${LOCATION_ID}</span>
                            </div>
                            <div class="r-meta" style="margin-top:6px; display:flex; gap:6px; flex-wrap:wrap;">
                                <span class="pill">Retail: <b>${money(retail)}</b></span>
                                <span class="pill">Whole: <b>${money(whole)}</b></span>
                                <span class="pill">Customer: <b>${money(customer)}</b></span>
                            </div>
                        </div>
                        <div class="actions" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                            <select class="selectx" style="width:auto;">
                                <option value="retail" selected>Retail</option>
                                <option value="whole">Whole</option>
                                <option value="customer_whole">Customer</option>
                            </select>
                            <input class="qtyx" type="number" min="0.0001" step="0.0001" value="1" style="width:70px; height:34px; padding:0 8px; border:1px solid var(--border); border-radius:calc(var(--radius)-8px); background:var(--input); color:var(--foreground);">
                            <button class="btnx" type="button" style="padding:6px 12px;">Add</button>
                        </div>
                    `;

                    const sel = row.querySelector('select');
                    const qty = row.querySelector('input');
                    const btn = row.querySelector('button');

                    row.addEventListener('click', (e) => {
                        const tag = (e.target.tagName || '').toLowerCase();
                        if (['select', 'option', 'input', 'button'].includes(tag)) return;
                        btn.click();
                    });

                    btn.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        await addToCart(
                            item.batch_id,
                            Math.max(0.0001, Number(qty.value || 1)),
                            sel.value,
                            btn
                        );
                    });

                    dom.searchResults.appendChild(row);
                });
            } catch (error) {
                console.error('Search error:', error);
                searching = false;
                dom.searchResults.innerHTML =
                    `<div style="padding:12px 14px;" class="subtle">Error searching. Please try again.</div>`;
            }
        }

        if (dom.cartSearch) {
            dom.cartSearch.addEventListener('input', function() {
                clearTimeout(searchDebounce);
                const term = this.value.trim();
                searchDebounce = setTimeout(() => doSearch(term), 220);
            });
        }

        if (dom.clearSearchBtn) {
            dom.clearSearchBtn.addEventListener('click', function() {
                if (dom.cartSearch) {
                    dom.cartSearch.value = '';
                    dom.cartSearch.focus();
                }
                if (dom.searchResults) dom.searchResults.innerHTML = '';
            });
        }

        if (dom.clearCartBtn) {
            dom.clearCartBtn.addEventListener('click', async () => await clearCart());
        }

        if (dom.dismissHint) {
            dom.dismissHint.addEventListener('click', () => setHint("✅ Ready."));
        }

        /* ================================================================
           GIFT MODAL - FIXED IMAGE HANDLING
        ================================================================ */
        let giftDebounce = null;
        let giftSearching = false;

        function showGiftModalHint(msg) {
            if (dom.giftModalHint) dom.giftModalHint.textContent = msg || '';
        }

        function setGiftModalCustomerLine() {
            if (!selectedCustomer) {
                if (dom.giftModalCustomerLine) dom.giftModalCustomerLine.textContent = 'Customer: Guest';
                showGiftModalHint('Select a customer first.');
                return;
            }
            if (dom.giftModalCustomerLine) {
                dom.giftModalCustomerLine.textContent =
                    `Customer: ${selectedCustomer.name || 'Customer'} (${selectedCustomer.phone || '-'})`;
            }
            showGiftModalHint('');
        }

        function openGiftModal() {
            if (!selectedCustomer) {
                pushToast({ type: 'warning', title: 'Customer required', messages: ['Please select a customer first.'], timeout: 2800 });
                return;
            }
            setGiftModalCustomerLine();
            openWrap(dom.giftModalWrap);
            if (dom.giftSearchInput) {
                dom.giftSearchInput.value = '';
                dom.giftSearchInput.focus();
            }
            if (dom.giftSearchResults) dom.giftSearchResults.innerHTML = '';
        }

        if (dom.openGiftModal) {
            dom.openGiftModal.addEventListener('click', openGiftModal);
        }
        if (dom.openGiftModal2) {
            dom.openGiftModal2.addEventListener('click', openGiftModal);
        }

        async function giftSearch(term) {
            if (!dom.giftSearchResults) return;

            if (!term || term.length < 2) {
                dom.giftSearchResults.innerHTML = '';
                return;
            }
            if (giftSearching) return;
            giftSearching = true;
            dom.giftSearchResults.innerHTML =
                `<div style="padding:12px 14px;" class="subtle"><span class="spinner"></span>Searching gifts...</div>`;

            try {
                const res = await fetch(
                    `{{ route('products.quick.search') }}?q=${encodeURIComponent(term)}&location_id=${encodeURIComponent(LOCATION_ID)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                const rows = await res.json().catch(() => []);
                giftSearching = false;
                dom.giftSearchResults.innerHTML = '';

                if (!Array.isArray(rows) || rows.length === 0) {
                    dom.giftSearchResults.innerHTML =
                        `<div style="padding:12px 14px;" class="subtle">No products found</div>`;
                    return;
                }

                rows.forEach(p => {
                    const imgPath = pickPrimary(p.images);
                    const imgUrl = getImageUrl(imgPath);
                    const imgHtml = imgUrl ?
                        `<img src="${imgUrl}" alt="${escapeHtml(p.name ?? '')}" loading="lazy">` :
                        '';

                    const row = document.createElement('div');
                    row.className = 'result-row';
                    row.innerHTML = `
                        <div class="thumb">${imgHtml || '<div class="placeholder-img">📷</div>'}</div>
                        <div style="flex:1;">
                            <div class="r-title">${escapeHtml(p.name ?? '')} <span class="subtle" style="font-weight:900;">(${escapeHtml(p.barcode ?? '')})</span></div>
                            <div class="r-meta" style="margin-top:2px;">Tap to add as gift (Loc #${LOCATION_ID})</div>
                        </div>
                        <div class="actions" style="display:flex; gap:6px; align-items:center;">
                            <input class="qtyx" type="number" min="0.0001" step="0.0001" value="1" style="width:70px; height:34px; padding:0 8px; border:1px solid var(--border); border-radius:calc(var(--radius)-8px); background:var(--input); color:var(--foreground);">
                            <button class="btnx" type="button" style="padding:6px 12px;">Add Gift</button>
                        </div>
                    `;

                    const qtyInput = row.querySelector('input');
                    const btn = row.querySelector('button');

                    row.addEventListener('click', (e) => {
                        const tag = (e.target.tagName || '').toLowerCase();
                        if (['input', 'button'].includes(tag)) return;
                        btn.click();
                    });

                    btn.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        btn.disabled = true;
                        const old = btn.innerHTML;
                        btn.innerHTML = `<span class="spinner"></span>Adding`;

                        try {
                            const { res: r, data } = await jsonFetch('{{ route('cart.manual.gift.add') }}',
                                'POST', {
                                    product_id: p.id,
                                    quantity: Math.max(0.0001, Number(qtyInput.value || 1))
                                });

                            btn.disabled = false;
                            btn.innerHTML = old;

                            if (!r.ok || !data.success) {
                                toastAll(r, data, 'Gift add failed');
                                return;
                            }

                            renderCart(data.cart);
                            pushToast({ type: 'success', title: 'Gift', messages: [`Gift added (Loc #${LOCATION_ID})`], timeout: 1600 });
                            closeWrap(dom.giftModalWrap);
                        } catch (error) {
                            console.error('Add gift error:', error);
                            btn.disabled = false;
                            btn.innerHTML = old;
                            pushToast({ type: 'danger', title: 'Error', messages: ['Failed to add gift. Please try again.'], timeout: 3000 });
                        }
                    });

                    dom.giftSearchResults.appendChild(row);
                });
            } catch (error) {
                console.error('Gift search error:', error);
                giftSearching = false;
                dom.giftSearchResults.innerHTML =
                    `<div style="padding:12px 14px;" class="subtle">Error searching. Please try again.</div>`;
            }
        }

        if (dom.giftSearchInput) {
            dom.giftSearchInput.addEventListener('input', function() {
                clearTimeout(giftDebounce);
                const term = this.value.trim();
                giftDebounce = setTimeout(() => giftSearch(term), 220);
            });
        }

        /* ================================================================
           CHECKOUT
        ================================================================ */
        function validatePaymentsBeforeSubmit(net) {
            const rows = getPaymentRowsData();
            if (rows.length === 0) return { ok: true, payments: [] };

            const payments = [];
            for (const p of rows) {
                delete p._rowEl;
                if (!p.method) return { ok: false, msg: 'Please select payment method.' };
                if (p.amount <= 0) return { ok: false, msg: 'Payment amount must be greater than 0.' };
                if (p.channel === 'online' && !p.trx_id) {
                    return { ok: false, msg: 'Transaction ID is required for online payment.' };
                }
                payments.push({
                    channel: p.channel,
                    method: p.method,
                    amount: p.amount,
                    trx_id: p.trx_id || null,
                    account_label: p.account_label || null,
                });
            }
            return { ok: true, payments };
        }

        if (dom.checkoutBtn) {
            dom.checkoutBtn.addEventListener('click', async function() {
                if (isProcessing) return;

                const { ptsUse, rewardAmount, disc, net } = calcPayable();

                const valid = validatePaymentsBeforeSubmit(net);
                if (!valid.ok) {
                    pushToast({ type: 'warning', title: 'Payment', messages: [valid.msg || 'Payment invalid'], timeout: 3200 });
                    return;
                }

                isProcessing = true;
                this.disabled = true;
                const oldText = this.innerHTML;
                this.innerHTML = `<span class="spinner"></span>Processing`;

                const payload = {
                    location_id: LOCATION_ID,
                    rewards_points_used: ptsUse,
                    rewards_amount_used: rewardAmount,
                    order_discount: disc,
                    payment_note: (dom.paymentNote?.value || '').trim() || null,
                    apply_balance_mode: dom.autoBalanceMode?.value || 'auto',
                };
                if (valid.payments.length > 0) {
                    payload.payments = valid.payments;
                }

                try {
                    const { res, data } = await jsonFetch('{{ route('cart.checkout') }}', 'POST', payload);

                    this.disabled = false;
                    this.innerHTML = oldText;
                    isProcessing = false;

                    if (!res.ok || !data.success) {
                        toastAll(res, data, 'Checkout failed');
                        return;
                    }

                    pushToast({ type: 'success', title: 'Checkout', messages: [`Checkout complete • Order: ${data.order?.order_no || ''}`], timeout: 2800 });

                    if (data.invoice_url) {
                        window.location.href = data.invoice_url;
                        return;
                    }
                    window.location.reload();
                } catch (error) {
                    console.error('Checkout error:', error);
                    this.disabled = false;
                    this.innerHTML = oldText;
                    isProcessing = false;
                    pushToast({ type: 'danger', title: 'Error', messages: ['Checkout failed. Please try again.'], timeout: 3000 });
                }
            });
        }

        /* ================================================================
           INIT
        ================================================================ */
        setLocationBadges();
        bindCartRowEvents();
        updateRowNumbers();
        calcPayable();

        // Initial customer load
        @if ($cart->customer_id ?? false)
            (async function() {
                try {
                    const res = await fetch(`{{ route('customers.show', $cart->customer_id ?? 0) }}`);
                    const data = await res.json().catch(() => ({}));
                    if (data.id) {
                        renderOrderCustomer(data);
                        if (dom.selectedCustomer) {
                            dom.selectedCustomer.innerHTML =
                                `✅ <strong>${escapeHtml(data.name)}</strong> — Points: ${money(data.reward_points || 0)}`;
                        }
                    }
                } catch (e) {
                    console.error('Initial customer load error:', e);
                }
            })();
        @endif

        // Welcome toast
        pushToast({
            type: 'success',
            title: 'POS Ready',
            messages: [`Location #${LOCATION_ID} • Click any editable value to modify`],
            timeout: 3000
        });

        console.log('✅ POS Cart initialized with optimized AJAX');
    });
</script>


    </div>
@endsection
