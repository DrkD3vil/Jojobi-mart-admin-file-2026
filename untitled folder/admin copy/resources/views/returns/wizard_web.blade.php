@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =========================================================
       IMPORTANT: Your color variables are FIXED (keep as-is)
       (Paste your existing :root + html[data-theme='light'] here)
       ========================================================= */
        /* ... keep your :root + html[data-theme='light'] exactly ... */

        /* =========================================================
       Return Wizard (RWZ) - UNIQUE, SCOPED STYLES
       Prefix: rwz-
       ========================================================= */

        .rwz-wrap {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: var(--background);
            color: var(--foreground);
            line-height: 1.6;
            min-height: 100vh;
            padding: 20px;
            transition: background-color var(--transition-normal), color var(--transition-normal);
        }

        .rwz-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 16px;
        }

        .rwz-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .rwz-title {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--accent-color), var(--sidebar-primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 6px;
        }

        .rwz-subtitle {
            color: var(--text-secondary);
            font-size: .95rem;
        }

        .rwz-theme-toggle {
            position: relative;
            width: 56px;
            height: 28px;
            border-radius: 14px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: all var(--transition-normal);
            overflow: hidden;
        }

        .rwz-theme-toggle:hover {
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .rwz-theme-toggle::before {
            content: '';
            position: absolute;
            top: 2px;
            left: 2px;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--accent-color);
            transition: transform var(--transition-normal);
        }

        html[data-theme='light'] .rwz-theme-toggle::before {
            transform: translateX(28px);
            background: var(--sidebar-primary);
        }

        .rwz-theme-icon {
            position: absolute;
            top: 6px;
            font-size: 12px;
            color: var(--foreground);
        }

        .rwz-theme-icon--sun {
            left: 6px;
        }

        .rwz-theme-icon--moon {
            right: 6px;
        }

        .rwz-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            position: relative;
        }

        .rwz-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: var(--border-color);
            z-index: 1;
        }

        .rwz-step {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
        }

        .rwz-step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--bg-tertiary);
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            color: var(--text-secondary);
            transition: all var(--transition-normal);
        }

        .rwz-step.is-active .rwz-step-circle {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: #fff;
            box-shadow: 0 0 0 4px var(--accent-glow);
        }

        .rwz-step.is-done .rwz-step-circle {
            background: var(--success);
            border-color: var(--success);
            color: #fff;
        }

        .rwz-step.is-done .rwz-step-circle::after {
            content: '✓';
        }

        .rwz-step-label {
            font-size: .85rem;
            color: var(--text-secondary);
            text-align: center;
        }

        .rwz-step.is-active .rwz-step-label {
            color: var(--foreground);
            font-weight: 500;
        }

        .rwz-card {
            background: var(--card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--card-shadow);
            transition: box-shadow var(--transition-normal), transform var(--transition-normal);
            animation: rwzFadeUp var(--transition-slow) ease-out;
        }

        .rwz-card:hover {
            box-shadow: var(--card-shadow-hover);
            transform: translateY(-2px);
        }

        .rwz-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .rwz-card-title {
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .rwz-card-title i {
            color: var(--accent-color);
        }

        .rwz-card-note {
            color: var(--text-secondary);
            font-size: .9rem;
            margin-top: 4px;
        }

        .rwz-grid {
            display: grid;
            gap: 20px;
        }

        .rwz-grid--2 {
            grid-template-columns: repeat(2, 1fr);
        }

        @media(max-width:768px) {
            .rwz-grid--2 {
                grid-template-columns: 1fr;
            }
        }

        .rwz-field {
            margin-bottom: 20px;
        }

        .rwz-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: .95rem;
        }

        .rwz-input,
        .rwz-select {
            width: 100%;
            padding: 12px 16px;
            background: var(--input);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            color: var(--foreground);
            font-size: 1rem;
            transition: all var(--transition-fast);
        }

        .rwz-input:focus,
        .rwz-select:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .rwz-select {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .rwz-btn {
            padding: 12px 24px;
            border-radius: var(--radius);
            font-weight: 500;
            font-size: .95rem;
            cursor: pointer;
            border: none;
            transition: all var(--transition-fast);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .rwz-btn--primary {
            background: var(--accent-color);
            color: var(--sidebar-primary-foreground);
        }

        .rwz-btn--primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        .rwz-btn--ghost {
            background: var(--bg-tertiary);
            color: var(--foreground);
            border: 1px solid var(--border-color);
        }

        .rwz-btn--ghost:hover {
            background: var(--accent);
            border-color: var(--accent-color);
        }

        .rwz-btn--sm {
            padding: 8px 16px;
            font-size: .85rem;
        }

        .rwz-btn[disabled] {
            opacity: .6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .rwz-alert {
            padding: 16px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            animation: rwzSlideDown var(--transition-normal) ease-out;
            color: #fff;
        }

        .rwz-alert i {
            font-size: 1.2rem;
            margin-top: 2px;
        }

        .rwz-alert--ok {
            background: var(--success);
            border-left: 4px solid oklch(0.696 0.17 162.48 / 0.8);
        }

        .rwz-alert--err {
            background: var(--danger);
            border-left: 4px solid oklch(0.704 0.191 22.216 / 0.8);
        }

        .rwz-alert--warn {
            background: var(--warning);
            border-left: 4px solid oklch(0.769 0.188 70.08 / 0.8);
        }

        .rwz-alert ul {
            margin-top: 8px;
            padding-left: 20px;
        }

        .rwz-tabs {
            display: flex;
            gap: 8px;
            background: var(--bg-tertiary);
            padding: 4px;
            border-radius: var(--radius);
            width: fit-content;
            margin-bottom: 16px;
        }

        .rwz-tab {
            padding: 12px 24px;
            border-radius: calc(var(--radius) - 2px);
            cursor: pointer;
            font-weight: 500;
            display: flex;
            gap: 8px;
            align-items: center;
            transition: all var(--transition-fast);
            border: none;
            background: transparent;
            color: var(--text-secondary);
        }

        .rwz-tab:hover {
            color: var(--foreground);
            background: var(--accent);
        }

        .rwz-tab.is-active {
            background: var(--card);
            color: var(--foreground);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .rwz-panel {
            display: none;
            animation: rwzFade var(--transition-normal) ease-out;
        }

        .rwz-panel.is-active {
            display: block;
        }

        .rwz-info {
            background: var(--bg-tertiary);
            border-radius: var(--radius);
            padding: 16px;
            display: flex;
            gap: 16px;
            align-items: center;
            margin-top: 16px;
        }

        .rwz-info-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--accent-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .rwz-tableWrap {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            margin-top: 16px;
        }

        .rwz-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .rwz-table thead {
            background: var(--bg-tertiary);
        }

        .rwz-table th,
        .rwz-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            text-align: left;
            vertical-align: middle;
            white-space: nowrap;
        }

        .rwz-table tbody tr:hover {
            background: var(--accent);
        }

        .rwz-table tbody tr.is-selected {
            background: var(--accent);
            border-left: 3px solid var(--accent-color);
        }

        .rwz-check {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 2px solid var(--border-color);
            background: var(--input);
            cursor: pointer;
            position: relative;
            transition: all var(--transition-fast);
            margin: 0 auto;
        }

        .rwz-check:hover {
            border-color: var(--accent-color);
        }

        .rwz-check.is-on {
            background: var(--accent-color);
            border-color: var(--accent-color);
        }

        .rwz-check.is-on::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
        }

        .rwz-qty {
            width: 110px;
            padding: 8px 12px;
            border-radius: var(--radius);
            border: 1px solid var(--border-color);
            background: var(--input);
            color: var(--foreground);
            text-align: center;
        }

        .rwz-qty:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .rwz-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: .8rem;
            font-weight: 500;
            min-width: 60px;
            text-align: center;
            background: var(--info);
            color: #fff;
        }

        .rwz-empty {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
        }

        .rwz-empty i {
            font-size: 3rem;
            margin-bottom: 16px;
            color: var(--border-color);
        }

        .rwz-preview {
            background: var(--bg-tertiary);
            border-radius: var(--radius);
            padding: 20px;
            margin-top: 16px;
            min-height: 120px;
            border: 1px dashed var(--border-color);
        }

        .rwz-previewItem {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background: var(--card);
            border-radius: calc(var(--radius) - 2px);
            margin-bottom: 8px;
            border-left: 3px solid var(--accent-color);
        }

        .rwz-previewItem:last-child {
            margin-bottom: 0;
        }

        .rwz-previewName {
            font-weight: 500;
            margin-bottom: 4px;
        }

        .rwz-previewMeta {
            font-size: .85rem;
            color: var(--text-secondary);
        }

        .rwz-previewQty {
            font-weight: 700;
            color: var(--accent-color);
            font-size: 1.1rem;
        }

        .rwz-config {
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 16px;
            margin-top: 16px;
        }

        .rwz-config h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .rwz-configGrid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media(max-width:768px) {
            .rwz-configGrid {
                grid-template-columns: 1fr;
            }
        }

        .rwz-configBox {
            padding: 12px;
            background: var(--card);
            border-radius: calc(var(--radius) - 2px);
        }

        .rwz-configLabel {
            font-size: .85rem;
            color: var(--text-secondary);
            margin-bottom: 4px;
        }

        .rwz-configVal {
            font-weight: 600;
        }

        .rwz-configVal.is-empty {
            color: var(--text-muted);
            font-style: italic;
        }

        .rwz-hidden {
            display: none !important;
        }

        .rwz-center {
            text-align: center;
        }

        .rwz-mt16 {
            margin-top: 16px;
        }

        .rwz-mt24 {
            margin-top: 24px;
        }

        .rwz-mt32 {
            margin-top: 32px;
        }

        .rwz-mb16 {
            margin-bottom: 16px;
        }

        .rwz-mb24 {
            margin-bottom: 24px;
        }

        .rwz-dd {
            position: relative;
        }

        .rwz-ddbox {
            position: absolute;
            top: 56px;
            left: 0;
            right: 0;
            z-index: 999;
            background: var(--card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: var(--dropdown-shadow);
            padding: 10px;
            max-height: 360px;
            overflow: auto;
        }

        .rwz-dditem {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-radius: calc(var(--radius) - 2px);
            cursor: pointer;
        }

        .rwz-dditem:hover {
            background: var(--accent);
        }

        .rwz-ddleft h5 {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .rwz-ddleft p {
            font-size: .85rem;
            color: var(--text-secondary);
        }

        .rwz-ddright {
            color: var(--accent-color);
            font-weight: 700;
        }

        @keyframes rwzFade {
            from {
                opacity: 0
            }

            to {
                opacity: 1
            }
        }

        @keyframes rwzFadeUp {
            from {
                opacity: 0;
                transform: translateY(20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @keyframes rwzSlideDown {
            from {
                opacity: 0;
                transform: translateY(-20px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        @media(max-width:768px) {
            .rwz-wrap {
                padding: 12px;
            }

            .rwz-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .rwz-steps {
                flex-wrap: wrap;
                gap: 16px;
            }

            .rwz-steps::before {
                display: none;
            }

            .rwz-step {
                flex: none;
                width: calc(50% - 8px);
            }
        }

        @media(max-width:480px) {
            .rwz-step {
                width: 100%;
            }

            .rwz-btn {
                width: 100%;
            }
        }
    </style>

    <div class="rwz-wrap" id="rwzRoot">
        <div class="rwz-container">

            {{-- Header --}}
            <div class="rwz-header">
                <div>
                    <div class="rwz-title"><i class="fas fa-magic"></i> Return Wizard</div>
                    <div class="rwz-subtitle">Fast return processing with order-based or customer-based search</div>
                </div>
                <div class="rwz-theme-toggle" id="rwzThemeToggle" title="Toggle theme">
                    <div class="rwz-theme-icon rwz-theme-icon--sun"><i class="fas fa-sun"></i></div>
                    <div class="rwz-theme-icon rwz-theme-icon--moon"><i class="fas fa-moon"></i></div>
                </div>
            </div>

            {{-- Steps --}}
            <div class="rwz-steps" id="rwzSteps">
                <div class="rwz-step is-active" data-step="1">
                    <div class="rwz-step-circle">1</div>
                    <div class="rwz-step-label">Configuration</div>
                </div>
                <div class="rwz-step" data-step="2">
                    <div class="rwz-step-circle">2</div>
                    <div class="rwz-step-label">Search Method</div>
                </div>
                <div class="rwz-step" data-step="3">
                    <div class="rwz-step-circle">3</div>
                    <div class="rwz-step-label">Select Items</div>
                </div>
                <div class="rwz-step" data-step="4">
                    <div class="rwz-step-circle">4</div>
                    <div class="rwz-step-label">Submit Return</div>
                </div>
            </div>

            {{-- Messages --}}
            @if (session('ok'))
                <div class="rwz-alert rwz-alert--ok"><i class="fas fa-check-circle"></i>
                    <div>{{ session('ok') }}</div>
                </div>
            @endif
            @if (session('err'))
                <div class="rwz-alert rwz-alert--err"><i class="fas fa-exclamation-circle"></i>
                    <div>{{ session('err') }}</div>
                </div>
            @endif
            @if ($errors->any())
                <div class="rwz-alert rwz-alert--err">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <b>Please fix the following errors:</b>
                        <ul>
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- =========================
        STEP 1
       ========================= --}}
            <div class="rwz-card" id="rwzStep1">
                <div class="rwz-card-head">
                    <div class="rwz-card-title"><i class="fas fa-cog"></i> Return Configuration</div>
                </div>

                <div class="rwz-grid rwz-grid--2">
                    <div class="rwz-field">
                        <label class="rwz-label">Return Location (Stock IN)</label>
                        <select id="rwzLocation" class="rwz-select" required>
                            <option value="">Select return location</option>
                            @foreach ($locations as $l)
                                <option value="{{ $l->id }}">{{ $l->name }}</option>
                            @endforeach
                        </select>
                        <div class="rwz-card-note">Where returned items will be stored</div>
                    </div>

                    <div class="rwz-field">
                        <label class="rwz-label">Refund Method</label>
                        <select id="rwzRefund" class="rwz-select">
                            <option value="">Select refund method (optional)</option>
                            <option value="cash">Cash</option>
                            <option value="bkash">bKash</option>
                            <option value="card">Credit/Debit Card</option>
                            <option value="wallet">Wallet</option>
                            <option value="adjust_customer_balance">Adjust Customer Balance</option>
                        </select>
                        <div class="rwz-card-note">How the customer will receive their refund</div>
                    </div>
                </div>

                <div class="rwz-config">
                    <h4><i class="fas fa-info-circle"></i> Configuration Summary</h4>
                    <div class="rwz-configGrid">
                        <div class="rwz-configBox">
                            <div class="rwz-configLabel">Location</div>
                            <div class="rwz-configVal is-empty" id="rwzSumLoc">Not selected</div>
                        </div>
                        <div class="rwz-configBox">
                            <div class="rwz-configLabel">Refund Method</div>
                            <div class="rwz-configVal is-empty" id="rwzSumRefund">Not selected</div>
                        </div>
                    </div>
                </div>

                <div class="rwz-center rwz-mt24">
                    <button class="rwz-btn rwz-btn--primary" id="rwzNext1">Continue to Search <i
                            class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            {{-- =========================
        STEP 2
       ========================= --}}
            <div class="rwz-card rwz-hidden" id="rwzStep2">
                <div class="rwz-card-head">
                    <div class="rwz-card-title"><i class="fas fa-search"></i> Search Method</div>
                    <button class="rwz-btn rwz-btn--ghost rwz-btn--sm" id="rwzBack2"><i class="fas fa-arrow-left"></i>
                        Back</button>
                </div>

                <div class="rwz-tabs" id="rwzTabs">
                    <button class="rwz-tab is-active" type="button" data-tab="order"><i class="fas fa-receipt"></i>
                        Order / Phone</button>
                    <button class="rwz-tab" type="button" data-tab="customer"><i class="fas fa-user"></i>
                        Customer</button>
                </div>

                {{-- Order Search Panel --}}
                <div class="rwz-panel is-active" id="rwzPanelOrder">
                    <h3 class="rwz-mb16"><i class="fas fa-receipt"></i> Search Orders</h3>
                    <div class="rwz-card-note rwz-mb16">Type order number OR customer phone/name. Click a result to load
                        items.</div>

                    <div class="rwz-grid rwz-grid--2">
                        <div class="rwz-field rwz-dd">
                            <input id="rwzOrderSearch" class="rwz-input"
                                placeholder="ORD-2026-0001 or 017xxxxxxxx or Customer name">
                            <div id="rwzOrderResults" class="rwz-ddbox rwz-hidden"></div>
                        </div>

                        <div class="rwz-field">
                            <button class="rwz-btn rwz-btn--ghost" type="button" id="rwzOrderClear"><i
                                    class="fas fa-eraser"></i> Clear</button>
                        </div>
                    </div>

                    <div id="rwzOrderInfo" class="rwz-hidden"></div>
                </div>

                {{-- Customer Search Panel --}}
                <div class="rwz-panel" id="rwzPanelCustomer">
                    <h3 class="rwz-mb16"><i class="fas fa-user"></i> Search Customers</h3>
                    <div class="rwz-card-note rwz-mb16">Type customer name or phone. Click a customer to auto-search orders
                        by phone.</div>

                    <div class="rwz-grid rwz-grid--2">
                        <div class="rwz-field rwz-dd">
                            <input id="rwzCustomerSearch" class="rwz-input" placeholder="Customer name or phone">
                            <div id="rwzCustomerResults" class="rwz-ddbox rwz-hidden"></div>
                        </div>

                        <div class="rwz-field">
                            <button class="rwz-btn rwz-btn--ghost" type="button" id="rwzCustomerClear"><i
                                    class="fas fa-eraser"></i> Clear</button>
                        </div>
                    </div>
                </div>

                <div class="rwz-center rwz-mt24">
                    <button class="rwz-btn rwz-btn--primary rwz-hidden" id="rwzNext2">Continue to Items <i
                            class="fas fa-arrow-right"></i></button>
                </div>
            </div>

            {{-- =========================
        STEP 3
       ========================= --}}
            <div class="rwz-card rwz-hidden" id="rwzStep3">
                <div class="rwz-card-head">
                    <div class="rwz-card-title"><i class="fas fa-boxes"></i> Select Items to Return</div>
                    <button class="rwz-btn rwz-btn--ghost rwz-btn--sm" id="rwzBack3"><i class="fas fa-arrow-left"></i>
                        Back</button>
                </div>

                <div id="rwzNoOrder" class="rwz-empty">
                    <i class="fas fa-shopping-basket"></i>
                    <h3>No Order Loaded</h3>
                    <p>Search by order number or customer first to load items.</p>
                </div>

                <div id="rwzItemsBlock" class="rwz-hidden">
                    <div class="rwz-card-note rwz-mb16">Select items and set return quantity. Maximum returnable quantity
                        is shown.</div>

                    <div class="rwz-tableWrap">
                        <table class="rwz-table" id="rwzItemsTable">
                            <thead>
                                <tr>
                                    <th style="width:60px;">Select</th>
                                    <th>Product Details</th>
                                    <th style="width:80px;">Sold</th>
                                    <th style="width:100px;">Returned</th>
                                    <th style="width:120px;">Returnable</th>
                                    <th style="width:150px;">Return Qty</th>
                                </tr>
                            </thead>
                            <tbody id="rwzItemsTbody">
                                {{-- filled by JS --}}
                            </tbody>
                        </table>
                    </div>

                    <div class="rwz-center rwz-mt24">
                        <button class="rwz-btn rwz-btn--primary" id="rwzNext3">Review Selection <i
                                class="fas fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>

            {{-- =========================
        STEP 4
       ========================= --}}
            <div class="rwz-card rwz-hidden" id="rwzStep4">
                <div class="rwz-card-head">
                    <div class="rwz-card-title"><i class="fas fa-paper-plane"></i> Submit Return</div>
                    <button class="rwz-btn rwz-btn--ghost rwz-btn--sm" id="rwzBack4"><i class="fas fa-arrow-left"></i>
                        Back</button>
                </div>

                <form id="rwzReturnForm" method="POST" action="{{ route('returns.store') }}">
                    @csrf

                    <h3 class="rwz-mb16">Selected Items</h3>

                    <div id="rwzPreview" class="rwz-preview">
                        <div class="rwz-empty" style="padding: 10px 0;">
                            <i class="fas fa-clipboard-list"></i>
                            <h3>No Items Selected</h3>
                            <p>Select items from the order to return.</p>
                        </div>
                    </div>

                    <div class="rwz-config rwz-mt24">
                        <h4><i class="fas fa-info-circle"></i> Configuration Summary</h4>
                        <div class="rwz-configGrid">
                            <div class="rwz-configBox">
                                <div class="rwz-configLabel">Location</div>
                                <div class="rwz-configVal" id="rwzFinalLoc">-</div>
                            </div>
                            <div class="rwz-configBox">
                                <div class="rwz-configLabel">Refund Method</div>
                                <div class="rwz-configVal" id="rwzFinalRefund">-</div>
                            </div>
                            <div class="rwz-configBox">
                                <div class="rwz-configLabel">Note</div>
                                <div class="rwz-configVal" id="rwzFinalNote">No note</div>
                            </div>
                            <div class="rwz-configBox">
                                <div class="rwz-configLabel">Items Selected</div>
                                <div class="rwz-configVal" id="rwzFinalItems">0 items</div>
                            </div>
                        </div>
                    </div>

                    <div class="rwz-grid rwz-grid--2 rwz-mt24">
                        <div class="rwz-field">
                            <label class="rwz-label">Return Note (Optional)</label>
                            <input type="text" id="rwzNote" class="rwz-input"
                                placeholder="Example: Customer changed mind, product defective">
                        </div>
                        <div class="rwz-field">
                            <label class="rwz-label">Final Check</label>
                            <div class="rwz-info" style="margin:0; padding:12px;">
                                <div>
                                    <p><i class="fas fa-check-circle"></i> <strong>Review before submitting</strong></p>
                                    <p class="rwz-card-note">Make sure all information is correct before proceeding.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" name="order_id" id="rwzFormOrderId">
                    <input type="hidden" name="location_id" id="rwzFormLoc">
                    <input type="hidden" name="refund_method" id="rwzFormRefund">
                    <input type="hidden" name="note" id="rwzFormNote">

                    <div class="rwz-center rwz-mt24">
                        <button type="submit" class="rwz-btn rwz-btn--primary"
                            style="padding:14px 32px; font-size:1.1rem;">
                            <i class="fas fa-check-circle"></i> Submit Return Request
                        </button>
                        <div class="rwz-card-note rwz-mt16">This will process the return and update inventory</div>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        /**
         * RWZ – optimized JS
         * - Single state object
         * - Debounced AJAX search
         * - Event delegation for table selection
         * - Uses your fixed theme variables via html[data-theme]
         */
        (() => {
            // ---------------------------
            // Routes
            // ---------------------------
            const URL_CUSTOMERS = @json(route('returns.wizard.ajax.customers'));
            const URL_ORDERS = @json(route('returns.wizard.ajax.orders'));
            const URL_ITEMS = @json(route('returns.wizard.ajax.orderItems'));

            // ---------------------------
            // DOM helpers
            // ---------------------------
            const $ = (id) => document.getElementById(id);
            const qs = (sel, root = document) => root.querySelector(sel);
            const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

            function debounce(fn, delay = 300) {
                let t;
                return (...args) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...args), delay);
                };
            }

            async function getJSON(url) {
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) throw new Error(await res.text());
                return res.json();
            }

            function showToast(msg, type = 'info') {
                const old = qs('.rwz-toast');
                if (old) old.remove();

                const toast = document.createElement('div');
                toast.className = 'rwz-toast';
                toast.innerHTML = `
      <i class="fas fa-${type==='error'?'exclamation-circle':type==='warning'?'exclamation-triangle':'info-circle'}"></i>
      <span>${msg}</span>
    `;
                toast.style.cssText = `
      position: fixed; top: 20px; right: 20px;
      padding: 12px 20px;
      background: ${type==='error'?'var(--danger)':type==='warning'?'var(--warning)':'var(--info)'};
      color: white; border-radius: var(--radius);
      display:flex; align-items:center; gap:10px;
      box-shadow: var(--dropdown-shadow);
      z-index: 2000;
      animation: rwzSlideDown .3s ease-out;
      max-width: 420px;
    `;
                document.body.appendChild(toast);
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transition = 'opacity .2s';
                    setTimeout(() => toast.remove(), 250);
                }, 3500);
            }

            // ---------------------------
            // Wizard state
            // ---------------------------
            const state = {
                step: 1,
                order: null,
                items: [],
                selected: new Set(), // indexes
                qty: {}, // idx -> qty number
                totals: {
                    items: 0,
                    qty: 0
                },

                config: {
                    location_id: '',
                    refund_method: '',
                    note: ''
                }
            };

            // ---------------------------
            // Theme toggle (same behavior, just unique id)
            // ---------------------------
            const themeToggle = $('rwzThemeToggle');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (!localStorage.getItem('theme')) {
                localStorage.setItem('theme', prefersDark ? 'dark' : 'light');
            }
            document.documentElement.setAttribute('data-theme', localStorage.getItem('theme'));
            themeToggle?.addEventListener('click', () => {
                const cur = document.documentElement.getAttribute('data-theme');
                const next = (cur === 'dark') ? 'light' : 'dark';
                document.documentElement.setAttribute('data-theme', next);
                localStorage.setItem('theme', next);
                themeToggle.style.transform = 'scale(0.95)';
                setTimeout(() => themeToggle.style.transform = 'scale(1)', 150);
            });

            // ---------------------------
            // Step navigation
            // ---------------------------
            const stepEls = qsa('#rwzSteps .rwz-step');
            const stepBlocks = [$('rwzStep1'), $('rwzStep2'), $('rwzStep3'), $('rwzStep4')];

            function renderSteps() {
                stepEls.forEach(el => {
                    const n = parseInt(el.dataset.step, 10);
                    el.classList.toggle('is-active', n === state.step);
                    el.classList.toggle('is-done', n < state.step);
                });
                stepBlocks.forEach((b, idx) => b.classList.toggle('rwz-hidden', (idx + 1) !== state.step));

                if (state.step === 1) updateConfigSummary();
                if (state.step === 4) updateFinalSummary();
            }

            // Step buttons
            $('rwzNext1')?.addEventListener('click', () => {
                if (!$('rwzLocation').value) {
                    showToast('Please select a return location before continuing.', 'warning');
                    $('rwzLocation').focus();
                    return;
                }
                saveConfig();
                state.step = 2;
                renderSteps();
            });

            $('rwzBack2')?.addEventListener('click', () => {
                state.step = 1;
                renderSteps();
            });
            $('rwzBack3')?.addEventListener('click', () => {
                state.step = 2;
                renderSteps();
            });
            $('rwzBack4')?.addEventListener('click', () => {
                state.step = 3;
                renderSteps();
            });

            $('rwzNext3')?.addEventListener('click', () => {
                if (state.selected.size === 0) {
                    showToast('Please select at least one item to return.', 'warning');
                    return;
                }
                saveConfig();
                state.step = 4;
                renderSteps();
                updateFinalSummary();
            });

            // ---------------------------
            // Tabs (Step 2)
            // ---------------------------
            const tabs = qsa('#rwzTabs .rwz-tab');
            const panelOrder = $('rwzPanelOrder');
            const panelCustomer = $('rwzPanelCustomer');

            tabs.forEach(t => {
                t.addEventListener('click', () => {
                    tabs.forEach(x => x.classList.remove('is-active'));
                    t.classList.add('is-active');

                    const tab = t.dataset.tab;
                    panelOrder.classList.toggle('is-active', tab === 'order');
                    panelCustomer.classList.toggle('is-active', tab === 'customer');
                });
            });

            // ---------------------------
            // Config save/load (localStorage)
            // ---------------------------
            function saveConfig() {
                state.config.location_id = $('rwzLocation')?.value || '';
                state.config.refund_method = $('rwzRefund')?.value || '';
                state.config.note = $('rwzNote')?.value || '';

                localStorage.setItem('rwzConfig', JSON.stringify(state.config));
                updateConfigSummary();
            }

            function loadConfig() {
                try {
                    const raw = localStorage.getItem('rwzConfig');
                    if (!raw) return;
                    const cfg = JSON.parse(raw);
                    state.config.location_id = cfg.location_id || '';
                    state.config.refund_method = cfg.refund_method || '';
                    state.config.note = cfg.note || '';
                } catch (e) {}
            }

            function applyConfig() {
                if ($('rwzLocation')) $('rwzLocation').value = state.config.location_id || '';
                if ($('rwzRefund')) $('rwzRefund').value = state.config.refund_method || '';
                if ($('rwzNote')) $('rwzNote').value = state.config.note || '';
                updateConfigSummary();
            }

            function updateConfigSummary() {
                const locSel = $('rwzLocation');
                const refSel = $('rwzRefund');

                const locText = (locSel && locSel.value) ? locSel.options[locSel.selectedIndex]?.text : 'Not selected';
                const refText = (refSel && refSel.value) ? refSel.options[refSel.selectedIndex]?.text : 'Not selected';

                const locEl = $('rwzSumLoc');
                const refEl = $('rwzSumRefund');

                if (locEl) {
                    locEl.textContent = locText;
                    locEl.classList.toggle('is-empty', !(locSel && locSel.value));
                }
                if (refEl) {
                    refEl.textContent = refText;
                    refEl.classList.toggle('is-empty', !(refSel && refSel.value));
                }
            }

            // live sync
            $('rwzLocation')?.addEventListener('change', saveConfig);
            $('rwzRefund')?.addEventListener('change', saveConfig);
            $('rwzNote')?.addEventListener('input', () => {
                saveConfig();
                if (state.step === 4) updateFinalSummary();
            });

            // ---------------------------
            // Dropdown helpers
            // ---------------------------
            function ddShow(box, html) {
                box.innerHTML = html;
                box.classList.remove('rwz-hidden');
            }

            function ddHide(box) {
                box.classList.add('rwz-hidden');
                box.innerHTML = '';
            }

            // click outside closes dropdowns
            document.addEventListener('click', (e) => {
                const oBox = $('rwzOrderResults');
                const cBox = $('rwzCustomerResults');
                const oInp = $('rwzOrderSearch');
                const cInp = $('rwzCustomerSearch');

                if (oBox && !oBox.contains(e.target) && e.target !== oInp) ddHide(oBox);
                if (cBox && !cBox.contains(e.target) && e.target !== cInp) ddHide(cBox);
            });

            // ---------------------------
            // AJAX: Search Orders
            // ---------------------------
            const orderInp = $('rwzOrderSearch');
            const orderBox = $('rwzOrderResults');

            const doOrderSearch = debounce(async () => {
                const q = (orderInp.value || '').trim();
                if (q.length < 2) {
                    ddHide(orderBox);
                    return;
                }

                try {
                    const res = await getJSON(`${URL_ORDERS}?q=${encodeURIComponent(q)}`);
                    const rows = res.data || [];

                    if (!rows.length) {
                        ddShow(orderBox, `<div class="rwz-empty" style="padding:16px;">
          <i class="fas fa-search"></i><h3>No results</h3><p>Try another order no / phone / name.</p>
        </div>`);
                        return;
                    }

                    const html = rows.map(o => `
        <div class="rwz-dditem" data-order-id="${o.id}">
          <div class="rwz-ddleft">
            <h5>${o.order_no}</h5>
            <p>${o.customer ? `${o.customer.name} (${o.customer.phone}) | ` : ``}${o.date ?? ''} | ${o.status ?? ''}</p>
          </div>
          <div class="rwz-ddright"><i class="fas fa-arrow-right"></i></div>
        </div>
      `).join('');
                    ddShow(orderBox, html);
                } catch (err) {
                    console.error(err);
                    showToast('Order search failed', 'error');
                }
            }, 300);

            orderInp?.addEventListener('input', doOrderSearch);

            $('rwzOrderClear')?.addEventListener('click', () => {
                orderInp.value = '';
                ddHide(orderBox);
            });

            // delegate click on results
            orderBox?.addEventListener('click', async (e) => {
                const item = e.target.closest('[data-order-id]');
                if (!item) return;
                ddHide(orderBox);
                await loadOrder(item.dataset.orderId);
            });

            // ---------------------------
            // AJAX: Search Customers
            // ---------------------------
            const custInp = $('rwzCustomerSearch');
            const custBox = $('rwzCustomerResults');

            const doCustomerSearch = debounce(async () => {
                const q = (custInp.value || '').trim();
                if (q.length < 2) {
                    ddHide(custBox);
                    return;
                }

                try {
                    const res = await getJSON(`${URL_CUSTOMERS}?q=${encodeURIComponent(q)}`);
                    const rows = res.data || [];

                    if (!rows.length) {
                        ddShow(custBox, `<div class="rwz-empty" style="padding:16px;">
          <i class="fas fa-user"></i><h3>No customers</h3><p>Try another name/phone.</p>
        </div>`);
                        return;
                    }

                    const html = rows.map(c => `
        <div class="rwz-dditem" data-customer-phone="${c.phone}">
          <div class="rwz-ddleft">
            <h5>${c.name}</h5>
            <p>${c.phone}</p>
          </div>
          <div class="rwz-ddright"><i class="fas fa-arrow-right"></i></div>
        </div>
      `).join('');
                    ddShow(custBox, html);
                } catch (err) {
                    console.error(err);
                    showToast('Customer search failed', 'error');
                }
            }, 300);

            custInp?.addEventListener('input', doCustomerSearch);

            $('rwzCustomerClear')?.addEventListener('click', () => {
                custInp.value = '';
                ddHide(custBox);
            });

            // click customer -> auto search orders by phone in Order tab
            custBox?.addEventListener('click', (e) => {
                const item = e.target.closest('[data-customer-phone]');
                if (!item) return;
                ddHide(custBox);

                // switch tab
                tabs.forEach(x => x.classList.remove('is-active'));
                qs('#rwzTabs .rwz-tab[data-tab="order"]').classList.add('is-active');
                panelOrder.classList.add('is-active');
                panelCustomer.classList.remove('is-active');

                orderInp.value = item.dataset.customerPhone || '';
                doOrderSearch();
            });

            // ---------------------------
            // Load order items
            // ---------------------------
            async function loadOrder(orderId) {
                try {
                    const data = await getJSON(`${URL_ITEMS}?order_id=${encodeURIComponent(orderId)}`);
                    state.order = data.order;
                    state.items = data.items || [];

                    // reset selections for new order
                    state.selected = new Set();
                    state.qty = {};
                    state.totals = {
                        items: 0,
                        qty: 0
                    };

                    // show info
                    const info = $('rwzOrderInfo');
                    info.classList.remove('rwz-hidden');
                    info.innerHTML = `
        <div class="rwz-info">
          <div class="rwz-info-icon"><i class="fas fa-shopping-cart"></i></div>
          <div>
            <h4>Order Loaded: ${state.order.order_no}</h4>
            <p class="rwz-card-note">
              ${state.order.customer ? `Customer: ${state.order.customer.name} (${state.order.customer.phone}) | ` : ``}
              Date: ${state.order.date ?? '-'} | Status: ${state.order.status ?? '-'}
            </p>
          </div>
        </div>
      `;

                    renderItemsTable();
                    state.step = 3;
                    renderSteps();

                    $('rwzNext2')?.classList.add('rwz-hidden');
                } catch (err) {
                    console.error(err);
                    showToast('Failed to load order items', 'error');
                }
            }

            // ---------------------------
            // Render items table (Step 3)
            // ---------------------------
            function renderItemsTable() {
                const tbody = $('rwzItemsTbody');
                tbody.innerHTML = '';

                if (!state.order) {
                    $('rwzNoOrder').classList.remove('rwz-hidden');
                    $('rwzItemsBlock').classList.add('rwz-hidden');
                    return;
                }

                $('rwzNoOrder').classList.add('rwz-hidden');
                $('rwzItemsBlock').classList.remove('rwz-hidden');

                if (!state.items.length) {
                    tbody.innerHTML = `<tr><td colspan="6">
        <div class="rwz-empty">
          <i class="fas fa-box-open"></i>
          <h3>No items found</h3>
          <p>This order has no items.</p>
        </div>
      </td></tr>`;
                    return;
                }

                tbody.innerHTML = state.items.map((it, idx) => `
      <tr data-idx="${idx}">
        <td><div class="rwz-check" data-idx="${idx}"></div></td>
        <td>
          <div style="font-weight:600;">${it.product_name ?? ('Product #' + it.product_id)}</div>
          <div class="rwz-previewMeta">
            Order Item #${it.id} | Batch #${it.product_batch_id}
            ${it.barcode ? `<br>Barcode: ${it.barcode}` : ``}
          </div>
        </td>
        <td>${it.qty_sold}</td>
        <td>${it.qty_returned}</td>
        <td><span class="rwz-pill">${it.qty_returnable}</span></td>
        <td>
          <input class="rwz-qty" type="number" step="0.0001"
                 min="0" max="${it.qty_returnable}" value="${it.qty_returnable}"
                 data-idx="${idx}" disabled>
        </td>
      </tr>
    `).join('');

                updateNextButtons();
                rebuildPreview();
            }

            // Event delegation: selection + qty input
            $('rwzItemsTable')?.addEventListener('click', (e) => {
                const check = e.target.closest('.rwz-check');
                if (!check) return;

                const idx = parseInt(check.dataset.idx, 10);
                const row = e.target.closest('tr');
                const qtyInput = qs(`.rwz-qty[data-idx="${idx}"]`);

                if (check.classList.contains('is-on')) {
                    check.classList.remove('is-on');
                    row?.classList.remove('is-selected');
                    qtyInput.disabled = true;

                    state.selected.delete(idx);
                    delete state.qty[idx];
                } else {
                    check.classList.add('is-on');
                    row?.classList.add('is-selected');
                    qtyInput.disabled = false;
                    qtyInput.focus();

                    state.selected.add(idx);
                    state.qty[idx] = parseFloat(qtyInput.value) || 0;
                }

                rebuildPreview();
                updateNextButtons();
                saveConfig(); // keeps config synced
            });

            $('rwzItemsTable')?.addEventListener('input', (e) => {
                const inp = e.target.closest('.rwz-qty');
                if (!inp) return;

                const idx = parseInt(inp.dataset.idx, 10);
                const max = parseFloat(state.items[idx].qty_returnable);
                let val = parseFloat(inp.value) || 0;

                if (val > max) {
                    val = max;
                    inp.value = max;
                    showToast(`Quantity cannot exceed ${max}`, 'warning');
                }
                if (val < 0) {
                    val = 0;
                    inp.value = 0;
                }

                state.qty[idx] = val;
                rebuildPreview();
            });

            $('rwzItemsTable')?.addEventListener('change', (e) => {
                const inp = e.target.closest('.rwz-qty');
                if (!inp) return;

                const idx = parseInt(inp.dataset.idx, 10);
                const val = parseFloat(inp.value) || 0;

                // if qty becomes 0 -> auto unselect
                if (val === 0 && state.selected.has(idx)) {
                    const check = qs(`.rwz-check[data-idx="${idx}"]`);
                    check?.click();
                }
            });

            function updateNextButtons() {
                // Step 3 to Step 4 button
                $('rwzNext3').disabled = (state.selected.size === 0);
            }

            function rebuildPreview() {
                const preview = $('rwzPreview');

                if (state.selected.size === 0) {
                    preview.innerHTML = `
        <div class="rwz-empty" style="padding: 10px 0;">
          <i class="fas fa-clipboard-list"></i>
          <h3>No Items Selected</h3>
          <p>Select items from the order to return.</p>
        </div>`;
                    state.totals = {
                        items: 0,
                        qty: 0
                    };
                    if (state.step === 4) updateFinalSummary();
                    return;
                }

                let html = '';
                let totalItems = 0;
                let totalQty = 0;

                state.selected.forEach(idx => {
                    const it = state.items[idx];
                    const q = state.qty[idx] || 0;
                    if (q <= 0) return;

                    totalItems++;
                    totalQty += q;

                    html += `
        <div class="rwz-previewItem">
          <div>
            <div class="rwz-previewName">${it.product_name ?? ('Product #' + it.product_id)}</div>
            <div class="rwz-previewMeta">Order Item #${it.id} | Batch #${it.product_batch_id}</div>
          </div>
          <div class="rwz-previewQty">${q}</div>
        </div>
      `;
                });

                state.totals = {
                    items: totalItems,
                    qty: totalQty
                };

                preview.innerHTML = html || `
      <div class="rwz-empty" style="padding: 10px 0;">
        <i class="fas fa-exclamation-circle"></i>
        <h3>No Valid Quantities</h3>
        <p>Selected items have return quantity set to 0.</p>
      </div>
    `;

                if (state.step === 4) updateFinalSummary();
            }

            function updateFinalSummary() {
                const locSel = $('rwzLocation');
                const refSel = $('rwzRefund');

                const locText = locSel?.options[locSel.selectedIndex]?.text || 'Not selected';
                const refText = refSel?.options[refSel.selectedIndex]?.text || 'Not selected';
                const noteText = $('rwzNote')?.value || 'No note provided';

                $('rwzFinalLoc').textContent = locText;
                $('rwzFinalRefund').textContent = refText;
                $('rwzFinalNote').textContent = noteText;
                $('rwzFinalItems').textContent = `${state.totals.items} items (${state.totals.qty} total quantity)`;
            }

            // ---------------------------
            // Submit: build hidden inputs
            // ---------------------------
            function clearDyn() {
                qsa('.rwz-dyn').forEach(el => el.remove());
            }

            function addHidden(name, value) {
                const i = document.createElement('input');
                i.type = 'hidden';
                i.name = name;
                i.value = value;
                i.className = 'rwz-dyn';
                $('rwzReturnForm').appendChild(i);
            }

            $('rwzReturnForm')?.addEventListener('submit', (e) => {
                saveConfig();

                if (!state.order) {
                    e.preventDefault();
                    showToast('Please load an order first', 'error');
                    return;
                }

                const loc = $('rwzLocation')?.value || '';
                if (!loc) {
                    e.preventDefault();
                    showToast('Please select a return location', 'error');
                    return;
                }

                if (state.selected.size === 0) {
                    e.preventDefault();
                    showToast('Please select at least one item to return', 'error');
                    return;
                }

                clearDyn();

                // sync meta
                $('rwzFormOrderId').value = state.order.id;
                $('rwzFormLoc').value = state.config.location_id || '';
                $('rwzFormRefund').value = state.config.refund_method || '';
                $('rwzFormNote').value = $('rwzNote')?.value || '';

                let i = 0;
                state.selected.forEach(idx => {
                    const it = state.items[idx];
                    const q = state.qty[idx] || 0;
                    if (q <= 0) return;

                    addHidden(`items[${i}][order_item_id]`, it.id);
                    addHidden(`items[${i}][product_id]`, it.product_id);
                    addHidden(`items[${i}][product_batch_id]`, it.product_batch_id);
                    addHidden(`items[${i}][qty]`, q);
                    addHidden(`items[${i}][condition]`, 'GOOD');
                    addHidden(`items[${i}][reason_code]`, 'wizard_web');
                    i++;
                });

                if (i === 0) {
                    e.preventDefault();
                    showToast('Please set valid quantities for selected items', 'error');
                    return;
                }

                // optional: clear config after submit
                setTimeout(() => {
                    localStorage.removeItem('rwzConfig');
                }, 1000);
            });

            // Step 2 -> Step 3 shortcut button (only if you want it)
            $('rwzNext2')?.addEventListener('click', () => {
                if (!state.order) {
                    showToast('Load an order first', 'warning');
                    return;
                }
                state.step = 3;
                renderSteps();
            });

            // Step 2 back button shown already
            // Step 2 "Continue to Items" can be enabled when order is loaded:
            function enableStep2Continue() {
                $('rwzNext2')?.classList.remove('rwz-hidden');
            }

            // After loadOrder(), you could call enableStep2Continue()
            // but we directly jump to Step 3 in loadOrder()

            // ---------------------------
            // Init
            // ---------------------------
            loadConfig();
            applyConfig();
            renderSteps();

        })();
    </script>
@endsection
