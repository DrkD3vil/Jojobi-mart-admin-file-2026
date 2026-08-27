{{-- resources/views/products/product-batches/all.blade.php --}}
@extends('layouts.app')

@section('content')
    {{-- =========================================================
    STYLES (ONE FILE)
    - Uses your palette variables as-is
    - Minimal but modern & responsive
    ========================================================= --}}
    <style>
        /* ---------------------------------------------------------
                   Uses your color palette variables (already defined globally)
                   --------------------------------------------------------- */

        /* Page container */
        .pbx-page {
            padding: 18px;
            max-width: 1400px;
            margin: 0 auto;
            animation: pbxFadeUp .45s cubic-bezier(.2, .9, .2, 1) both;
        }

        @keyframes pbxFadeUp {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* Header */
        .pbx-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .pbx-header__left {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            min-width: 0
        }

        .pbx-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -10px color-mix(in oklch, var(--accent-color) 45%, black);
            position: relative;
        }

        .pbx-icon::after {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: 20px;
            background: radial-gradient(circle at 30% 30%, var(--accent-glow), transparent 55%);
            filter: blur(10px);
            opacity: .9;
            z-index: -1;
        }

        .pbx-icon svg {
            width: 26px;
            height: 26px;
            color: var(--sidebar-primary-foreground)
        }

        .pbx-titlewrap {
            min-width: 0
        }

        .pbx-title {
            margin: 0;
            font-size: clamp(1.35rem, 2.2vw, 2rem);
            font-weight: 900;
            letter-spacing: -.02em;
            background: linear-gradient(135deg, var(--text-primary), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .pbx-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 6px
        }

        .pbx-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 70%, transparent);
            box-shadow: var(--card-shadow);
        }

        .pbx-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--success);
            animation: pbxPulse 1.8s ease-in-out infinite
        }

        @keyframes pbxPulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .45
            }
        }

        .pbx-pill__text {
            font-weight: 800
        }

        .pbx-sep {
            opacity: .45
        }

        .pbx-muted {
            color: var(--text-muted)
        }

        .pbx-small {
            font-size: .85rem
        }

        .pbx-strong {
            font-weight: 900
        }

        .pbx-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace
        }

        /* Buttons */
        .pbx-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: calc(var(--radius) + 8px);
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 70%, transparent);
            color: var(--text-primary);
            font-weight: 850;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal), border-color var(--transition-normal), background var(--transition-normal);
            user-select: none;
            white-space: nowrap;
        }

        .pbx-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--card-shadow-hover);
            border-color: color-mix(in oklch, var(--accent-color) 55%, var(--border-color))
        }

        .pbx-btn:active {
            transform: translateY(0)
        }

        .pbx-btn__icon {
            width: 18px;
            height: 18px
        }

        .pbx-btn-primary {
            border: none;
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            color: var(--sidebar-primary-foreground);
            box-shadow: 0 12px 30px -14px color-mix(in oklch, var(--accent-color) 60%, black);
        }

        .pbx-btn-primary:hover {
            filter: brightness(1.05)
        }

        .pbx-btn-ghost {
            background: transparent
        }

        .pbx-btn-danger {
            background: color-mix(in oklch, var(--danger) 20%, transparent);
            border-color: color-mix(in oklch, var(--danger) 40%, var(--border-color));
            color: color-mix(in oklch, var(--danger) 90%, white);
        }

        .pbx-btn-sm {
            padding: 8px 10px;
            border-radius: calc(var(--radius) + 6px);
            font-weight: 850
        }

        /* Stats */
        .pbx-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin: 14px 0 16px;
        }

        .pbx-stat {
            border-radius: calc(var(--radius) + 14px);
            border: 1px solid var(--border-color);
            background: linear-gradient(145deg, var(--card), color-mix(in oklch, var(--secondary) 55%, transparent));
            box-shadow: var(--card-shadow);
            padding: 14px;
            display: flex;
            gap: 12px;
            align-items: center;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal), border-color var(--transition-normal);
        }

        .pbx-stat:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow-hover);
            border-color: color-mix(in oklch, var(--accent-color) 55%, var(--border-color))
        }

        .pbx-stat__icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: color-mix(in oklch, var(--accent-color) 16%, transparent);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pbx-stat__icon svg {
            width: 22px;
            height: 22px;
            color: var(--accent-color)
        }

        .pbx-stat__value {
            font-size: 1.25rem;
            font-weight: 950
        }

        .pbx-stat__label {
            color: var(--text-muted);
            font-weight: 800;
            font-size: .86rem
        }

        /* Filters */
        .pbx-filters {
            margin-bottom: 14px
        }

        .pbx-card {
            border-radius: calc(var(--radius) + 14px);
            border: 1px solid var(--border-color);
            background: linear-gradient(145deg, var(--card), color-mix(in oklch, var(--secondary) 55%, transparent));
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .pbx-card__head {
            padding: 14px 14px 10px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: color-mix(in oklch, var(--glass-base) 60%, transparent);
        }

        .pbx-card__body {
            padding: 14px
        }

        .pbx-card__foot {
            padding: 14px;
            border-top: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 55%, transparent)
        }

        .pbx-pad0 {
            padding: 0
        }

        .pbx-h2 {
            margin: 0;
            font-weight: 950;
            font-size: 1.05rem
        }

        .pbx-headtools {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .pbx-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 12px;
        }

        .pbx-col-4 {
            grid-column: span 4
        }

        .pbx-col-2 {
            grid-column: span 2
        }

        .pbx-actionsrow {
            display: flex;
            align-items: end;
            justify-content: flex-end;
            gap: 10px
        }

        .pbx-field {
            min-width: 0
        }

        .pbx-label {
            display: block;
            font-weight: 900;
            font-size: .78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 6px
        }

        .pbx-inputwrap {
            position: relative
        }

        .pbx-inputicon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--text-muted)
        }

        .pbx-input,
        .pbx-select {
            width: 100%;
            border-radius: calc(var(--radius) + 10px);
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--input) 65%, transparent);
            color: var(--text-primary);
            padding: 10px 12px;
            font-weight: 800;
            transition: box-shadow var(--transition-normal), border-color var(--transition-normal), transform var(--transition-normal);
        }

        .pbx-input {
            padding-left: 40px
        }

        .pbx-input:focus,
        .pbx-select:focus {
            outline: none;
            border-color: color-mix(in oklch, var(--accent-color) 60%, var(--border-color));
            box-shadow: 0 0 0 4px var(--accent-glow);
            transform: translateY(-1px);
        }

        .pbx-help {
            margin-top: 10px;
            color: var(--text-muted);
            font-weight: 700
        }

        /* Table */
        .pbx-tablewrap {
            overflow: auto
        }

        .pbx-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 980px;
        }

        .pbx-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            padding: 12px 14px;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .72rem;
            color: var(--text-muted);
            background: linear-gradient(to bottom, var(--muted), color-mix(in oklch, var(--secondary) 65%, transparent));
            border-bottom: 1px solid var(--border-color);
        }

        .pbx-table tbody td {
            padding: 14px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            transition: background var(--transition-fast), transform var(--transition-fast);
        }

        .pbx-table tbody tr:hover td {
            background: color-mix(in oklch, var(--accent) 55%, transparent);
        }

        .pbx-center {
            text-align: center
        }

        .pbx-right {
            text-align: right
        }

        /* Product cell */
        .pbx-prod {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0
        }

        .pbx-thumb {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--muted) 65%, transparent);
            flex: 0 0 auto;
        }

        .pbx-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .pbx-thumb__fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 950;
            color: var(--text-muted);
            font-size: .7rem
        }

        .pbx-linkbtn {
            border: 0;
            background: transparent;
            padding: 0;
            margin: 0;
            text-align: left;
            cursor: pointer;
            color: var(--text-primary);
            font-weight: 950;
            line-height: 1.15;
            max-width: 100%;
        }

        .pbx-linkbtn:hover {
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 4px;
        }

        /* Quantity bar */
        .pbx-qty .pbx-bar {
            margin-top: 8px;
            height: 6px;
            border-radius: 999px;
            background: color-mix(in oklch, var(--muted) 65%, transparent);
            overflow: hidden;
        }

        .pbx-qty .pbx-bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, var(--accent-color), var(--info));
            border-radius: 999px;
            animation: pbxGrow 700ms cubic-bezier(.2, .9, .2, 1) both;
        }

        @keyframes pbxGrow {
            from {
                transform: scaleX(.6);
                transform-origin: left
            }

            to {
                transform: scaleX(1)
            }
        }

        /* Price block */
        .pbx-price {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .pbx-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: var(--text-muted);
            font-weight: 800
        }

        .pbx-line b {
            color: var(--text-primary)
        }

        /* Chips */
        .pbx-chips {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-start
        }

        .pbx-chip {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: 950;
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 70%, transparent);
            transition: transform var(--transition-fast), box-shadow var(--transition-fast);
        }

        .pbx-chip:hover {
            transform: translateY(-1px) scale(1.05);
            box-shadow: var(--card-shadow-hover)
        }

        .pbx-chip--success {
            color: color-mix(in oklch, var(--success) 90%, white);
            background: color-mix(in oklch, var(--success) 18%, transparent)
        }

        .pbx-chip--warning {
            color: color-mix(in oklch, var(--warning) 95%, black);
            background: color-mix(in oklch, var(--warning) 22%, transparent)
        }

        .pbx-chip--info {
            color: color-mix(in oklch, var(--info) 95%, white);
            background: color-mix(in oklch, var(--info) 18%, transparent)
        }

        .pbx-chip--danger {
            color: color-mix(in oklch, var(--danger) 95%, white);
            background: color-mix(in oklch, var(--danger) 18%, transparent)
        }

        /* Actions */
        .pbx-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex-wrap: wrap
        }

        .pbx-inline {
            display: inline
        }

        /* Bulk bar */
        .pbx-bulk {
            position: sticky;
            bottom: 0;
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            color: var(--sidebar-primary-foreground);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        /* Checkbox */
        .pbx-check {
            width: 18px;
            height: 18px;
            accent-color: color-mix(in oklch, var(--accent-color) 80%, white);
            cursor: pointer;
        }

        /* Status colors */
        .pbx-success {
            color: var(--success)
        }

        .pbx-warning {
            color: var(--warning)
        }

        .pbx-danger {
            color: var(--danger)
        }

        .pbx-info {
            color: var(--info)
        }

        /* Modal */
        .pbx-modal {
            position: fixed;
            inset: 0;
            display: none;
            z-index: 1000
        }

        .pbx-modal[aria-hidden="false"] {
            display: block
        }

        .pbx-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgb(0 0 0 / .45);
            backdrop-filter: blur(4px);
            animation: pbxFade .18s ease-out both;
        }

        .pbx-modal__panel {
            position: relative;
            width: min(900px, 92vw);
            margin: min(8vh, 72px) auto;
            border-radius: calc(var(--radius) + 16px);
            max-height: min(86vh, 820px);
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--card) 90%, transparent);
            box-shadow: var(--dropdown-shadow);
            overflow: hidden;
            animation: pbxPop .22s cubic-bezier(.2, .9, .2, 1) both;
        }

        @keyframes pbxFade {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @keyframes pbxPop {
            from {
                opacity: 0;
                transform: translateY(12px) scale(.98)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        .pbx-modal__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 62%, transparent);
        }

        .pbx-modal__title {
            font-weight: 950
        }

        .pbx-modal__body {
            padding: 14px
        }

        /* ✅ Scrollable content */
        .pbx-modal-body {
            overflow: auto;
            /* ✅ scroll */
            -webkit-overflow-scrolling: touch;
            padding: 14px;
        }


        /* Skeleton */
        .pbx-skel .sk {
            border-radius: 14px;
            border: 1px solid var(--border-color);
            background: linear-gradient(90deg,
                    color-mix(in oklch, var(--muted) 35%, transparent),
                    color-mix(in oklch, var(--accent-color) 14%, transparent),
                    color-mix(in oklch, var(--muted) 35%, transparent));
            background-size: 200% 100%;
            animation: pbxShimmer 1.15s linear infinite;
        }

        .pbx-skel .sk1 {
            height: 64px;
            margin-bottom: 10px
        }

        .pbx-skel .sk2 {
            height: 120px;
            margin-bottom: 10px
        }

        .pbx-skel .sk3 {
            height: 90px
        }

        @keyframes pbxShimmer {
            0% {
                background-position: 200% 0
            }

            100% {
                background-position: -200% 0
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .pbx-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .pbx-grid {
                gap: 10px
            }

            .pbx-col-4 {
                grid-column: span 12
            }

            .pbx-col-2 {
                grid-column: span 6
            }

            .pbx-actionsrow {
                grid-column: span 12;
                justify-content: flex-end
            }
        }

        @media (max-width: 640px) {
            .pbx-header {
                flex-direction: column;
                align-items: stretch
            }

            .pbx-header__right {
                display: flex;
                gap: 10px
            }

            .pbx-btn {
                width: 100%
            }

            .pbx-col-2 {
                grid-column: span 12
            }
        }

        /* Print */
        @media print {

            .pbx-header__right,
            .pbx-filters,
            .pbx-headtools,
            .pbx-bulk,
            .pbx-modal {
                display: none !important
            }

            .pbx-page {
                padding: 0
            }
        }

        /* Sticky top bar */
        .pbx-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 14px 16px;
            background: linear-gradient(135deg, var(--sidebar-accent), var(--bg-tertiary));
            border-bottom: 1px solid var(--border);
        }

        .pbx-modal-title {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin: 0;
            font-weight: 900;
            color: var(--foreground);
            line-height: 1.1;
            font-size: 1rem;
        }

        .pbx-modal-title small {
            font-weight: 700;
            color: var(--muted-foreground);
        }

        /* ✅ Scrollable content */
        .pbx-modal-body {
            overflow: auto;
            /* ✅ scroll */
            -webkit-overflow-scrolling: touch;
            padding: 14px;
        }

        /* optional: nicer scrollbars */
        .pbx-modal-body::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        .pbx-modal-body::-webkit-scrollbar-thumb {
            background: color-mix(in oklch, var(--accent-color) 35%, transparent);
            border-radius: 999px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        .pbx-modal-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .pbx-modal-close {
            appearance: none;
            border: 1px solid var(--border);
            background: color-mix(in oklch, var(--glass-base) 70%, transparent);
            color: var(--foreground);
            border-radius: 999px;
            padding: 8px 10px;
            font-weight: 900;
            cursor: pointer;
            transition: transform var(--transition-fast) ease, box-shadow var(--transition-fast) ease;
        }

        .pbx-modal-close:hover {
            transform: translateY(-1px);
            box-shadow: var(--card-shadow-hover);
        }

        @keyframes pbxFadeIn {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        /* Lock page scroll when modal is open */
        html.pbx-lock,
        body.pbx-lock {
            overflow: hidden !important;
        }

        /* Make the blade content fit modal nicely */
        [data-modal-view="1"] .batch-details-container {
            padding: 0;
        }

        [data-modal-view="1"] .batch-grid {
            margin-bottom: 0;
        }
    </style>

    {{-- resources/views/products/product-batches/all.blade.php --}}

    <div class="pbx-page">
        {{-- =========================================================
        PAGE HEADER
    ========================================================= --}}
        <header class="pbx-header">
            <div class="pbx-header__left">
                <div class="pbx-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z" />
                    </svg>
                </div>

                <div class="pbx-titlewrap">
                    <h1 class="pbx-title">Product Batches All</h1>
                    <div class="pbx-meta">
                        <span class="pbx-pill">
                            <span class="pbx-dot"></span>
                            <span class="pbx-pill__text">{{ $batches->total() }} Total</span>
                        </span>
                        <span class="pbx-sep">•</span>
                        <span class="pbx-muted">
                            <b>{{ $batches->firstItem() ?? 0 }}-{{ $batches->lastItem() ?? 0 }}</b> of
                            {{ $batches->total() }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="pbx-header__right">
                <button type="button" class="pbx-btn pbx-btn-ghost" data-action="toggleFilters" aria-controls="pbxFilters"
                    aria-expanded="false">
                    <svg viewBox="0 0 24 24" class="pbx-btn__icon">
                        <path fill="currentColor" d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" />
                    </svg>
                    Filters
                </button>

                <a class="pbx-btn pbx-btn-primary" href="{{ route('product.batches.create') }}">
                    <svg viewBox="0 0 24 24" class="pbx-btn__icon">
                        <path fill="currentColor" d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                    </svg>
                    Add Batch
                </a>
            </div>
        </header>

        {{-- =========================================================
        QUICK STATS
    ========================================================= --}}
        <section class="pbx-stats">
            <article class="pbx-stat">
                <div class="pbx-stat__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" />
                    </svg>
                </div>
                <div class="pbx-stat__body">
                    <div class="pbx-stat__value">
                        {{ number_format($metrics['channel_distribution']['online'] + $metrics['channel_distribution']['offline'] + $metrics['channel_distribution']['pos'], 0) }}
                    </div>
                    <div class="pbx-stat__label">Batches (page)</div>
                </div>
            </article>

            <article class="pbx-stat">
                <div class="pbx-stat__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z" />
                    </svg>
                </div>
                <div class="pbx-stat__body">
                    <div class="pbx-stat__value">${{ number_format($metrics['total_stock_value'], 2) }}</div>
                    <div class="pbx-stat__label">Stock Value (page)</div>
                </div>
            </article>

            <a href="{{ route('product-batches.trash') }}" class="text-decoration-none text-reset">

                <article class="pbx-stat">
                    <div class="pbx-stat__icon text-danger" aria-hidden="true">
                        <svg viewBox="0 0 24 24">
                            <path fill="currentColor" d="M6 19c0 1.1.9 2 2 2h8
                           c1.1 0 2-.9 2-2V7H6v12z
                           M19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                        </svg>
                    </div>

                    <div class="pbx-stat__body">
                        <div class="pbx-stat__value">
                            {{ $trashedBatchCount }}
                        </div>
                        <div class="pbx-stat__label">
                            Batch Trash
                        </div>
                    </div>
                </article>

            </a>


            <article class="pbx-stat">
                <div class="pbx-stat__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M17 12c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm1.65 7.35L16.5 17.2V14h1v2.79l1.85 1.85-.7.71zM18 3h-3.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H6c-1.1 0-2 .9-2 2v15c0 1.1.9 2 2 2h6.11c-1.26-1.29-2-3-2-4.89 0-3.87 3.13-7 7-7 1.9 0 3.62.8 4.89 2H18V3zm-6 2c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1z" />
                    </svg>
                </div>
                <div class="pbx-stat__body">
                    <div class="pbx-stat__value">{{ $metrics['expiring_soon'] }}</div>
                    <div class="pbx-stat__label">Expiring Soon (30d)</div>
                </div>
            </article>

            <article class="pbx-stat">
                <div class="pbx-stat__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24">
                        <path fill="currentColor"
                            d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                    </svg>
                </div>
                <div class="pbx-stat__body">
                    <div class="pbx-stat__value">{{ $metrics['low_stock'] }}</div>
                    <div class="pbx-stat__label">Low Stock (≤10)</div>
                </div>
            </article>
        </section>

        {{-- =========================================================
        FILTERS (DOM filter)
    ========================================================= --}}
        <section class="pbx-filters" id="pbxFilters" hidden>
            <div class="pbx-card">
                <div class="pbx-card__body">
                    <div class="pbx-grid">
                        <div class="pbx-field pbx-col-4">
                            <label class="pbx-label" for="pbxSearch">Search</label>
                            <div class="pbx-inputwrap">
                                <svg viewBox="0 0 24 24" class="pbx-inputicon" aria-hidden="true">
                                    <path fill="currentColor"
                                        d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5C16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                                </svg>
                                <input id="pbxSearch" type="text" class="pbx-input"
                                    placeholder="Search product, batch no..." autocomplete="off">
                            </div>
                        </div>

                        <div class="pbx-field pbx-col-2">
                            <label class="pbx-label" for="pbxStatus">Status</label>
                            <select id="pbxStatus" class="pbx-select">
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="pbx-field pbx-col-2">
                            <label class="pbx-label" for="pbxStock">Stock</label>
                            <select id="pbxStock" class="pbx-select">
                                <option value="all">All</option>
                                <option value="in-stock">In Stock</option>
                                <option value="low-stock">Low (≤10)</option>
                                <option value="out-of-stock">Out</option>
                            </select>
                        </div>

                        <div class="pbx-field pbx-col-2">
                            <label class="pbx-label" for="pbxExpiry">Expiry</label>
                            <select id="pbxExpiry" class="pbx-select">
                                <option value="all">All</option>
                                <option value="expired">Expired</option>
                                <option value="expiring-soon">Soon (30d)</option>
                                <option value="no-expiry">No Expiry</option>
                            </select>
                        </div>

                        <div class="pbx-field pbx-col-2 pbx-actionsrow">
                            <button type="button" class="pbx-btn pbx-btn-ghost"
                                data-action="clearFilters">Clear</button>
                            <button type="button" class="pbx-btn pbx-btn-primary"
                                data-action="applyFilters">Apply</button>
                        </div>
                    </div>

                    <div class="pbx-help">
                        Tip: Click a <b>product name</b> to open a fast modal preview. Press <b>Esc</b> to close.
                    </div>
                </div>
            </div>
        </section>

        {{-- =========================================================
        TABLE
    ========================================================= --}}
        <section class="pbx-card pbx-tablecard">
            <div class="pbx-card__head">
                <div>
                    <h2 class="pbx-h2">All Batches</h2>
                    <div class="pbx-muted" id="pbxResultsInfo">
                        Showing {{ $batches->count() }} of {{ $batches->total() }} (page: {{ $batches->count() }})
                    </div>
                </div>

                <div class="pbx-headtools">
                    <button type="button" class="pbx-btn pbx-btn-ghost pbx-btn-sm"
                        data-action="toggleDense">Dense</button>
                    <button type="button" class="pbx-btn pbx-btn-ghost pbx-btn-sm" data-action="exportCsv">Export
                        CSV</button>
                    <button type="button" class="pbx-btn pbx-btn-ghost pbx-btn-sm"
                        onclick="window.print()">Print</button>
                </div>
            </div>

            <div class="pbx-card__body pbx-pad0">
                <div class="pbx-tablewrap" role="region" aria-label="Product batches table">
                    <table class="pbx-table" id="pbxTable">
                        <thead>
                            <tr>
                                <th style="width:52px" class="pbx-center">
                                    <input type="checkbox" class="pbx-check" id="pbxSelectAll" aria-label="Select all">
                                </th>
                                <th>Product</th>
                                <th>Batch No</th>
                                <th>Quantity</th>
                                <th>Pricing</th>
                                <th>Wholesale</th>
                                <th>Expiry</th>
                                <th>Channels</th>
                                <th class="pbx-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($batches as $batch)
                                @php
                                    $isExpired = $batch->expiry_date && $batch->expiry_date->lt(now());
                                    $isExpSoon =
                                        $batch->expiry_date &&
                                        $batch->expiry_date->gt(now()) &&
                                        $batch->expiry_date->diffInDays(now()) <= 30;
                                    $isLow = $batch->quantity > 0 && $batch->quantity <= 10;

                                    $img = null;
                                    if ($batch->product && $batch->product->images->count()) {
                                        $img =
                                            optional($batch->product->images->firstWhere('is_primary', 1))
                                                ->image_path ?? $batch->product->images->first()->image_path;
                                    }
                                @endphp

                                <tr data-row
                                    data-search="{{ strtolower(($batch->product->name ?? 'unknown') . ' ' . ($batch->batch_no ?? '') . ' ' . ($batch->notes ?? '')) }}"
                                    data-status="{{ $batch->is_active ? 'active' : 'inactive' }}"
                                    data-stock="{{ $batch->quantity <= 0 ? 'out-of-stock' : ($isLow ? 'low-stock' : 'in-stock') }}"
                                    data-expiry="{{ $isExpired ? 'expired' : ($isExpSoon ? 'expiring-soon' : ($batch->expiry_date ? 'valid' : 'no-expiry')) }}">
                                    <td class="pbx-center">
                                        <input type="checkbox" class="pbx-check pbx-rowcheck"
                                            value="{{ $batch->id }}" aria-label="Select batch">
                                    </td>

                                    <td>
                                        <div class="pbx-prod">
                                            <div class="pbx-thumb" aria-hidden="true">
                                                @if ($img)
                                                    <img src="{{ $img }}"
                                                        alt="{{ $batch->product->name ?? 'Product' }}" loading="lazy"
                                                        decoding="async">
                                                @else
                                                    <div class="pbx-thumb__fallback">IMG</div>
                                                @endif
                                            </div>

                                            <div class="minw-0">
                                                {{-- Click -> AJAX modal quick view --}}
                                                <button type="button" class="pbx-linkbtn"
                                                    data-qv-url="{{ route('product.batches.show', $batch) }}"
                                                    aria-haspopup="dialog" aria-controls="pbxModal">
                                                    {{ $batch->product->name ?? 'Unknown' }}
                                                </button>

                                                <div class="pbx-muted pbx-small">
                                                    {{ $batch->product->category->name ?? '' }}
                                                    @if ($batch->product && $batch->product->brand)
                                                        • {{ $batch->product->brand->name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="pbx-mono pbx-strong">{{ $batch->batch_no ?? 'N/A' }}</div>
                                        <div class="pbx-muted pbx-small">{{ $batch->created_at->format('M d, Y') }}</div>
                                    </td>

                                   <td>
    @php
        $qty = (float) ($batch->stock_qty ?? 0);
        $isLow = $qty > 0 && $qty <= 10;
        $max = 100; // adjust if needed
        $w = $max > 0 ? min(100, ($qty / $max) * 100) : 0;
    @endphp

    <div class="pbx-qty">
        <div
            class="pbx-strong
                {{ $qty <= 0 ? 'pbx-danger' : ($isLow ? 'pbx-warning' : 'pbx-success') }}">
            {{ number_format($qty, 2) }}
        </div>

        <div class="pbx-muted pbx-small">{{ $batch->unit }}</div>

        <div class="pbx-bar" aria-hidden="true">
            <span style="width: {{ $w }}%"></span>
        </div>
    </div>
</td>

                                    <td>
                                        <div class="pbx-price">
                                            <div class="pbx-line"><span>Buy</span><b
                                                    class="pbx-mono">${{ number_format($batch->buy_price, 2) }}</b></div>
                                            <div class="pbx-line"><span>Sell</span><b
                                                    class="pbx-mono">${{ number_format($batch->original_sell_price, 2) }}</b>
                                            </div>
                                            @if ($batch->discounted_price)
                                                <div class="pbx-line"><span>Disc</span><b
                                                        class="pbx-mono pbx-info">${{ number_format($batch->discounted_price, 2) }}</b>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td>
                                        @if ($batch->whole_sell_price)
                                            <div class="pbx-price">
                                                <div class="pbx-line"><span>Price</span><b
                                                        class="pbx-mono">${{ number_format($batch->whole_sell_price, 2) }}</b>
                                                </div>
                                                <div class="pbx-line"><span>Qty</span><b
                                                        class="pbx-mono">{{ number_format($batch->whole_sell_min_qty ?? 0, 2) }}–{{ number_format($batch->whole_sell_max_qty ?? 0, 2) }}</b>
                                                </div>
                                            </div>
                                        @else
                                            <span class="pbx-muted pbx-small">Not set</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($batch->expiry_date)
                                            <div
                                                class="{{ $isExpired ? 'pbx-danger' : ($isExpSoon ? 'pbx-warning' : 'pbx-success') }} pbx-strong">
                                                {{ $batch->expiry_date->format('M d, Y') }}
                                            </div>
                                            <div class="pbx-muted pbx-small">
                                                @if (!$isExpired)
                                                    {{ $batch->expiry_date->diffInDays(now()) }} days left
                                                @else
                                                    Expired
                                                @endif
                                            </div>
                                        @else
                                            <span class="pbx-muted pbx-small">No expiry</span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="pbx-chips" aria-label="Sales channels">
                                            @if ($batch->is_online)
                                                <span class="pbx-chip pbx-chip--success" title="Online">O</span>
                                            @endif
                                            @if ($batch->is_offline)
                                                <span class="pbx-chip pbx-chip--info" title="Offline">S</span>
                                            @endif
                                            @if ($batch->is_pos)
                                                <span class="pbx-chip pbx-chip--warning" title="POS">P</span>
                                            @endif
                                            @if (!$batch->is_active)
                                                <span class="pbx-chip pbx-chip--danger" title="Inactive">X</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="pbx-right">
                                        <div class="pbx-actions">
                                            <a class="pbx-btn pbx-btn-ghost pbx-btn-sm"
                                                href="{{ route('product.batches.show', $batch) }}">View</a>
                                            <a class="pbx-btn pbx-btn-ghost pbx-btn-sm"
                                                href="{{ route('product.batches.edit', $batch) }}">Edit</a>

                                            <form method="POST" action="{{ route('product.batches.destroy', $batch) }}"
                                                class="pbx-inline"
                                                onsubmit="return confirm('Delete this batch? This cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="pbx-btn pbx-btn-danger pbx-btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Bulk bar --}}
                <div class="pbx-bulk" id="pbxBulk" hidden>
                    <div class="pbx-bulk__left"><b id="pbxBulkCount">0</b> selected</div>
                    <div class="pbx-bulk__right">
                        <button type="button" class="pbx-btn pbx-btn-ghost pbx-btn-sm"
                            data-action="clearSelection">Clear</button>
                    </div>
                </div>
            </div>

            @if ($batches->hasPages())
                <div class="pbx-card__foot">
                    {{ $batches->onEachSide(1)->links('vendor.pagination.custom') }}

                </div>
            @endif
        </section>
    </div>

    {{-- =========================================================
    MODAL (ONE FILE)
    ✅ Scrollable
    ✅ Responsive
    ✅ Locks background scroll
    ✅ Sticky header
    ✅ ESC & backdrop close
========================================================= --}}
    <div class="pbx-modal" id="pbxModal" role="dialog" aria-modal="true" aria-hidden="true"
        aria-labelledby="pbxModalTitle">
        <div class="pbx-modal__backdrop" data-close="1" aria-hidden="true"></div>

        <div class="pbx-modal__panel" role="document">
            <div class="pbx-modal__top">
                <div class="pbx-modal__titlewrap">
                    <div class="pbx-modal__title" id="pbxModalTitle">Quick View</div>
                    <div class="pbx-modal__subtitle" id="pbxModalSubtitle">—</div>
                </div>

                <div class="pbx-modal__tools">
                    <a class="pbx-btn pbx-btn-ghost pbx-btn-sm" id="pbxModalOpenPage" href="#" target="_self"
                        rel="noopener">Open</a>
                    <button type="button" class="pbx-btn pbx-btn-ghost pbx-btn-sm" data-close="1"
                        aria-label="Close modal">Close</button>
                </div>
            </div>

            {{-- ✅ IMPORTANT: this is the scroll container --}}
            <div class="pbx-modal__body" id="pbxModalBody" tabindex="0">
                <div class="pbx-skel" aria-hidden="true">
                    <div class="sk sk1"></div>
                    <div class="sk sk2"></div>
                    <div class="sk sk3"></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* =========================================================
               CORE UI (uses your existing :root palette variables)
            ========================================================= */
        .pbx-page {
            padding: 18px;
            max-width: 1400px;
            margin: 0 auto;
            animation: pbxFadeUp .45s cubic-bezier(.2, .9, .2, 1) both
        }

        @keyframes pbxFadeUp {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        .pbx-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px
        }

        .pbx-header__left {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            min-width: 0
        }

        .pbx-header__right {
            display: flex;
            gap: 10px;
            flex-wrap: wrap
        }

        .pbx-icon {
            width: 56px;
            height: 56px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -10px color-mix(in oklch, var(--accent-color) 45%, black);
            position: relative
        }

        .pbx-icon::after {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: 20px;
            background: radial-gradient(circle at 30% 30%, var(--accent-glow), transparent 55%);
            filter: blur(10px);
            opacity: .9;
            z-index: -1
        }

        .pbx-icon svg {
            width: 26px;
            height: 26px;
            color: var(--sidebar-primary-foreground)
        }

        .pbx-titlewrap {
            min-width: 0
        }

        .pbx-title {
            margin: 0;
            font-size: clamp(1.35rem, 2.2vw, 2rem);
            font-weight: 900;
            letter-spacing: -.02em;
            background: linear-gradient(135deg, var(--text-primary), var(--accent-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent
        }

        .pbx-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 6px
        }

        .pbx-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 70%, transparent);
            box-shadow: var(--card-shadow)
        }

        .pbx-dot {
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: var(--success);
            animation: pbxPulse 1.8s ease-in-out infinite
        }

        @keyframes pbxPulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .45
            }
        }

        .pbx-pill__text {
            font-weight: 800
        }

        .pbx-sep {
            opacity: .45
        }

        .pbx-muted {
            color: var(--text-muted)
        }

        .pbx-small {
            font-size: .85rem
        }

        .pbx-strong {
            font-weight: 900
        }

        .pbx-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace
        }

        .pbx-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: calc(var(--radius) + 8px);
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 70%, transparent);
            color: var(--text-primary);
            font-weight: 850;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal), border-color var(--transition-normal), background var(--transition-normal);
            user-select: none;
            white-space: nowrap
        }

        .pbx-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--card-shadow-hover);
            border-color: color-mix(in oklch, var(--accent-color) 55%, var(--border-color))
        }

        .pbx-btn:active {
            transform: translateY(0)
        }

        .pbx-btn__icon {
            width: 18px;
            height: 18px
        }

        .pbx-btn-primary {
            border: none;
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            color: var(--sidebar-primary-foreground);
            box-shadow: 0 12px 30px -14px color-mix(in oklch, var(--accent-color) 60%, black)
        }

        .pbx-btn-primary:hover {
            filter: brightness(1.05)
        }

        .pbx-btn-ghost {
            background: transparent
        }

        .pbx-btn-danger {
            background: color-mix(in oklch, var(--danger) 20%, transparent);
            border-color: color-mix(in oklch, var(--danger) 40%, var(--border-color));
            color: color-mix(in oklch, var(--danger) 90%, white)
        }

        .pbx-btn-sm {
            padding: 8px 10px;
            border-radius: calc(var(--radius) + 6px);
            font-weight: 850
        }

        .pbx-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin: 14px 0 16px
        }

        .pbx-stat {
            border-radius: calc(var(--radius) + 14px);
            border: 1px solid var(--border-color);
            background: linear-gradient(145deg, var(--card), color-mix(in oklch, var(--secondary) 55%, transparent));
            box-shadow: var(--card-shadow);
            padding: 14px;
            display: flex;
            gap: 12px;
            align-items: center;
            transition: transform var(--transition-normal), box-shadow var(--transition-normal), border-color var(--transition-normal)
        }

        .pbx-stat:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow-hover);
            border-color: color-mix(in oklch, var(--accent-color) 55%, var(--border-color))
        }

        .pbx-stat__icon {
            width: 44px;
            height: 44px;
            border-radius: 16px;
            background: color-mix(in oklch, var(--accent-color) 16%, transparent);
            display: flex;
            align-items: center;
            justify-content: center
        }

        .pbx-stat__icon svg {
            width: 22px;
            height: 22px;
            color: var(--accent-color)
        }

        .pbx-stat__value {
            font-size: 1.25rem;
            font-weight: 950
        }

        .pbx-stat__label {
            color: var(--text-muted);
            font-weight: 800;
            font-size: .86rem
        }

        .pbx-filters {
            margin-bottom: 14px
        }

        .pbx-card {
            border-radius: calc(var(--radius) + 14px);
            border: 1px solid var(--border-color);
            background: linear-gradient(145deg, var(--card), color-mix(in oklch, var(--secondary) 55%, transparent));
            box-shadow: var(--card-shadow);
            overflow: hidden
        }

        .pbx-card__head {
            padding: 14px 14px 10px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            background: color-mix(in oklch, var(--glass-base) 60%, transparent)
        }

        .pbx-card__body {
            padding: 14px
        }

        .pbx-card__foot {
            padding: 14px;
            border-top: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 55%, transparent)
        }

        .pbx-pad0 {
            padding: 0
        }

        .pbx-h2 {
            margin: 0;
            font-weight: 950;
            font-size: 1.05rem
        }

        .pbx-headtools {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        .pbx-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 12px
        }

        .pbx-col-4 {
            grid-column: span 4
        }

        .pbx-col-2 {
            grid-column: span 2
        }

        .pbx-actionsrow {
            display: flex;
            align-items: end;
            justify-content: flex-end;
            gap: 10px
        }

        .pbx-label {
            display: block;
            font-weight: 900;
            font-size: .78rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 6px
        }

        .pbx-inputwrap {
            position: relative
        }

        .pbx-inputicon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--text-muted)
        }

        .pbx-input,
        .pbx-select {
            width: 100%;
            border-radius: calc(var(--radius) + 10px);
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--input) 65%, transparent);
            color: var(--text-primary);
            padding: 10px 12px;
            font-weight: 800;
            transition: box-shadow var(--transition-normal), border-color var(--transition-normal), transform var(--transition-normal)
        }

        .pbx-input {
            padding-left: 40px
        }

        .pbx-input:focus,
        .pbx-select:focus {
            outline: none;
            border-color: color-mix(in oklch, var(--accent-color) 60%, var(--border-color));
            box-shadow: 0 0 0 4px var(--accent-glow);
            transform: translateY(-1px)
        }

        .pbx-help {
            margin-top: 10px;
            color: var(--text-muted);
            font-weight: 700
        }

        .pbx-tablewrap {
            overflow: auto
        }

        .pbx-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 980px
        }

        .pbx-table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            padding: 12px 14px;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: .72rem;
            color: var(--text-muted);
            background: linear-gradient(to bottom, var(--muted), color-mix(in oklch, var(--secondary) 65%, transparent));
            border-bottom: 1px solid var(--border-color)
        }

        .pbx-table tbody td {
            padding: 14px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            transition: background var(--transition-fast)
        }

        .pbx-table tbody tr:hover td {
            background: color-mix(in oklch, var(--accent) 55%, transparent)
        }

        .pbx-center {
            text-align: center
        }

        .pbx-right {
            text-align: right
        }

        .pbx-prod {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0
        }

        .pbx-thumb {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--muted) 65%, transparent);
            flex: 0 0 auto
        }

        .pbx-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .pbx-thumb__fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 950;
            color: var(--text-muted);
            font-size: .7rem
        }

        .pbx-linkbtn {
            border: 0;
            background: transparent;
            padding: 0;
            margin: 0;
            text-align: left;
            cursor: pointer;
            color: var(--text-primary);
            font-weight: 950;
            line-height: 1.15;
            max-width: 100%
        }

        .pbx-linkbtn:hover {
            text-decoration: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 4px
        }

        .pbx-qty .pbx-bar {
            margin-top: 8px;
            height: 6px;
            border-radius: 999px;
            background: color-mix(in oklch, var(--muted) 65%, transparent);
            overflow: hidden
        }

        .pbx-qty .pbx-bar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, var(--accent-color), var(--info));
            border-radius: 999px;
            animation: pbxGrow 700ms cubic-bezier(.2, .9, .2, 1) both
        }

        @keyframes pbxGrow {
            from {
                transform: scaleX(.6);
                transform-origin: left
            }

            to {
                transform: scaleX(1)
            }
        }

        .pbx-price {
            display: flex;
            flex-direction: column;
            gap: 6px
        }

        .pbx-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: var(--text-muted);
            font-weight: 800
        }

        .pbx-line b {
            color: var(--text-primary)
        }

        .pbx-chips {
            display: flex;
            gap: 6px;
            flex-wrap: wrap
        }

        .pbx-chip {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-weight: 950;
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--glass-base) 70%, transparent);
            transition: transform var(--transition-fast), box-shadow var(--transition-fast)
        }

        .pbx-chip:hover {
            transform: translateY(-1px) scale(1.05);
            box-shadow: var(--card-shadow-hover)
        }

        .pbx-chip--success {
            color: color-mix(in oklch, var(--success) 90%, white);
            background: color-mix(in oklch, var(--success) 18%, transparent)
        }

        .pbx-chip--warning {
            color: color-mix(in oklch, var(--warning) 95%, black);
            background: color-mix(in oklch, var(--warning) 22%, transparent)
        }

        .pbx-chip--info {
            color: color-mix(in oklch, var(--info) 95%, white);
            background: color-mix(in oklch, var(--info) 18%, transparent)
        }

        .pbx-chip--danger {
            color: color-mix(in oklch, var(--danger) 95%, white);
            background: color-mix(in oklch, var(--danger) 18%, transparent)
        }

        .pbx-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex-wrap: wrap
        }

        .pbx-inline {
            display: inline
        }

        .pbx-bulk {
            position: sticky;
            bottom: 0;
            background: linear-gradient(135deg, var(--accent-color), var(--info));
            color: var(--sidebar-primary-foreground);
            padding: 10px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px
        }

        .pbx-check {
            width: 18px;
            height: 18px;
            accent-color: color-mix(in oklch, var(--accent-color) 80%, white);
            cursor: pointer
        }

        .pbx-success {
            color: var(--success)
        }

        .pbx-warning {
            color: var(--warning)
        }

        .pbx-danger {
            color: var(--danger)
        }

        .pbx-info {
            color: var(--info)
        }

        /* =========================================================
               MODAL (✅ scrollable + responsive)
            ========================================================= */
        .pbx-modal {
            position: fixed;
            inset: 0;
            display: none;
            z-index: 1000
        }

        .pbx-modal[aria-hidden="false"] {
            display: block
        }

        /* Backdrop */
        .pbx-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgb(0 0 0 / .45);
            backdrop-filter: blur(6px);
            animation: pbxFade .18s ease-out both
        }

        @keyframes pbxFade {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        /* Panel: make it a grid (header + scroll body) */
        .pbx-modal__panel {
            position: relative;
            width: min(920px, 94vw);
            margin: min(6vh, 52px) auto;
            border-radius: calc(var(--radius) + 18px);
            border: 1px solid var(--border-color);
            background: color-mix(in oklch, var(--card) 92%, transparent);
            box-shadow: var(--dropdown-shadow);
            overflow: hidden;

            /* ✅ key: height limits + internal scroll */
            max-height: min(88vh, 860px);
            display: grid;
            grid-template-rows: auto 1fr;
            /* header + body */
            animation: pbxPop .22s cubic-bezier(.2, .9, .2, 1) both;
        }

        @keyframes pbxPop {
            from {
                opacity: 0;
                transform: translateY(12px) scale(.98)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        /* Sticky header (always visible) */
        .pbx-modal__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, var(--sidebar-accent), var(--bg-tertiary));
        }

        .pbx-modal__titlewrap {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0
        }

        .pbx-modal__title {
            font-weight: 950;
            color: var(--foreground);
            line-height: 1.1
        }

        .pbx-modal__subtitle {
            font-weight: 800;
            color: var(--muted-foreground);
            font-size: .85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .pbx-modal__tools {
            display: flex;
            gap: 8px;
            flex-wrap: wrap
        }

        /* ✅ Scroll container */
        .pbx-modal__body {
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            padding: 14px;
            scroll-behavior: smooth;
        }

        /* nicer scrollbars */
        .pbx-modal__body::-webkit-scrollbar {
            width: 10px;
            height: 10px
        }

        .pbx-modal__body::-webkit-scrollbar-thumb {
            background: color-mix(in oklch, var(--accent-color) 35%, transparent);
            border-radius: 999px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        .pbx-modal__body::-webkit-scrollbar-track {
            background: transparent
        }

        /* Skeleton */
        .pbx-skel .sk {
            border-radius: 14px;
            border: 1px solid var(--border-color);
            background: linear-gradient(90deg,
                    color-mix(in oklch, var(--muted) 35%, transparent),
                    color-mix(in oklch, var(--accent-color) 14%, transparent),
                    color-mix(in oklch, var(--muted) 35%, transparent));
            background-size: 200% 100%;
            animation: pbxShimmer 1.15s linear infinite;
        }

        .pbx-skel .sk1 {
            height: 64px;
            margin-bottom: 10px
        }

        .pbx-skel .sk2 {
            height: 120px;
            margin-bottom: 10px
        }

        .pbx-skel .sk3 {
            height: 90px
        }

        @keyframes pbxShimmer {
            0% {
                background-position: 200% 0
            }

            100% {
                background-position: -200% 0
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .pbx-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .pbx-grid {
                gap: 10px
            }

            .pbx-col-4 {
                grid-column: span 12
            }

            .pbx-col-2 {
                grid-column: span 6
            }

            .pbx-actionsrow {
                grid-column: span 12;
                justify-content: flex-end
            }
        }

        @media (max-width: 640px) {
            .pbx-header {
                flex-direction: column;
                align-items: stretch
            }

            .pbx-header__right {
                display: flex;
                flex-direction: column
            }

            .pbx-btn {
                width: 100%
            }

            .pbx-col-2 {
                grid-column: span 12
            }

            /* Modal on small device */
            .pbx-modal__panel {
                width: 96vw;
                margin: 3vh auto;
                max-height: 92vh;
                border-radius: calc(var(--radius) + 14px);
            }
        }

        /* Print */
        @media print {

            .pbx-header__right,
            .pbx-filters,
            .pbx-headtools,
            .pbx-bulk,
            .pbx-modal {
                display: none !important
            }

            .pbx-page {
                padding: 0
            }
        }

        /* ✅ Scroll lock helper class */
        html.pbx-lock,
        body.pbx-lock {
            overflow: hidden !important
        }
    </style>

    <script>
        /**
         * One-file JS:
         * - Filters page rows (DOM only)
         * - Modal loads HTML from route('product.batches.show', $batch) via fetch()
         * - ✅ Modal body scrolls (CSS + panel grid)
         * - ✅ Locks background scrolling (html/body .pbx-lock)
         *
         * NOTE (recommended):
         * In ProductBatchController@show:
         * if(request()->ajax()) return a lightweight html block (not full layout)
         * else normal full page.
         */

        document.addEventListener('DOMContentLoaded', () => {
            const filtersPanel = document.getElementById('pbxFilters');
            const toggleBtn = document.querySelector('[data-action="toggleFilters"]');

            const search = document.getElementById('pbxSearch');
            const status = document.getElementById('pbxStatus');
            const stock = document.getElementById('pbxStock');
            const expiry = document.getElementById('pbxExpiry');

            const table = document.getElementById('pbxTable');
            const rows = [...table.querySelectorAll('tbody tr[data-row]')];
            const resultsInfo = document.getElementById('pbxResultsInfo');

            const selectAll = document.getElementById('pbxSelectAll');
            const bulk = document.getElementById('pbxBulk');
            const bulkCount = document.getElementById('pbxBulkCount');

            // Modal
            const modal = document.getElementById('pbxModal');
            const modalBody = document.getElementById('pbxModalBody');
            const modalSubtitle = document.getElementById('pbxModalSubtitle');
            const modalOpenPage = document.getElementById('pbxModalOpenPage');
            let lastFocus = null;

            // -------------------------
            // Filters toggle
            // -------------------------
            function toggleFilters(forceOpen = null) {
                const isHidden = filtersPanel.hasAttribute('hidden');
                const open = (forceOpen === null) ? isHidden : forceOpen;

                if (open) {
                    filtersPanel.removeAttribute('hidden');
                    toggleBtn?.setAttribute('aria-expanded', 'true');
                    search?.focus();
                } else {
                    filtersPanel.setAttribute('hidden', 'hidden');
                    toggleBtn?.setAttribute('aria-expanded', 'false');
                }
            }

            // -------------------------
            // Filtering (DOM)
            // -------------------------
            function applyFilters() {
                const q = (search?.value || '').trim().toLowerCase();
                const st = status?.value || 'all';
                const sk = stock?.value || 'all';
                const ex = expiry?.value || 'all';

                let visible = 0;
                rows.forEach(r => {
                    const matchesSearch = !q || (r.dataset.search || '').includes(q);
                    const matchesStatus = (st === 'all') || (r.dataset.status === st);
                    const matchesStock = (sk === 'all') || (r.dataset.stock === sk);
                    const matchesExpiry = (ex === 'all') || (r.dataset.expiry === ex);

                    const show = matchesSearch && matchesStatus && matchesStock && matchesExpiry;
                    r.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                if (resultsInfo) resultsInfo.textContent = `Showing ${visible} of ${rows.length} (page)`;
            }

            function clearFilters() {
                if (search) search.value = '';
                if (status) status.value = 'all';
                if (stock) stock.value = 'all';
                if (expiry) expiry.value = 'all';
                applyFilters();
            }

            let t = null;
            search?.addEventListener('input', () => {
                clearTimeout(t);
                t = setTimeout(applyFilters, 160);
            });
            [status, stock, expiry].forEach(el => el?.addEventListener('change', applyFilters));

            // -------------------------
            // Bulk selection
            // -------------------------
            function updateBulk() {
                const checked = table.querySelectorAll('.pbx-rowcheck:checked').length;
                if (bulkCount) bulkCount.textContent = String(checked);
                bulk?.toggleAttribute('hidden', checked === 0);
            }

            selectAll?.addEventListener('change', () => {
                const on = selectAll.checked;
                table.querySelectorAll('.pbx-rowcheck').forEach(cb => cb.checked = on);
                updateBulk();
            });

            table?.addEventListener('change', (e) => {
                if (e.target.classList?.contains('pbx-rowcheck')) {
                    const all = table.querySelectorAll('.pbx-rowcheck').length;
                    const checked = table.querySelectorAll('.pbx-rowcheck:checked').length;
                    if (selectAll) selectAll.checked = (all === checked);
                    updateBulk();
                }
            });

            // -------------------------
            // Dense mode (optional)
            // -------------------------
            const denseCSS = document.createElement('style');
            denseCSS.textContent = `
        body.pbx-dense .pbx-table tbody td{padding:10px}
        body.pbx-dense .pbx-table thead th{padding:10px 12px}
        body.pbx-dense .pbx-stat{padding:12px}
    `;
            document.head.appendChild(denseCSS);

            // -------------------------
            // Modal helpers
            // -------------------------
            function lockScroll(on) {
                document.documentElement.classList.toggle('pbx-lock', !!on);
                document.body.classList.toggle('pbx-lock', !!on);
            }

            function setSkeleton() {
                modalBody.innerHTML = `
            <div class="pbx-skel" aria-hidden="true">
                <div class="sk sk1"></div>
                <div class="sk sk2"></div>
                <div class="sk sk3"></div>
            </div>
        `;
            }

            function openModal(url) {
                modal.setAttribute('aria-hidden', 'false');
                lockScroll(true);

                // for accessibility (return focus)
                modalBody.focus();

                // keep "Open" link correct
                if (modalOpenPage) modalOpenPage.href = url;
            }

            function closeModal() {
                modal.setAttribute('aria-hidden', 'true');
                lockScroll(false);
                setSkeleton();
                modalSubtitle.textContent = '—';
                if (lastFocus) lastFocus.focus();
            }

            async function loadModal(url, subtitleText) {
                setSkeleton();
                modalSubtitle.textContent = subtitleText || '—';
                openModal(url);

                try {
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!res.ok) throw new Error('Request failed');
                    const html = await res.text();

                    // Put HTML inside scroll container
                    modalBody.innerHTML = html;

                    // Ensure scroll starts top every open
                    modalBody.scrollTop = 0;
                } catch (err) {
                    modalBody.innerHTML = `
                <div class="pbx-card">
                    <div class="pbx-card__body">
                        <div class="pbx-strong">Could not load preview.</div>
                        <div class="pbx-muted pbx-small" style="margin-top:6px">You can open the full page instead.</div>
                        <div style="margin-top:12px">
                            <a class="pbx-btn pbx-btn-primary" href="${url}">Open Page</a>
                        </div>
                    </div>
                </div>
            `;
                }
            }

            // -------------------------
            // Click handling (actions + modal)
            // -------------------------
            document.addEventListener('click', (e) => {
                const action = e.target.closest('[data-action]');
                if (action) {
                    const a = action.getAttribute('data-action');
                    if (a === 'toggleFilters') return toggleFilters();
                    if (a === 'applyFilters') return applyFilters();
                    if (a === 'clearFilters') return clearFilters();
                    if (a === 'toggleDense') return document.body.classList.toggle('pbx-dense');
                    if (a === 'exportCsv') return exportCsvVisible();
                    if (a === 'clearSelection') {
                        table.querySelectorAll('.pbx-rowcheck').forEach(cb => cb.checked = false);
                        if (selectAll) selectAll.checked = false;
                        return updateBulk();
                    }
                }

                // close modal
                if (e.target.closest('[data-close="1"]')) return closeModal();

                // open quick view
                const btn = e.target.closest('.pbx-linkbtn[data-qv-url]');
                if (btn) {
                    lastFocus = btn;

                    // subtitle: product + batch no
                    const row = btn.closest('tr[data-row]');
                    const batchNo = row?.querySelector('td:nth-child(3) .pbx-strong')?.textContent
                        ?.trim() || '';
                    const prod = btn.textContent.trim();
                    const subtitle = [prod, batchNo ? `Batch ${batchNo}` : ''].filter(Boolean).join(' • ');

                    return loadModal(btn.dataset.qvUrl, subtitle);
                }
            });

            // ESC closes modal
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.getAttribute('aria-hidden') === 'false') closeModal();
            });

            // -------------------------
            // CSV export (visible rows only)
            // -------------------------
            function exportCsvVisible() {
                const visibleRows = rows.filter(r => r.style.display !== 'none');
                const lines = [];
                lines.push(['Product', 'Batch No', 'Quantity', 'Pricing', 'Expiry', 'Status'].join(','));

                visibleRows.forEach(r => {
                    const tds = r.querySelectorAll('td');
                    const product = (tds[1]?.innerText || '').split('\n')[0].trim().replaceAll(',', ' ');
                    const batchNo = (tds[2]?.innerText || '').split('\n')[0].trim().replaceAll(',', ' ');
                    const qty = (tds[3]?.innerText || '').split('\n')[0].trim().replaceAll(',', ' ');
                    const pricing = (tds[4]?.innerText || '').replaceAll('\n', ' | ').replaceAll(',', ' ');
                    const expiryTxt = (tds[6]?.innerText || '').split('\n')[0].trim().replaceAll(',', ' ');
                    const statusTxt = r.dataset.status || '';

                    lines.push([product, batchNo, qty, pricing, expiryTxt, statusTxt].join(','));
                });

                const blob = new Blob([lines.join('\n')], {
                    type: 'text/csv;charset=utf-8;'
                });
                const a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'product-batches.csv';
                document.body.appendChild(a);
                a.click();
                URL.revokeObjectURL(a.href);
                a.remove();
            }

            // Initial
            applyFilters();
            setSkeleton();
        });
    </script>

    {{-- =========================================================
    CONTROLLER TIP (no new blade file required)
    Make your show() return lightweight html when ajax:
    ------------------------------------------------------
    public function show(ProductBatch $batch) {
        $isExpired = $batch->expiry_date && $batch->expiry_date->lt(now());
        if(request()->ajax()){
            // Return only the inside HTML to render inside modal body
            // (You can reuse same show view but WITHOUT layout if you want.)
            return view('products.product-batches.show', compact('batch','isExpired'));
        }
        return view('products.product-batches.show', compact('batch','isExpired'));
    }
    ------------------------------------------------------
    Best practice: in show.blade.php, wrap @extends with:
      @if (!request()->ajax()) @extends('layouts.app') @endif
      @if (!request()->ajax()) @section('content') ... @endif
    So modal gets ONLY content, page gets full layout.
========================================================= --}}
@endsection
