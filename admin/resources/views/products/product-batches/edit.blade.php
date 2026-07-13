{{-- resources/views/products/product-batches/edit.blade.php --}}
@extends('layouts.app')

@section('content')


    <style>
        /* Uses your theme vars (already in your layout). Only component css here. */

        .pb-batchitem.is-active {
            outline: 2px solid color-mix(in oklch, var(--accent-color) 55%, transparent);
        }

        .pb-badge-warn {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .15rem .5rem;
            border-radius: 999px;
            font-weight: 900;
            font-size: .75rem;
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--danger) 12%, transparent);
            color: var(--danger);
        }

        .pb-badge-soon {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .15rem .5rem;
            border-radius: 999px;
            font-weight: 900;
            font-size: .75rem;
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--warning) 14%, transparent);
            color: var(--warning);
        }

        .pb-page {
            max-width: 1100px;
            margin: 1.5rem auto;
            padding: 0 1rem;
            animation: pbFade .35s ease-out;
        }

        @keyframes pbFade {
            from {
                opacity: 0;
                transform: translateY(8px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .pb-header {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
            flex-wrap: wrap
        }

        .pb-kicker {
            color: var(--text-muted);
            font-size: .85rem;
            margin-bottom: .25rem
        }

        .pb-title {
            margin: 0;
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: .2px;
            color: var(--text-primary);
            background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text
        }

        .pb-subtitle {
            margin: .35rem 0 0;
            color: var(--text-secondary)
        }

        .pb-header__actions {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            justify-content: flex-end
        }

        .pb-card {
            border: 1px solid var(--border-color);
            border-radius: calc(var(--radius)*1.2);
            background: var(--card);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .glass-effect {
            background: var(--glass-base);
            backdrop-filter: blur(10px)
        }

        .pb-selected {
            display: flex;
            gap: 1rem;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1rem;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, var(--accent-glow), transparent);
        }

        .pb-selected__left {
            display: flex;
            gap: .85rem;
            align-items: center
        }

        .pb-selected__icon {
            width: 42px;
            height: 42px;
            border-radius: calc(var(--radius));
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--accent-color), var(--chart-4));
            color: white;
            box-shadow: 0 8px 20px var(--accent-glow);
        }

        .pb-selected__icon svg {
            width: 22px;
            height: 22px;
            fill: currentColor
        }

        .pb-selected__title {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: .25rem
        }

        .pb-selected__meta {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem
        }

        .pb-chip {
            border: 1px solid var(--border-color);
            background: var(--accent);
            padding: .35rem .6rem;
            border-radius: 999px;
            color: var(--text-primary);
            font-size: .85rem
        }

        .pb-hint {
            color: var(--text-secondary);
            font-size: .9rem
        }

        .pb-form {
            padding: 1rem
        }

        .pb-section {
            padding: .25rem 0
        }

        .pb-section__head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: .75rem;
            flex-wrap: wrap;
            margin: .25rem 0 .75rem
        }

        .pb-section__title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--text-primary)
        }

        .pb-section__desc {
            margin: 0;
            color: var(--text-secondary);
            font-size: .9rem
        }

        .pb-divider {
            height: 1px;
            background: var(--border-color);
            opacity: .9;
            margin: 1rem 0
        }

        .pb-grid {
            display: grid;
            gap: 1rem
        }

        .pb-grid--2 {
            grid-template-columns: repeat(2, minmax(0, 1fr))
        }

        .pb-grid--3 {
            grid-template-columns: repeat(3, minmax(0, 1fr))
        }

        .pb-grid--5 {
            grid-template-columns: repeat(5, minmax(0, 1fr))
        }

        @media (max-width:1100px) {
            .pb-grid--5 {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media (max-width:900px) {
            .pb-grid--3 {
                grid-template-columns: 1fr
            }

            .pb-grid--2 {
                grid-template-columns: 1fr
            }
        }

        .pb-field {
            position: relative;
            padding: .65rem .65rem .55rem;
            border: 1px solid var(--border-color);
            border-radius: calc(var(--radius)*1.05);
            background: color-mix(in oklch, var(--card) 75%, transparent);
            transition: transform var(--transition-normal), box-shadow var(--transition-normal), border-color var(--transition-normal);
        }

        .pb-field:hover {
            border-color: color-mix(in oklch, var(--accent-color) 45%, var(--border-color));
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-1px);
        }

        .pb-field[data-tip]::after {
            content: attr(data-tip);
            position: absolute;
            left: 12px;
            right: 12px;
            bottom: -12px;
            transform: translateY(12px);
            opacity: 0;
            pointer-events: none;
            background: var(--popover);
            color: var(--popover-foreground);
            border: 1px solid var(--border-color);
            box-shadow: var(--dropdown-shadow);
            padding: .5rem .65rem;
            border-radius: calc(var(--radius)*.9);
            font-size: .8rem;
            line-height: 1.25;
            transition: opacity var(--transition-fast), transform var(--transition-fast);
            z-index: 10;
        }

        .pb-field:hover[data-tip]::after {
            opacity: 1;
            transform: translateY(18px)
        }

        .pb-label {
            display: block;
            font-weight: 800;
            color: var(--text-primary);
            font-size: .9rem;
            margin-bottom: .4rem
        }

        .pb-label.required::after {
            content: " *";
            color: var(--danger)
        }

        .pb-control {
            position: relative;
            border: 2px solid var(--border-color);
            border-radius: calc(var(--radius));
            background: var(--input);
            transition: border-color var(--transition-normal), box-shadow var(--transition-normal);
        }

        .pb-field:hover .pb-control {
            border-color: color-mix(in oklch, var(--accent-color) 35%, var(--border-color))
        }

        .pb-control:focus-within {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .pb-control--icon .pb-input {
            padding-left: 2.75rem
        }

        .pb-control__icon {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .pb-control__icon svg {
            width: 18px;
            height: 18px;
            fill: currentColor
        }

        .pb-input,
        .pb-select {
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--text-primary);
            padding: .85rem .9rem;
            font-size: 1rem;
        }

        .pb-select {
            appearance: none;
            cursor: pointer
        }

        .pb-textarea {
            min-height: 90px;
            resize: vertical
        }

        .pb-control--money .pb-input,
        .pb-control--percent .pb-input {
            padding-right: 3.25rem
        }

        .pb-suffix {
            position: absolute;
            right: .85rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-weight: 800;
            font-size: .9rem;
            pointer-events: none;
        }

        .pb-help {
            margin-top: .45rem;
            color: var(--text-muted);
            font-size: .82rem;
            line-height: 1.25
        }

        .pb-error {
            margin-top: .45rem;
            color: var(--danger);
            font-size: .85rem;
            font-weight: 700
        }

        .pb-pricebox {
            border-style: solid
        }

        .pb-selectedline {
            margin-top: .6rem;
            color: var(--text-secondary);
            font-size: .9rem
        }

        .pb-selectedline .pb-inline {
            display: inline-flex;
            gap: .5rem;
            align-items: center;
            padding: .35rem .55rem;
            border: 1px solid var(--border-color);
            border-radius: 999px;
            background: var(--accent)
        }

        .pb-selectedline button {
            border: 0;
            background: transparent;
            color: var(--text-secondary);
            cursor: pointer;
            padding: .15rem .4rem;
            border-radius: 8px;
        }

        .pb-selectedline button:hover {
            background: var(--border-color);
            color: var(--text-primary)
        }

        .pb-togglegrid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin-top: .5rem;
        }

        @media (max-width:900px) {
            .pb-togglegrid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media (max-width:520px) {
            .pb-togglegrid {
                grid-template-columns: 1fr
            }
        }

        .pb-toggle {
            display: flex;
            gap: .65rem;
            align-items: center;
            padding: .65rem .65rem;
            border: 1px solid var(--border-color);
            border-radius: calc(var(--radius));
            background: var(--accent);
            cursor: pointer;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal), border-color var(--transition-normal);
            user-select: none;
        }

        .pb-toggle:hover {
            transform: translateY(-1px);
            box-shadow: var(--card-shadow-hover);
            border-color: color-mix(in oklch, var(--accent-color) 35%, var(--border-color))
        }

        .pb-toggle input {
            display: none
        }

        .pb-toggle__track {
            width: 42px;
            height: 24px;
            border-radius: 999px;
            background: color-mix(in oklch, var(--text-muted) 30%, transparent);
            position: relative;
            flex: 0 0 auto;
            border: 1px solid var(--border-color);
            transition: background var(--transition-normal), border-color var(--transition-normal);
        }

        .pb-toggle__track::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 3px;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: var(--card);
            transition: transform var(--transition-normal), background var(--transition-normal);
            box-shadow: 0 4px 10px rgb(0 0 0 / .25);
        }

        .pb-toggle input:checked+.pb-toggle__track {
            background: color-mix(in oklch, var(--accent-color) 40%, transparent);
            border-color: color-mix(in oklch, var(--accent-color) 45%, var(--border-color));
        }

        .pb-toggle input:checked+.pb-toggle__track::after {
            transform: translate(18px, -50%);
            background: var(--primary)
        }

        .pb-toggle__label {
            font-weight: 800;
            color: var(--text-primary);
            font-size: .9rem
        }

        .pb-previewbox {
            border: 1px dashed color-mix(in oklch, var(--accent-color) 40%, var(--border-color));
            background: linear-gradient(135deg, var(--accent-glow), transparent);
            border-radius: calc(var(--radius));
            padding: .85rem;
        }

        .pb-previewbox__title {
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: .5rem
        }

        .pb-previewbox__row {
            display: flex;
            justify-content: space-between;
            color: var(--text-secondary);
            margin: .25rem 0
        }

        .pb-previewbox__row strong {
            color: var(--text-primary)
        }

        .pb-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            background: linear-gradient(135deg, transparent, var(--accent));
        }

        .pb-footer__left,
        .pb-footer__right {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: center
        }

        .pb-btn {
            display: inline-flex;
            gap: .6rem;
            align-items: center;
            justify-content: center;
            padding: .8rem 1rem;
            border-radius: calc(var(--radius));
            border: 1px solid var(--border-color);
            text-decoration: none;
            cursor: pointer;
            font-weight: 900;
            font-size: .95rem;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal), background var(--transition-normal), border-color var(--transition-normal);
            user-select: none;
        }

        .pb-btn svg {
            width: 18px;
            height: 18px;
            fill: currentColor
        }

        .pb-btn--primary {
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            color: var(--primary-foreground);
            border-color: color-mix(in oklch, var(--accent-color) 55%, var(--border-color));
            box-shadow: 0 8px 22px var(--accent-glow);
            position: relative;
        }

        .pb-btn--primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px var(--accent-glow)
        }

        .pb-btn--secondary {
            background: var(--secondary);
            color: var(--secondary-foreground)
        }

        .pb-btn--secondary:hover {
            transform: translateY(-2px);
            background: var(--accent)
        }

        .pb-btn--ghost {
            background: transparent;
            color: var(--text-primary)
        }

        .pb-btn--ghost:hover {
            transform: translateY(-2px);
            background: var(--accent)
        }

        .pb-btn:disabled {
            opacity: .55;
            cursor: not-allowed;
            transform: none;
            box-shadow: none
        }

        .pb-btn__spinner {
            display: none;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            border: 2px solid rgba(255, 255, 255, .35);
            border-top-color: white;
            animation: pbSpin 1s linear infinite;
        }

        @keyframes pbSpin {
            to {
                transform: rotate(360deg)
            }
        }

        .pb-suggest {
            display: none;
            position: absolute;
            left: .65rem;
            right: .65rem;
            top: calc(100% - .2rem);
            background: var(--card);
            border: 1px solid var(--border-color);
            border-radius: calc(var(--radius));
            box-shadow: var(--dropdown-shadow);
            z-index: 50;
            max-height: 280px;
            overflow: auto;
        }

        .pb-suggest.is-show {
            display: block
        }

        .pb-suggest__item {
            padding: .75rem .85rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background var(--transition-fast);
        }

        .pb-suggest__item:hover {
            background: var(--accent)
        }

        .pb-suggest__item:last-child {
            border-bottom: 0
        }

        .pb-suggest__name {
            font-weight: 900;
            color: var(--text-primary)
        }

        .pb-suggest__meta {
            font-size: .85rem;
            color: var(--text-secondary);
            margin-top: .2rem
        }

        .pb-skel {
            padding: 1rem;
            color: var(--text-secondary)
        }

        .pb-toast {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            padding: 1rem;
            border-radius: calc(var(--radius));
            border: 1px solid var(--border-color);
            background: var(--card);
            box-shadow: var(--dropdown-shadow);
            margin-bottom: 1rem;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity var(--transition-normal), transform var(--transition-normal);
        }

        .pb-toast.is-show {
            opacity: 1;
            transform: translateY(0)
        }

        .pb-toast--success {
            border-left: 4px solid var(--success)
        }

        .pb-toast--danger {
            border-left: 4px solid var(--danger)
        }

        .pb-toast__icon {
            color: var(--text-primary)
        }

        .pb-toast--success .pb-toast__icon {
            color: var(--success)
        }

        .pb-toast--danger .pb-toast__icon {
            color: var(--danger)
        }

        .pb-toast__icon svg {
            width: 22px;
            height: 22px;
            fill: currentColor
        }

        .pb-toast__title {
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: .2rem
        }

        .pb-toast__text {
            color: var(--text-secondary);
            font-size: .9rem
        }

        .pb-toast__list {
            margin: .5rem 0 0;
            padding-left: 1.2rem;
            color: var(--text-secondary)
        }

        .pb-toast__close {
            margin-left: auto;
            border: 0;
            background: transparent;
            color: var(--text-secondary);
            font-size: 1.2rem;
            cursor: pointer
        }

        .pb-toast__close:hover {
            color: var(--text-primary)
        }

        .pb-animblock {
            animation: pbDrop .25s ease-out
        }

        @keyframes pbDrop {
            from {
                opacity: 0;
                transform: translateY(-6px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* Drawer */
        .pb-drawer {
            position: fixed;
            inset: 0;
            z-index: 2000;
            display: none
        }

        .pb-drawer.is-open {
            display: block
        }

        .pb-drawer__overlay {
            position: absolute;
            inset: 0;
            background: rgb(0 0 0 / .45);
            backdrop-filter: blur(3px)
        }

        .pb-drawer__panel {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: min(520px, 96vw);
            background: var(--card);
            border-left: 1px solid var(--border-color);
            box-shadow: var(--dropdown-shadow);
            display: flex;
            flex-direction: column;
            animation: pbSlideIn .25s ease-out;
        }

        @keyframes pbSlideIn {
            from {
                transform: translateX(12px);
                opacity: .5
            }

            to {
                transform: translateX(0);
                opacity: 1
            }
        }

        .pb-drawer__head {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            gap: 1rem
        }

        .pb-drawer__title {
            font-weight: 950;
            color: var(--text-primary);
            font-size: 1.1rem
        }

        .pb-drawer__desc {
            color: var(--text-secondary);
            font-size: .9rem;
            margin-top: .2rem
        }

        .pb-drawer__close {
            border: 1px solid var(--border-color);
            background: transparent;
            color: var(--text-primary);
            border-radius: 10px;
            width: 38px;
            height: 38px;
            font-size: 1.35rem;
            cursor: pointer
        }

        .pb-drawer__close:hover {
            background: var(--accent)
        }

        .pb-drawer__body {
            padding: 1rem;
            overflow: auto;
            flex: 1
        }

        .pb-drawer__foot {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            background: linear-gradient(135deg, transparent, var(--accent))
        }

        .pb-mini {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            margin-bottom: 1rem
        }

        .pb-mini__label {
            color: var(--text-muted);
            font-size: .82rem
        }

        .pb-mini__value {
            color: var(--text-primary);
            font-weight: 950
        }

        .pb-batchlist {
            display: flex;
            flex-direction: column;
            border: 1px solid var(--border-color);
            border-radius: calc(var(--radius));
            overflow: hidden
        }

        .pb-batchitem {
            padding: .85rem;
            border-bottom: 1px solid var(--border-color);
            cursor: pointer;
            transition: background var(--transition-fast), transform var(--transition-fast);
        }

        .pb-batchitem:hover {
            background: var(--accent);
            transform: translateY(-1px)
        }

        .pb-batchitem:last-child {
            border-bottom: 0
        }

        .pb-batchitem__top {
            display: flex;
            justify-content: space-between;
            gap: 1rem
        }

        .pb-batchitem__sku {
            font-weight: 950;
            color: var(--text-primary)
        }

        .pb-batchitem__qty {
            color: var(--text-secondary);
            font-weight: 900
        }

        .pb-batchitem__meta {
            margin-top: .35rem;
            color: var(--text-secondary);
            font-size: .86rem;
            display: flex;
            gap: .75rem;
            flex-wrap: wrap
        }
    </style>

    @php
        // ✅ Routes (new names) + safe fallback to old
        $urlProductsSearch = Route::has('products.quick.search')
            ? route('products.quick.search')
            : (Route::has('products.search')
                ? route('products.search')
                : '');

        $urlGiftSearch = Route::has('products.gift.search') ? route('products.gift.search') : $urlProductsSearch;

        $urlBatchesJsonByProduct = Route::has('product.batches.json.byProduct')
            ? route('product.batches.json.byProduct', ['product' => '__PID__'])
            : '';

        $urlBatchJson = Route::has('product.batches.json.show')
            ? route('product.batches.json.show', ['batch' => '__BID__'])
            : '';

        // ✅ Prefill helpers
        $mfg = old('manufacture_date', optional($batch->manufacture_date)->format('Y-m-d'));
        $exp = old('expiry_date', optional($batch->expiry_date)->format('Y-m-d'));
    @endphp

    <div class="pb-page" id="pbEditRoot" data-products-search="{{ $urlProductsSearch }}" data-gift-search="{{ $urlGiftSearch }}"
        data-batches-by-product="{{ $urlBatchesJsonByProduct }}" data-batch-json="{{ $urlBatchJson }}">
        {{-- Toasts --}}
        @if (session('error'))
            <div class="pb-toast pb-toast--danger is-show">
                <div class="pb-toast__icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 13h-2v-2h2v2zm0-4h-2V7h2v4z" />
                    </svg>
                </div>
                <div class="pb-toast__body">
                    <div class="pb-toast__title">Error</div>
                    <div class="pb-toast__text">{{ session('error') }}</div>
                </div>
                <button type="button" class="pb-toast__close" data-toast-close aria-label="Close">×</button>
            </div>
        @endif

        @if (session('success'))
            <div class="pb-toast pb-toast--success is-show">
                <div class="pb-toast__icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z" />
                    </svg>
                </div>
                <div class="pb-toast__body">
                    <div class="pb-toast__title">Success</div>
                    <div class="pb-toast__text">{{ session('success') }}</div>
                </div>
                <button type="button" class="pb-toast__close" data-toast-close aria-label="Close">×</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="pb-toast pb-toast--danger is-show">
                <div class="pb-toast__icon">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                    </svg>
                </div>
                <div class="pb-toast__body">
                    <div class="pb-toast__title">Validation Error</div>
                    <div class="pb-toast__text">Please fix the following:</div>
                    <ul class="pb-toast__list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="pb-toast__close" data-toast-close aria-label="Close">×</button>
            </div>
        @endif

        {{-- Header --}}
        <div class="pb-header">
            <div>
                <div class="pb-kicker">Inventory / Product Batches</div>
                <h2 class="pb-title">✏️ Edit Product Batch</h2>
                <p class="pb-subtitle">
                    Update stock, pricing, dates & gift offer. You can also copy from another batch.
                </p>
            </div>

            <div class="pb-header__actions">
                <a class="pb-btn pb-btn--ghost" href="{{ url()->previous() }}">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" />
                    </svg>
                    Back
                </a>

                <a class="pb-btn pb-btn--secondary" href="{{ route('product.batches.all') }}">
                    <svg viewBox="0 0 24 24">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                    </svg>
                    All Batches
                </a>

                <button type="button" class="pb-btn pb-btn--secondary" id="pbOpenCopyDrawer">
                    <svg viewBox="0 0 24 24">
                        <path
                            d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm4 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h12v14z" />
                    </svg>
                    Copy Another Batch
                </button>
            </div>
        </div>

        {{-- Card --}}
        <div class="pb-card glass-effect">

            {{-- Current Batch badge --}}
            <div class="pb-selected">
                <div class="pb-selected__left">
                    <div class="pb-selected__icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z" />
                        </svg>
                    </div>
                    <div>
                        <div class="pb-selected__title">Editing Batch</div>
                        <div class="pb-selected__meta">
                            <span class="pb-chip"><strong>{{ $batch->batch_sku ?? '—' }}</strong></span>
                            <span class="pb-chip">Product: {{ $batch->product?->name ?? '—' }}</span>
                            @if ($batch->product?->barcode)
                                <span class="pb-chip">Barcode: {{ $batch->product->barcode }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="pb-selected__right">
                    <div class="pb-hint">Tip: Copy another batch to apply pricing/wholesale/gift instantly.</div>
                </div>
            </div>

            <form method="POST" action="{{ route('product.batches.update', $batch) }}" class="pb-form" id="pbBatchForm"
                novalidate>
                @csrf
                @method('PUT')

                {{-- Hidden Product ID (editable via search/manual) --}}
                <input type="hidden" name="product_id" id="product_id"
                    value="{{ old('product_id', $batch->product_id) }}">




                {{-- PRODUCT SELECT --}}
                <div class="pb-section">
                    <div class="pb-section__head">
                        <h4 class="pb-section__title">🔎 Select Product</h4>
                        <p class="pb-section__desc">Change product if needed (search by name or barcode).</p>
                    </div>

                    <div class="pb-grid pb-grid--2">
                        <div class="pb-field" data-tip="Type at least 2 characters. Barcode is fastest.">
                            <label class="pb-label required">Live Search</label>
                            <div class="pb-control pb-control--icon">
                                <span class="pb-control__icon">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                                    </svg>
                                </span>
                                <input type="text" class="pb-input" id="pbProductSearch"
                                    placeholder="Type product name or barcode..." autocomplete="off">
                            </div>
                            <div class="pb-suggest" id="pbProductResults"></div>
                            @error('product_id')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                            <div class="pb-help">Selecting a product updates the hidden product_id.</div>
                        </div>

                        <div class="pb-field" data-tip="Manual dropdown fallback.">
                            <label class="pb-label">Manual Select</label>
                            <div class="pb-control">
                                <select id="product_id_manual" name="product_id_manual" class="pb-select">
                                    <option value="">-- Select Manually --</option>
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}" @selected(old('product_id', $batch->product_id) == $p->id)>
                                            {{ $p->name }} {{ $p->barcode ? '(' . $p->barcode . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="pb-selectedline" id="pbSelectedProductLine"></div>
                        </div>
                    </div>
                </div>

                {{-- ✅ LOCATION (Edit) --}}
                <div style="margin-top:12px;">
                    <div class="pb-grid pb-grid--2">
                        <div class="pb-field" data-tip="Select where this batch stock is stored (warehouse/store).">
                            <label class="pb-label required">Stock Location</label>
                            <div class="pb-control">
                                <select name="location_id" class="pb-select" required>
                                    <option value="">-- Select Location --</option>
                                    @foreach ($locations ?? [] as $loc)
                                        <option value="{{ $loc->id }}" @selected(old('location_id', $batchStockLocationId ?? null) == $loc->id)>
                                            {{ $loc->name }} ({{ ucfirst($loc->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('location_id')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                            <div class="pb-help">
                                Changing this will move/transfer stock to the new location (if you implement transfer
                                logic).
                            </div>
                        </div>

                        <div class="pb-field"
                            data-tip="Best practice: keep batches created in Warehouse, then transfer to Store for POS selling.">
                            <label class="pb-label">Tip</label>
                            <div class="pb-help" style="margin-top:.25rem;">
                                - Warehouse = main receiving<br>
                                - Store = POS deduction<br>
                                - Transfer between locations for distribution
                            </div>
                        </div>
                    </div>
                </div>


                <div class="pb-divider"></div>

                {{-- BASIC --}}
                <div class="pb-section">
                    <div class="pb-section__head">
                        <h4 class="pb-section__title">📦 Basic Details</h4>
                        <p class="pb-section__desc">Stock + unit info.</p>
                    </div>

                    <div class="pb-grid pb-grid--3">
                        <div class="pb-field" data-tip="Optional. Helpful for vendor tracking.">
                            <label class="pb-label">Batch Number</label>
                            <div class="pb-control">
                                <input type="text" class="pb-input" name="batch_no"
                                    value="{{ old('batch_no', $batch->batch_no) }}" placeholder="e.g. BATCH-001">
                            </div>
                            @error('batch_no')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pb-field" data-tip="Supports decimals like 0.100.">
                            <label class="pb-label required">Quantity</label>
                            <div class="pb-control">
                                <input type="number" class="pb-input" name="quantity"
                                    value="{{ old('quantity', $batch->quantity) }}" required min="0"
                                    step="any">
                            </div>
                            @error('quantity')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pb-field" data-tip="Select unit of measurement">
                            <label class="pb-label">Unit</label>

                            <div class="pb-control">
                                <select name="unit" class="pb-input">
                                    @php
                                        $units = [
                                            'pcs' => 'Pieces (pcs)',
                                            'dozen' => 'Dozen',
                                            'box' => 'Box',
                                            'kg' => 'Kilogram (kg)',
                                            'g' => 'Gram (g)',
                                            'l' => 'Liter (l)',
                                            'ml' => 'Milliliter (ml)',
                                        ];

                                        $current = old('unit', $batch->unit ?? 'pcs');
                                    @endphp

                                    @foreach ($units as $value => $label)
                                        <option value="{{ $value }}" {{ $current === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @error('unit')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="pb-divider"></div>

                {{-- PRICING --}}
                <div class="pb-section">
                    <div class="pb-section__head">
                        <h4 class="pb-section__title">💰 Pricing Configuration</h4>
                        <p class="pb-section__desc">Discount logic stays unchanged.</p>
                    </div>

                    <div class="pb-grid pb-grid--5">
                        <div class="pb-field pb-pricebox" data-tip="Your purchase cost per unit.">
                            <label class="pb-label required">Buy Price</label>
                            <div class="pb-control pb-control--money">
                                <input type="number" class="pb-input" name="buy_price"
                                    value="{{ old('buy_price', $batch->buy_price) }}" required min="0"
                                    step="0.01" placeholder="0.00">
                                <span class="pb-suffix">Tk.</span>
                            </div>
                            @error('buy_price')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pb-field pb-pricebox" data-tip="Original price before any discount.">
                            <label class="pb-label required">Original Sell Price</label>
                            <div class="pb-control pb-control--money">
                                <input type="number" class="pb-input" name="original_sell_price"
                                    value="{{ old('original_sell_price', $batch->original_sell_price) }}" required
                                    min="0" step="0.01" placeholder="0.00">
                                <span class="pb-suffix">Tk.</span>
                            </div>
                            @error('original_sell_price')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pb-field pb-pricebox" data-tip="If you set discounted price, discount % will lock.">
                            <label class="pb-label">Discounted Price</label>
                            <div class="pb-control pb-control--money">
                                <input type="number" class="pb-input" name="discounted_price" id="discounted_price"
                                    value="{{ old('discounted_price', $batch->discounted_price ?? 0) }}" min="0"
                                    step="0.01" placeholder="0.00">
                                <span class="pb-suffix">Tk.</span>
                            </div>
                            @error('discounted_price')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pb-field pb-pricebox" data-tip="If you set discount %, discounted price will lock.">
                            <label class="pb-label">Discount %</label>
                            <div class="pb-control pb-control--percent">
                                <input type="number" class="pb-input" name="discount_percentage"
                                    id="discount_percentage"
                                    value="{{ old('discount_percentage', $batch->discount_percentage ?? 0) }}"
                                    min="0" max="100" step="0.01" placeholder="0">
                                <span class="pb-suffix">%</span>
                            </div>
                            @error('discount_percentage')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pb-field pb-pricebox" data-tip="Auto calculated from your discount settings.">
                            <label class="pb-label">Sell Price</label>
                            <div class="pb-control pb-control--money">
                                <input type="number" class="pb-input" name="sell_price" id="sell_price"
                                    value="{{ old('sell_price', $batch->sell_price ?? 0) }}" readonly>
                                <span class="pb-suffix">Tk.</span>
                            </div>
                            <div class="pb-help">Read-only. Computed automatically.</div>
                        </div>
                    </div>

                    {{-- ✅ DISCOUNT LOGIC: DO NOT CHANGE --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const originalPriceInput = document.querySelector('[name="original_sell_price"]');
                            const discountedPriceInput = document.getElementById('discounted_price');
                            const discountPercentInput = document.getElementById('discount_percentage');
                            const sellPriceInput = document.getElementById('sell_price');

                            function calculateSellPrice() {
                                const originalPrice = parseFloat(originalPriceInput?.value) || 0;
                                const discountedPrice = parseFloat(discountedPriceInput.value) || 0;
                                const discountPercent = parseFloat(discountPercentInput.value) || 0;

                                let sellPrice = originalPrice;

                                discountedPriceInput.readOnly = false;
                                discountPercentInput.readOnly = false;

                                if (discountedPrice > 0) {
                                    sellPrice = originalPrice - discountedPrice;
                                    discountPercentInput.readOnly = true;
                                    discountPercentInput.value = 0;
                                } else if (discountPercent > 0) {
                                    sellPrice = originalPrice - (originalPrice * (discountPercent / 100));
                                    discountedPriceInput.readOnly = true;
                                    discountedPriceInput.value = 0;
                                }

                                sellPriceInput.value = sellPrice.toFixed(4);
                            }

                            discountedPriceInput.addEventListener('input', calculateSellPrice);
                            discountPercentInput.addEventListener('input', calculateSellPrice);
                            originalPriceInput?.addEventListener('input', calculateSellPrice);
                            calculateSellPrice();
                        });
                    </script>
                </div>

                <div class="pb-divider"></div>

                {{-- WHOLESALE --}}
                <div class="pb-section">
                    <div class="pb-section__head">
                        <h4 class="pb-section__title">🏭 Dealer Wholesale (Optional)</h4>
                        <p class="pb-section__desc">Dealers/shops pricing tiers.</p>
                    </div>

                    <div class="pb-grid pb-grid--3">
                        <div class="pb-field" data-tip="Dealer wholesale selling price.">
                            <label class="pb-label">Wholesale Price</label>
                            <div class="pb-control pb-control--money">
                                <input type="number" class="pb-input" name="whole_sell_price"
                                    value="{{ old('whole_sell_price', $batch->whole_sell_price) }}" min="0"
                                    step="0.01" placeholder="0.00">
                                <span class="pb-suffix">Tk.</span>
                            </div>
                        </div>

                        <div class="pb-field" data-tip="Minimum quantity to allow wholesale price.">
                            <label class="pb-label">Min Qty</label>
                            <div class="pb-control">
                                <input type="number" class="pb-input" name="whole_sell_min_qty"
                                    value="{{ old('whole_sell_min_qty', $batch->whole_sell_min_qty) }}" min="0"
                                    step="any" placeholder="0">
                            </div>
                        </div>

                        <div class="pb-field" data-tip="Maximum qty for wholesale tier (optional).">
                            <label class="pb-label">Max Qty</label>
                            <div class="pb-control">
                                <input type="number" class="pb-input" name="whole_sell_max_qty"
                                    value="{{ old('whole_sell_max_qty', $batch->whole_sell_max_qty) }}" min="0"
                                    step="any" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-divider"></div>

                {{-- CUSTOMER BULK --}}
                <div class="pb-section">
                    <div class="pb-section__head">
                        <h4 class="pb-section__title">👥 Customer Bulk Price (Optional)</h4>
                        <p class="pb-section__desc">Bulk pricing for walk-in customers.</p>
                    </div>

                    <div class="pb-grid pb-grid--3">
                        <div class="pb-field" data-tip="Customer bulk selling price.">
                            <label class="pb-label">Bulk Price</label>
                            <div class="pb-control pb-control--money">
                                <input type="number" class="pb-input" name="customer_whole_price"
                                    value="{{ old('customer_whole_price', $batch->customer_whole_price) }}"
                                    min="0" step="0.01" placeholder="0.00">
                                <span class="pb-suffix">Tk.</span>
                            </div>
                        </div>

                        <div class="pb-field" data-tip="Minimum qty to allow bulk price.">
                            <label class="pb-label">Min Qty</label>
                            <div class="pb-control">
                                <input type="number" class="pb-input" name="customer_whole_min_qty"
                                    value="{{ old('customer_whole_min_qty', $batch->customer_whole_min_qty) }}"
                                    min="0" step="any" placeholder="0">
                            </div>
                        </div>

                        <div class="pb-field" data-tip="Maximum qty for bulk tier (optional).">
                            <label class="pb-label">Max Qty</label>
                            <div class="pb-control">
                                <input type="number" class="pb-input" name="customer_whole_max_qty"
                                    value="{{ old('customer_whole_max_qty', $batch->customer_whole_max_qty) }}"
                                    min="0" step="any" placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-divider"></div>

                {{-- FREE OFFER --}}
                <div class="pb-section">
                    <div class="pb-section__head">
                        <h4 class="pb-section__title">🎁 Free Offer (Gift)</h4>
                        <p class="pb-section__desc">Enable gift offer per batch. Gift search included.</p>
                    </div>

                    <div class="pb-grid pb-grid--2">
                        <div class="pb-field" data-tip="Turn ON only when you want buy X get Y gift.">
                            <label class="pb-label">Enable</label>
                            <label class="pb-toggle">
                                <input type="hidden" name="is_free_offer_active" value="0">
                                <input type="checkbox" id="is_free_offer_active" name="is_free_offer_active"
                                    value="1"
                                    {{ old('is_free_offer_active', $batch->is_free_offer_active) ? 'checked' : '' }}>
                                <span class="pb-toggle__track"></span>
                                <span class="pb-toggle__label">Enable Free Offer</span>
                            </label>
                            <div class="pb-help">When enabled, gift product + quantities become required.</div>
                        </div>

                        <div class="pb-field" data-tip="Use quick search for faster selection.">
                            <label class="pb-label">Gift Product Quick Search</label>
                            <div class="pb-control pb-control--icon">
                                <span class="pb-control__icon">
                                    <svg viewBox="0 0 24 24">
                                        <path
                                            d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5z" />
                                    </svg>
                                </span>
                                <input type="text" class="pb-input" id="pbGiftSearch"
                                    placeholder="Search gift product..." autocomplete="off">
                            </div>
                            <div class="pb-suggest" id="pbGiftResults"></div>

                            {{-- Hidden final gift id --}}
                            <input type="hidden" name="free_product_id" id="free_product_id"
                                value="{{ old('free_product_id', $batch->free_product_id) }}">

                            <div class="pb-selectedline" id="pbGiftSelectedLine"></div>

                            {{-- fallback --}}
                            <div class="pb-help">Fallback select:</div>
                            <div class="pb-control">
                                <select class="pb-select" id="pbGiftSelectFallback">
                                    <option value="">-- Select Gift Product --</option>
                                    @foreach ($giftProducts as $gp)
                                        <option value="{{ $gp->id }}" @selected(old('free_product_id', $batch->free_product_id) == $gp->id)>
                                            {{ $gp->name }} {{ $gp->barcode ? '(' . $gp->barcode . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('free_product_id')
                                <div class="pb-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div id="freeOfferFields" class="pb-animblock" style="display:none;">
                        <div class="pb-grid pb-grid--3">
                            <div class="pb-field" data-tip="Buy quantity required to trigger gift.">
                                <label class="pb-label required">Buy Qty</label>
                                <div class="pb-control">
                                    <input type="number" class="pb-input" name="free_buy_qty"
                                        value="{{ old('free_buy_qty', $batch->free_buy_qty ?? 1) }}" min="0.0001"
                                        step="any" placeholder="1">
                                </div>
                                @error('free_buy_qty')
                                    <div class="pb-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="pb-field" data-tip="How many gift units to give per rule.">
                                <label class="pb-label required">Free Qty</label>
                                <div class="pb-control">
                                    <input type="number" class="pb-input" name="free_qty"
                                        value="{{ old('free_qty', $batch->free_qty ?? 1) }}" min="0.0001"
                                        step="any" placeholder="1">
                                </div>
                                @error('free_qty')
                                    <div class="pb-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="pb-field pb-giftpreview">
                                <label class="pb-label">Preview</label>
                                <div class="pb-previewbox">
                                    <div class="pb-previewbox__title">Offer Summary</div>
                                    <div class="pb-previewbox__row"><span>Buy</span> <strong id="pbPrevBuy">1</strong>
                                    </div>
                                    <div class="pb-previewbox__row"><span>Get</span> <strong id="pbPrevFree">1</strong>
                                    </div>
                                    <div class="pb-previewbox__row"><span>Gift</span> <strong id="pbPrevGiftName">Not
                                            selected</strong></div>
                                </div>
                                <div class="pb-help">Preview only. Stock validation is backend.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pb-divider"></div>

                {{-- DATES + STATUS --}}
                <div class="pb-section">
                    <div class="pb-section__head">
                        <h4 class="pb-section__title">📅 Dates & Status</h4>
                        <p class="pb-section__desc">Optional dates + sales channel toggles.</p>
                    </div>

                    <div class="pb-grid pb-grid--2">
                        <div class="pb-field" data-tip="Optional. Useful for manufacturing tracking.">
                            <label class="pb-label">Manufacture Date</label>
                            <div class="pb-control">
                                <input type="date" class="pb-input" name="manufacture_date"
                                    value="{{ $mfg }}">
                            </div>
                        </div>

                        <div class="pb-field" data-tip="Optional. Helps prevent selling expired stock.">
                            <label class="pb-label">Expiry Date</label>
                            <div class="pb-control">
                                <input type="date" class="pb-input" name="expiry_date" value="{{ $exp }}">
                            </div>
                        </div>
                    </div>

                    <div class="pb-togglegrid">
                        <label class="pb-toggle" data-tip="Available in online store.">
                            <input type="hidden" name="is_online" value="0">
                            <input type="checkbox" name="is_online" value="1"
                                {{ old('is_online', $batch->is_online) ? 'checked' : '' }}>
                            <span class="pb-toggle__track"></span>
                            <span class="pb-toggle__label">Sell Online</span>
                        </label>

                        <label class="pb-toggle" data-tip="Available in offline store.">
                            <input type="hidden" name="is_offline" value="0">
                            <input type="checkbox" name="is_offline" value="1"
                                {{ old('is_offline', $batch->is_offline) ? 'checked' : '' }}>
                            <span class="pb-toggle__track"></span>
                            <span class="pb-toggle__label">Sell Offline</span>
                        </label>

                        <label class="pb-toggle" data-tip="Available in POS.">
                            <input type="hidden" name="is_pos" value="0">
                            <input type="checkbox" name="is_pos" value="1"
                                {{ old('is_pos', $batch->is_pos) ? 'checked' : '' }}>
                            <span class="pb-toggle__track"></span>
                            <span class="pb-toggle__label">Show in POS</span>
                        </label>

                        <label class="pb-toggle" data-tip="Disable if this batch should not be used.">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $batch->is_active) ? 'checked' : '' }}>
                            <span class="pb-toggle__track"></span>
                            <span class="pb-toggle__label">Is Active</span>
                        </label>
                    </div>

                    <div class="pb-field" style="margin-top:14px;" data-tip="Internal notes (optional).">
                        <label class="pb-label">Notes</label>
                        <div class="pb-control">
                            <textarea name="notes" class="pb-input pb-textarea" rows="3"
                                placeholder="Additional notes about this batch...">{{ old('notes', $batch->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- FOOTER ACTIONS --}}
                <div class="pb-footer">
                    <div class="pb-footer__left">
                        <button type="button" class="pb-btn pb-btn--ghost" id="pbSaveDraft">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" />
                            </svg>
                            Save Draft
                        </button>
                        <button type="button" class="pb-btn pb-btn--ghost" id="pbClearDraft">
                            <svg viewBox="0 0 24 24">
                                <path
                                    d="M19 6.41 17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                            </svg>
                            Clear Draft
                        </button>
                    </div>

                    <div class="pb-footer__right">
                        <a href="{{ route('product.batches.all') }}" class="pb-btn pb-btn--secondary">Cancel</a>

                        <button type="submit" class="pb-btn pb-btn--primary" id="pbSubmitBtn">
                            <span class="pb-btn__spinner" id="pbSubmitSpinner" aria-hidden="true"></span>
                            <svg class="pb-btn__icon" viewBox="0 0 24 24">
                                <path
                                    d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" />
                            </svg>
                            Update Batch
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- COPY DRAWER --}}
        <div class="pb-drawer" id="pbCopyDrawer" aria-hidden="true">
            <div class="pb-drawer__overlay" data-drawer-close></div>
            <div class="pb-drawer__panel">

                <div class="pb-drawer__head">
                    <div>
                        <div class="pb-drawer__title">Copy Another Batch</div>
                        <div class="pb-drawer__desc">Search any product → select a batch → copy into this form.</div>
                    </div>
                    <button class="pb-drawer__close" type="button" data-drawer-close aria-label="Close">×</button>
                </div>

                <div class="pb-drawer__body">
                    <div class="pb-field" data-tip="Search any product here. Use ↑ ↓ Enter. Esc closes.">
                        <label class="pb-label">Search Product</label>
                        <div class="pb-control pb-control--icon">
                            <span class="pb-control__icon">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                                </svg>
                            </span>
                            <input type="text" class="pb-input" id="pbDrawerProductSearch"
                                placeholder="Type name or barcode..." autocomplete="off">
                        </div>
                        <div class="pb-suggest" id="pbDrawerProductResults"></div>
                        <div class="pb-selectedline" id="pbDrawerSelectedProductLine"></div>
                    </div>

                    <div class="pb-mini" style="margin-top:12px;">
                        <div class="pb-mini__left">
                            <div class="pb-mini__label">Batches For</div>
                            <div class="pb-mini__value" id="pbDrawerProductName">—</div>
                        </div>

                        <div style="display:flex; gap:10px; align-items:center;">
                            <button type="button" class="pb-btn pb-btn--secondary" id="pbApplyDrawerProduct" disabled>
                                Use Product
                            </button>
                            <button type="button" class="pb-btn pb-btn--secondary" id="pbRefreshBatches">
                                Refresh
                            </button>
                        </div>
                    </div>

                    <div class="pb-field" style="margin-top:10px;"
                        data-tip="Choose what you want to copy. Quantity is never overwritten.">
                        <label class="pb-label">Copy Mode</label>
                        <div class="pb-control">
                            <select class="pb-select" id="pbCopyMode">
                                <option value="all" selected>Copy All (Pricing + Wholesale + Gift + Dates)</option>
                                <option value="pricing">Only Pricing</option>
                                <option value="wholesale">Only Wholesale</option>
                                <option value="gift">Only Gift Offer</option>
                                <option value="dates">Only Dates + Notes</option>
                            </select>
                        </div>
                    </div>

                    <div class="pb-batchlist" id="pbBatchList" style="margin-top:12px;">
                        <div class="pb-skel">Search a product to load batches…</div>
                    </div>
                </div>

                <div class="pb-drawer__foot">
                    <div class="pb-hint">Tip: click batch or use ↑ ↓ Enter. Expired batches show warning.</div>
                </div>
            </div>
        </div>

    </div>

    {{-- ✅ IMPORTANT: Use the SAME CSS from create.blade.php --}}
    {{-- Copy/paste your <style> block from create.blade.php here unchanged --}}
    {{-- For shortness I did not duplicate it again. Use exact same CSS. --}}

    {{-- SCRIPT (same engine as create, but with edit prefill) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const root = document.getElementById('pbEditRoot');
            const cfg = {
                productsSearch: root?.dataset.productsSearch || '',
                giftSearch: root?.dataset.giftSearch || '',
                batchesByProduct: root?.dataset.batchesByProduct || '',
                batchJson: root?.dataset.batchJson || '',
            };

            // Toast close
            document.querySelectorAll('[data-toast-close]').forEach(btn => {
                btn.addEventListener('click', () => btn.closest('.pb-toast')?.remove());
            });

            // Helpers
            const $ = (sel) => document.querySelector(sel);
            const show = (el) => el && el.classList.add('is-show');
            const hide = (el) => el && el.classList.remove('is-show');

            function escapeHtml(s) {
                return String(s ?? '').replace(/[&<>"']/g, m => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                } [m]));
            }

            async function fetchJSON(url) {
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) return null;
                return await res.json().catch(() => null);
            }

            async function fetchProducts(q, url) {
                if (!url) return [];
                const res = await fetch(`${url}?q=${encodeURIComponent(q)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) return [];
                return await res.json().catch(() => []);
            }

            function renderSuggest(el, items, onPick, state) {
                if (!el) return;
                if (!items.length) {
                    el.innerHTML = `
                <div class="pb-suggest__item">
                    <div class="pb-suggest__name">No results</div>
                    <div class="pb-suggest__meta">Try different keyword</div>
                </div>`;
                    show(el);
                    return;
                }
                el.innerHTML = '';
                items.forEach((p, idx) => {
                    const div = document.createElement('div');
                    div.className = 'pb-suggest__item';
                    div.dataset.index = idx;
                    div.innerHTML = `
                <div class="pb-suggest__name">${escapeHtml(p.name)}</div>
                <div class="pb-suggest__meta">${escapeHtml(p.barcode || '')}</div>
            `;
                    div.addEventListener('click', () => onPick(p));
                    el.appendChild(div);
                });

                if (state) state.activeIndex = 0;
                show(el);
                highlightSuggestActive(el, state?.activeIndex ?? 0);
            }

            function highlightSuggestActive(el, idx) {
                if (!el) return;
                const items = Array.from(el.querySelectorAll('.pb-suggest__item'));
                items.forEach((it, i) => it.style.background = (i === idx) ? 'var(--accent)' : '');
                items[idx]?.scrollIntoView({
                    block: 'nearest'
                });
            }

            function flashButton(btn, text) {
                if (!btn) return;
                const old = btn.textContent;
                btn.textContent = text;
                setTimeout(() => btn.textContent = old, 900);
            }

            function isDatePast(dateStr) {
                if (!dateStr) return false;
                const d = new Date(dateStr + 'T00:00:00');
                const now = new Date();
                now.setHours(0, 0, 0, 0);
                return d < now;
            }

            function isDateSoon(dateStr, days = 30) {
                if (!dateStr) return false;
                const d = new Date(dateStr + 'T00:00:00');
                const now = new Date();
                now.setHours(0, 0, 0, 0);
                const limit = new Date(now);
                limit.setDate(limit.getDate() + days);
                return d >= now && d <= limit;
            }

            // -------------------------
            // Product select (edit)
            // -------------------------
            const productIdInput = $('#product_id');
            const productManual = $('#product_id_manual');

            const productSearch = $('#pbProductSearch');
            const productResults = $('#pbProductResults');
            const selectedLine = $('#pbSelectedProductLine');

            const mainSuggestState = {
                activeIndex: 0,
                items: []
            };

            function renderSelectedProduct(p) {
                if (!selectedLine) return;
                selectedLine.innerHTML = `
            <span class="pb-inline">
                <strong>${escapeHtml(p.name)}</strong>
                <span style="opacity:.8">${escapeHtml(p.barcode || '')}</span>
                <button type="button" data-clear-product title="Change">×</button>
            </span>
        `;
                selectedLine.querySelector('[data-clear-product]')?.addEventListener('click', () => {
                    if (productIdInput) productIdInput.value = '';
                    if (productManual) productManual.value = '';
                    if (productSearch) productSearch.value = '';
                    selectedLine.innerHTML = '';
                });
            }

            // Prefill selected product line from manual selected text
            if (productManual && productManual.value) {
                const text = productManual.options[productManual.selectedIndex]?.text ||
                    `Product #${productManual.value}`;
                renderSelectedProduct({
                    id: productManual.value,
                    name: text,
                    barcode: ''
                });
            }

            if (productSearch && productResults && cfg.productsSearch) {
                let t = null;

                productSearch.addEventListener('input', () => {
                    clearTimeout(t);
                    const q = productSearch.value.trim();
                    if (q.length < 2) {
                        hide(productResults);
                        productResults.innerHTML = '';
                        return;
                    }

                    t = setTimeout(async () => {
                        const items = await fetchProducts(q, cfg.productsSearch);
                        mainSuggestState.items = items || [];

                        renderSuggest(productResults, mainSuggestState.items, (p) => {
                            if (productIdInput) productIdInput.value = p.id;
                            if (productManual) productManual.value = p.id;
                            renderSelectedProduct(p);
                            productSearch.value = '';
                            hide(productResults);
                        }, mainSuggestState);
                    }, 250);
                });

                productSearch.addEventListener('keydown', (e) => {
                    if (!productResults.classList.contains('is-show')) return;
                    const max = (mainSuggestState.items || []).length - 1;

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        mainSuggestState.activeIndex = Math.min(max, mainSuggestState.activeIndex + 1);
                        highlightSuggestActive(productResults, mainSuggestState.activeIndex);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        mainSuggestState.activeIndex = Math.max(0, mainSuggestState.activeIndex - 1);
                        highlightSuggestActive(productResults, mainSuggestState.activeIndex);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        const p = mainSuggestState.items?.[mainSuggestState.activeIndex];
                        if (p) {
                            if (productIdInput) productIdInput.value = p.id;
                            if (productManual) productManual.value = p.id;
                            renderSelectedProduct(p);
                            productSearch.value = '';
                            hide(productResults);
                        }
                    } else if (e.key === 'Escape') {
                        hide(productResults);
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!productResults.contains(e.target) && !productSearch.contains(e.target)) hide(
                        productResults);
                });
            }

            if (productManual) {
                productManual.addEventListener('change', function() {
                    const val = this.value;
                    if (!val) {
                        if (productIdInput) productIdInput.value = '';
                        if (selectedLine) selectedLine.innerHTML = '';
                        return;
                    }
                    if (productIdInput) productIdInput.value = val;
                    const text = this.options[this.selectedIndex]?.text || `Product #${val}`;
                    renderSelectedProduct({
                        id: val,
                        name: text,
                        barcode: ''
                    });
                });
            }

            // -------------------------
            // Gift search + preview (edit)
            // -------------------------
            const offerToggle = $('#is_free_offer_active');
            const offerFields = $('#freeOfferFields');

            const giftSearch = $('#pbGiftSearch');
            const giftResults = $('#pbGiftResults');
            const giftHidden = $('#free_product_id');
            const giftSelectedLine = $('#pbGiftSelectedLine');
            const giftFallback = $('#pbGiftSelectFallback');

            const prevBuy = $('#pbPrevBuy');
            const prevFree = $('#pbPrevFree');
            const prevGiftName = $('#pbPrevGiftName');

            const giftSuggestState = {
                activeIndex: 0,
                items: []
            };

            function syncOfferBox() {
                if (!offerToggle || !offerFields) return;
                offerFields.style.display = offerToggle.checked ? 'block' : 'none';
            }
            offerToggle?.addEventListener('change', syncOfferBox);
            syncOfferBox();

            const buyInput = document.querySelector('[name="free_buy_qty"]');
            const freeInput = document.querySelector('[name="free_qty"]');

            function syncGiftPreview() {
                if (prevBuy && buyInput) prevBuy.textContent = (buyInput.value || '1');
                if (prevFree && freeInput) prevFree.textContent = (freeInput.value || '1');
            }
            buyInput?.addEventListener('input', syncGiftPreview);
            freeInput?.addEventListener('input', syncGiftPreview);
            syncGiftPreview();

            function renderGiftSelected(p) {
                if (!giftSelectedLine) return;
                giftSelectedLine.innerHTML = `
            <span class="pb-inline">
                🎁 <strong>${escapeHtml(p.name)}</strong>
                <span style="opacity:.8">${escapeHtml(p.barcode || '')}</span>
                <button type="button" data-clear-gift title="Change">×</button>
            </span>
        `;
                giftSelectedLine.querySelector('[data-clear-gift]')?.addEventListener('click', () => {
                    if (giftHidden) giftHidden.value = '';
                    if (giftSearch) giftSearch.value = '';
                    if (giftFallback) giftFallback.value = '';
                    giftSelectedLine.innerHTML = '';
                    if (prevGiftName) prevGiftName.textContent = 'Not selected';
                });
                if (prevGiftName) prevGiftName.textContent = p.name || 'Selected';
            }

            // Prefill gift selected line from fallback selected
            if (giftFallback && giftFallback.value) {
                const text = giftFallback.options[giftFallback.selectedIndex]?.text ||
                    `Gift #${giftFallback.value}`;
                renderGiftSelected({
                    id: giftFallback.value,
                    name: text,
                    barcode: ''
                });
            }

            giftFallback?.addEventListener('change', function() {
                if (!giftHidden) return;
                giftHidden.value = this.value || '';
                const text = this.options[this.selectedIndex]?.text || '';
                if (this.value) renderGiftSelected({
                    id: this.value,
                    name: text,
                    barcode: ''
                });
            });

            if (giftSearch && giftResults && cfg.giftSearch) {
                let t = null;

                giftSearch.addEventListener('input', () => {
                    clearTimeout(t);
                    const q = giftSearch.value.trim();
                    if (q.length < 2) {
                        hide(giftResults);
                        giftResults.innerHTML = '';
                        return;
                    }

                    t = setTimeout(async () => {
                        const items = await fetchProducts(q, cfg.giftSearch);
                        giftSuggestState.items = items || [];

                        renderSuggest(giftResults, giftSuggestState.items, (p) => {
                            if (giftHidden) giftHidden.value = p.id;
                            if (giftFallback) giftFallback.value = p.id;
                            renderGiftSelected(p);
                            giftSearch.value = '';
                            hide(giftResults);
                        }, giftSuggestState);
                    }, 250);
                });

                giftSearch.addEventListener('keydown', (e) => {
                    if (!giftResults.classList.contains('is-show')) return;
                    const max = (giftSuggestState.items || []).length - 1;

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        giftSuggestState.activeIndex = Math.min(max, giftSuggestState.activeIndex + 1);
                        highlightSuggestActive(giftResults, giftSuggestState.activeIndex);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        giftSuggestState.activeIndex = Math.max(0, giftSuggestState.activeIndex - 1);
                        highlightSuggestActive(giftResults, giftSuggestState.activeIndex);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        const p = giftSuggestState.items?.[giftSuggestState.activeIndex];
                        if (p) {
                            if (giftHidden) giftHidden.value = p.id;
                            if (giftFallback) giftFallback.value = p.id;
                            renderGiftSelected(p);
                            giftSearch.value = '';
                            hide(giftResults);
                        }
                    } else if (e.key === 'Escape') {
                        hide(giftResults);
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!giftResults.contains(e.target) && !giftSearch.contains(e.target)) hide(
                        giftResults);
                });
            }

            // -------------------------
            // Drawer (Copy from batch)
            // -------------------------
            const openDrawerBtn = $('#pbOpenCopyDrawer');
            const drawer = $('#pbCopyDrawer');
            const drawerList = $('#pbBatchList');
            const drawerProdName = $('#pbDrawerProductName');
            const refreshBtn = $('#pbRefreshBatches');

            const drawerProductSearch = $('#pbDrawerProductSearch');
            const drawerProductResults = $('#pbDrawerProductResults');
            const drawerSelectedProductLine = $('#pbDrawerSelectedProductLine');
            const applyDrawerProductBtn = $('#pbApplyDrawerProduct');
            const copyModeSelect = $('#pbCopyMode');

            let drawerSelectedProduct = null;
            let drawerBatchItems = [];
            let drawerActiveBatchIndex = 0;
            const drawerSuggestState = {
                activeIndex: 0,
                items: []
            };

            function openDrawer() {
                if (!drawer) return;
                drawer.classList.add('is-open');
                drawer.setAttribute('aria-hidden', 'false');
                drawerList.innerHTML = `<div class="pb-skel">Search a product to load batches…</div>`;
                setTimeout(() => drawerProductSearch?.focus(), 40);
            }

            function closeDrawer() {
                if (!drawer) return;
                drawer.classList.remove('is-open');
                drawer.setAttribute('aria-hidden', 'true');
                hide(drawerProductResults);
            }
            openDrawerBtn?.addEventListener('click', openDrawer);
            document.querySelectorAll('[data-drawer-close]').forEach(el => el.addEventListener('click',
                closeDrawer));

            function renderDrawerSelectedProduct(p) {
                drawerSelectedProduct = p;
                if (drawerSelectedProductLine) {
                    drawerSelectedProductLine.innerHTML = `
                <span class="pb-inline">
                    <strong>${escapeHtml(p.name)}</strong>
                    <span style="opacity:.8">${escapeHtml(p.barcode || '')}</span>
                    <button type="button" data-clear-drawer-product title="Change">×</button>
                </span>
            `;
                    drawerSelectedProductLine.querySelector('[data-clear-drawer-product]')?.addEventListener(
                        'click', () => {
                            drawerSelectedProduct = null;
                            drawerSelectedProductLine.innerHTML = '';
                            if (drawerProductSearch) {
                                drawerProductSearch.value = '';
                                drawerProductSearch.disabled = false;
                            }
                            if (drawerProdName) drawerProdName.textContent = '—';
                            drawerList.innerHTML =
                                `<div class="pb-skel">Search a product to load batches…</div>`;
                            if (applyDrawerProductBtn) applyDrawerProductBtn.disabled = true;
                            drawerBatchItems = [];
                            drawerActiveBatchIndex = 0;
                        });
                }
                if (drawerProdName) drawerProdName.textContent = p.name || `Product #${p.id}`;
                if (applyDrawerProductBtn) applyDrawerProductBtn.disabled = false;
            }

            async function loadBatchesForProduct(pid, productName) {
                if (!drawerList || !pid || !cfg.batchesByProduct) return;
                drawerList.innerHTML = `<div class="pb-skel">Loading batches…</div>`;
                if (drawerProdName) drawerProdName.textContent = productName || `Product #${pid}`;
                const url = cfg.batchesByProduct.replace('__PID__', pid);
                const rows = await fetchJSON(url);

                if (!Array.isArray(rows) || rows.length === 0) {
                    drawerList.innerHTML = `<div class="pb-skel">No batches found for this product.</div>`;
                    drawerBatchItems = [];
                    drawerActiveBatchIndex = 0;
                    return;
                }

                drawerList.innerHTML = '';
                drawerBatchItems = [];
                drawerActiveBatchIndex = 0;

                rows.forEach((b, idx) => {
                    const expPast = isDatePast(b.expiry_date);
                    const expSoon = !expPast && isDateSoon(b.expiry_date, 30);

                    const badge = expPast ?
                        `<span class="pb-badge-warn">Expired</span>` :
                        (expSoon ? `<span class="pb-badge-soon">Expiring Soon</span>` : ``);

                    const giftText = b.is_free_offer_active ?
                        `🎁 Gift: ${escapeHtml(b.gift_name || ('#'+(b.free_product_id||'')))}` :
                        '— Gift OFF';

                    const div = document.createElement('div');
                    div.className = 'pb-batchitem';
                    div.dataset.index = idx;

                    div.innerHTML = `
                <div class="pb-batchitem__top">
                    <div class="pb-batchitem__sku">${escapeHtml(b.batch_sku || ('Batch #' + b.id))} ${badge}</div>
                    <div class="pb-batchitem__qty">${escapeHtml(String(b.quantity ?? ''))} ${escapeHtml(b.unit || '')}</div>
                </div>
                <div class="pb-batchitem__meta">
                    <span>Buy: ${escapeHtml(String(b.buy_price ?? '0'))}</span>
                    <span>Org: ${escapeHtml(String(b.original_sell_price ?? '0'))}</span>
                    <span>Sell: ${escapeHtml(String(b.sell_price ?? '0'))}</span>
                    <span>${b.expiry_date ? 'Exp: '+escapeHtml(b.expiry_date) : 'No expiry'}</span>
                    <span>${giftText}</span>
                </div>
            `;

                    div.addEventListener('click', async () => {
                        // optional: apply drawer product to form
                        if (drawerSelectedProduct?.id) {
                            productIdInput.value = drawerSelectedProduct.id;
                            if (productManual) productManual.value = drawerSelectedProduct
                                .id;
                            renderSelectedProduct(drawerSelectedProduct);
                        }
                        await applyBatchToFormAdvanced(b, copyModeSelect?.value || 'all');
                        closeDrawer();
                    });

                    div.addEventListener('mouseenter', () => {
                        drawerActiveBatchIndex = idx;
                        highlightDrawerBatchActive();
                    });

                    drawerList.appendChild(div);
                    drawerBatchItems.push(div);
                });

                highlightDrawerBatchActive();
            }

            function highlightDrawerBatchActive() {
                drawerBatchItems.forEach((el, i) => el.classList.toggle('is-active', i === drawerActiveBatchIndex));
                drawerBatchItems[drawerActiveBatchIndex]?.scrollIntoView({
                    block: 'nearest'
                });
            }

            if (drawerProductSearch && drawerProductResults && cfg.productsSearch) {
                let t = null;

                drawerProductSearch.addEventListener('input', () => {
                    clearTimeout(t);
                    const q = drawerProductSearch.value.trim();
                    if (q.length < 2) {
                        hide(drawerProductResults);
                        drawerProductResults.innerHTML = '';
                        return;
                    }

                    t = setTimeout(async () => {
                        const items = await fetchProducts(q, cfg.productsSearch);
                        drawerSuggestState.items = items || [];

                        renderSuggest(drawerProductResults, drawerSuggestState.items, async (
                            p) => {
                            renderDrawerSelectedProduct(p);
                            drawerProductSearch.value = '';
                            drawerProductSearch.disabled = true;
                            hide(drawerProductResults);
                            await loadBatchesForProduct(p.id, p.name);
                        }, drawerSuggestState);
                    }, 220);
                });

                drawerProductSearch.addEventListener('keydown', async (e) => {
                    if (!drawerProductResults.classList.contains('is-show')) return;
                    const max = (drawerSuggestState.items || []).length - 1;

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        drawerSuggestState.activeIndex = Math.min(max, drawerSuggestState.activeIndex +
                            1);
                        highlightSuggestActive(drawerProductResults, drawerSuggestState.activeIndex);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        drawerSuggestState.activeIndex = Math.max(0, drawerSuggestState.activeIndex -
                            1);
                        highlightSuggestActive(drawerProductResults, drawerSuggestState.activeIndex);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        const p = drawerSuggestState.items?.[drawerSuggestState.activeIndex];
                        if (p) {
                            renderDrawerSelectedProduct(p);
                            drawerProductSearch.value = '';
                            drawerProductSearch.disabled = true;
                            hide(drawerProductResults);
                            await loadBatchesForProduct(p.id, p.name);
                        }
                    } else if (e.key === 'Escape') {
                        hide(drawerProductResults);
                    }
                });

                document.addEventListener('click', (e) => {
                    if (!drawerProductResults.contains(e.target) && !drawerProductSearch.contains(e.target))
                        hide(drawerProductResults);
                });
            }

            $('#pbApplyDrawerProduct')?.addEventListener('click', () => {
                if (!drawerSelectedProduct || !productIdInput) return;
                productIdInput.value = drawerSelectedProduct.id;
                if (productManual) productManual.value = drawerSelectedProduct.id;
                renderSelectedProduct(drawerSelectedProduct);
                flashButton($('#pbApplyDrawerProduct'), 'Applied ✓');
            });

            refreshBtn?.addEventListener('click', async () => {
                const pid = drawerSelectedProduct?.id || String(productIdInput?.value || '').trim();
                if (!pid) return;
                await loadBatchesForProduct(pid, drawerSelectedProduct?.name || `Product #${pid}`);
            });

            async function applyBatchToFormAdvanced(batch, mode = 'all') {
                const MAPS = {
                    pricing: {
                        buy_price: 'buy_price',
                        original_sell_price: 'original_sell_price',
                        discounted_price: 'discounted_price',
                        discount_percentage: 'discount_percentage'
                    },
                    wholesale: {
                        whole_sell_price: 'whole_sell_price',
                        whole_sell_min_qty: 'whole_sell_min_qty',
                        whole_sell_max_qty: 'whole_sell_max_qty',
                        customer_whole_price: 'customer_whole_price',
                        customer_whole_min_qty: 'customer_whole_min_qty',
                        customer_whole_max_qty: 'customer_whole_max_qty'
                    },
                    gift: {
                        free_buy_qty: 'free_buy_qty',
                        free_qty: 'free_qty',
                        free_product_id: 'free_product_id'
                    },
                    dates: {
                        manufacture_date: 'manufacture_date',
                        expiry_date: 'expiry_date',
                        notes: 'notes',
                        batch_no: 'batch_no',
                        unit: 'unit'
                    }
                };

                const groups = (mode === 'all') ? ['pricing', 'wholesale', 'gift', 'dates'] : [mode];

                groups.forEach(group => {
                    const map = MAPS[group];
                    Object.keys(map).forEach(k => {
                        const name = map[k];
                        const el = document.querySelector(`[name="${name}"]`);
                        if (!el) return;
                        if (name === 'quantity') return;
                        if (batch[k] === null || typeof batch[k] === 'undefined') return;

                        if (name === 'free_product_id') {
                            if (giftHidden) giftHidden.value = batch.free_product_id || '';
                            if (giftFallback) giftFallback.value = batch.free_product_id || '';
                            if (batch.free_product_id) {
                                renderGiftSelected({
                                    id: batch.free_product_id,
                                    name: batch.gift_name ||
                                        `Gift Product #${batch.free_product_id}`,
                                    barcode: batch.gift_barcode || ''
                                });
                            }
                            return;
                        }

                        el.value = batch[k];
                        el.dispatchEvent(new Event('input', {
                            bubbles: true
                        }));
                    });
                });

                if (mode === 'all') {
                    ['is_online', 'is_offline', 'is_pos', 'is_active'].forEach(k => {
                        const cb = document.querySelector(`[name="${k}"][type="checkbox"]`);
                        if (cb) cb.checked = !!batch[k];
                    });
                }

                if (mode === 'all' || mode === 'gift') {
                    if (offerToggle) offerToggle.checked = !!batch.is_free_offer_active;
                    syncOfferBox();
                    syncGiftPreview();
                }

                // re-trigger discount calc
                document.getElementById('discounted_price')?.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                document.getElementById('discount_percentage')?.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                document.querySelector('[name="original_sell_price"]')?.dispatchEvent(new Event('input', {
                    bubbles: true
                }));

                flashButton(openDrawerBtn, 'Copied ✓');
            }

            // -------------------------
            // Draft save/restore
            // -------------------------
            const DRAFT_KEY = 'pb_edit_draft_v2_{{ $batch->id }}';
            const saveDraftBtn = $('#pbSaveDraft');
            const clearDraftBtn = $('#pbClearDraft');
            const form = $('#pbBatchForm');

            function getFormData() {
                if (!form) return {};
                const fd = new FormData(form);
                const obj = {};
                fd.forEach((v, k) => obj[k] = v);
                form.querySelectorAll('input[type="checkbox"][name]').forEach(cb => obj[cb.name] = cb.checked ? 1 :
                    0);
                return obj;
            }

            function setFormData(obj) {
                if (!form || !obj) return;
                Object.keys(obj).forEach(k => {
                    const el = form.querySelector(`[name="${k}"]`);
                    if (!el) return;
                    if (el.type === 'checkbox') el.checked = String(obj[k]) === '1' || obj[k] === true;
                    else el.value = obj[k];
                    el.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                });
                syncOfferBox();
                syncGiftPreview();
            }

            saveDraftBtn?.addEventListener('click', () => {
                localStorage.setItem(DRAFT_KEY, JSON.stringify(getFormData()));
                flashButton(saveDraftBtn, 'Saved ✓');
            });
            clearDraftBtn?.addEventListener('click', () => {
                localStorage.removeItem(DRAFT_KEY);
                flashButton(clearDraftBtn, 'Cleared ✓');
            });

            // restore if no validation errors
            try {
                const saved = localStorage.getItem(DRAFT_KEY);
                if (saved && !{{ $errors->any() ? 'true' : 'false' }}) setFormData(JSON.parse(saved));
            } catch (e) {}

            // -------------------------
            // Prevent double submit + sync hidden ids
            // -------------------------
            const submitBtn = $('#pbSubmitBtn');
            const spinner = $('#pbSubmitSpinner');

            form?.addEventListener('submit', function() {
                // sync product_id from manual if empty
                if (productIdInput && productManual && !productIdInput.value && productManual.value) {
                    productIdInput.value = productManual.value;
                }
                // sync gift id from fallback if empty
                if (giftHidden && giftFallback && !giftHidden.value && giftFallback.value) {
                    giftHidden.value = giftFallback.value;
                }

                if (submitBtn) submitBtn.disabled = true;
                if (spinner) spinner.style.display = 'inline-block';
            });
        });
    </script>

@endsection
