@extends('layouts.app')

@section('content')
<div class="container py-3">

<style>
    /* ===== DESIGN SYSTEM =====
       Tokens (--background, --primary, --radius, etc.) are defined globally
       in resources/views/components/style.blade.php and already include
       light/dark variants. Intentionally not redefined here — this page
       just references them via var(...) below. */

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
        box-shadow: var(--card-shadow);
        overflow: hidden;
        transition: box-shadow var(--transition-normal) ease, transform var(--transition-normal) ease;
    }

    .cardx:hover {
        box-shadow: var(--card-shadow-hover);
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
        to { transform: rotate(360deg); }
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

    .toastx-progress > div {
        height: 100%;
        width: 100%;
        transform-origin: left;
        animation: toastProg linear forwards;
    }

    @keyframes toastProg {
        to { transform: scaleX(0); }
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
    .gap-2 { gap: 8px; }
    .gap-3 { gap: 12px; }
    .mt-1 { margin-top: 4px; }
    .mt-2 { margin-top: 8px; }
    .mb-1 { margin-bottom: 4px; }
    .mb-2 { margin-bottom: 8px; }
    .mb-3 { margin-bottom: 12px; }
    .flex { display: flex; }
    .flex-between { display: flex; justify-content: space-between; align-items: center; }
    .flex-wrap { flex-wrap: wrap; }
    .items-center { align-items: center; }
    .w-full { width: 100%; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-success { color: var(--success); }
    .text-warning { color: var(--warning); }
    .text-danger { color: var(--danger); }
    .text-info { color: var(--info); }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }

    @media (max-width: 576px) {
        .grid-2, .grid-3 {
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

    /* Order edit specific styles */
    .order-header {
        border-left: 4px solid var(--primary);
        padding-left: 12px;
    }

    .order-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-weight: 900;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .order-status-badge.pending {
        background: color-mix(in oklch, var(--warning) 20%, transparent 80%);
        color: var(--warning);
        border: 1px solid var(--warning);
    }

    .order-status-badge.completed {
        background: color-mix(in oklch, var(--success) 20%, transparent 80%);
        color: var(--success);
        border: 1px solid var(--success);
    }

    .order-status-badge.unpaid {
        background: color-mix(in oklch, var(--danger) 20%, transparent 80%);
        color: var(--danger);
        border: 1px solid var(--danger);
    }

    .order-status-badge.paid {
        background: color-mix(in oklch, var(--success) 20%, transparent 80%);
        color: var(--success);
        border: 1px solid var(--success);
    }

    .order-status-badge.partial {
        background: color-mix(in oklch, var(--warning) 20%, transparent 80%);
        color: var(--warning);
        border: 1px solid var(--warning);
    }

    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        padding: 12px;
    }

    .order-info-item {
        padding: 8px 12px;
        background: var(--input);
        border-radius: calc(var(--radius) - 6px);
        border: 1px solid var(--border);
    }

    .order-info-item .label {
        font-size: 10px;
        text-transform: uppercase;
        color: var(--muted-foreground);
        letter-spacing: 0.5px;
        font-weight: 700;
        display: block;
    }

    .order-info-item .value {
        font-weight: 900;
        font-size: 15px;
        margin-top: 2px;
    }

    .order-info-item .value .pill {
        font-size: 11px;
        margin-left: 6px;
    }

    .edit-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }

    .btnx-success {
        background: var(--success);
        border-color: var(--success);
        color: #fff;
    }

    .btnx-success:hover {
        background: color-mix(in oklch, var(--success) 80%, transparent 20%);
    }

    .btnx-danger {
        background: var(--danger);
        border-color: var(--danger);
        color: #fff;
    }

    .btnx-danger:hover {
        background: color-mix(in oklch, var(--danger) 80%, transparent 20%);
    }

    .btnx-warning {
        background: var(--warning);
        border-color: var(--warning);
        color: #000;
    }

    .btnx-warning:hover {
        background: color-mix(in oklch, var(--warning) 80%, transparent 20%);
    }

    .order-note {
        padding: 10px 14px;
        background: var(--input);
        border-radius: calc(var(--radius) - 6px);
        border: 1px solid var(--border);
        margin-top: 8px;
        font-size: 13px;
        color: var(--muted-foreground);
    }

    .order-note strong {
        color: var(--foreground);
    }

    .order-timeline {
        padding: 12px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .order-timeline .event {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 8px 12px;
        border-left: 2px solid var(--border);
        padding-left: 16px;
    }

    .order-timeline .event .time {
        font-size: 11px;
        color: var(--muted-foreground);
        white-space: nowrap;
        min-width: 100px;
    }

    .order-timeline .event .desc {
        font-size: 13px;
    }

    .order-timeline .event .desc .icon {
        margin-right: 6px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .order-info-grid {
            grid-template-columns: 1fr 1fr;
        }
        .order-info-item .value {
            font-size: 13px;
        }
    }

    @media (max-width: 576px) {
        .order-info-grid {
            grid-template-columns: 1fr;
        }
        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        .edit-actions {
            width: 100%;
            justify-content: stretch;
        }
        .edit-actions .btnx {
            flex: 1;
            text-align: center;
        }
    }

    /* Back button */
    .back-link {
        color: var(--muted-foreground);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        transition: color 150ms ease;
    }

    .back-link:hover {
        color: var(--foreground);
    }
</style>


@php
    // Helper functions
    if (!function_exists('normalizeUnit')) {
        function normalizeUnit($u) {
            $u = strtolower(trim((string)$u));
            if (in_array($u, ['gm','gram','grams'])) return 'g';
            if (in_array($u, ['kilogram','kilograms'])) return 'kg';
            if (in_array($u, ['lt','liter','litre','liters','litres'])) return 'l';
            if (in_array($u, ['milliliter','millilitre','milliliters','millilitres'])) return 'ml';
            return $u ?: 'pcs';
        }
    }

    if (!function_exists('unitGroup')) {
        function unitGroup($u) {
            $u = strtolower(trim((string)$u));
            if (in_array($u, ['kg','kilogram','kilograms'])) return 'weight';
            if (in_array($u, ['g','gm','gram','grams'])) return 'weight';
            if (in_array($u, ['l','lt','liter','litre','liters','litres'])) return 'volume';
            if (in_array($u, ['ml','milliliter','millilitre','milliliters','millilitres'])) return 'volume';
            return 'count';
        }
    }

    if (!function_exists('fm')) {
        function fm($n) {
            return number_format((float) $n, 2);
        }
    }

    $currentLocationId = (int) session('location_id', $order->location_id ?? 1);

    // Get order items with product data
    $orderItems = $order->items->map(function($item) {
        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product_batch_id' => $item->product_batch_id,
            'name' => $item->product_name ?? $item->product?->name ?? 'Unknown',
            'barcode' => $item->barcode ?? $item->product?->barcode ?? '',
            'batch_sku' => $item->batch?->batch_sku ?? '',
            'unit' => $item->unit ?? $item->batch?->unit ?? 'pcs',
            'batch_unit' => $item->batch?->unit ?? 'pcs',
            'price_type' => $item->price_type ?? 'retail',
            'unit_price' => (float) $item->unit_price,
            'quantity' => (float) $item->quantity,
            'discount_amount' => (float) ($item->discount_amount ?? 0),
            'total_price' => (float) $item->total_price,
            'is_gift' => false,
            'gift_source' => null,
            'image' => $item->product?->images?->first()?->image_path ?? null,
        ];
    })->values()->toArray();

    $cartTotal = array_sum(array_column($orderItems, 'total_price'));

    // Get customer data
    $customerData = null;
    if ($order->customer) {
        $customerData = [
            'id' => $order->customer->id,
            'name' => $order->customer->name,
            'phone' => $order->customer->phone,
            'due_balance' => (float) $order->customer->due_balance,
            'advance_balance' => (float) $order->customer->advance_balance,
            'reward_points' => (float) $order->customer->reward_points,
        ];
    }

    // Get existing payments
    $existingPayments = $order->payments->map(function($p) {
        return [
            'channel' => $p->channel,
            'method' => $p->method,
            'amount' => (float) $p->amount,
            'trx_id' => $p->trx_id,
            'account_label' => $p->account_label,
        ];
    })->toArray();

    if (empty($existingPayments)) {
        $existingPayments = [
            ['channel' => 'offline', 'method' => 'cash', 'amount' => 0, 'trx_id' => null, 'account_label' => null]
        ];
    }
@endphp

<div class="page">

    <!-- ===== HEADER ===== -->
    <div class="flex-between mb-3 flex-wrap">
        <div class="flex items-center gap-2">
            <a href="{{ route('orders.show', $order) }}" class="back-link">← Back</a>
            <div class="order-header">
                <div class="subtle">Edit Order</div>
                <h3 class="title m-0">Order #{{ $order->order_no ?? $order->id }}</h3>
            </div>
        </div>
        <div class="flex gap-2 items-center" style="flex-wrap:wrap;">
            <div style="min-width:200px;">
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
            <div class="edit-actions">
                <button class="btnx btnx-danger btnx-sm" type="button" id="cancelEditBtn">Cancel</button>
                <button class="btnx btnx-success btnx-sm" type="button" id="saveOrderBtn">💾 Save Order</button>
            </div>
        </div>
    </div>

    <!-- ===== ORDER INFO ===== -->
    <div class="cardx mb-3">
        <div class="cardx-hd">
            <div>
                <div class="strong">Order Information</div>
                <div class="subtle">Status: <span class="order-status-badge {{ $order->status }}">{{ ucfirst($order->status ?? 'pending') }}</span></div>
            </div>
            <div class="flex gap-2">
                <span class="pill">Created: {{ $order->created_at->format('Y-m-d H:i') }}</span>
                @if($order->payment_status)
                    <span class="pill {{ $order->payment_status === 'paid' ? 'success' : ($order->payment_status === 'partial' ? 'warning' : 'danger') }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="order-info-grid">
            <div class="order-info-item">
                <span class="label">Order #</span>
                <span class="value">{{ $order->order_no ?? $order->id }}</span>
            </div>
            <div class="order-info-item">
                <span class="label">Customer</span>
                <span class="value" id="orderInfoCustomer">
                    {{ $order->customer?->name ?? 'Guest' }}
                    @if($order->customer)
                        <span class="subtle" style="font-weight:400; font-size:12px;">({{ $order->customer->phone ?? '-' }})</span>
                    @endif
                </span>
            </div>
            <div class="order-info-item">
                <span class="label">Location</span>
                <span class="value">{{ $order->location?->name ?? 'Location #' . ($order->location_id ?? $currentLocationId) }}</span>
            </div>
            <div class="order-info-item">
                <span class="label">Total</span>
                <span class="value" style="color:var(--primary);">{{ fm($order->subtotal ?? 0) }}</span>
            </div>
            <div class="order-info-item">
                <span class="label">Discount</span>
                <span class="value" style="color:var(--warning);">{{ fm($order->discount_total ?? 0) }}</span>
            </div>
            <div class="order-info-item">
                <span class="label">Payable</span>
                <span class="value" style="color:var(--success);">{{ fm($order->payable_total ?? 0) }}</span>
            </div>
            <div class="order-info-item">
                <span class="label">Paid</span>
                <span class="value" style="color:var(--info);">{{ fm($order->paid_total ?? 0) }}</span>
            </div>
            <div class="order-info-item">
                <span class="label">Due</span>
                <span class="value" style="color:var(--danger);">{{ fm($order->due_total ?? 0) }}</span>
            </div>
        </div>

        @if($order->payment_note)
            <div class="order-note">
                <strong>📝 Payment Note:</strong> {{ $order->payment_note }}
            </div>
        @endif
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
            <div id="selectedCustomer" class="subtle mt-2">
                @if($customerData)
                    ✅ <strong>{{ $customerData['name'] }}</strong> — Points: {{ fm($customerData['reward_points'] ?? 0) }}
                @else
                    👤 Guest customer
                @endif
            </div>
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
                    <div class="subtle">Location #<span id="locBadge">{{ $currentLocationId }}</span> • Editing Order #{{ $order->id }}</div>
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
                        @forelse($orderItems as $item)
                            @php
                                $isGift = (bool) ($item['is_gift'] ?? false);
                                $giftSource = $item['gift_source'] ?? null;
                                $batchUnit = $item['batch_unit'] ?? 'pcs';
                                $grp = unitGroup($batchUnit);
                                $saleUnit = normalizeUnit($item['unit'] ?? $batchUnit ?? 'pcs');
                            @endphp

                            <tr data-item-id="{{ $item['id'] }}"
                                data-batch-unit="{{ strtolower($batchUnit ?? 'pcs') }}"
                                class="{{ $isGift ? 'gift-row' : '' }}">
                                <td><span class="rowNo"></span></td>

                                <td>
                                    <div class="mini-img">
                                        @if ($item['image'] ?? false)
                                            <img src="{{ asset('storage/' . $item['image']) }}" alt="">
                                        @endif
                                    </div>
                                </td>

                                <td class="namecell">
                                    <div class="nm">
                                        {{ $item['name'] }}
                                        @if ($isGift)
                                            <span class="pill success giftTag">GIFT • {{ $giftSource }}</span>
                                        @endif
                                    </div>
                                    <div class="bc">{{ $item['barcode'] }}</div>
                                    <div class="sku">Batch: {{ $item['batch_sku'] }}</div>
                                </td>

                                <td>
                                    @if($isGift)
                                        <span class="pill">gift</span>
                                    @else
                                        <select class="selectx priceTypeSelect" data-item-id="{{ $item['id'] }}">
                                            <option value="retail" {{ $item['price_type'] === 'retail' ? 'selected' : '' }}>Retail</option>
                                            <option value="whole" {{ $item['price_type'] === 'whole' ? 'selected' : '' }}>Whole</option>
                                            <option value="customer_whole" {{ $item['price_type'] === 'customer_whole' ? 'selected' : '' }}>Customer</option>
                                        </select>
                                    @endif
                                </td>

                                <td>
                                    @if($isGift)
                                        <span class="pill">{{ $saleUnit }}</span>
                                        <div class="subtle">Fixed</div>
                                    @else
                                        @if($grp === 'weight')
                                            <select class="selectx unitSelect" data-item-id="{{ $item['id'] }}">
                                                <option value="kg" {{ $saleUnit==='kg'?'selected':'' }}>kg</option>
                                                <option value="g"  {{ $saleUnit==='g'?'selected':'' }}>g</option>
                                            </select>
                                            <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                        @elseif($grp === 'volume')
                                            <select class="selectx unitSelect" data-item-id="{{ $item['id'] }}">
                                                <option value="l"  {{ $saleUnit==='l'?'selected':'' }}>L</option>
                                                <option value="ml" {{ $saleUnit==='ml'?'selected':'' }}>ml</option>
                                            </select>
                                            <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                        @else
                                            <select class="selectx unitSelect" data-item-id="{{ $item['id'] }}">
                                                <option value="pcs"   {{ $saleUnit==='pcs'?'selected':'' }}>pcs</option>
                                                <option value="dozen" {{ $saleUnit==='dozen'?'selected':'' }}>dozen</option>
                                                <option value="box"   {{ $saleUnit==='box'?'selected':'' }}>box</option>
                                            </select>
                                            <div class="subtle">Batch: {{ strtolower($batchUnit) }}</div>
                                        @endif
                                    @endif
                                </td>

                                <td class="money">
                                    <span class="price-highlight unitPrice">{{ fm($item['unit_price']) }}</span>
                                    <div class="subtle">per <span class="unitLabel">{{ $saleUnit }}</span></div>
                                </td>

                                <td>
                                    @if($isGift)
                                        <span class="strong">{{ number_format($item['quantity'], 3) }}</span>
                                        <div class="subtle">Gift qty</div>
                                    @else
                                        <input class="editable-input qtyInput" type="number" min="0.0001" step="0.0001"
                                               value="{{ $item['quantity'] }}"
                                               data-item-id="{{ $item['id'] }}">
                                        <div class="qty-msg" data-msg-for="{{ $item['id'] }}"></div>
                                    @endif
                                </td>

                                <td class="money">
                                    @php $amt = (float) ($item['discount_amount'] ?? 0); @endphp
                                    @if ($isGift)
                                        <span class="subtle">—</span>
                                    @elseif ($amt > 0)
                                        <span class="pill success">{{ fm($amt) }}</span>
                                    @else
                                        <span class="subtle">—</span>
                                    @endif
                                </td>

                                <td class="money editable">
                                    @if($isGift)
                                        <span class="strong lineSubtotal">{{ fm($item['total_price']) }}</span>
                                    @else
                                        <input class="editable-input subInput" type="number" min="0" step="0.01"
                                               value="{{ fm($item['total_price']) }}"
                                               data-item-id="{{ $item['id'] }}"
                                               title="Type subtotal to auto-calc qty + unit">
                                        <span class="editable-label">Click to edit</span>
                                        <button class="copy-btn subCopyBtn" data-item-id="{{ $item['id'] }}" title="Copy subtotal">📋</button>
                                    @endif
                                </td>

                                <td class="money">
                                    @if($isGift && $giftSource === 'batch_offer')
                                        <button class="btnx btnx-ghost icon" type="button" disabled title="Auto gift can't be removed">✕</button>
                                    @elseif($isGift && $giftSource === 'manual')
                                        <button class="btnx btnx-ghost icon removeManualGiftBtn" type="button"
                                                data-item-id="{{ $item['id'] }}">✕</button>
                                    @else
                                        <button class="btnx btnx-ghost icon removeBtn" type="button"
                                                data-item-id="{{ $item['id'] }}">✕</button>
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
                            <span id="cartTotalFoot">{{ fm($cartTotal) }}</span>
                        </div>
                        <div class="subtle" id="autoAdjustNote" style="margin-top:4px;"></div>
                        <div class="subtle" id="giftHintLine" style="margin-top:6px;"></div>
                    </div>
                    <div class="flex gap-2" style="flex-wrap:wrap;">
                        <button class="btnx btnx-ghost btnx-sm" type="button" id="togglePaymentsBtn">Payments</button>
                        <button type="button" class="btnx btnx-ghost btnx-sm" id="fullPaymentBtn">Full Payment</button>
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
                <div class="strong">Order Details</div>
                <div class="subtle">Reward + Discount + Auto Balance</div>
            </div>
            <button class="btnx btnx-ghost btnx-sm" type="button" id="openGiftModalBtn2">🎁 Manual Gift</button>
        </div>

        <div style="padding:12px;">
            <div class="grid-2">
                <div>
                    <div class="subtle">Customer</div>
                    <div class="strong" id="orderCustomerName">{{ $order->customer?->name ?? 'Guest' }}</div>
                    <div class="subtle" id="orderCustomerPhone">{{ $order->customer?->phone ?? '—' }}</div>
                </div>

                <div>
                    <div class="subtle">Due / Advance</div>
                    <div class="strong" id="orderCustomerBalance">
                        Due: {{ fm($order->customer?->due_balance ?? 0) }} | Advance: {{ fm($order->customer?->advance_balance ?? 0) }}
                    </div>
                </div>

                <div>
                    <div class="subtle">Reward Points Available</div>
                    <div class="strong" id="rewardAvailable">{{ fm($order->customer?->reward_points ?? 0) }}</div>
                </div>

                <div>
                    <div class="subtle">Reward Points Use (amount auto = points × 1)</div>
                    <input class="inputx" id="rewardPointsUse" type="number" min="0" step="0.01" value="{{ $order->rewards_points_used ?? 0 }}">
                </div>

                <div>
                    <div class="subtle">Order Discount</div>
                    <input class="inputx" id="orderDiscount" type="number" min="0" step="0.01" value="{{ $order->discount_total ?? 0 }}">
                </div>

                <div>
                    <div class="subtle">Auto Apply Balance</div>
                    <select class="selectx" id="autoBalanceMode" style="height:42px;">
                        <option value="auto" {{ ($order->apply_balance_mode ?? 'auto') === 'auto' ? 'selected' : '' }}>Auto (Advance reduce, Due add)</option>
                        <option value="none" {{ ($order->apply_balance_mode ?? 'auto') === 'none' ? 'selected' : '' }}>Do not apply</option>
                    </select>
                </div>
            </div>

            <div style="border-top:1px solid var(--border); padding-top:10px; margin-top:8px;">
                <div class="grid-3">
                    <div>
                        <div class="subtle">Cart Total</div>
                        <div class="strong" id="cartTotalLive">{{ fm($cartTotal) }}</div>
                    </div>
                    <div>
                        <div class="subtle">Payable</div>
                        <div class="strong" id="payableTotalLive">{{ fm($cartTotal) }}</div>
                    </div>
                    <div>
                        <div class="subtle">Net After Balance</div>
                        <div class="strong" id="netAfterBalanceLive">{{ fm($cartTotal) }}</div>
                    </div>
                </div>
                <div class="subtle" id="orderHint" style="margin-top:8px;"></div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ===== PAYMENTS ===== -->
    <!-- ============================================================ -->
    <div class="cardx mb-3" id="paymentsCard" style="display:block;">
        <div class="cardx-hd">
            <div>
                <div class="strong">Payments</div>
                <div class="subtle">Edit payment details</div>
            </div>
            <div class="flex gap-2" style="flex-wrap:wrap;">
                <button type="button" class="btnx btnx-ghost btnx-sm" id="addPaymentRowBtn">+ Add Payment</button>
            </div>
        </div>

        <div style="padding:12px;">
            <div class="subtle" style="margin-bottom:8px;">
                Tip: If Net After Balance is 0.00, you can checkout without adding payment.
            </div>

            <div id="paymentRows">
                @foreach($existingPayments as $payment)
                    <div class="payment-row">
                        <div>
                            <span class="label">Channel</span>
                            <select class="selectx payChannel" style="width:100%;">
                                <option value="offline" {{ $payment['channel'] === 'offline' ? 'selected' : '' }}>offline</option>
                                <option value="online" {{ $payment['channel'] === 'online' ? 'selected' : '' }}>online</option>
                            </select>
                        </div>
                        <div>
                            <span class="label">Method</span>
                            <select class="selectx payMethod" style="width:100%;">
                                @php
                                    $methods = $payment['channel'] === 'online' ? ['bkash', 'nagad', 'rocket', 'upay', 'stripe', 'paypal', 'sslcommerz'] : ['cash', 'card', 'bank', 'cheque'];
                                @endphp
                                @foreach($methods as $m)
                                    <option value="{{ $m }}" {{ $payment['method'] === $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <span class="label">Amount</span>
                            <input class="inputx payAmount" type="number" min="0" step="0.0001" placeholder="0.00" value="{{ $payment['amount'] }}" style="height:34px;">
                        </div>
                        <div>
                            <span class="label">Account Label</span>
                            <input class="inputx payAccount" placeholder="Optional" value="{{ $payment['account_label'] ?? '' }}" style="height:34px;">
                        </div>
                        <div class="payTrxWrap full-width" style="display:{{ $payment['channel'] === 'online' ? 'block' : 'none' }};">
                            <span class="label">Transaction ID (Required for online)</span>
                            <input class="inputx payTrx" placeholder="Example: BK123456789" value="{{ $payment['trx_id'] ?? '' }}" style="height:34px;">
                        </div>
                        <div class="full-width" style="display:flex; justify-content:space-between; align-items:center;">
                            <span class="pay-hint"></span>
                            <button type="button" class="btnx btnx-ghost btnx-sm remove-payment">✕ Remove</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid-3" style="margin-top:10px;">
                <div>
                    <div class="subtle">Paid Total</div>
                    <div class="strong" id="paidTotalLive">{{ fm(array_sum(array_column($existingPayments, 'amount'))) }}</div>
                </div>
                <div>
                    <div class="subtle">Due Total</div>
                    <div class="strong" id="dueTotalLive">{{ fm(max(0, $cartTotal - array_sum(array_column($existingPayments, 'amount')))) }}</div>
                </div>
                <div>
                    <div class="subtle">Change</div>
                    <div class="strong" id="changeTotalLive">0.00</div>
                </div>
            </div>

            <div style="margin-top:10px;">
                <div class="subtle">Payment Note (optional)</div>
                <input class="inputx" id="paymentNote" placeholder="Example: Cash + bKash, partial paid, etc." value="{{ $order->payment_note ?? '' }}">
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
                <div class="subtle" id="giftModalCustomerLine">Customer: {{ $order->customer?->name ?? 'Guest' }}</div>
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

    let LOCATION_ID = Number(document.getElementById('locationSelect')?.value || {{ $currentLocationId }});
    let selectedCustomer = @json($customerData);
    let updating = new Set();
    const ORDER_ID = {{ $order->id }};

    const METHODS = {
        offline: ['cash', 'card', 'bank', 'cheque'],
        online: ['bkash', 'nagad', 'rocket', 'upay', 'stripe', 'paypal', 'sslcommerz'],
    };

    /* ================================================================
       DOM REFS
    ================================================================ */
    const $ = id => document.getElementById(id);
    const qs = (sel, ctx) => (ctx || document).querySelector(sel);
    const qsa = (sel, ctx) => (ctx || document).querySelectorAll(sel);

    const locationSelect = $('locationSelect');
    const locBadge = $('locBadge');
    const giftLocBadge = $('giftLocBadge');
    const toastStack = $('toastStack');
    const overlay = $('overlay');

    const customerSearch = $('customerSearch');
    const customerResults = $('customerResults');
    const selectedCustomerEl = $('selectedCustomer');
    const addCustomerBtn = $('addCustomerBtn');
    const clearCustomerBtn = $('clearCustomerBtn');
    const customerModalWrap = $('customerModalWrap');
    const closeCustomerModalBtn = $('closeCustomerModalBtn');
    const cancelCustomerBtn = $('cancelCustomerBtn');
    const saveCustomerBtn = $('saveCustomerBtn');
    const newCustomerName = $('newCustomerName');
    const newCustomerPhone = $('newCustomerPhone');

    const cartSearch = $('cartSearch');
    const searchResults = $('searchResults');
    const clearSearchBtn = $('clearSearchBtn');

    const cartBody = $('cartBody');
    const cartTotalFoot = $('cartTotalFoot');
    const clearCartBtn = $('clearCartBtn');

    const togglePaymentsBtn = $('togglePaymentsBtn');
    const paymentsCard = $('paymentsCard');
    const paymentRowsEl = $('paymentRows');
    const addPaymentRowBtn = $('addPaymentRowBtn');
    const fullPaymentBtn = $('fullPaymentBtn');
    const paidTotalLive = $('paidTotalLive');
    const dueTotalLive = $('dueTotalLive');
    const changeTotalLive = $('changeTotalLive');
    const paymentNoteEl = $('paymentNote');
    const paymentHintEl = $('paymentHint');

    const saveOrderBtn = $('saveOrderBtn');
    const cancelEditBtn = $('cancelEditBtn');

    const orderCustomerName = $('orderCustomerName');
    const orderCustomerPhone = $('orderCustomerPhone');
    const orderCustomerBalance = $('orderCustomerBalance');
    const rewardAvailable = $('rewardAvailable');
    const rewardPointsUse = $('rewardPointsUse');
    const orderDiscount = $('orderDiscount');
    const autoBalanceMode = $('autoBalanceMode');
    const cartTotalLive = $('cartTotalLive');
    const payableTotalLive = $('payableTotalLive');
    const netAfterBalanceLive = $('netAfterBalanceLive');
    const orderHint = $('orderHint');
    const autoAdjustNote = $('autoAdjustNote');

    const giftHintLine = $('giftHintLine');
    const openGiftModalBtn = $('openGiftModalBtn');
    const openGiftModalBtn2 = $('openGiftModalBtn2');
    const giftModalWrap = $('giftModalWrap');
    const closeGiftModalBtn = $('closeGiftModalBtn');
    const giftSearchInput = $('giftSearchInput');
    const giftSearchResults = $('giftSearchResults');
    const giftModalCustomerLine = $('giftModalCustomerLine');
    const giftModalHint = $('giftModalHint');

    const sweetHint = $('sweetHint');
    const dismissHintBtn = $('dismissHintBtn');

    /* ================================================================
       HELPERS
    ================================================================ */
    function money(n) { return Number(n || 0).toFixed(2); }
    function num(v) { return Math.max(0, Number(v || 0)); }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function normalizeUnit(u) {
        u = String(u || '').trim().toLowerCase();
        if (['gm','gram','grams'].includes(u)) return 'g';
        if (['kilogram','kilograms'].includes(u)) return 'kg';
        if (['lt','liter','litre','liters','litres'].includes(u)) return 'l';
        if (['milliliter','millilitre','milliliters','millilitres'].includes(u)) return 'ml';
        return u || 'pcs';
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
            if (unit === 'kg') return 1000;
            return 1;
        }
        if (group === 'volume') {
            if (unit === 'l') return 1000;
            return 1;
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
        sweetHint.textContent = msg || "💡 Tip: You can type subtotal in cart row to auto-calc Qty & Unit.";
    }

    function setLocationBadges() {
        if (locBadge) locBadge.textContent = String(LOCATION_ID);
        if (giftLocBadge) giftLocBadge.textContent = String(LOCATION_ID);
    }

    function cartTotalNow() {
        return num(cartTotalFoot?.textContent || cartTotalLive?.textContent || 0);
    }

    /* ================================================================
       TOAST
    ================================================================ */
    function pushToast({ type = 'warning', title = 'Notice', messages = [], timeout = 3200 } = {}) {
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

        while (toastStack.children.length > 4) {
            toastStack.removeChild(toastStack.lastChild);
        }

        if (timeout > 0) el._t = setTimeout(remove, timeout);
    }

    function extractMessages(data) {
        const msgs = [];
        if (data?.errors && typeof data.errors === 'object') {
            Object.values(data.errors).forEach(arr => {
                if (Array.isArray(arr)) arr.forEach(m => msgs.push(String(m)));
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

    /* ================================================================
       MODALS
    ================================================================ */
    function openWrap(wrap) {
        if (!wrap) return;
        overlay.style.display = 'block';
        wrap.classList.add('show');
        wrap.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeWrap(wrap) {
        if (!wrap) return;
        wrap.classList.remove('show');
        wrap.setAttribute('aria-hidden', 'true');
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    function closeAllModals() {
        closeWrap(customerModalWrap);
        closeWrap(giftModalWrap);
    }

    overlay.addEventListener('click', closeAllModals);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAllModals(); });

    [customerModalWrap, giftModalWrap].forEach(wrap => {
        wrap?.addEventListener('click', (e) => {
            if (e.target === wrap) closeWrap(wrap);
        });
    });

    addCustomerBtn?.addEventListener('click', () => openWrap(customerModalWrap));
    closeCustomerModalBtn?.addEventListener('click', () => closeWrap(customerModalWrap));
    cancelCustomerBtn?.addEventListener('click', () => closeWrap(customerModalWrap));
    closeGiftModalBtn?.addEventListener('click', () => closeWrap(giftModalWrap));

    /* ================================================================
       LOCATION
    ================================================================ */
    locationSelect?.addEventListener('change', () => {
        LOCATION_ID = Number(locationSelect.value || 1);
        setLocationBadges();
        pushToast({ type: 'success', title: 'Location', messages: [`Switched to location #${LOCATION_ID}`], timeout: 1800 });
        refreshOrderItems();
    });

    /* ================================================================
       CUSTOMER
    ================================================================ */
    let cDebounce = null;
    let activeQuery = '';

    function renderOrderCustomer(customer) {
        selectedCustomer = customer || null;
        if (!selectedCustomer) {
            orderCustomerName.textContent = 'Guest';
            orderCustomerPhone.textContent = '—';
            orderCustomerBalance.textContent = 'Due: 0.00 | Advance: 0.00';
            rewardAvailable.textContent = '0.00';
            rewardPointsUse.value = '0.00';
            selectedCustomerEl.textContent = '👤 Guest customer';
            calcPayable();
            return;
        }
        orderCustomerName.textContent = selectedCustomer.name || 'Customer';
        orderCustomerPhone.textContent = selectedCustomer.phone || '—';
        orderCustomerBalance.textContent =
            `Due: ${money(selectedCustomer.due_balance)} | Advance: ${money(selectedCustomer.advance_balance)}`;
        rewardAvailable.textContent = money(selectedCustomer.reward_points);
        selectedCustomerEl.innerHTML = `✅ <strong>${escapeHtml(selectedCustomer.name)}</strong> — Points: ${money(selectedCustomer.reward_points || 0)}`;
        calcPayable();
    }

    async function setCustomer(customer) {
        const res = await fetch(`{{ route('cart.customer.set') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ customer_id: customer ? customer.id : null })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.success) {
            toastAll(res, data, 'Customer');
            return;
        }
        const c = customer ? data.customer : null;
        renderOrderCustomer(c);
        customerResults.innerHTML = '';
        customerSearch.value = '';
        pushToast({
            type: 'success',
            title: 'Customer',
            messages: [c ? `${c.name} selected` : 'Guest selected'],
            timeout: 1600
        });
    }

    customerSearch?.addEventListener('input', () => {
        clearTimeout(cDebounce);
        const q = customerSearch.value.trim();
        if (q.length < 2) {
            customerResults.innerHTML = '';
            return;
        }
        cDebounce = setTimeout(async () => {
            activeQuery = q;
            const res = await fetch(`{{ route('customers.quick.search') }}?q=${encodeURIComponent(q)}`);
            const rows = await res.json().catch(() => []);
            if (activeQuery !== q) return;
            customerResults.innerHTML = '';

            const guestRow = document.createElement('div');
            guestRow.className = 'result-row';
            guestRow.innerHTML = `<div><strong>Guest</strong><div class="subtle">No customer selected</div></div>`;
            guestRow.onclick = () => setCustomer(null);
            customerResults.appendChild(guestRow);

            if (!rows.length) {
                customerResults.insertAdjacentHTML('beforeend', `<div class="subtle" style="padding:8px">No customers found</div>`);
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
                customerResults.appendChild(row);
            });
        }, 250);
    });

    clearCustomerBtn?.addEventListener('click', () => setCustomer(null));

    saveCustomerBtn?.addEventListener('click', async () => {
        const name = newCustomerName.value.trim();
        if (!name) {
            pushToast({ type: 'warning', title: 'Customer', messages: ['Name required'], timeout: 2500 });
            return;
        }
        const phone = newCustomerPhone.value.trim();
        const res = await fetch(`{{ route('customers.store') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, phone })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok || (!data.success && !data.customer)) {
            toastAll(res, data, 'Create customer');
            return;
        }
        await setCustomer(data.customer);
        newCustomerName.value = '';
        newCustomerPhone.value = '';
        closeWrap(customerModalWrap);
    });

    /* ================================================================
       CALCULATE PAYABLE
    ================================================================ */
    function calcPayable() {
        const cartT = cartTotalNow();
        const availablePts = num(selectedCustomer?.reward_points || 0);
        rewardAvailable.textContent = money(availablePts);

        let ptsUse = num(rewardPointsUse.value);
        if (!selectedCustomer) ptsUse = 0;
        if (ptsUse > availablePts) ptsUse = availablePts;
        const rewardAmount = ptsUse * POINT_RATE;

        let disc = num(orderDiscount.value);
        if (disc > cartT) disc = cartT;

        let payable = cartT - disc - rewardAmount;
        if (payable < 0) payable = 0;

        const mode = autoBalanceMode.value || 'auto';
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

        rewardPointsUse.value = money(ptsUse);
        cartTotalLive.textContent = money(cartT);
        payableTotalLive.textContent = money(payable);
        netAfterBalanceLive.textContent = money(net);
        autoAdjustNote.textContent = (selectedCustomer && mode === 'auto')
            ? `Auto: ${note.trim() || 'No balance applied.'}`
            : (mode === 'none' ? 'Auto balance disabled.' : '');
        orderHint.textContent = `Reward: ${money(ptsUse)} pts (${money(rewardAmount)}), Discount: ${money(disc)}.`;

        recalcPaymentSummary(net);
        renderPaymentHint(net);
        return { cartT, ptsUse, rewardAmount, disc, payable, net };
    }

    [rewardPointsUse, orderDiscount, autoBalanceMode].forEach(el => {
        el?.addEventListener('input', calcPayable);
        el?.addEventListener('change', calcPayable);
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
        methodSelect.value = method;

        const channelSelect = row.querySelector('.payChannel');
        const trxWrap = row.querySelector('.payTrxWrap');

        function refresh() {
            const net = num(netAfterBalanceLive.textContent || 0);
            recalcPaymentSummary(net);
            renderPaymentHint(net);
            renderPaymentHintRow(row);
        }

        channelSelect.addEventListener('change', () => {
            const ch = channelSelect.value;
            methodSelect.innerHTML = methodOptions(ch);
            methodSelect.value = METHODS[ch]?.[0] || '';
            trxWrap.style.display = (ch === 'online') ? 'block' : 'none';
            if (ch !== 'online') row.querySelector('.payTrx').value = '';
            refresh();
        });

        row.querySelector('.payAmount').addEventListener('input', refresh);
        row.querySelector('.payTrx').addEventListener('input', refresh);
        row.querySelector('.payAccount').addEventListener('input', refresh);

        row.querySelector('.remove-payment').addEventListener('click', () => {
            row.remove();
            refresh();
        });

        renderPaymentHintRow(row);
        return row;
    }

    function getPaymentRowsData() {
        const rows = Array.from(paymentRowsEl.querySelectorAll('.payment-row'));
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
        if (!m) { hint.textContent = 'Please select a method.'; return; }
        if (a <= 0) { hint.textContent = 'Enter an amount greater than 0.'; return; }
        if (ch === 'online' && !trx) { hint.textContent = 'Online payment needs Transaction ID.'; return; }
        hint.textContent = `✓ ${ch} / ${m} / ${money(a)}`;
    }

    function recalcPaymentSummary(net) {
        const rows = getPaymentRowsData();
        const paid = rows.reduce((s, p) => s + num(p.amount), 0);
        let due = 0, change = 0;
        if (net <= 0) { due = 0; change = 0; }
        else if (paid < net) { due = net - paid; change = 0; }
        else { due = 0; change = paid - net; }
        paidTotalLive.textContent = money(paid);
        dueTotalLive.textContent = money(due);
        changeTotalLive.textContent = money(change);
    }

    function renderPaymentHint(net) {
        const rows = getPaymentRowsData();
        rows.forEach(p => renderPaymentHintRow(p._rowEl));
        const paid = rows.reduce((s, p) => s + num(p.amount), 0);
        if (net <= 0) {
            paymentHintEl.textContent = 'Net is 0.00. You can save without payment.';
            return;
        }
        if (rows.length === 0) {
            paymentHintEl.textContent = 'Add payment(s) if customer pays now. Otherwise leave empty and save as due.';
            return;
        }
        if (paid <= 0) {
            paymentHintEl.textContent = 'Payment rows added but total is 0.00. Enter amounts.';
            return;
        }
        if (paid < net) {
            paymentHintEl.textContent = `Partial payment. Due will be ${money(net - paid)}.`;
            return;
        }
        if (paid === net) {
            paymentHintEl.textContent = 'Full payment. Order will be completed.';
            return;
        }
        paymentHintEl.textContent = `Over payment. Change will be ${money(paid - net)}.`;
    }

    function ensureOnePaymentRow() {
        if (paymentRowsEl.querySelectorAll('.payment-row').length === 0) {
            paymentRowsEl.appendChild(createPaymentRow());
        }
    }

    togglePaymentsBtn?.addEventListener('click', () => {
        const show = paymentsCard.style.display === 'none';
        paymentsCard.style.display = show ? 'block' : 'none';
        if (show) {
            ensureOnePaymentRow();
            const net = num(netAfterBalanceLive.textContent || 0);
            recalcPaymentSummary(net);
            renderPaymentHint(net);
        }
    });

    addPaymentRowBtn?.addEventListener('click', () => {
        paymentRowsEl.appendChild(createPaymentRow());
        const net = num(netAfterBalanceLive.textContent || 0);
        recalcPaymentSummary(net);
        renderPaymentHint(net);
    });

    fullPaymentBtn?.addEventListener('click', () => {
        const { net } = calcPayable();
        paymentsCard.style.display = 'block';
        ensureOnePaymentRow();
        const firstRow = paymentRowsEl.querySelector('.payment-row');
        const amountInput = firstRow?.querySelector('.payAmount');
        if (amountInput) {
            amountInput.value = money(net);
            amountInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
        recalcPaymentSummary(num(net));
        renderPaymentHint(num(net));
    });

    /* ================================================================
       RENDER CART
    ================================================================ */
    function renderCart(items, total) {
        cartBody.innerHTML = '';
        const sortedItems = [...(items || [])].sort((a, b) => Number(b.id) - Number(a.id));

        if (!sortedItems.length) {
            cartBody.innerHTML = `<tr id="emptyRow"><td colspan="10"><div class="empty-state">🧺 Cart is empty — search and add products</div></td></tr>`;
            cartTotalFoot.textContent = money(total || 0);
            calcPayable();
            bindCartRowEvents();
            updateRowNumbers();
            return;
        }

        sortedItems.forEach((item, index) => {
            const isGift = item.is_gift || false;
            const giftSource = item.gift_source || null;
            const batchUnit = normalizeUnit(item.batch_unit || 'pcs');
            const grp = unitGroupFromBatchUnit(batchUnit);
            const currentUnit = normalizeUnit(item.unit || batchUnit);
            const imgHtml = item.image ? `<img src="${item.image}" alt="">` : '';
            const trClass = isGift ? 'gift-row' : '';
            const rowNo = sortedItems.length - index;

            cartBody.insertAdjacentHTML('beforeend', `
                <tr data-item-id="${item.id}" data-batch-unit="${escapeHtml(batchUnit)}" class="${trClass}">
                    <td><span class="rowNo">${rowNo}</span></td>
                    <td><div class="mini-img">${imgHtml}</div></td>
                    <td class="namecell">
                        <div class="nm">${escapeHtml(item.product_name || item.name || '')} ${isGift ? `<span class="pill success giftTag">GIFT</span>` : ''}</div>
                        <div class="bc">${escapeHtml(item.barcode || '')}</div>
                        <div class="sku">Batch: ${escapeHtml(item.batch_sku || '')}</div>
                    </td>
                    <td>
                        ${isGift ? `<span class="pill">gift</span>` : `
                            <select class="selectx priceTypeSelect" data-item-id="${item.id}">
                                <option value="retail" ${(item.price_type||'retail') === 'retail' ? 'selected' : ''}>Retail</option>
                                <option value="whole" ${(item.price_type||'retail') === 'whole' ? 'selected' : ''}>Whole</option>
                                <option value="customer_whole" ${(item.price_type||'retail') === 'customer_whole' ? 'selected' : ''}>Customer</option>
                            </select>
                        `}
                    </td>
                    <td>
                        ${isGift ? `<span class="pill">${escapeHtml(currentUnit)}</span><div class="subtle">Fixed</div>` : `
                            ${grp === 'weight' ? `
                                <select class="selectx unitSelect" data-item-id="${item.id}">
                                    <option value="kg" ${currentUnit==='kg'?'selected':''}>kg</option>
                                    <option value="g" ${currentUnit==='g'?'selected':''}>g</option>
                                </select>
                            ` : grp === 'volume' ? `
                                <select class="selectx unitSelect" data-item-id="${item.id}">
                                    <option value="l" ${currentUnit==='l'?'selected':''}>L</option>
                                    <option value="ml" ${currentUnit==='ml'?'selected':''}>ml</option>
                                </select>
                            ` : `
                                <select class="selectx unitSelect" data-item-id="${item.id}">
                                    <option value="pcs" ${currentUnit==='pcs'?'selected':''}>pcs</option>
                                    <option value="dozen" ${currentUnit==='dozen'?'selected':''}>dozen</option>
                                    <option value="box" ${currentUnit==='box'?'selected':''}>box</option>
                                </select>
                            `}
                            <div class="subtle">Batch: ${escapeHtml(batchUnit)}</div>
                        `}
                    </td>
                    <td class="money">
                        <span class="price-highlight unitPrice">${money(item.unit_price)}</span>
                        <div class="subtle">per <span class="unitLabel">${escapeHtml(currentUnit)}</span></div>
                    </td>
                    <td>
                        ${isGift ? `<span class="strong">${Number(item.quantity||0).toFixed(3)}</span><div class="subtle">Gift qty</div>` : `
                            <input class="editable-input qtyInput" type="number" min="0.0001" step="0.0001"
                                   value="${Number(item.quantity||0)}" data-item-id="${item.id}">
                            <div class="qty-msg" data-msg-for="${item.id}"></div>
                        `}
                    </td>
                    <td class="money">
                        ${isGift ? `<span class="subtle">—</span>` : `
                            ${Number(item.discount_amount||0) > 0 ? `<span class="pill success">${money(item.discount_amount)}</span>` : '<span class="subtle">—</span>'}
                        `}
                    </td>
                    <td class="money editable">
                        ${isGift ? `<span class="strong lineSubtotal">${money(item.total_price)}</span>` : `
                            <input class="editable-input subInput" type="number" min="0" step="0.01"
                                   value="${money(item.total_price)}" data-item-id="${item.id}"
                                   title="Type subtotal to auto-calc qty + unit">
                            <span class="editable-label">Click to edit</span>
                            <button class="copy-btn subCopyBtn" data-item-id="${item.id}" title="Copy subtotal">📋</button>
                        `}
                    </td>
                    <td class="money">
                        ${isGift && giftSource === 'batch_offer' ? `
                            <button class="btnx btnx-ghost icon" type="button" disabled title="Auto gift can't be removed">✕</button>
                        ` : isGift && giftSource === 'manual' ? `
                            <button class="btnx btnx-ghost icon removeManualGiftBtn" type="button" data-item-id="${item.id}">✕</button>
                        ` : `
                            <button class="btnx btnx-ghost icon removeBtn" type="button" data-item-id="${item.id}">✕</button>
                        `}
                    </td>
                </tr>
            `);
        });

        cartTotalFoot.textContent = money(total || 0);
        calcPayable();
        bindCartRowEvents();
        updateRowNumbers();
    }

    function updateRowNumbers() {
        const rows = Array.from(cartBody.querySelectorAll('tr[data-item-id]'));
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

    /* ================================================================
       CART ACTIONS - FIXED ENDPOINTS
    ================================================================ */

    // ✅ FIXED: Add item to ORDER (not cart)
    async function addToOrder(batchId, qty, priceType, btn) {
        clearQtyMessages();
        if (btn) {
            btn.disabled = true;
            btn._old = btn.innerHTML;
            btn.innerHTML = `<span class="spin"></span>Adding`;
        }

        const { res, data } = await jsonFetch(`{{ route('order.add.item', $order) }}`, 'POST', {
            batch_id: batchId,
            quantity: qty,
            price_type: priceType,
            unit: null // let controller determine unit
        });

        if (btn) {
            btn.disabled = false;
            btn.innerHTML = btn._old || 'Add';
        }

        if (!res.ok || !data.success) {
            toastAll(res, data, 'Add failed');
            return;
        }

        await refreshOrderItems();
        pushToast({ type: 'success', title: 'Order', messages: [`Added to order (Location #${LOCATION_ID})`], timeout: 1600 });
    }

    // ✅ FIXED: Update item in ORDER (not cart)
    async function updateOrderItem(itemId, priceType, quantity, unit) {
        clearQtyMessages();
        if (updating.has(itemId)) return;
        updating.add(itemId);

        const { res, data } = await jsonFetch(`{{ route('order.item.update', $order) }}`, 'POST', {
            item_id: itemId,
            price_type: priceType,
            quantity: quantity,
            unit: unit || null
        });

        updating.delete(itemId);

        if (!res.ok || !data.success) {
            toastAll(res, data, 'Update failed');
            return;
        }

        await refreshOrderItems();
        pushToast({ type: 'success', title: 'Order', messages: [`Updated`], timeout: 1500 });
    }

    // ✅ FIXED: Remove item from ORDER (not cart)
    async function removeOrderItem(itemId) {
        clearQtyMessages();
        const { res, data } = await jsonFetch(`{{ route('order.item.remove', [$order, 'itemId']) }}`.replace('itemId', itemId), 'DELETE', {});
        if (!res.ok || !data.success) {
            toastAll(res, data, 'Remove failed');
            return;
        }
        await refreshOrderItems();
        pushToast({ type: 'success', title: 'Order', messages: ['Removed'], timeout: 1500 });
    }

    // ✅ FIXED: Remove manual gift from ORDER (not cart)
    async function removeManualGiftFromOrder(itemId) {
        clearQtyMessages();
        const { res, data } = await jsonFetch(`{{ route('order.gift.manual.remove', [$order, 'itemId']) }}`.replace('itemId', itemId), 'DELETE', {});
        if (!res.ok || !data.success) {
            toastAll(res, data, 'Remove gift failed');
            return;
        }
        await refreshOrderItems();
        pushToast({ type: 'success', title: 'Gift', messages: ['Gift removed'], timeout: 1600 });
    }

    // ✅ FIXED: Clear all items from ORDER (not cart)
    async function clearOrderItems() {
        clearQtyMessages();
        const { res, data } = await jsonFetch(`{{ route('order.clear.items', $order) }}`, 'DELETE', {});
        if (!res.ok || !data.success) {
            toastAll(res, data, 'Clear failed');
            return;
        }
        await refreshOrderItems();
        pushToast({ type: 'success', title: 'Order', messages: ['Cleared'], timeout: 1600 });
    }

    // ✅ FIXED: Refresh ORDER items (not cart)
    async function refreshOrderItems() {
        try {
            const res = await fetch(`{{ route('orders.items', $order) }}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json().catch(() => ([]));

            // Calculate total
            const total = Array.isArray(data) ? data.reduce((sum, item) => sum + Number(item.total_price || 0), 0) : 0;

            renderCart(data, total);

            // Update order totals display
            const subtotalEl = document.querySelector('.order-info-item .value[style*="primary"]');
            if (subtotalEl) subtotalEl.textContent = money(total);
            const payableEl = document.querySelector('.order-info-item .value[style*="success"]');
            if (payableEl) payableEl.textContent = money(total);

        } catch (error) {
            console.error('Error refreshing order items:', error);
        }
    }

    /* ================================================================
       BIND CART ROW EVENTS - UPDATED
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
            await updateOrderItem(itemId, priceType, qty, unit);
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

            await updateOrderItem(itemId, priceType, q, pick.unit);
        }, SUB_DELAY_MS);
    }

    function bindCartRowEvents() {
        document.querySelectorAll('#cartBody tr[data-item-id]').forEach(tr => {
            const selType = tr.querySelector('.priceTypeSelect');
            const selUnit = tr.querySelector('.unitSelect');
            const qtyInput = tr.querySelector('.qtyInput');
            const subInput = tr.querySelector('.subInput');

            if (selType) selType.onchange = () => scheduleRowUpdate(tr);
            if (selUnit) selUnit.onchange = () => scheduleRowUpdate(tr);
            if (qtyInput) {
                qtyInput.oninput = () => scheduleRowUpdate(tr);
                qtyInput.onchange = () => scheduleRowUpdate(tr);
            }
            if (subInput) {
                subInput.oninput = () => scheduleSubUpdate(tr);
                subInput.onchange = () => scheduleSubUpdate(tr);
            }

            const copyBtn = tr.querySelector('.subCopyBtn');
            if (copyBtn) {
                copyBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const val = subInput?.value || '';
                    if (!val) return;
                    navigator.clipboard?.writeText(val).then(() => {
                        copyBtn.textContent = '✓';
                        copyBtn.classList.add('copied');
                        setTimeout(() => {
                            copyBtn.textContent = '📋';
                            copyBtn.classList.remove('copied');
                        }, 1200);
                    }).catch(() => {});
                });
            }
        });

        // ✅ FIXED: Use removeOrderItem instead of removeCartItem
        document.querySelectorAll('.removeBtn').forEach(btn => {
            btn.onclick = async () => await removeOrderItem(btn.dataset.itemId);
        });

        // ✅ FIXED: Use removeManualGiftFromOrder instead of removeManualGift
        document.querySelectorAll('.removeManualGiftBtn').forEach(btn => {
            btn.onclick = async () => await removeManualGiftFromOrder(btn.dataset.itemId);
        });

        document.querySelectorAll('.editable-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.select();
            });
        });
    }

    /* ================================================================
       SEARCH - FIXED ADD TO ORDER
    ================================================================ */
    function pickPrimary(images) {
        if (!Array.isArray(images) || images.length === 0) return '';
        const p = images.find(x => Number(x.is_primary) === 1);
        return (p?.image_path) || images[0].image_path || '';
    }

    let searchDebounce = null;
    let searching = false;

    async function doSearch(term) {
        if (term.length < 2) {
            searchResults.innerHTML = '';
            return;
        }
        if (searching) return;
        searching = true;
        searchResults.innerHTML = `<div style="padding:12px 14px;" class="subtle"><span class="spin"></span>Searching...</div>`;

        const res = await fetch(`{{ route('cart.search') }}?q=${encodeURIComponent(term)}&location_id=${encodeURIComponent(LOCATION_ID)}`, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json().catch(() => []);
        searching = false;
        searchResults.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
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

            // ✅ FIXED: Use addToOrder instead of addToCart
            btn.addEventListener('click', async (e) => {
                e.stopPropagation();
                await addToOrder(item.batch_id, Math.max(0.0001, Number(qty.value || 1)), sel.value, btn);
            });

            searchResults.appendChild(row);
        });
    }

    cartSearch?.addEventListener('input', function() {
        clearTimeout(searchDebounce);
        const term = this.value.trim();
        searchDebounce = setTimeout(() => doSearch(term), 220);
    });

    clearSearchBtn?.addEventListener('click', () => {
        cartSearch.value = '';
        searchResults.innerHTML = '';
        cartSearch.focus();
    });

    // ✅ FIXED: Use clearOrderItems instead of clearCart
    clearCartBtn?.addEventListener('click', async () => await clearOrderItems());
    dismissHintBtn?.addEventListener('click', () => setHint("✅ Ready."));

    /* ================================================================
       GIFT MODAL - FIXED ADD GIFT TO ORDER
    ================================================================ */
    let giftDebounce = null;
    let giftSearching = false;

    function showGiftModalHint(msg) {
        giftModalHint.textContent = msg || '';
    }

    function setGiftModalCustomerLine() {
        if (!selectedCustomer) {
            giftModalCustomerLine.textContent = 'Customer: Guest';
            showGiftModalHint('Select a customer first.');
            return;
        }
        giftModalCustomerLine.textContent = `Customer: ${selectedCustomer.name || 'Customer'} (${selectedCustomer.phone || '-'})`;
        showGiftModalHint('');
    }

    function openGiftModal() {
        if (!selectedCustomer) {
            pushToast({ type: 'warning', title: 'Customer required', messages: ['Please select a customer first.'], timeout: 2800 });
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

    async function giftSearch(term) {
        if (!term || term.length < 2) {
            giftSearchResults.innerHTML = '';
            return;
        }
        if (giftSearching) return;
        giftSearching = true;
        giftSearchResults.innerHTML = `<div style="padding:12px 14px;" class="subtle"><span class="spin"></span>Searching gifts...</div>`;

        const res = await fetch(`{{ route('products.quick.search') }}?q=${encodeURIComponent(term)}&location_id=${encodeURIComponent(LOCATION_ID)}`, {
            headers: { 'Accept': 'application/json' }
        });
        const rows = await res.json().catch(() => []);
        giftSearching = false;
        giftSearchResults.innerHTML = '';

        if (!Array.isArray(rows) || rows.length === 0) {
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

            // ✅ FIXED: Add gift to ORDER (not cart)
            btn.addEventListener('click', async (e) => {
                e.stopPropagation();
                btn.disabled = true;
                const old = btn.innerHTML;
                btn.innerHTML = `<span class="spin"></span>Adding`;

                const { res: r, data } = await jsonFetch(`{{ route('order.gift.manual.add', $order) }}`, 'POST', {
                    product_id: p.id,
                    quantity: Math.max(0.0001, Number(qtyInput.value || 1))
                });

                btn.disabled = false;
                btn.innerHTML = old;

                if (!r.ok || !data.success) {
                    toastAll(r, data, 'Gift add failed');
                    return;
                }

                await refreshOrderItems();
                pushToast({ type: 'success', title: 'Gift', messages: [`Gift added (Loc #${LOCATION_ID})`], timeout: 1600 });
                closeWrap(giftModalWrap);
            });

            giftSearchResults.appendChild(row);
        });
    }

    giftSearchInput?.addEventListener('input', () => {
        clearTimeout(giftDebounce);
        const term = giftSearchInput.value.trim();
        giftDebounce = setTimeout(() => giftSearch(term), 220);
    });

    /* ================================================================
       SAVE ORDER
    ================================================================ */
    function validatePaymentsBeforeSubmit(net) {
        const rows = getPaymentRowsData();
        if (rows.length === 0) return { ok: true, payments: [] };

        const payments = [];
        for (const p of rows) {
            delete p._rowEl;
            if (!p.method) return { ok: false, msg: 'Please select payment method.' };
            if (p.amount <= 0) return { ok: false, msg: 'Payment amount must be greater than 0.' };
            if (p.channel === 'online' && !p.trx_id) return { ok: false, msg: 'Transaction ID is required for online payment.' };
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

    saveOrderBtn?.addEventListener('click', async () => {
        const { ptsUse, rewardAmount, disc, net } = calcPayable();

        const valid = validatePaymentsBeforeSubmit(net);
        if (!valid.ok) {
            pushToast({ type: 'warning', title: 'Payment', messages: [valid.msg || 'Payment invalid'], timeout: 3200 });
            return;
        }

        saveOrderBtn.disabled = true;
        const oldText = saveOrderBtn.innerHTML;
        saveOrderBtn.innerHTML = `<span class="spin"></span>Saving...`;

        const payload = {
            order_discount: disc,
            rewards_points_used: ptsUse,
            rewards_amount_used: rewardAmount,
            payment_note: (paymentNoteEl.value || '').trim() || null,
            apply_balance_mode: autoBalanceMode.value || 'auto',
            location_id: LOCATION_ID,
        };
        if (valid.payments.length > 0) payload.payments = valid.payments;

        try {
            const res = await fetch(`{{ route('orders.update', $order) }}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json().catch(() => ({}));
            saveOrderBtn.disabled = false;
            saveOrderBtn.innerHTML = oldText;

            if (!res.ok || !data.success) {
                toastAll(res, data, 'Save failed');
                return;
            }

            pushToast({
                type: 'success',
                title: 'Order Saved',
                messages: [`Order #{{ $order->order_no }} updated successfully`],
                timeout: 3000
            });

            if (data.invoice_url) {
                window.location.href = data.invoice_url;
            } else {
                window.location.href = `{{ route('orders.show', $order) }}`;
            }
        } catch (error) {
            saveOrderBtn.disabled = false;
            saveOrderBtn.innerHTML = oldText;
            pushToast({
                type: 'danger',
                title: 'Error',
                messages: [error.message || 'Failed to save order'],
                timeout: 4000
            });
        }
    });

    cancelEditBtn?.addEventListener('click', () => {
        if (confirm('Are you sure you want to cancel editing? Changes will be lost.')) {
            window.location.href = `{{ route('orders.show', $order) }}`;
        }
    });

    /* ================================================================
       INIT
    ================================================================ */
    setLocationBadges();
    bindCartRowEvents();
    updateRowNumbers();
    calcPayable();

    // Load initial order items
    refreshOrderItems();

    pushToast({
        type: 'info',
        title: 'Editing Order',
        messages: [`Order #{{ $order->order_no }} • Location #${LOCATION_ID} • Click any editable value to modify`],
        timeout: 3000
    });

    console.log('✅ Order Edit initialized');
});
</script>
</div>
@endsection
