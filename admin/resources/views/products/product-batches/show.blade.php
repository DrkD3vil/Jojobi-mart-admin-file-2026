{{-- resources/views/products/product-batches/show.blade.php
|--------------------------------------------------------------------------
| ONE FILE that works for BOTH:
| 1) Normal full page view (extends layouts.app)
| 2) AJAX modal quick-view (returns only inner HTML)
|
| HOW IT WORKS:
| - Your table calls: fetch(route('product.batches.show', $batch)) with X-Requested-With header
| - This blade checks request()->ajax()
|   - If AJAX: returns a compact "modal body" block (lightweight)
|   - Else: returns full page UI
|--------------------------------------------------------------------------
--}}

@if (!request()->ajax())
    @extends('layouts.app')
    @section('content')
    @endif

    @php
        // ---------------------------------------------------------
        // FIX: $isExpired was used but not defined in your blade.
        // ---------------------------------------------------------
        $isExpired = $batch->expiry_date ? $batch->expiry_date->lt(now()) : false;

        // "soon" helps show status
        $daysUntilExpiry = $batch->expiry_date ? now()->diffInDays($batch->expiry_date, false) : null;
        $isSoon = is_int($daysUntilExpiry) && $daysUntilExpiry > 0 && $daysUntilExpiry <= 30;

        // Build some safe strings
        $productName = $batch->product->name ?? 'N/A';
        $categoryName = $batch->product->category->name ?? 'N/A';
        $brandName = $batch->product->brand->name ?? 'N/A';
    @endphp

    <style>
        /* ------------------------------------------------------------------
       NOTE:
       - Keep this CSS in one file as requested.
       - For AJAX modal, the CSS also applies inside modal content.
       - Avoid too heavy animations for fast load: remove parallax on scroll.
        ------------------------------------------------------------------- */

        /* Enhanced styles for the batch details page */
        :root {
            --animation-duration: 0.45s;
            --stagger-delay: 0.05s;
        }

        .batch-details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.75rem 1rem;
            animation: fadeInUp var(--transition-normal) ease-out;
        }

        /* When loaded in modal, reduce padding */
        .pbx-modal .batch-details-container,
        [data-modal-view="1"] .batch-details-container {
            padding: 0;
            max-width: 100%;
        }

        .batch-details-header {
            font-size: 2.15rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2.25rem;
            text-align: center;
            position: relative;
            padding-bottom: 0.9rem;
            animation: slideInFromTop var(--transition-normal) ease-out 0.15s both;
        }

        .batch-details-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 140px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent-color));
            border-radius: var(--radius);
            animation: widthGrow var(--transition-slow) ease-out 0.25s both;
        }

        /* Compact header for modal view */
        [data-modal-view="1"] .batch-details-header {
            font-size: 1.35rem;
            margin: 0 0 1rem;
            padding-bottom: .65rem;
            text-align: left;
        }

        [data-modal-view="1"] .batch-details-header::after {
            left: 0;
            transform: none;
            width: 90px;
        }

        .batch-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .batch-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: calc(var(--radius) + 6px);
            box-shadow: var(--card-shadow);
            transition: transform var(--transition-normal) ease, box-shadow var(--transition-normal) ease, border-color var(--transition-normal) ease;
            overflow: hidden;
            opacity: 0;
            transform: translateY(14px);
            animation: fadeInUp var(--animation-duration) ease-out forwards;
        }

        /* stagger */
        .batch-card:nth-child(1) {
            animation-delay: 0.06s;
        }

        .batch-card:nth-child(2) {
            animation-delay: 0.12s;
        }

        .batch-card:nth-child(3) {
            animation-delay: 0.18s;
        }

        .batch-card:nth-child(4) {
            animation-delay: 0.24s;
        }

        .batch-card:nth-child(5) {
            animation-delay: 0.30s;
        }

        .batch-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-4px);
            border-color: color-mix(in oklch, var(--accent-color) 65%, var(--border));
        }

        .batch-card-header {
            background: linear-gradient(135deg, var(--sidebar-accent), var(--bg-tertiary));
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .batch-card-header h3 {
            font-size: 1.05rem;
            font-weight: 850;
            color: var(--sidebar-foreground);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .batch-card-header h3 svg {
            flex-shrink: 0;
        }

        .batch-card-body {
            padding: 1.25rem;
        }

        .batch-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .batch-table tr {
            transition: background-color var(--transition-fast) ease;
        }

        .batch-table tr:hover {
            background-color: var(--sidebar-accent);
        }

        .batch-table th {
            font-weight: 750;
            color: var(--text-secondary);
            padding: 0.9rem 0.4rem;
            border-bottom: 1px solid var(--border);
            width: 42%;
            vertical-align: top;
            text-transform: uppercase;
            font-size: 0.82rem;
            letter-spacing: 0.6px;
        }

        .batch-table td {
            padding: 0.9rem 0.4rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
            font-weight: 600;
            font-size: 0.98rem;
        }

        .batch-table tr:last-child th,
        .batch-table tr:last-child td {
            border-bottom: none;
        }

        .price-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.35rem 0.7rem;
            background: linear-gradient(135deg, var(--success) 15%, var(--chart-2) 100%);
            color: white;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.92rem;
            box-shadow: 0 10px 25px -18px color-mix(in oklch, var(--success) 70%, black);
        }

        .price-comparison {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .original-price {
            text-decoration: line-through;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .discount-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.65rem;
            background: linear-gradient(135deg, var(--warning) 15%, var(--chart-3) 100%);
            color: white;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 850;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.85rem;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 850;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: transform var(--transition-fast) ease, box-shadow var(--transition-fast) ease;
        }

        .status-badge:hover {
            transform: translateY(-1px);
        }

        .status-badge.valid {
            background: linear-gradient(135deg, var(--success) 15%, var(--chart-2) 100%);
            color: white;
            box-shadow: 0 12px 30px -22px color-mix(in oklch, var(--success) 70%, black);
        }

        .status-badge.expired {
            background: linear-gradient(135deg, var(--danger) 15%, var(--chart-5) 100%);
            color: white;
        }

        .status-badge.pending {
            background: linear-gradient(135deg, var(--warning) 15%, var(--chart-3) 100%);
            color: white;
        }

        .availability-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }

        .availability-badge {
            padding: 0.22rem 0.7rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 750;
            border: 1px solid var(--border);
            background: color-mix(in oklch, var(--glass-base) 70%, transparent);
        }

        .availability-badge.active {
            background: var(--success);
            color: white;
            border-color: var(--success);
        }

        .availability-badge.inactive {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }

        .notes-container {
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: calc(var(--radius) + 6px);
            padding: 1.15rem;
            margin-top: 1.25rem;
            animation: fadeInUp var(--animation-duration) ease-out 0.18s both;
        }

        .notes-container h4 {
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 900;
        }

        .notes-content {
            color: var(--text-secondary);
            line-height: 1.6;
            white-space: pre-wrap;
            font-weight: 600;
        }

        .batch-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.25rem;
            flex-wrap: wrap;
        }

        .batch-btn {
            padding: 0.75rem 1.1rem;
            border-radius: calc(var(--radius) + 10px);
            font-weight: 850;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: 1px solid var(--border);
            transition: transform var(--transition-fast) ease, box-shadow var(--transition-fast) ease, filter var(--transition-fast) ease;
            user-select: none;
        }

        .batch-back-btn {
            background: color-mix(in oklch, var(--secondary) 70%, transparent);
            color: var(--secondary-foreground);
        }

        .batch-back-btn:hover {
            transform: translateX(-3px);
            box-shadow: var(--card-shadow-hover);
        }

        .batch-edit-btn {
            background: linear-gradient(135deg, var(--info) 15%, var(--chart-1) 100%);
            color: white;
            border-color: transparent;
        }

        .batch-edit-btn:hover {
            transform: translateY(-2px);
            filter: brightness(1.03);
            box-shadow: 0 16px 35px -26px color-mix(in oklch, var(--info) 70%, black);
        }

        /* Modal view: hide big buttons (optional) */
        [data-modal-view="1"] .batch-actions {
            display: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .batch-details-container {
                padding: 1rem 0.5rem;
            }

            .batch-details-header {
                font-size: 1.65rem;
                margin-bottom: 1.5rem;
            }

            .batch-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .batch-card-body {
                padding: 1rem;
            }

            .batch-table th,
            .batch-table td {
                display: block;
                width: 100%;
                padding: 0.65rem 0;
            }

            .batch-table th {
                font-weight: 900;
                color: var(--text-primary);
                margin-top: 0.85rem;
                border-bottom: none;
                padding-bottom: 0.15rem;
            }

            .batch-table td {
                padding-left: 0.75rem;
                border-bottom: 1px solid var(--border);
            }

            .batch-table tr {
                display: block;
                border: 1px solid var(--border);
                border-radius: calc(var(--radius) + 6px);
                margin-bottom: 0.85rem;
                padding: 0.85rem;
                background: var(--bg-secondary);
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(14px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes widthGrow {
            from {
                width: 0;
            }

            to {
                width: 140px;
            }
        }
    </style>

    {{-- ------------------------------------------------------------------
    MODAL VIEW WRAPPER
    - When AJAX, wrap the content in a simple container so your modal
      can style it (and we can hide actions etc).
------------------------------------------------------------------- --}}
    <div @if (request()->ajax()) data-modal-view="1" @endif>
        <div class="batch-details-container">
            <h1 class="batch-details-header">
                <svg width="22" height="22" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                    <path
                        d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z" />
                    <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5V4z" />
                </svg>
                Batch Details
            </h1>

            <div class="batch-grid">
                {{-- BASIC --}}
                <div class="batch-card">
                    <div class="batch-card-header">
                        <h3>
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path
                                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm1-11a1 1 0 0 0-2 0v3a1 1 0 0 0 .5.866l2 1.154a1 1 0 0 0 1-1.732L9 8.616V5z" />
                            </svg>
                            Basic Information
                        </h3>
                    </div>
                    <div class="batch-card-body">
                        <table class="batch-table">
                            <tr>
                                <th>Product</th>
                                <td>{{ $productName }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $categoryName }}</td>
                            </tr>
                            <tr>
                                <th>Brand</th>
                                <td>{{ $brandName }}</td>
                            </tr>
                            <tr>
                                <th>Batch SKU</th>
                                <td>{{ $batch->batch_sku ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Batch Number</th>
                                <td>{{ $batch->batch_no ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td>{{ $batch->unit ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- PRICING --}}
                <div class="batch-card">
                    <div class="batch-card-header">
                        <h3>
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5h-2v12h2V2z" />
                                <path
                                    d="M12 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zM1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z" />
                            </svg>
                            Pricing Information
                        </h3>
                    </div>
                    <div class="batch-card-body">
                        <table class="batch-table">
                            <tr>
                                <th>Buy Price</th>
                                <td><span class="price-tag">${{ number_format($batch->buy_price, 2) }}</span></td>
                            </tr>
                            <tr>
                                <th>Sell Price</th>
                                <td>
                                    <div class="price-comparison">
                                        <span class="price-tag">${{ number_format($batch->original_sell_price, 2) }}</span>
                                        @if ($batch->discounted_price)
                                            <span
                                                class="original-price">${{ number_format($batch->original_sell_price, 2) }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            @if ($batch->discounted_price)
                                <tr>
                                    <th>Discounted</th>
                                    <td>
                                        <div class="price-comparison">
                                            <span class="price-tag"
                                                style="background: linear-gradient(135deg, var(--warning) 15%, var(--chart-3) 100%);">
                                                ${{ number_format($batch->discounted_price, 2) }}
                                            </span>
                                            @if ($batch->discount_percentage)
                                                <span
                                                    class="discount-badge">{{ number_format($batch->discount_percentage, 1) }}%
                                                    OFF</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            @if ($batch->whole_sell_price)
                                <tr>
                                    <th>Wholesale</th>
                                    <td>
                                        <span class="price-tag"
                                            style="background: linear-gradient(135deg, var(--info) 15%, var(--chart-1) 100%);">
                                            ${{ number_format($batch->whole_sell_price, 2) }}
                                        </span>
                                        <small class="text-muted d-block mt-1">
                                            (Min: {{ $batch->whole_sell_min_qty ?? 'Any' }} | Max:
                                            {{ $batch->whole_sell_max_qty ?? 'Any' }})
                                        </small>
                                    </td>
                                </tr>
                            @endif

                            @if ($batch->customer_whole_price)
                                <tr>
                                    <th>Customer Wholesale</th>
                                    <td>
                                        <span class="price-tag"
                                            style="background: linear-gradient(135deg, var(--accent-color) 15%, var(--chart-4) 100%);">
                                            ${{ number_format($batch->customer_whole_price, 2) }}
                                        </span>
                                        <small class="text-muted d-block mt-1">
                                            (Min: {{ $batch->customer_whole_min_qty ?? 'Any' }} | Max:
                                            {{ $batch->customer_whole_max_qty ?? 'Any' }})
                                        </small>
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- STOCK --}}
                <div class="batch-card">
                    <div class="batch-card-header">
                        <h3>
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path
                                    d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5v-2z" />
                            </svg>
                            Stock & Quantity
                        </h3>
                    </div>
                    <div class="batch-card-body">
                        <table class="batch-table">
                            <tr>
                                <th>Quantity</th>
                                <td style="font-size: 1.15rem; font-weight: 900;">
                                    {{ number_format($batch->quantity, 2) }} {{ $batch->unit ?? 'units' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Manufacture</th>
                                <td>{{ $batch->manufacture_date?->format('d M Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Expiry</th>
                                <td>
                                    {{ $batch->expiry_date?->format('d M Y') ?? 'N/A' }}
                                    @if ($batch->expiry_date)
                                        <small
                                            class="d-block mt-1
                                        @if ($daysUntilExpiry < 0) text-danger
                                        @elseif($daysUntilExpiry <= 30) text-warning
                                        @else text-success @endif
                                    ">
                                            @if ($daysUntilExpiry > 0)
                                                {{ $daysUntilExpiry }} days remaining
                                            @elseif($daysUntilExpiry === 0)
                                                Expires today!
                                            @else
                                                Expired {{ abs($daysUntilExpiry) }} days ago
                                            @endif
                                        </small>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if ($isExpired)
                                        <span class="status-badge expired">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"
                                                aria-hidden="true">
                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14z" />
                                                <path
                                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                                            </svg>
                                            Expired
                                        </span>
                                    @elseif($isSoon)
                                        <span class="status-badge pending">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"
                                                aria-hidden="true">
                                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                                <path
                                                    d="M8 4a.5.5 0 0 1 .5.5v3.2l2.1 1.2a.5.5 0 1 1-.5.86l-2.35-1.35A.5.5 0 0 1 7.5 9V4.5A.5.5 0 0 1 8 4z" />
                                            </svg>
                                            Expiring Soon
                                        </span>
                                    @else
                                        <span class="status-badge valid">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"
                                                aria-hidden="true">
                                                <path
                                                    d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                                            </svg>
                                            Valid
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {{-- AVAILABILITY --}}
                <div class="batch-card">
                    <div class="batch-card-header">
                        <h3>
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path
                                    d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34z" />
                            </svg>
                            Availability & Settings
                        </h3>
                    </div>
                    <div class="batch-card-body">
                        <table class="batch-table">
                            <tr>
                                <th>Active</th>
                                <td>
                                    @if ($batch->is_active)
                                        <span class="status-badge valid">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"
                                                aria-hidden="true">
                                                <path
                                                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                            </svg>
                                            Active
                                        </span>
                                    @else
                                        <span class="status-badge expired">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"
                                                aria-hidden="true">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0z" />
                                                <path
                                                    d="M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                                            </svg>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Channels</th>
                                <td>
                                    <div class="availability-badges">
                                        <span
                                            class="availability-badge {{ $batch->is_online ? 'active' : 'inactive' }}">Online</span>
                                        <span
                                            class="availability-badge {{ $batch->is_offline ? 'active' : 'inactive' }}">Offline</span>
                                        <span
                                            class="availability-badge {{ $batch->is_pos ? 'active' : 'inactive' }}">POS</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Created</th>
                                <td>{{ $batch->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Updated</th>
                                <td>{{ $batch->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- NOTES --}}
            @if ($batch->notes)
                <div class="notes-container">
                    <h4>
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                            <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12z" />
                            <path
                                d="M4.5 5.5A.5.5 0 0 1 5 5h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0 3A.5.5 0 0 1 5 8h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zm0 3A.5.5 0 0 1 5 11h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5z" />
                        </svg>
                        Additional Notes
                    </h4>
                    <div class="notes-content">{{ $batch->notes }}</div>
                </div>
            @endif

            {{-- ACTIONS (only show on full page, not modal) --}}
            @if (!request()->ajax())
                <div class="batch-actions">
                    <a href="{{ route('product.batches.by-product', $batch->product) }}"
                        class="batch-btn batch-back-btn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                        </svg>
                        Back to Product Batches
                    </a>

                    <a href="{{ route('product.batches.edit', $batch) }}" class="batch-btn batch-edit-btn">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                            <path
                                d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10z" />
                        </svg>
                        Edit Batch
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- ------------------------------------------------------------------
    JS: lightweight + safe
    - Removes heavy scroll parallax (that was causing jitter/bugs)
    - Uses CSS transitions instead of mutating transforms repeatedly
    - Works in both page and modal
------------------------------------------------------------------- --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Guard: if view is injected multiple times into modal, avoid duplicate listeners
            if (window.__pbxShowInitDone) return;
            window.__pbxShowInitDone = true;

            // Micro interactions only (no scroll parallax = faster, fewer bugs)
            const cards = document.querySelectorAll('.batch-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => card.classList.add('is-hover'));
                card.addEventListener('mouseleave', () => card.classList.remove('is-hover'));
            });

            const priceTags = document.querySelectorAll('.price-tag');
            priceTags.forEach(tag => {
                tag.addEventListener('mouseenter', () => tag.style.transform = 'scale(1.04)');
                tag.addEventListener('mouseleave', () => tag.style.transform = 'scale(1)');
            });

            // Prevent anchor clicks inside modal from navigating whole page unintentionally
            // (optional: remove if you want normal navigation)
            document.querySelectorAll('[data-modal-view="1"] a').forEach(a => {
                a.addEventListener('click', (e) => {
                    // let "Edit" / "Back" be hidden in modal anyway, but just in case:
                    e.preventDefault();
                    window.open(a.href, '_self');
                });
            });
        });
    </script>



    @if (!request()->ajax())
    @endsection
@endif
