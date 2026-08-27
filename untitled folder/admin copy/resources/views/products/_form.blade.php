{{-- =======================
    CSS (Responsive + Clean)
======================= --}}
<style>
    /* Basic (uses your CSS variables if already defined) */
    .product-form-container {
        margin-bottom: 2rem;
    }

    .form-card {
        background: var(--card, #fff);
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: var(--radius, 12px);
        overflow: hidden;
        box-shadow: var(--card-shadow, 0 10px 30px rgba(0, 0, 0, .06));
    }

    .glass-effect {
        backdrop-filter: blur(10px);
        background: var(--glass-base, rgba(255, 255, 255, 0.85));
    }

    /* Tabs */
    .form-tabs {
        display: flex;
        flex-wrap: wrap;
        background: var(--accent, oklch(0.269 0 0));
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
    }

    .tab-btn {
        flex: 1;
        min-width: 160px;
        padding: 1rem 1.25rem;
        border: none;
        background: transparent;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        font-weight: 800;
        color: var(--text-secondary, oklch(0.708 0 0));
        border-bottom: 3px solid transparent;
        transition: color var(--transition-normal, 250ms) ease,
            background-color var(--transition-normal, 250ms) ease;
    }

    .tab-btn svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .tab-btn.active {
        background: var(--card, oklch(0.205 0 0));
        color: var(--accent-color, oklch(0.488 0.243 264.376));
        border-bottom-color: var(--accent-color, oklch(0.488 0.243 264.376));
    }

    /* Panes */
    .tab-content {
        padding: 1.5rem;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    .tab-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 900px) {
        .tab-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Sections */
    .form-section {
        margin-bottom: 1.2rem;
    }

    .form-label-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .75rem;
        margin-bottom: .5rem;
    }

    .form-label {
        font-weight: 800;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .form-label.required::after {
        content: "*";
        color: var(--danger, oklch(0.704 0.191 22.216));
        margin-left: 4px;
    }

    .form-label-hint {
        font-size: .8rem;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        padding: .2rem .6rem;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: 999px;
        background: var(--accent, oklch(0.269 0 0));
    }

    /* Inputs */
    .input-wrapper,
    .select-wrapper,
    .textarea-wrapper {
        position: relative;
        border: 2px solid var(--border-color, oklch(0.9 0 0));
        border-radius: var(--radius, 12px);
        background: var(--input, oklch(1 0 0 / 15%));
        transition: border-color var(--transition-fast, 150ms) ease,
            box-shadow var(--transition-fast, 150ms) ease;
    }

    .input-wrapper:focus-within,
    .select-wrapper:focus-within,
    .textarea-wrapper:focus-within {
        border-color: var(--accent-color, oklch(0.488 0.243 264.376));
        box-shadow: 0 0 0 3px var(--accent-glow, rgba(37, 99, 235, .15));
    }

    .input-icon,
    .textarea-icon {
        position: absolute;
        left: 1rem;
        color: var(--text-secondary, oklch(0.708 0 0));
        display: flex;
        align-items: center;
    }

    .input-icon {
        top: 50%;
        transform: translateY(-50%);
    }

    .textarea-icon {
        top: 1rem;
    }

    .input-icon svg,
    .textarea-icon svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: .9rem 1rem .9rem 3rem;
        border: none;
        outline: none;
        background: transparent;
        color: var(--text-primary, oklch(0.985 0 0));
        font-weight: 600;
    }

    .form-textarea {
        padding: 1rem 1rem 1rem 3rem;
        min-height: 110px;
        resize: vertical;
    }

    .select-arrow {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary, oklch(0.708 0 0));
        pointer-events: none;
    }

    .select-arrow svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .row-inline {
        display: flex;
        gap: .75rem;
        align-items: stretch;
    }

    @media (max-width: 520px) {
        .row-inline {
            flex-direction: column;
        }
    }

    /* Buttons */
    .btn-primary,
    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .85rem 1.1rem;
        border-radius: var(--radius, 12px);
        border: 1px solid transparent;
        cursor: pointer;
        text-decoration: none;
        font-weight: 900;
        transition: background-color var(--transition-normal, 250ms) ease,
            border-color var(--transition-normal, 250ms) ease,
            transform var(--transition-fast, 150ms) ease,
            box-shadow var(--transition-fast, 150ms) ease;
    }

    .btn-primary {
        background: var(--accent-color, oklch(0.488 0.243 264.376));
        color: var(--sidebar-primary-foreground, #fff);
    }

    .btn-primary:hover {
        background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
        transform: translateY(-1px);
        box-shadow: 0 4px 12px var(--accent-glow, rgba(37, 99, 235, .2));
    }

    .btn-secondary {
        background: var(--accent, oklch(0.269 0 0));
        border-color: var(--border-color, oklch(0.9 0 0));
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .btn-secondary:hover {
        background: var(--bg-tertiary, oklch(0.269 0 0));
        border-color: var(--accent-color, oklch(0.488 0.243 264.376));
        transform: translateY(-1px);
    }

    .btn-sm {
        padding: .55rem .9rem;
        font-size: .9rem;
    }

    .btn-primary svg,
    .btn-secondary svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    /* Dropdown */
    .pf_dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        z-index: 999;
        background: var(--card, oklch(0.205 0 0));
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: var(--radius, 12px);
        box-shadow: var(--dropdown-shadow, 0 18px 40px rgba(0, 0, 0, .10));
        overflow: auto;
        max-height: 260px;
        padding: .35rem;
    }

    .pf_dropdown.show {
        display: block;
    }

    .pf_dropdown-item {
        padding: .75rem .8rem;
        border-radius: calc(var(--radius, 12px) - 2px);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        user-select: none;
        color: var(--text-primary, oklch(0.985 0 0));
        transition: background-color var(--transition-fast, 150ms) ease;
    }

    .pf_dropdown-item:hover {
        background: var(--accent, oklch(0.269 0 0));
    }

    .pf_dropdown-item .muted {
        font-size: .78rem;
        opacity: .75;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        padding: .1rem .5rem;
        border-radius: 999px;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    }

    /* Toggle */
    .toggle-switch {
        display: inline-flex;
        align-items: center;
        gap: 1rem;
    }

    .toggle-input {
        display: none;
    }

    .toggle-label {
        display: inline-flex;
        align-items: center;
        gap: .75rem;
        cursor: pointer;
        user-select: none;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .toggle-track {
        width: 3rem;
        height: 1.6rem;
        border-radius: 999px;
        background: var(--border-color, oklch(0.9 0 0));
        position: relative;
        transition: background-color var(--transition-normal, 250ms) ease;
    }

    .toggle-thumb {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 50%;
        background: var(--card, #fff);
        position: absolute;
        top: .18rem;
        left: .18rem;
        transition: transform var(--transition-normal, 250ms) ease;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .15);
    }

    .toggle-input:checked+.toggle-label .toggle-track {
        background: var(--success, #22c55e);
    }

    .toggle-input:checked+.toggle-label .toggle-thumb {
        transform: translateX(1.4rem);
    }

    .toggle-help {
        margin-top: .5rem;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        font-size: .9rem;
    }

    /* Tables responsive */
    .table-container {
        overflow-x: auto;
    }

    .data-table,
    .status-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td,
    .status-table th,
    .status-table td {
        padding: .9rem;
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        text-align: left;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .data-table th,
    .status-table th {
        background: var(--accent, oklch(0.269 0 0));
        color: var(--text-primary, oklch(0.985 0 0));
        font-weight: 800;
    }

    .action-buttons {
        display: flex;
        gap: .5rem;
    }

    .btn-action {
        width: 38px;
        height: 38px;
        border-radius: var(--radius, 12px);
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        background: var(--accent, oklch(0.269 0 0));
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all var(--transition-fast, 150ms) ease;
    }

    .btn-action svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    .btn-action.edit:hover {
        background: var(--info, #0ea5e9);
        color: #fff;
        border-color: var(--info, #0ea5e9);
    }

    .btn-action.delete:hover {
        background: var(--danger, #ef4444);
        color: #fff;
        border-color: var(--danger, #ef4444);
    }

    .inline-form {
        display: inline;
    }

    /* Status pills */
    .pill {
        display: inline-block;
        padding: .2rem .65rem;
        border-radius: 999px;
        font-weight: 800;
        font-size: .8rem;
    }

    .pill-ok {
        background: rgba(34, 197, 94, .12);
        color: var(--success, #16a34a);
        border: 1px solid rgba(34, 197, 94, .25);
    }

    .pill-off {
        background: rgba(107, 114, 128, .12);
        color: var(--text-muted, #6b7280);
        border: 1px solid rgba(107, 114, 128, .25);
    }

    /* Empty & comment UI */
    .create-mode-message {
        text-align: center;
        padding: 2rem 1rem;
        border: 2px dashed var(--border-color, oklch(0.9 0 0));
        border-radius: var(--radius, 12px);
        background: var(--accent, oklch(0.269 0 0));
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .message-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto .9rem;
        color: var(--accent-color, oklch(0.488 0.243 264.376));
    }

    .message-icon svg {
        width: 100%;
        height: 100%;
        fill: currentColor;
    }

    .comments-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .comments-title {
        margin: 0;
        font-weight: 900;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .comments-hint {
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        font-size: .9rem;
    }

    .comment-form {
        padding: 1rem;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: var(--radius, 12px);
        background: var(--card, oklch(0.205 0 0));
    }

    .comment-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-top: .9rem;
        flex-wrap: wrap;
    }

    .muted-text {
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    }

    .comment-list {
        margin-top: 1rem;
        display: grid;
        gap: .75rem;
    }

    .comment-item {
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: var(--radius, 12px);
        padding: 1rem;
        background: var(--card, oklch(0.205 0 0));
    }

    .comment-meta {
        display: flex;
        gap: .5rem;
        font-size: .85rem;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    }

    .comment-author {
        font-weight: 900;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .comment-body {
        margin-top: .5rem;
        color: var(--text-secondary, oklch(0.708 0 0));
        line-height: 1.6;
    }

    .empty-state {
        display: flex;
        gap: 1rem;
        align-items: center;
        padding: 1rem;
        border-radius: var(--radius, 12px);
        border: 1px dashed var(--border-color, oklch(0.9 0 0));
        background: var(--accent, oklch(0.269 0 0));
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .empty-icon {
        width: 40px;
        height: 40px;
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
    }

    .empty-icon svg {
        width: 100%;
        height: 100%;
        fill: currentColor;
    }

    /* Toasts */
    .toast-stack {
        position: fixed;
        right: 1rem;
        top: 1rem;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }

    .toast {
        width: min(420px, calc(100vw - 2rem));
        border-radius: calc(var(--radius, 12px) + 2px);
        background: var(--card, oklch(0.205 0 0));
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-left: 4px solid var(--accent-color, oklch(0.488 0.243 264.376));
        box-shadow: var(--dropdown-shadow, 0 18px 40px rgba(0, 0, 0, .12));
        padding: .9rem 1rem;
        display: flex;
        gap: .75rem;
        align-items: flex-start;
        animation: toastSlideIn 0.3s ease forwards;
    }

    @keyframes toastSlideIn {
        from {
            opacity: 0;
            transform: translateX(100%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .toast-success {
        border-left-color: var(--success, #22c55e);
    }

    .toast-error {
        border-left-color: var(--danger, #ef4444);
    }

    .toast-warning {
        border-left-color: var(--warning, #f59e0b);
    }

    .toast-info {
        border-left-color: var(--info, #0ea5e9);
    }

    .toast-icon {
        width: 20px;
        height: 20px;
        flex: 0 0 auto;
        margin-top: .1rem;
    }

    .toast-title {
        font-weight: 900;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .toast-message {
        margin-top: .15rem;
        color: var(--text-secondary, oklch(0.708 0 0));
    }

    .toast-close {
        margin-left: auto;
        border: none;
        background: transparent;
        cursor: pointer;
        padding: .2rem;
        border-radius: calc(var(--radius, 12px) - 2px);
        transition: background-color var(--transition-fast, 150ms) ease;
    }

    .toast-close:hover {
        background: var(--accent, oklch(0.269 0 0));
    }

    .toast-close svg {
        width: 18px;
        height: 18px;
        fill: currentColor;
    }

    /* Scanner */
    .scanner-overlay {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, .55);
        backdrop-filter: blur(6px);
        z-index: 99999;
    }

    .scanner-overlay.show {
        display: flex;
    }

    .scanner-modal {
        width: min(680px, calc(100vw - 2rem));
        background: var(--card, oklch(0.205 0 0));
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        border-radius: calc(var(--radius, 12px) + 4px);
        overflow: hidden;
        box-shadow: var(--dropdown-shadow, 0 18px 50px rgba(0, 0, 0, .18));
    }

    .scanner-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border-color, oklch(0.9 0 0));
        background: var(--accent, oklch(0.269 0 0));
    }

    .scanner-title {
        font-weight: 900;
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .scanner-sub {
        color: var(--text-muted, oklch(0.708 0 0 / 0.7));
        font-size: .9rem;
        margin-top: .15rem;
    }

    .scanner-close {
        border: none;
        background: transparent;
        cursor: pointer;
        color: var(--text-secondary, oklch(0.708 0 0));
        padding: .35rem;
        border-radius: calc(var(--radius, 12px) - 2px);
        transition: background-color var(--transition-fast, 150ms) ease,
            color var(--transition-fast, 150ms) ease;
    }

    .scanner-close:hover {
        background: var(--card, oklch(0.205 0 0));
        color: var(--text-primary, oklch(0.985 0 0));
    }

    .scanner-close svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .scanner-body {
        padding: 1rem 1.25rem 1.25rem;
    }

    .scanner-frame {
        position: relative;
        border-radius: calc(var(--radius, 12px) + 4px);
        overflow: hidden;
        border: 1px solid var(--border-color, oklch(0.9 0 0));
        background: #111;
        aspect-ratio: 16 / 10;
    }

    #scannerVideo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .scanner-reticle {
        position: absolute;
        inset: 0;
        pointer-events: none;
    }

    .scanner-reticle::after {
        content: "";
        position: absolute;
        inset: 18%;
        border-radius: calc(var(--radius, 12px) + 4px);
        border: 2px solid var(--accent-color, rgba(37, 99, 235, .9));
        box-shadow: 0 0 0 4px var(--accent-glow, rgba(37, 99, 235, .18));
    }

    .scanner-actions {
        display: flex;
        gap: .75rem;
        margin-top: .9rem;
        flex-wrap: wrap;
    }

    /* Stock Management Tab specific styles */
    #pf_stock_management {
        --st-radius: var(--radius, 16px);
        --st-border: var(--border-color, oklch(0.9 0 0));
        --st-card: var(--card, oklch(0.205 0 0));
        --st-bg: var(--accent, oklch(0.269 0 0));
        --st-text: var(--text-primary, oklch(0.985 0 0));
        --st-muted: var(--text-muted, oklch(0.708 0 0 / 0.7));
        --st-accent: var(--accent-color, oklch(0.488 0.243 264.376));
        --st-ok: var(--success, #22c55e);
        --st-warn: var(--warning, #f59e0b);
        --st-danger: var(--danger, #ef4444);
        --st-shadow: var(--card-shadow, 0 18px 50px rgba(0, 0, 0, .10));
        --st-shadow-hover: var(--card-shadow-hover, 0 24px 70px rgba(0, 0, 0, .16));
    }

    /* Product Status Tab specific styles */
    #pf_product_status {
        --ps-radius: calc(var(--radius, 12px) + 6px);
        --ps-border: var(--border-color, oklch(0.9 0 0));
        --ps-card: var(--card, oklch(0.205 0 0));
        --ps-accent: var(--accent-color, oklch(0.488 0.243 264.376));
        --ps-bg: var(--accent, oklch(0.269 0 0));
        --ps-text: var(--text-primary, oklch(0.985 0 0));
        --ps-muted: var(--text-secondary, oklch(0.708 0 0));
        --ps-ok: var(--success, #22c55e);
        --ps-warn: var(--warning, #f59e0b);
        --ps-danger: var(--danger, #ef4444);
        --ps-shadow: var(--card-shadow, 0 18px 50px rgba(0, 0, 0, .10));
        --ps-shadow-hover: var(--card-shadow-hover, 0 24px 70px rgba(0, 0, 0, .16));
    }

    /* Error messages */
    .error-message {
        display: none;
        color: var(--danger, #ef4444);
        font-size: .85rem;
        margin-top: .35rem;
        font-weight: 600;
    }

    .error-message.show {
        display: block;
    }

    /* Focus styles */
    .form-input:focus,
    .form-textarea:focus,
    .btn-primary:focus,
    .btn-secondary:focus,
    .tab-btn:focus,
    .scanner-close:focus,
    .toast-close:focus {
        outline: 2px solid var(--ring, oklch(0.556 0 0));
        outline-offset: 2px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .form-tabs {
            flex-direction: column;
        }

        .tab-btn {
            min-width: 100%;
            justify-content: flex-start;
            padding-left: 1.5rem;
        }

        .tab-content {
            padding: 1rem;
        }

        .toast-stack {
            left: 1rem;
            right: 1rem;
            top: auto;
            bottom: 1rem;
        }

        .toast {
            width: 100%;
        }
    }
</style>

@php
    $isEdit = isset($product) && $product->exists;
    $batches = $isEdit ? $product->batches ?? collect() : collect();
    $hasMultipleBatches = $batches->count() > 1;
    $hasSingleBatch = $batches->count() === 1;
    $batch = $hasSingleBatch ? $batches->first() : null;

    $productStatuses = $isEdit ? $product->statuses ?? collect() : collect();

    function getContrastColorSafe($hexColor, $light = '#FFFFFF', $dark = '#000000')
    {
        $hex = ltrim((string) $hexColor, '#');
        if (!preg_match('/^[0-9A-Fa-f]{6}$/', $hex)) {
            return $light;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
        return $brightness > 160 ? $dark : $light;
    }
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- =======================
    TOAST STACK
======================= --}}
<div class="toast-stack" id="pf_toastStack" aria-live="polite" aria-relevant="additions"></div>

{{-- Flash toasts (optional) --}}
@if (session('success'))
    <script>
        window.__PF_FLASH_TOASTS__ = window.__PF_FLASH_TOASTS__ || [];
        window.__PF_FLASH_TOASTS__.push({
            type: 'success',
            message: @json(session('success'))
        });
    </script>
@endif
@if (session('error'))
    <script>
        window.__PF_FLASH_TOASTS__ = window.__PF_FLASH_TOASTS__ || [];
        window.__PF_FLASH_TOASTS__.push({
            type: 'error',
            message: @json(session('error'))
        });
    </script>
@endif
@if ($errors->any())
    <script>
        window.__PF_FLASH_TOASTS__ = window.__PF_FLASH_TOASTS__ || [];
        window.__PF_FLASH_TOASTS__.push({
            type: 'error',
            message: "Please fix the highlighted fields and try again."
        });
    </script>
@endif

<div class="product-form-container" data-pf-root>
    <div class="form-card glass-effect">

        {{-- Tabs --}}
        <div class="form-tabs" role="tablist" aria-label="Product form tabs">
            <button type="button" class="tab-btn active" data-pf-tab="pf_product_details" role="tab"
                aria-selected="true">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path
                        d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z" />
                </svg>
                Product Details
            </button>

            <button type="button" class="tab-btn" data-pf-tab="pf_stock_management" role="tab"
                aria-selected="false">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path
                        d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4z" />
                </svg>
                Stock Management
            </button>

            <button type="button" class="tab-btn" data-pf-tab="pf_product_status" role="tab" aria-selected="false">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                </svg>
                Product Status
            </button>

            <button type="button" class="tab-btn" data-pf-tab="pf_product_comments" role="tab"
                aria-selected="false">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M21 6h-2v9H7l-4 4V6c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2z" />
                </svg>
                Comments
            </button>
        </div>

        <div class="tab-content">
            {{-- =======================
                TAB: PRODUCT DETAILS
            ======================= --}}
            <div class="tab-pane active" id="pf_product_details" role="tabpanel">
                <div class="tab-grid">
                    {{-- LEFT --}}
                    <div class="tab-column">

                        {{-- Barcode --}}
                        <div class="form-section" data-section="barcode">
                            <div class="form-label-group">
                                <label class="form-label required" for="pf_barcodeInput">Barcode</label>
                                <span class="form-label-hint">Required</span>
                            </div>

                            <div class="row-inline">
                                <div class="input-wrapper">
                                    <div class="input-icon">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M2 6h4v12H2zm5 0h4v12H7zm5 0h4v12h-4zm5 0h4v12h-4z" />
                                        </svg>
                                    </div>

                                    <input type="text" id="pf_barcodeInput" class="form-input" name="barcode"
                                        required minlength="3" placeholder="Scan / type / generate barcode"
                                        value="{{ old('barcode', $product->barcode ?? '') }}" autocomplete="off"
                                        inputmode="numeric">
                                    <div class="input-focus-line"></div>
                                </div>

                                <button type="button" class="btn-secondary btn-sm" data-pf-scan-barcode>
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            d="M4 7V5c0-1.1.9-2 2-2h2v2H6v2H4zm12-4h2c1.1 0 2 .9 2 2v2h-2V5h-2V3zM4 17v2c0 1.1.9 2 2 2h2v-2H6v-2H4zm16 0v2c0 1.1-.9 2-2 2h-2v-2h2v-2h2zM7 11h10v2H7v-2z" />
                                    </svg>
                                    Scan
                                </button>

                                <button type="button" class="btn-secondary btn-sm" data-pf-generate-barcode>
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 5v14m-7-7h14" />
                                    </svg>
                                    Generate
                                </button>
                            </div>

                            @error('barcode')
                                <span class="error-message show" data-error-for="barcode">{{ $message }}</span>
                            @else
                                <span class="error-message" data-error-for="barcode"></span>
                            @enderror

                            <small class="muted-text" style="display:block;margin-top:.35rem;">
                                Tip: Hardware barcode scanner works automatically (click field then scan + Enter).
                            </small>
                        </div>

                        {{-- Product Name --}}
                        <div class="form-section" data-section="name">
                            <div class="form-label-group">
                                <label class="form-label required" for="pf_productName">Product Name</label>
                                <span class="form-label-hint">Required</span>
                            </div>

                            <div class="input-wrapper">
                                <div class="input-icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V9h11v4zm5 5h-4V9h4v9z" />
                                    </svg>
                                </div>
                                <input id="pf_productName" type="text" class="form-input" name="name" required
                                    minlength="2" placeholder="Enter product name"
                                    value="{{ old('name', $product->name ?? '') }}" autocomplete="off">
                                <div class="input-focus-line"></div>
                            </div>

                            @error('name')
                                <span class="error-message show" data-error-for="name">{{ $message }}</span>
                            @else
                                <span class="error-message" data-error-for="name"></span>
                            @enderror
                        </div>

                        {{-- Category (search + create) --}}
                        <div class="form-section">
                            <div class="form-label-group">
                                <label class="form-label" for="pf_category_search">Category</label>
                                <span class="form-label-hint">Search / Create</span>
                            </div>

                            <div class="select-wrapper" data-pf-autocomplete="category">
                                <div class="input-icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M10 3H3v7h7V3zm11 0h-7v7h7V3zM10 14H3v7h7v-7zm11 0h-7v7h7v-7z" />
                                    </svg>
                                </div>

                                <input type="text" id="pf_category_search" class="form-input"
                                    name="category_name" placeholder="Search or type to create…"
                                    value="{{ old('category_name', $product->category->name ?? '') }}"
                                    autocomplete="off">
                                <input type="hidden" name="category_id" id="pf_category_id"
                                    value="{{ old('category_id', $product->category_id ?? '') }}">

                                <div class="select-arrow" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M7 10l5 5 5-5z" />
                                    </svg>
                                </div>

                                <div class="pf_dropdown" id="pf_category_results" role="listbox"
                                    aria-label="Category results"></div>
                                <div class="select-focus-line"></div>
                            </div>
                        </div>

                        {{-- Brand (search + create) --}}
                        <div class="form-section">
                            <div class="form-label-group">
                                <label class="form-label" for="pf_brand_search">Brand</label>
                                <span class="form-label-hint">Search / Create</span>
                            </div>

                            <div class="select-wrapper" data-pf-autocomplete="brand">
                                <div class="input-icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M12 2l9 4v6c0 5-3.8 9.7-9 10-5.2-.3-9-5-9-10V6l9-4z" />
                                    </svg>
                                </div>

                                <input type="text" id="pf_brand_search" class="form-input" name="brand_name"
                                    placeholder="Search or type to create…"
                                    value="{{ old('brand_name', $product->brand->name ?? '') }}" autocomplete="off">
                                <input type="hidden" name="brand_id" id="pf_brand_id"
                                    value="{{ old('brand_id', $product->brand_id ?? '') }}">

                                <div class="select-arrow" aria-hidden="true">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M7 10l5 5 5-5z" />
                                    </svg>
                                </div>

                                <div class="pf_dropdown" id="pf_brand_results" role="listbox"
                                    aria-label="Brand results"></div>
                                <div class="select-focus-line"></div>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT --}}
                    <div class="tab-column">
                        {{-- Description --}}
                        <div class="form-section">
                            <div class="form-label-group">
                                <label class="form-label" for="pf_desc">Description</label>
                                <span class="form-label-hint">Optional</span>
                            </div>

                            <div class="textarea-wrapper">
                                <div class="textarea-icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                    </svg>
                                </div>
                                <textarea id="pf_desc" class="form-textarea" name="description" placeholder="Product description…"
                                    rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                                <div class="textarea-focus-line"></div>
                            </div>

                            @error('description')
                                <span class="error-message show" data-error-for="description">{{ $message }}</span>
                            @else
                                <span class="error-message" data-error-for="description"></span>
                            @enderror
                        </div>

                        {{-- Note --}}
                        <div class="form-section">
                            <div class="form-label-group">
                                <label class="form-label" for="pf_note">Internal Note</label>
                                <span class="form-label-hint">Optional</span>
                            </div>

                            <div class="textarea-wrapper">
                                <div class="textarea-icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                                    </svg>
                                </div>
                                <textarea id="pf_note" class="form-textarea" name="note" placeholder="Internal notes for this product…"
                                    rows="3">{{ old('note', $product->note ?? '') }}</textarea>
                                <div class="textarea-focus-line"></div>
                            </div>

                            @error('note')
                                <span class="error-message show" data-error-for="note">{{ $message }}</span>
                            @else
                                <span class="error-message" data-error-for="note"></span>
                            @enderror
                        </div>

                        {{-- Active --}}
                        <div class="form-section">
                            <div class="form-label-group">
                                <label class="form-label" for="pf_is_active">Status</label>
                                <span class="form-label-hint">Visibility</span>
                            </div>

                            <div class="toggle-switch">
                                <input type="checkbox" class="toggle-input" id="pf_is_active" name="is_active"
                                    value="1"
                                    {{ old('is_active', $product->is_active ?? true) ? 'checked' : '' }}>
                                <label class="toggle-label" for="pf_is_active">
                                    <span class="toggle-track"><span class="toggle-thumb"></span></span>
                                    <span class="toggle-text">Active</span>
                                </label>
                            </div>
                            <p class="toggle-help">Inactive products won’t appear in sales.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- =======================
                SCANNER MODAL
            ======================= --}}
            <div class="scanner-overlay" id="pf_scannerOverlay" aria-hidden="true">
                <div class="scanner-modal" role="dialog" aria-modal="true" aria-label="Barcode scanner">
                    <div class="scanner-head">
                        <div>
                            <div class="scanner-title">Scan Barcode</div>
                            <div class="scanner-sub">Use your camera (iOS / Android / Desktop)</div>
                        </div>

                        <button type="button" class="scanner-close" data-pf-scan-close aria-label="Close">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M18.3 5.71L12 12l6.3 6.29-1.41 1.42L12 13.41l-6.89 6.3-1.41-1.42L10.59 12 3.7 5.71 5.11 4.29 12 10.59l6.89-6.3z" />
                            </svg>
                        </button>
                    </div>

                    <div class="scanner-body">
                        <div class="scanner-frame">
                            <video id="pf_scannerVideo" playsinline muted></video>
                            <div class="scanner-reticle"></div>
                        </div>

                        <div class="scanner-actions">
                            <button type="button" class="btn-secondary btn-sm" data-pf-scan-switch>Switch
                                Camera</button>
                            <button type="button" class="btn-secondary btn-sm" data-pf-scan-torch>Torch</button>
                        </div>

                        <small class="muted-text" style="display:block;margin-top:.6rem;">
                            If camera doesn’t open, allow permission in browser settings (HTTPS required).
                        </small>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const input = document.getElementById('pf_barcodeInput');
                    if (!input) return;

                    let timer = null;

                    // Prevent Enter from submitting form
                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    });

                    // When scan finishes (small pause), keep value in input
                    input.addEventListener('input', () => {
                        clearTimeout(timer);

                        timer = setTimeout(() => {
                            const code = input.value.trim();
                            if (!code) return;

                            console.log('SCANNED:', code);

                            // ✅ HOLD the data (do not clear)
                            // If you want to move focus away after scan:
                            // input.blur();
                        }, 150); // increase to 150-250ms if scanner is slow
                    });

                    input.focus();
                });
            </script>



            {{-- ======================================================================
                TAB: STOCK MANAGEMENT (REWRITTEN)
                ✅ Includes inline CSS (scoped)
                ✅ Smooth animations + prefers-reduced-motion safe
                ✅ Responsive table for multi-batch (desktop table, mobile cards)
                ✅ Awesome single-batch UI (glass + chips + quick actions)
                ✅ Fixes undefined variable issues by using safe defaults
            ====================================================================== --}}


            <style>
                /* =======================
                STOCK TAB (Scoped) - Updated with Theme Variables
                ======================= */
                #pf_stock_management {
                    --st-radius: var(--radius, 16px);
                    --st-border: var(--border-color, oklch(0.9 0 0));
                    --st-card: var(--card, oklch(0.205 0 0));
                    --st-bg: var(--accent, oklch(0.269 0 0));
                    --st-text: var(--text-primary, oklch(0.985 0 0));
                    --st-muted: var(--text-muted, oklch(0.708 0 0 / 0.7));
                    --st-accent: var(--accent-color, oklch(0.488 0.243 264.376));
                    --st-ok: var(--success, #22c55e);
                    --st-warn: var(--warning, #f59e0b);
                    --st-danger: var(--danger, #ef4444);

                    --st-shadow: var(--card-shadow, 0 18px 50px rgba(0, 0, 0, .10));
                    --st-shadow-hover: var(--card-shadow-hover, 0 24px 70px rgba(0, 0, 0, .16));
                }

                /* Head / header row */
                #pf_stock_management .st-head {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                    flex-wrap: wrap;
                    margin-bottom: 1rem;
                    padding: 1rem 1.1rem;
                    border: 1px solid var(--st-border);
                    border-radius: var(--st-radius);
                    background: linear-gradient(135deg, var(--st-bg), var(--glass-base, rgba(255, 255, 255, .45)));
                    box-shadow: var(--st-shadow);
                    position: relative;
                    overflow: hidden;
                    animation: stFadeUp .45s ease both;
                }

                #pf_stock_management .st-head::before {
                    content: "";
                    position: absolute;
                    inset: -2px;
                    background:
                        radial-gradient(60% 60% at 20% 0%, var(--accent-glow, rgba(37, 99, 235, .22)), transparent 60%),
                        radial-gradient(70% 70% at 80% 10%, rgba(34, 197, 94, .18), transparent 65%);
                    filter: blur(14px);
                    pointer-events: none;
                    opacity: .9;
                }

                #pf_stock_management .st-head>* {
                    position: relative;
                    z-index: 1;
                }

                #pf_stock_management .st-title {
                    margin: 0;
                    font-weight: 1000;
                    color: var(--st-text);
                    letter-spacing: .2px;
                }

                #pf_stock_management .st-sub {
                    margin: .25rem 0 0;
                    color: var(--st-muted);
                    font-weight: 800;
                    font-size: .9rem;
                }

                #pf_stock_management .st-actions {
                    display: flex;
                    gap: .65rem;
                    flex-wrap: wrap;
                    align-items: center;
                }

                #pf_stock_management .st-btn {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: .55rem;
                    padding: .7rem 1rem;
                    border-radius: calc(var(--st-radius) - 2px);
                    border: 1px solid var(--st-border);
                    background: var(--glass-base, rgba(255, 255, 255, .75));
                    color: var(--st-text);
                    font-weight: 1000;
                    text-decoration: none;
                    cursor: pointer;
                    transition: transform var(--transition-fast, 150ms) ease,
                        box-shadow var(--transition-fast, 150ms) ease,
                        border-color var(--transition-fast, 150ms) ease,
                        background var(--transition-fast, 150ms) ease;
                    user-select: none;
                }

                #pf_stock_management .st-btn svg {
                    width: 16px;
                    height: 16px;
                    fill: currentColor;
                }

                #pf_stock_management .st-btn:hover {
                    transform: translateY(-1px);
                    border-color: var(--st-accent);
                    box-shadow: 0 18px 44px var(--accent-glow, rgba(37, 99, 235, .16));
                }

                #pf_stock_management .st-btn.primary {
                    background: var(--st-accent);
                    border-color: var(--st-accent);
                    color: var(--sidebar-primary-foreground, #fff);
                }

                #pf_stock_management .st-btn.primary:hover {
                    background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
                    box-shadow: 0 18px 44px var(--accent-glow, rgba(37, 99, 235, .22));
                }

                #pf_stock_management .st-chip {
                    display: inline-flex;
                    align-items: center;
                    gap: .45rem;
                    padding: .35rem .7rem;
                    border-radius: 999px;
                    border: 1px solid var(--st-border);
                    background: var(--glass-base, rgba(255, 255, 255, .7));
                    color: var(--st-text);
                    font-weight: 1000;
                    font-size: .78rem;
                    white-space: nowrap;
                }

                #pf_stock_management .st-chip.ok {
                    border-color: rgba(34, 197, 94, .35);
                    background: rgba(34, 197, 94, .10);
                    color: var(--success, #16a34a);
                }

                #pf_stock_management .st-chip.warn {
                    border-color: rgba(245, 158, 11, .40);
                    background: rgba(245, 158, 11, .12);
                    color: var(--warning, #b45309);
                }

                #pf_stock_management .st-chip.danger {
                    border-color: rgba(239, 68, 68, .40);
                    background: rgba(239, 68, 68, .10);
                    color: var(--danger, #b91c1c);
                }

                #pf_stock_management .st-dot {
                    width: 8px;
                    height: 8px;
                    border-radius: 999px;
                    background: currentColor;
                    display: inline-block;
                }

                @keyframes stFadeUp {
                    from {
                        opacity: 0;
                        transform: translateY(8px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @media (prefers-reduced-motion: reduce) {
                    #pf_stock_management .st-head {
                        animation: none;
                    }
                }

                /* =======================
                MULTI-BATCH RESPONSIVE TABLE
                ======================= */
                #pf_stock_management .st-table-wrap {
                    border: 1px solid var(--st-border);
                    border-radius: var(--st-radius);
                    background: var(--glass-base, rgba(255, 255, 255, .85));
                    box-shadow: var(--st-shadow);
                    overflow: hidden;
                    animation: stFadeUp .45s ease both;
                }

                #pf_stock_management .st-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                #pf_stock_management .st-table thead th {
                    text-align: left;
                    padding: .95rem .9rem;
                    font-weight: 1000;
                    color: var(--st-text);
                    background: linear-gradient(135deg, var(--st-bg), var(--glass-base, rgba(255, 255, 255, .45)));
                    border-bottom: 1px solid var(--st-border);
                    white-space: nowrap;
                }

                #pf_stock_management .st-table tbody td {
                    padding: .95rem .9rem;
                    border-bottom: 1px solid var(--st-border);
                    vertical-align: middle;
                    color: var(--st-text);
                }

                #pf_stock_management .st-table tbody tr:hover {
                    background: var(--accent-glow, rgba(37, 99, 235, .04));
                }

                #pf_stock_management .st-mono {
                    font-variant-numeric: tabular-nums;
                    font-weight: 900;
                }

                #pf_stock_management .st-price {
                    font-weight: 1000;
                }

                #pf_stock_management .st-muted {
                    color: var(--st-muted);
                    font-weight: 800;
                    font-size: .86rem;
                }

                #pf_stock_management .st-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: .45rem;
                    padding: .25rem .6rem;
                    border-radius: 999px;
                    font-weight: 1000;
                    font-size: .78rem;
                    border: 1px solid var(--st-border);
                    background: var(--glass-base, rgba(0, 0, 0, .03));
                    white-space: nowrap;
                }

                #pf_stock_management .st-pill.ok {
                    border-color: rgba(34, 197, 94, .35);
                    background: rgba(34, 197, 94, .10);
                    color: var(--success, #16a34a);
                }

                #pf_stock_management .st-pill.warn {
                    border-color: rgba(245, 158, 11, .40);
                    background: rgba(245, 158, 11, .12);
                    color: var(--warning, #b45309);
                }

                #pf_stock_management .st-pill.danger {
                    border-color: rgba(239, 68, 68, .40);
                    background: rgba(239, 68, 68, .10);
                    color: var(--danger, #b91c1c);
                }

                #pf_stock_management .st-row-actions {
                    display: flex;
                    gap: .5rem;
                    flex-wrap: wrap;
                }

                #pf_stock_management .st-icon {
                    width: 38px;
                    height: 38px;
                    border-radius: calc(var(--st-radius) - 2px);
                    border: 1px solid var(--st-border);
                    background: var(--glass-base, rgba(255, 255, 255, .75));
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: transform var(--transition-fast, 150ms) ease,
                        box-shadow var(--transition-fast, 150ms) ease,
                        border-color var(--transition-fast, 150ms) ease;
                }

                #pf_stock_management .st-icon svg {
                    width: 18px;
                    height: 18px;
                    fill: currentColor;
                }

                #pf_stock_management .st-icon:hover {
                    transform: translateY(-1px);
                    border-color: var(--st-accent);
                    box-shadow: 0 16px 36px rgba(0, 0, 0, .12);
                }

                #pf_stock_management .st-icon.edit:hover {
                    background: rgba(14, 165, 233, .12);
                    color: var(--info, #0ea5e9);
                    border-color: rgba(14, 165, 233, .35);
                }

                #pf_stock_management .st-icon.delete:hover {
                    background: rgba(239, 68, 68, .12);
                    color: var(--danger, #ef4444);
                    border-color: rgba(239, 68, 68, .35);
                }

                #pf_stock_management .st-table-scroll {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }

                /* MOBILE: turn rows into cards */
                @media (max-width: 900px) {
                    #pf_stock_management .st-table thead {
                        display: none;
                    }

                    #pf_stock_management .st-table,
                    #pf_stock_management .st-table tbody,
                    #pf_stock_management .st-table tr,
                    #pf_stock_management .st-table td {
                        display: block;
                        width: 100%;
                    }

                    #pf_stock_management .st-table tbody tr {
                        border-bottom: 1px solid var(--st-border);
                        padding: .75rem .75rem;
                        background: var(--st-card);
                    }

                    #pf_stock_management .st-table tbody td {
                        border: none;
                        padding: .45rem .25rem;
                        display: flex;
                        justify-content: space-between;
                        gap: 1rem;
                    }

                    #pf_stock_management .st-table tbody td::before {
                        content: attr(data-label);
                        font-weight: 1000;
                        color: var(--st-muted);
                        text-transform: uppercase;
                        font-size: .74rem;
                        letter-spacing: .3px;
                        flex: 0 0 auto;
                    }
                }

                /* =======================
                SINGLE-BATCH CARD + "Responsive table"
                ======================= */
                #pf_stock_management .sb-wrap {
                    display: grid;
                    gap: 1rem;
                    animation: stFadeUp .45s ease both;
                }

                #pf_stock_management .sb-card {
                    border: 1px solid var(--st-border);
                    border-radius: var(--st-radius);
                    background: linear-gradient(180deg, var(--glass-base, rgba(255, 255, 255, .65)), var(--st-card));
                    box-shadow: var(--st-shadow);
                    overflow: hidden;
                    backdrop-filter: blur(10px);
                    transition: transform var(--transition-normal, 250ms) ease,
                        box-shadow var(--transition-normal, 250ms) ease;
                }

                #pf_stock_management .sb-card:hover {
                    transform: translateY(-2px);
                    box-shadow: var(--st-shadow-hover);
                }

                #pf_stock_management .sb-head {
                    padding: 1rem 1.1rem;
                    border-bottom: 1px solid var(--st-border);
                    background: linear-gradient(135deg, var(--st-bg), var(--glass-base, rgba(255, 255, 255, .45)));
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 1rem;
                    flex-wrap: wrap;
                }

                #pf_stock_management .sb-head h4 {
                    margin: 0;
                    font-weight: 1000;
                    color: var(--st-text);
                }

                #pf_stock_management .sb-head .sb-sub {
                    margin: .25rem 0 0;
                    color: var(--st-muted);
                    font-weight: 800;
                    font-size: .9rem;
                }

                #pf_stock_management .sb-badges {
                    display: flex;
                    gap: .5rem;
                    flex-wrap: wrap;
                    justify-content: flex-end;
                }

                #pf_stock_management .sb-body {
                    padding: 1rem 1.1rem 1.1rem;
                }

                #pf_stock_management .sb-grid {
                    border: 1px solid var(--st-border);
                    border-radius: calc(var(--st-radius) - 2px);
                    overflow: hidden;
                    background: var(--glass-base, rgba(255, 255, 255, .78));
                }

                #pf_stock_management .sb-row {
                    display: grid;
                    grid-template-columns: 1.2fr 1fr 1fr 1.1fr;
                    border-bottom: 1px solid var(--st-border);
                }

                #pf_stock_management .sb-row:last-child {
                    border-bottom: none;
                }

                #pf_stock_management .sb-cell {
                    padding: .85rem .9rem;
                    border-right: 1px solid var(--st-border);
                    display: flex;
                    flex-direction: column;
                    gap: .25rem;
                    min-width: 0;
                }

                #pf_stock_management .sb-row .sb-cell:last-child {
                    border-right: none;
                }

                #pf_stock_management .sb-label {
                    font-weight: 1000;
                    color: var(--st-muted);
                    text-transform: uppercase;
                    font-size: .74rem;
                    letter-spacing: .3px;
                }

                #pf_stock_management .sb-value {
                    font-weight: 1000;
                    color: var(--st-text);
                    font-size: 1rem;
                }

                #pf_stock_management .sb-value.low {
                    color: var(--danger, #b91c1c);
                }

                #pf_stock_management .sb-value.mono {
                    font-variant-numeric: tabular-nums;
                }

                #pf_stock_management .sb-price {
                    display: flex;
                    flex-wrap: wrap;
                    gap: .5rem .75rem;
                    align-items: baseline;
                }

                #pf_stock_management .sb-now {
                    font-weight: 1100;
                    font-size: 1.12rem;
                }

                #pf_stock_management .sb-was {
                    font-weight: 1000;
                    color: var(--st-muted);
                    text-decoration: line-through;
                    font-size: .9rem;
                }

                #pf_stock_management .sb-chips {
                    display: flex;
                    flex-wrap: wrap;
                    gap: .45rem;
                    margin-top: .25rem;
                }

                #pf_stock_management .sb-actions {
                    margin-top: 1rem;
                    display: flex;
                    justify-content: space-between;
                    gap: 1rem;
                    flex-wrap: wrap;
                    align-items: center;
                }

                #pf_stock_management .sb-cta {
                    border: 1px dashed var(--st-border);
                    border-radius: var(--st-radius);
                    padding: 1rem 1.1rem;
                    background: linear-gradient(135deg, var(--accent-glow, rgba(37, 99, 235, .06)), rgba(34, 197, 94, .06));
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                    flex-wrap: wrap;
                }

                #pf_stock_management .sb-cta p {
                    margin: 0;
                    font-weight: 1000;
                    color: var(--st-text);
                }

                #pf_stock_management .sb-cta small {
                    display: block;
                    margin-top: .25rem;
                    color: var(--st-muted);
                    font-weight: 800;
                }

                @media (max-width: 900px) {
                    #pf_stock_management .sb-row {
                        grid-template-columns: 1fr 1fr;
                    }
                }

                @media (max-width: 560px) {
                    #pf_stock_management .sb-row {
                        grid-template-columns: 1fr;
                    }

                    #pf_stock_management .sb-cell {
                        border-right: none;
                        border-bottom: 1px solid var(--st-border);
                    }

                    #pf_stock_management .sb-row .sb-cell:last-child {
                        border-bottom: none;
                    }
                }

                /* Minor utility */
                #pf_stock_management .inline-form {
                    display: inline;
                }

                /* Focus styles for accessibility */
                #pf_stock_management .st-btn:focus,
                #pf_stock_management .st-icon:focus,
                #pf_stock_management .sb-cta a:focus {
                    outline: 2px solid var(--ring, oklch(0.556 0 0));
                    outline-offset: 2px;
                }
            </style>

            <div class="tab-pane" id="pf_stock_management" role="tabpanel">

                {{-- =======================
                    MULTIPLE BATCHES
                ======================= --}}
                @if ($isEdit && $hasMultipleBatches)

                    <div class="st-head">
                        <div>
                            <h4 class="st-title">Product Batches</h4>
                            <p class="st-sub">Manage stock, pricing, expiry & channels per batch.</p>
                        </div>
                        <div class="st-actions">
                            <span class="st-chip info">
                                <span class="st-dot"></span>
                                {{ $batches->count() }} Batches
                            </span>
                            <a href="{{ route('product.batches.create', ['product' => $product->id]) }}"
                                class="st-btn primary">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                Add New Batch
                            </a>
                        </div>
                    </div>

                    <div class="st-table-wrap">
                        <div class="st-table-scroll">
                            <table class="st-table" data-responsive-table>
                                <thead>
                                    <tr>
                                        <th>Batch</th>
                                        <th>Buy</th>
                                        <th>Original</th>
                                        <th>Discount</th>
                                        <th>Sell</th>
                                        <th>Qty</th>
                                        <th>Expiry</th>
                                        <th style="width:140px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($batches as $b)
                                        @php
                                            $expiryDate = $b->expiry_date
                                                ? \Carbon\Carbon::parse($b->expiry_date)
                                                : null;
                                            $isExpired = $expiryDate ? $expiryDate->isPast() : false;
                                            $isExpiringSoon = $expiryDate
                                                ? $expiryDate->isFuture() && $expiryDate->diffInDays(now()) <= 30
                                                : false;

                                            // $qty = (float) ($b->quantity ?? 0);
                                            $qty = $b->stock_qty ?? 0;
                                            $low = $qty <= ($b->low_stock_alert ?? 0);

                                            $discountLabel = '—';
                                            if (($b->discounted_price ?? 0) != 0) {
                                                $discountLabel = number_format((float) $b->discounted_price, 2) . ' tk';
                                            } elseif (($b->discount_percentage ?? 0) != 0) {
                                                $discountLabel =
                                                    number_format((float) $b->discount_percentage, 2) . '%';
                                            }
                                        @endphp
                                        {{-- @php
                                            $qty = $batch->stock_qty ?? 0;
                                            $lowStock = $qty <= ($batch->low_stock_alert ?? 0);
                                        @endphp --}}


                                        <tr class="batch-row">
                                            <td data-label="Batch">
                                                <div style="display:flex;flex-direction:column;gap:.15rem;">
                                                    <strong class="st-mono">{{ $b->batch_no ?? 'N/A' }}</strong>
                                                    <span class="st-muted">SKU: {{ $b->batch_sku ?? 'N/A' }}</span>
                                                </div>
                                            </td>

                                            <td data-label="Buy" class="st-mono">
                                                {{ number_format((float) $b->buy_price, 2) }} tk</td>
                                            <td data-label="Original" class="st-mono">
                                                {{ number_format((float) $b->original_sell_price, 2) }} tk</td>
                                            <td data-label="Discount" class="st-mono">{{ $discountLabel }}</td>

                                            <td data-label="Sell">
                                                <strong
                                                    class="st-price st-mono">{{ number_format((float) $b->sell_price, 2) }}
                                                    tk</strong>
                                            </td>

                                            <td data-label="Qty"> <span class="st-pill {{ $low ? 'danger' : 'ok' }}">
                                                    <span class="st-dot"></span> <span
                                                        class="st-mono">{{ number_format($stock[$b->id] ?? 0, 4) ?? 'Null' }}</span>
                                                    <span class="st-muted"
                                                        style="font-size:.78rem;">{{ $b->unit ?? '' }}</span> </span>
                                            </td>


                                            <td data-label="Expiry">
                                                @if ($expiryDate)
                                                    <span
                                                        class="st-pill {{ $isExpired ? 'danger' : ($isExpiringSoon ? 'warn' : 'ok') }}">
                                                        <span class="st-dot"></span>
                                                        {{ $expiryDate->format('M d, Y') }}
                                                    </span>
                                                @else
                                                    <span class="st-pill info"><span class="st-dot"></span>N/A</span>
                                                @endif
                                            </td>

                                            <td data-label="Actions">
                                                <div class="st-row-actions">
                                                    <a href="{{ route('product.batches.edit', $b->id) }}"
                                                        class="st-icon edit" title="Edit Batch"
                                                        aria-label="Edit batch">
                                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                                            <path
                                                                d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                        </svg>
                                                    </a>

                                                    <form action="{{ route('product.batches.destroy', $b->id) }}"
                                                        method="POST" class="inline-form"
                                                        data-pf-confirm="delete-batch">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="st-icon delete"
                                                            title="Delete Batch" aria-label="Delete batch">
                                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                                <path
                                                                    d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- =======================
                    SINGLE BATCH (Awesome UI)
                ======================= --}}
                @elseif($isEdit && $hasSingleBatch)
                    @php
                        $expiryDate = $batch->expiry_date ? \Carbon\Carbon::parse($batch->expiry_date) : null;
                        $isExpired = $expiryDate ? $expiryDate->isPast() : false;
                        $isExpiringSoon = $expiryDate
                            ? $expiryDate->isFuture() && $expiryDate->diffInDays(now()) <= 30
                            : false;

                        // Safe defaults (prevents Undefined variable issues)
                        $active = (bool) ($batch->is_active ?? true);
                        $qty = (float) ($batch->stock_qty ?? 0);
                        $lowStock = $qty < 10;

                        $original = (float) ($batch->original_sell_price ?? 0);
                        $sell = (float) ($batch->sell_price ?? 0);
                        $discounted = (float) ($batch->discounted_price ?? 0);

                        $nowPrice = $discounted > 0 ? $discounted : ($sell > 0 ? $sell : $original);
                        $hasDiscount = $discounted > 0 && $original > 0 && $discounted < $original;
                        $discountPct = $hasDiscount ? round((($original - $discounted) / $original) * 100, 1) : null;

                        $channels = [
                            'Online' => (bool) ($batch->is_online ?? false),
                            'Offline' => (bool) ($batch->is_offline ?? false),
                            'POS' => (bool) ($batch->is_pos ?? false),
                        ];
                    @endphp



                    <div class="st-head">
                        <div>
                            <h4 class="st-title">Current Batch</h4>
                            <p class="st-sub">
                                Batch No: <strong class="st-mono">{{ $batch->batch_no ?? 'No Batch No.' }}</strong>
                                @if (!empty($batch->batch_sku))
                                    <span style="margin:0 .35rem;opacity:.6;">•</span>
                                    SKU: <strong class="st-mono">{{ $batch->batch_sku }}</strong>
                                @endif
                            </p>
                        </div>
                        <div class="st-actions">
                            <span class="st-chip {{ $active ? 'ok' : 'danger' }}"><span
                                    class="st-dot"></span>{{ $active ? 'Active' : 'Inactive' }}</span>
                            <span
                                class="st-chip {{ $isExpired ? 'danger' : ($isExpiringSoon ? 'warn' : 'ok') }}"><span
                                    class="st-dot"></span>
                                {{ $isExpired ? 'Expired' : ($isExpiringSoon ? 'Expiring Soon' : 'Fresh') }}
                            </span>
                            <span class="st-chip {{ $lowStock ? 'danger' : 'ok' }}"><span
                                    class="st-dot"></span>{{ $lowStock ? 'Low Stock' : 'Stock OK' }}</span>
                        </div>
                    </div>


                    <div class="sb-wrap">
                        <div class="sb-card">
                            <div class="sb-head">
                                <div>
                                    <h4>Batch Overview</h4>
                                    <p class="sb-sub">Quick view of pricing, quantity, expiry, channels.</p>
                                </div>
                                <div class="sb-badges">
                                    @if ($hasDiscount)
                                        <span class="st-chip warn"><span
                                                class="st-dot"></span>-{{ $discountPct }}%</span>
                                    @endif
                                    @if (($batch->is_free_offer_active ?? false) && $batch->free_product_id)
                                        <span class="st-chip info"><span class="st-dot"></span>Free Offer</span>
                                    @endif
                                </div>
                            </div>

                            <div class="sb-body">
                                <div class="sb-grid">
                                    <div class="sb-row">
                                        <div class="sb-cell">
                                            <div class="sb-label">Now Price</div>
                                            <div class="sb-price">
                                                <div class="sb-value mono sb-now">{{ number_format($nowPrice, 2) }} tk
                                                </div>
                                                @if ($hasDiscount)
                                                    <div class="sb-was mono">{{ number_format($original, 2) }} tk
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="sb-cell">
                                            <div class="sb-label">Buy Price</div>
                                            <div class="sb-value mono">
                                                {{ number_format((float) $batch->buy_price, 2) }} tk</div>
                                        </div>

                                        <div class="sb-cell">
                                            <div class="sb-label">Quantity</div>
                                            <div class="sb-value mono {{ $lowStock ? 'low' : '' }}">
                                                {{ number_format($totalStock, 4) }} {{ $batch->unit ?? '' }}
                                            </div>
                                        </div>

                                        <div class="sb-cell">
                                            <div class="sb-label">Expiry Date</div>
                                            @if ($expiryDate)
                                                <div class="sb-value mono"
                                                    style="{{ $isExpired ? 'color:var(--st-danger);' : ($isExpiringSoon ? 'color:var(--st-warn);' : '') }}">
                                                    {{ $expiryDate->format('M d, Y') }}
                                                </div>
                                            @else
                                                <div class="sb-value">N/A</div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="sb-row">
                                        <div class="sb-cell">
                                            <div class="sb-label">Wholesale</div>
                                            <div class="sb-value mono">
                                                {{ $batch->whole_sell_price ? number_format((float) $batch->whole_sell_price, 2) . ' tk' : '—' }}
                                            </div>
                                            <div class="st-muted">Min: {{ $batch->whole_sell_min_qty ?? '—' }} • Max:
                                                {{ $batch->whole_sell_max_qty ?? '—' }}</div>
                                        </div>

                                        <div class="sb-cell">
                                            <div class="sb-label">Customer Wholesale</div>
                                            <div class="sb-value mono">
                                                {{ $batch->customer_whole_price ? number_format((float) $batch->customer_whole_price, 2) . ' tk' : '—' }}
                                            </div>
                                            <div class="st-muted">Min: {{ $batch->customer_whole_min_qty ?? '—' }} •
                                                Max: {{ $batch->customer_whole_max_qty ?? '—' }}</div>
                                        </div>

                                        <div class="sb-cell">
                                            <div class="sb-label">Manufacture</div>
                                            <div class="sb-value mono">
                                                {{ $batch->manufacture_date ? optional($batch->manufacture_date)->format('M d, Y') : '—' }}
                                            </div>
                                        </div>

                                        <div class="sb-cell">
                                            <div class="sb-label">Channels</div>
                                            <div class="sb-chips">
                                                @foreach ($channels as $label => $on)
                                                    <span class="st-pill {{ $on ? 'ok' : '' }}">{{ $label }}:
                                                        {{ $on ? 'On' : 'Off' }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    @if (!empty($batch->notes))
                                        <div class="sb-row">
                                            <div class="sb-cell" style="grid-column: 1 / -1;">
                                                <div class="sb-label">Notes</div>
                                                <div class="sb-value"
                                                    style="white-space: normal; line-height: 1.55; font-weight: 900;">
                                                    {{ $batch->notes }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (($batch->is_free_offer_active ?? false) && $batch->free_product_id)
                                        <div class="sb-row">
                                            <div class="sb-cell" style="grid-column: 1 / -1;">
                                                <div class="sb-label">Free Offer</div>
                                                <div class="sb-value" style="white-space: normal;">
                                                    Buy <strong
                                                        class="st-mono">{{ $batch->free_buy_qty ?? 0 }}</strong>
                                                    get <strong class="st-mono">{{ $batch->free_qty ?? 0 }}</strong>
                                                    free
                                                    @if ($batch->freeProduct)
                                                        — <span class="st-muted"
                                                            style="font-weight:1000;">{{ $batch->freeProduct->name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="sb-actions">
                                    <a href="{{ route('product.batches.edit', $batch->id) }}" class="st-btn">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path
                                                d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                        </svg>
                                        Edit This Batch
                                    </a>

                                    <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
                                        <button type="button" class="st-btn" style="padding:.65rem .9rem;"
                                            onclick="navigator.clipboard?.writeText(@json($batch->batch_no ?? '')); this.blur();">
                                            Copy Batch No
                                        </button>

                                        @if (!empty($batch->batch_sku))
                                            <button type="button" class="st-btn" style="padding:.65rem .9rem;"
                                                onclick="navigator.clipboard?.writeText(@json($batch->batch_sku)); this.blur();">
                                                Copy SKU
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sb-cta">
                            <div>
                                <p>Add another batch to manage stock separately.</p>
                                <small>Useful for different expiry dates, prices, or channels.</small>
                            </div>
                            <a href="{{ route('product.batches.create', ['product' => $product->id]) }}"
                                class="st-btn primary">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                Add New Batch
                            </a>
                        </div>
                    </div>

                    {{-- =======================
                    NO BATCHES / CREATE MODE
                ======================= --}}
                @else
                    <div class="create-mode-message">
                        <div class="message-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M20 8h-3V4H3c-1.1 0-1.1 0-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4z" />
                            </svg>
                        </div>
                        <h4>No Stock Batches</h4>
                        <p>
                            @if ($isEdit)
                                No stock batches found. Add the first batch to manage stock.
                            @else
                                After creating the product, you can add stock batches from the edit page.
                            @endif
                        </p>
                        @if ($isEdit)
                            <a href="{{ route('product.batches.create', ['product' => $product->id]) }}"
                                class="btn-primary">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                </svg>
                                Add First Batch
                            </a>
                        @endif
                    </div>
                @endif

            </div>

            {{-- =======================
                TAB: PRODUCT STATUS
            ======================= --}}
            {{-- =======================
                TAB: PRODUCT STATUS (Awesome UX + Inline Style + Bug-Free)
                Drop-in replacement for your whole tab block.
                ✅ Works even if $productStatuses is missing (safe defaults)
                ✅ Includes product context line + link + copy uuid
                ✅ Smooth responsive table (desktop) + card rows (mobile)
                ✅ Search + filter (active/all) client-side (no backend required)
                ✅ Confirm delete (safe), toast, empty state
            ======================= --}}


            {{-- Inline styles (scoped) --}}
            <style>
                #pf_product_status {
                    --ps-radius: calc(var(--radius, 12px) + 6px);
                    --ps-border: var(--border-color, oklch(0.9 0 0));
                    --ps-card: var(--card, oklch(0.205 0 0));
                    --ps-accent: var(--accent-color, oklch(0.488 0.243 264.376));
                    --ps-bg: var(--accent, oklch(0.269 0 0));
                    --ps-text: var(--text-primary, oklch(0.985 0 0));
                    --ps-muted: var(--text-secondary, oklch(0.708 0 0));
                    --ps-ok: var(--success, #22c55e);
                    --ps-warn: var(--warning, #f59e0b);
                    --ps-danger: var(--danger, #ef4444);
                    --ps-shadow: var(--card-shadow, 0 18px 50px rgba(0, 0, 0, .10));
                    --ps-shadow-hover: var(--card-shadow-hover, 0 24px 70px rgba(0, 0, 0, .16));
                }

                /* container */
                #pf_product_status .ps-wrap {
                    margin: 1rem 0;
                    display: grid;
                    gap: 1rem;
                }

                /* header */
                #pf_product_status .ps-card {
                    border: 1px solid var(--ps-border);
                    border-radius: var(--ps-radius);
                    background: linear-gradient(180deg, var(--glass-base, rgba(255, 255, 255, .65)), var(--ps-card));
                    box-shadow: var(--ps-shadow);
                    overflow: hidden;
                    backdrop-filter: blur(10px);
                    animation: psIn .35s ease both;
                }

                @keyframes psIn {
                    from {
                        opacity: 0;
                        transform: translateY(8px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                @media (prefers-reduced-motion: reduce) {
                    #pf_product_status .ps-card {
                        animation: none;
                    }
                }

                #pf_product_status .ps-head {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 1rem;
                    padding: 1.1rem 1.25rem;
                    border-bottom: 1px solid var(--ps-border);
                    background: linear-gradient(135deg, var(--ps-bg), var(--glass-base, rgba(255, 255, 255, .35)));
                    flex-wrap: wrap;
                }

                #pf_product_status .ps-title {
                    margin: 0;
                    font-weight: 1000;
                    letter-spacing: .2px;
                    color: var(--ps-text);
                    display: flex;
                    align-items: center;
                    gap: .6rem;
                }

                #pf_product_status .ps-title svg {
                    width: 20px;
                    height: 20px;
                    fill: currentColor;
                    color: var(--ps-accent);
                }

                #pf_product_status .ps-sub {
                    margin: .25rem 0 0;
                    color: var(--ps-muted);
                    font-weight: 800;
                    font-size: .92rem;
                }

                /* context bar */
                #pf_product_status .ps-context {
                    margin: .9rem 1.25rem 0;
                    padding: .8rem 1rem;
                    border-radius: calc(var(--ps-radius) - 4px);
                    border: 1px solid var(--ps-border);
                    background: var(--glass-base, rgba(0, 0, 0, .02));
                    display: flex;
                    align-items: center;
                    gap: .75rem;
                    flex-wrap: wrap;
                }

                #pf_product_status .ps-dot {
                    width: 10px;
                    height: 10px;
                    border-radius: 999px;
                    background: var(--ps-muted);
                }

                #pf_product_status .ps-context strong {
                    color: var(--ps-text);
                    font-weight: 1000;
                }

                #pf_product_status .ps-context a {
                    color: var(--ps-accent);
                    font-weight: 1000;
                    text-decoration: none;
                }

                #pf_product_status .ps-context a:hover {
                    text-decoration: underline;
                }

                #pf_product_status .ps-chip {
                    display: inline-flex;
                    align-items: center;
                    gap: .45rem;
                    padding: .25rem .6rem;
                    border-radius: 999px;
                    border: 1px solid var(--ps-border);
                    background: var(--glass-base, rgba(255, 255, 255, .65));
                    font-weight: 900;
                    font-size: .78rem;
                    color: var(--ps-text);
                    user-select: none;
                    white-space: nowrap;
                }

                #pf_product_status .ps-chip.ok {
                    border-color: rgba(34, 197, 94, .35);
                    background: rgba(34, 197, 94, .10);
                    color: var(--success, #16a34a);
                }

                #pf_product_status .ps-chip.off {
                    border-color: rgba(107, 114, 128, .25);
                    background: rgba(107, 114, 128, .10);
                    color: var(--text-muted, #6b7280);
                }

                /* buttons */
                #pf_product_status .ps-btn {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    gap: .55rem;
                    padding: .7rem 1rem;
                    border-radius: calc(var(--ps-radius) - 4px);
                    border: 1px solid var(--ps-border);
                    font-weight: 1000;
                    text-decoration: none;
                    cursor: pointer;
                    transition: transform var(--transition-fast, 150ms) ease,
                        box-shadow var(--transition-fast, 150ms) ease,
                        border-color var(--transition-fast, 150ms) ease,
                        background var(--transition-fast, 150ms) ease;
                    user-select: none;
                    white-space: nowrap;
                }

                #pf_product_status .ps-btn svg {
                    width: 16px;
                    height: 16px;
                    fill: currentColor;
                }

                #pf_product_status .ps-btn.primary {
                    background: var(--ps-accent);
                    border-color: var(--ps-accent);
                    color: var(--sidebar-primary-foreground, #fff);
                }

                #pf_product_status .ps-btn.primary:hover {
                    transform: translateY(-1px);
                    background: var(--accent-hover, oklch(0.488 0.243 264.376 / 0.8));
                    box-shadow: 0 18px 44px var(--accent-glow, rgba(37, 99, 235, .20));
                }

                #pf_product_status .ps-btn.ghost {
                    background: var(--glass-base, rgba(255, 255, 255, .70));
                    color: var(--ps-text);
                }

                #pf_product_status .ps-btn.ghost:hover {
                    transform: translateY(-1px);
                    border-color: var(--ps-accent);
                    box-shadow: 0 16px 36px rgba(0, 0, 0, .10);
                }

                /* toolbar */
                #pf_product_status .ps-toolbar {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: .75rem;
                    padding: 1rem 1.25rem 0;
                    flex-wrap: wrap;
                }

                #pf_product_status .ps-search {
                    flex: 1;
                    min-width: 240px;
                    display: flex;
                    align-items: center;
                    gap: .6rem;
                    padding: .65rem .8rem;
                    border-radius: calc(var(--ps-radius) - 4px);
                    border: 1px solid var(--ps-border);
                    background: var(--glass-base, rgba(255, 255, 255, .75));
                }

                #pf_product_status .ps-search svg {
                    width: 18px;
                    height: 18px;
                    fill: currentColor;
                    color: var(--ps-muted);
                }

                #pf_product_status .ps-search input {
                    width: 100%;
                    border: none;
                    outline: none;
                    background: transparent;
                    color: var(--ps-text);
                    font-weight: 800;
                }

                #pf_product_status .ps-filters {
                    display: flex;
                    gap: .5rem;
                    flex-wrap: wrap;
                    align-items: center;
                }

                #pf_product_status .ps-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: .45rem;
                    padding: .5rem .75rem;
                    border-radius: 999px;
                    border: 1px solid var(--ps-border);
                    background: var(--glass-base, rgba(255, 255, 255, .75));
                    color: var(--ps-text);
                    font-weight: 1000;
                    cursor: pointer;
                    user-select: none;
                    transition: all var(--transition-fast, 150ms) ease;
                }

                #pf_product_status .ps-pill[data-active="1"] {
                    border-color: var(--accent-glow, rgba(37, 99, 235, .35));
                    background: var(--accent-glow, rgba(37, 99, 235, .10));
                    color: var(--ps-accent);
                }

                /* table */
                #pf_product_status .ps-table-wrap {
                    padding: 1rem 1.25rem 1.25rem;
                }

                #pf_product_status table.ps-table {
                    width: 100%;
                    border-collapse: separate;
                    border-spacing: 0;
                    overflow: hidden;
                    border-radius: calc(var(--ps-radius) - 4px);
                    border: 1px solid var(--ps-border);
                    background: var(--glass-base, rgba(255, 255, 255, .75));
                }

                #pf_product_status table.ps-table thead th {
                    text-align: left;
                    padding: .9rem .95rem;
                    font-size: .78rem;
                    letter-spacing: .3px;
                    text-transform: uppercase;
                    color: var(--ps-muted);
                    border-bottom: 1px solid var(--ps-border);
                    background: var(--glass-base, rgba(0, 0, 0, .02));
                    font-weight: 1000;
                    white-space: nowrap;
                }

                #pf_product_status table.ps-table tbody td {
                    padding: .95rem;
                    border-bottom: 1px solid var(--ps-border);
                    vertical-align: top;
                    color: var(--ps-text);
                    font-weight: 800;
                }

                #pf_product_status table.ps-table tbody tr:hover {
                    background: var(--accent-glow, rgba(37, 99, 235, .04));
                }

                #pf_product_status table.ps-table tbody tr:last-child td {
                    border-bottom: none;
                }

                #pf_product_status .ps-badge {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    padding: .35rem .7rem;
                    border-radius: 999px;
                    font-weight: 1000;
                    font-size: .78rem;
                    letter-spacing: .25px;
                    text-transform: uppercase;
                    white-space: nowrap;
                }

                #pf_product_status .ps-muted {
                    color: var(--ps-muted);
                    font-weight: 800;
                }

                #pf_product_status .ps-kv {
                    font-size: .82rem;
                    margin-top: .25rem;
                    color: var(--ps-muted);
                    font-weight: 900;
                }

                /* actions */
                #pf_product_status .ps-actions {
                    display: flex;
                    gap: .5rem;
                    flex-wrap: wrap;
                    align-items: center;
                    justify-content: flex-end;
                }

                #pf_product_status .ps-iconbtn {
                    width: 40px;
                    height: 40px;
                    border-radius: calc(var(--ps-radius) - 6px);
                    border: 1px solid var(--ps-border);
                    background: var(--glass-base, rgba(255, 255, 255, .75));
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: transform var(--transition-fast, 150ms) ease,
                        box-shadow var(--transition-fast, 150ms) ease,
                        border-color var(--transition-fast, 150ms) ease;
                }

                #pf_product_status .ps-iconbtn:hover {
                    transform: translateY(-1px);
                    border-color: var(--ps-accent);
                    box-shadow: 0 14px 28px rgba(0, 0, 0, .12);
                }

                #pf_product_status .ps-iconbtn svg {
                    width: 18px;
                    height: 18px;
                    fill: currentColor;
                }

                #pf_product_status .ps-iconbtn.edit {
                    color: var(--ps-accent);
                }

                #pf_product_status .ps-iconbtn.del {
                    color: var(--ps-danger);
                }

                /* empty state */
                #pf_product_status .ps-empty {
                    padding: 1.5rem 1.25rem 1.25rem;
                }

                #pf_product_status .ps-empty .box {
                    border: 1px dashed var(--ps-border);
                    border-radius: var(--ps-radius);
                    padding: 1.2rem 1.2rem;
                    background: linear-gradient(135deg, var(--accent-glow, rgba(37, 99, 235, .06)), rgba(34, 197, 94, .06));
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 1rem;
                    flex-wrap: wrap;
                }

                #pf_product_status .ps-empty h4 {
                    margin: 0;
                    font-weight: 1000;
                    color: var(--ps-text);
                }

                #pf_product_status .ps-empty p {
                    margin: .35rem 0 0;
                    color: var(--ps-muted);
                    font-weight: 800;
                    max-width: 60ch;
                }

                /* responsive: switch to cards */
                @media (max-width: 860px) {
                    #pf_product_status .ps-hide-sm {
                        display: none !important;
                    }

                    #pf_product_status table.ps-table thead {
                        display: none;
                    }

                    #pf_product_status table.ps-table,
                    #pf_product_status table.ps-table tbody,
                    #pf_product_status table.ps-table tr,
                    #pf_product_status table.ps-table td {
                        display: block;
                        width: 100%;
                    }

                    #pf_product_status table.ps-table tr {
                        border-bottom: 1px solid var(--ps-border);
                        padding: .9rem .95rem;
                        background: var(--ps-card);
                    }

                    #pf_product_status table.ps-table tr:last-child {
                        border-bottom: none;
                    }

                    #pf_product_status table.ps-table td {
                        border: none;
                        padding: .35rem 0;
                    }

                    #pf_product_status .ps-rowgrid {
                        display: grid;
                        grid-template-columns: 1fr;
                        gap: .35rem;
                    }

                    #pf_product_status .ps-actions {
                        justify-content: flex-start;
                        margin-top: .6rem;
                    }
                }

                /* toast */
                #pf_product_status .ps-toast {
                    position: fixed;
                    right: 16px;
                    bottom: 16px;
                    z-index: 9999;
                    min-width: 240px;
                    max-width: 420px;
                    padding: 12px 14px;
                    border-radius: calc(var(--ps-radius) - 4px);
                    border: 1px solid var(--ps-border);
                    background: var(--glass-base, rgba(255, 255, 255, .92));
                    box-shadow: var(--dropdown-shadow, 0 18px 50px rgba(0, 0, 0, .16));
                    display: none;
                    align-items: flex-start;
                    gap: 10px;
                }

                #pf_product_status .ps-toast.show {
                    display: flex;
                    animation: psToastIn .18s ease;
                }

                @keyframes psToastIn {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }

                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                #pf_product_status .ps-toast .ic {
                    width: 22px;
                    height: 22px;
                }

                #pf_product_status .ps-toast .ic svg {
                    width: 22px;
                    height: 22px;
                    fill: currentColor;
                }

                #pf_product_status .ps-toast.success {
                    border-left: 4px solid var(--ps-ok);
                }

                #pf_product_status .ps-toast.error {
                    border-left: 4px solid var(--ps-danger);
                }

                #pf_product_status .ps-toast .t1 {
                    margin: 0;
                    font-weight: 1000;
                    color: var(--ps-text);
                }

                #pf_product_status .ps-toast .t2 {
                    margin: .15rem 0 0;
                    color: var(--ps-muted);
                    font-weight: 800;
                    font-size: .9rem;
                }

                /* Focus styles for accessibility */
                #pf_product_status .ps-btn:focus,
                #pf_product_status .ps-iconbtn:focus,
                #pf_product_status .ps-search input:focus,
                #pf_product_status .ps-pill:focus {
                    outline: 2px solid var(--ring, oklch(0.556 0 0));
                    outline-offset: 2px;
                }

                /* Additional mobile adjustments */
                @media (max-width: 768px) {
                    #pf_product_status .ps-head {
                        flex-direction: column;
                        align-items: flex-start;
                        gap: 0.75rem;
                    }

                    #pf_product_status .ps-toolbar {
                        flex-direction: column;
                        align-items: stretch;
                    }

                    #pf_product_status .ps-search {
                        min-width: 100%;
                    }

                    #pf_product_status .ps-context {
                        flex-direction: column;
                        align-items: flex-start;
                        gap: 0.5rem;
                    }
                }

                /* Toggle switch */
                .locf-switch {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-top: 6px;
                }

                .locf-toggle {
                    width: 46px;
                    height: 26px;
                    border-radius: 999px;
                    background: color-mix(in oklch, var(--muted) 80%, var(--card) 20%);
                    border: 1px solid var(--border-color);
                    position: relative;
                    cursor: pointer;
                    transition: background 150ms;
                }

                .locf-toggle::after {
                    content: "";
                    width: 22px;
                    height: 22px;
                    border-radius: 50%;
                    background: var(--card);
                    border: 1px solid var(--border-color);
                    position: absolute;
                    top: 1px;
                    left: 1px;
                    transition: transform 150ms;
                }

                .locf-check {
                    display: none;
                }

                .locf-check:checked+.locf-toggle {
                    background: color-mix(in oklch, var(--success) 55%, var(--card) 45%);
                    border-color: color-mix(in oklch, var(--success) 55%, var(--border-color) 45%);
                }

                .locf-check:checked+.locf-toggle::after {
                    transform: translateX(20px);
                }
            </style>

            @php
                // Safe defaults (prevents "undefined variable" bugs)
                $isEdit = $isEdit ?? isset($product) && $product->exists;
                $productStatuses = $productStatuses ?? collect();
                $product = $product ?? null;

                // helper safe function name (you already have getContrastColorSafe in your form)
                // ensure it exists; if not, fall back to white
                $getTextColor = function ($hex) {
                    try {
                        return function_exists('getContrastColorSafe') ? getContrastColorSafe($hex) : '#FFFFFF';
                    } catch (\Throwable $e) {
                        return '#FFFFFF';
                    }
                };
            @endphp

            <div class="tab-pane" id="pf_product_status" role="tabpanel">


                @if ($isEdit)

                    <div class="ps-wrap">

                        <div class="ps-card">

                            {{-- Header --}}
                            <div class="ps-head">
                                <div>
                                    <h4 class="ps-title">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M4 4h16v4H4V4zm0 6h16v10H4V10zm2 2v6h12v-6H6z" />
                                        </svg>
                                        Product Statuses
                                    </h4>
                                    <div class="ps-sub">Create, preview, search, and manage status badges for this
                                        product.</div>
                                </div>

                                <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                                    <a href="{{ route('product.status.create', ['productUuid' => $product->uuid]) }}"
                                        class="ps-btn primary">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                        </svg>
                                        Add New Status
                                    </a>

                                    <a href="{{ route('products.edit', $product->id) }}" class="ps-btn ghost"
                                        title="Open product">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M10 17l5-5-5-5v10zM4 4h2v16H4V4zm14 0h2v16h-2V4z" />
                                        </svg>
                                        Product Page
                                    </a>
                                </div>
                            </div>

                            {{-- Context line --}}
                            <div class="ps-context" role="note" aria-label="Status context">
                                <span class="ps-dot" style="background: var(--ps-ok);"></span>
                                <strong>For:</strong>

                                <a href="{{ route('products.edit', $product->id) }}" title="Open product edit">
                                    {{ $product->name }}
                                </a>

                                <span class="ps-chip ok" title="Product UUID">{{ $product->uuid }}</span>

                                <button type="button" class="ps-btn ghost" style="padding:.55rem .85rem;"
                                    data-ps-copy="{{ $product->uuid }}">
                                    Copy UUID
                                </button>

                                <span class="ps-chip {{ $productStatuses->count() ? 'ok' : 'off' }}">
                                    {{ $productStatuses->count() }}
                                    Status{{ $productStatuses->count() === 1 ? '' : 'es' }}
                                </span>
                            </div>

                            {{-- Toolbar --}}
                            <div class="ps-toolbar">
                                <div class="ps-search">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zM9.5 14C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                                    </svg>
                                    <input id="ps_search" type="text"
                                        placeholder="Search status name, badge text, slug..." autocomplete="off">
                                </div>

                                <div class="ps-filters" aria-label="Filters">
                                    <span class="ps-pill" data-filter="all" data-active="1">All</span>
                                    <span class="ps-pill" data-filter="active" data-active="0">Active</span>
                                    <span class="ps-pill" data-filter="inactive" data-active="0">Inactive</span>
                                </div>
                            </div>

                            {{-- List / Table --}}
                            @if ($productStatuses->isEmpty())
                                <div class="ps-empty">
                                    <div class="box">
                                        <div>
                                            <h4>No Statuses Added</h4>
                                            <p>Add statuses like “On Sale”, “Featured”, “New Arrival” to highlight your
                                                product. You can reuse templates too.</p>
                                        </div>
                                        <a href="{{ route('product.status.create', ['productUuid' => $product->uuid]) }}"
                                            class="ps-btn primary">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                            </svg>
                                            Add First Status
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="ps-table-wrap">
                                    <table class="ps-table" id="ps_table" data-responsive-table>
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th>Badge</th>
                                                <th class="ps-hide-sm">Description</th>
                                                <th>State</th>
                                                <th style="text-align:right;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($productStatuses as $status)
                                                @php
                                                    $badgeColor = $status->badge_color ?? '#3B82F6';
                                                    $textColor = $getTextColor($badgeColor);
                                                @endphp

                                                <tr data-name="{{ strtolower($status->name ?? '') }}"
                                                    data-slug="{{ strtolower($status->slug ?? '') }}"
                                                    data-badge="{{ strtolower($status->badge_text ?? '') }}"
                                                    data-active="{{ (int) ($status->is_active ? 1 : 0) }}">
                                                    <td>
                                                        <div class="ps-rowgrid">
                                                            <div style="font-weight:1000;">{{ $status->name }}</div>
                                                            <div class="ps-kv">slug: <span
                                                                    class="ps-muted">{{ $status->slug }}</span></div>

                                                            {{-- mobile-only meta --}}
                                                            <div class="ps-hide-lg" style="display:none;"></div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        @if ($status->badge_text)
                                                            <span class="ps-badge"
                                                                style="background-color: {{ $badgeColor }}; color: {{ $textColor }};">
                                                                {{ $status->badge_text }}
                                                            </span>
                                                        @else
                                                            <span class="ps-muted">—</span>
                                                        @endif
                                                    </td>

                                                    <td class="ps-hide-sm">
                                                        <span
                                                            class="ps-muted">{{ $status->description ?: 'No description' }}</span>
                                                    </td>

                                                    <td>
                                                        <!-- Checkbox for toggle -->
                                                        <div class="locf-switch">
                                                            <input type="checkbox" class="locf-check"
                                                                id="status-toggle-{{ $status->id }}"
                                                                data-status-id="{{ $status->id }}"
                                                                @checked($status->is_active)
                                                                onchange="toggleStatus({{ $status->id }})">
                                                            <label class="locf-toggle"
                                                                for="status-toggle-{{ $status->id }}"
                                                                title="Toggle active/inactive"></label>

                                                            <div>
                                                                <div style="font-weight:800;">
                                                                    <span id="status-{{ $status->id }}-text">
                                                                        {{ $status->is_active ? 'Active' : 'Inactive' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td style="text-align:right;">
                                                        <div class="ps-actions">
                                                            <a href="{{ route('product.status.edit', $status->uuid) }}"
                                                                class="ps-iconbtn edit" title="Edit status"
                                                                aria-label="Edit status">
                                                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                                                    <path
                                                                        d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" />
                                                                </svg>
                                                            </a>

                                                            <form
                                                                action="{{ route('product.status.destroy', $status->uuid) }}"
                                                                method="POST" class="inline-form"
                                                                data-ps-confirm="delete-status"
                                                                style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="ps-iconbtn del"
                                                                    title="Delete status" aria-label="Delete status">
                                                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                                                        <path
                                                                            d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                                    </svg>
                                                                </button>
                                                            </form>

                                                            <button type="button" class="ps-btn ghost"
                                                                style="padding:.55rem .85rem;"
                                                                data-ps-copy="{{ $status->slug }}">
                                                                Copy Slug
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div
                                        style="margin-top:.9rem; display:flex; gap:.5rem; flex-wrap:wrap; align-items:center; justify-content:space-between;">
                                        <div class="ps-muted" style="font-weight:900;">
                                            Tip: Use search + filters to find statuses quickly.
                                        </div>
                                        <a href="{{ route('product.status.create', ['productUuid' => $product->uuid]) }}"
                                            class="ps-btn primary">
                                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" />
                                            </svg>
                                            Add Another Status
                                        </a>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- toast --}}
                    <div class="ps-toast" id="ps_toast" role="status" aria-live="polite">
                        <div class="ic" id="ps_toast_ic"></div>
                        <div>
                            <p class="t1" id="ps_toast_t1">Done</p>
                            <p class="t2" id="ps_toast_t2"></p>
                        </div>
                    </div>

                    {{-- Inline JS (scoped + safe) --}}

                    <script>
                        function toggleStatus(statusId) {
                            const checkbox = document.getElementById('status-toggle-' + statusId);
                            const statusText = document.getElementById('status-' + statusId + '-text');

                            // Send AJAX request to toggle the status
                            fetch(`/product/status/${statusId}/toggle`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        is_active: checkbox.checked
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.ok) {
                                        // Update the status text based on the new is_active value
                                        statusText.textContent = data.is_active ? 'Active' : 'Inactive';
                                    } else {
                                        alert('Failed to update status');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Failed to update status');
                                });
                        }
                    </script>

                    <script>
                        (function() {
                            const root = document.getElementById('pf_product_status');
                            if (!root) return;

                            // ---------- Toast ----------
                            const toast = document.getElementById('ps_toast');
                            const t1 = document.getElementById('ps_toast_t1');
                            const t2 = document.getElementById('ps_toast_t2');
                            const ic = document.getElementById('ps_toast_ic');
                            let toastTimer = null;

                            function showToast(type, title, msg) {
                                if (!toast) return;
                                toast.classList.remove('success', 'error', 'show');
                                toast.classList.add(type === 'error' ? 'error' : 'success');
                                t1.textContent = title || (type === 'error' ? 'Error' : 'Success');
                                t2.textContent = msg || '';
                                ic.innerHTML = type === 'error' ?
                                    '<svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>' :
                                    '<svg viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>';

                                toast.classList.add('show');
                                clearTimeout(toastTimer);
                                toastTimer = setTimeout(() => toast.classList.remove('show'), 2200);
                            }

                            // ---------- Copy ----------
                            root.addEventListener('click', async (e) => {
                                const btn = e.target.closest('[data-ps-copy]');
                                if (!btn) return;
                                const value = btn.getAttribute('data-ps-copy') || '';
                                try {
                                    if (navigator.clipboard && value !== '') {
                                        await navigator.clipboard.writeText(value);
                                        showToast('success', 'Copied', value);
                                    } else {
                                        showToast('error', 'Copy failed', 'Clipboard not available.');
                                    }
                                } catch (err) {
                                    showToast('error', 'Copy failed', 'Browser blocked clipboard.');
                                }
                            });

                            // ---------- Delete confirm ----------
                            root.addEventListener('submit', (e) => {
                                const form = e.target.closest('form[data-ps-confirm="delete-status"]');
                                if (!form) return;
                                const ok = window.confirm('Delete this status? This cannot be undone.');
                                if (!ok) e.preventDefault();
                            });

                            // ---------- Search + filter ----------
                            const search = document.getElementById('ps_search');
                            const table = document.getElementById('ps_table');
                            const pills = root.querySelectorAll('.ps-pill');

                            let activeFilter = 'all';

                            function applyFilter() {
                                if (!table) return;
                                const q = (search?.value || '').trim().toLowerCase();

                                const rows = table.querySelectorAll('tbody tr');
                                rows.forEach(tr => {
                                    const name = tr.getAttribute('data-name') || '';
                                    const slug = tr.getAttribute('data-slug') || '';
                                    const badge = tr.getAttribute('data-badge') || '';
                                    const isActive = (tr.getAttribute('data-active') || '0') === '1';

                                    const matchesText = !q || (name.includes(q) || slug.includes(q) || badge.includes(q));
                                    const matchesFilter =
                                        activeFilter === 'all' ||
                                        (activeFilter === 'active' && isActive) ||
                                        (activeFilter === 'inactive' && !isActive);

                                    tr.style.display = (matchesText && matchesFilter) ? '' : 'none';
                                });
                            }

                            if (search) {
                                search.addEventListener('input', applyFilter);
                            }

                            pills.forEach(p => {
                                p.addEventListener('click', () => {
                                    pills.forEach(x => x.setAttribute('data-active', '0'));
                                    p.setAttribute('data-active', '1');
                                    activeFilter = p.getAttribute('data-filter') || 'all';
                                    applyFilter();
                                });
                            });

                            applyFilter();
                        })();
                    </script>
                @else
                    <div class="ps-card" style="margin:1rem 0;">
                        <div class="ps-head">
                            <div>
                                <h4 class="ps-title">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path
                                            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                    </svg>
                                    Product Status Management
                                </h4>
                                <div class="ps-sub">Save the product first — then you’ll be able to add and manage
                                    statuses here.</div>
                            </div>
                        </div>

                        <div class="ps-empty">
                            <div class="box">
                                <div>
                                    <h4>Save First</h4>
                                    <p>After the product is created, come back to this tab to add “On Sale”, “Featured”,
                                        “New Arrival”, etc.</p>
                                </div>
                                <span class="ps-chip off">Create mode</span>
                            </div>
                        </div>
                    </div>
                @endif

            </div>


            {{-- =======================
                TAB: COMMENTS
            ======================= --}}
            {{-- <div class="tab-pane" id="pf_product_comments" role="tabpanel">
                <div class="comments-wrap">
                    <div class="comments-header">
                        <h4 class="comments-title">Comments</h4>
                        <span class="comments-hint">Internal discussion / notes</span>
                    </div>

                    @if ($isEdit)
                        <div class="comment-form" data-comment-form>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="textarea-wrapper">
                                <div class="textarea-icon">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M21 6h-2v9H7l-4 4V6c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2z" />
                                    </svg>
                                </div>
                                <textarea class="form-textarea" name="comment" rows="3" minlength="2" required
                                    placeholder="Write a comment…"></textarea>
                                <div class="textarea-focus-line"></div>
                            </div>

                            <div class="comment-actions">
                                <button type="button" class="btn-primary btn-sm" data-comment-submit>
                                    <svg viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2 21l21-9L2 3v7l15 2-15 2v7z" />
                                    </svg>
                                    Post Comment
                                </button>
                                <small class="muted-text">Shows errors as toast.</small>
                            </div>
                        </div>

                        @php $comments = $product->comments ?? collect(); @endphp
                        <div class="comment-list">
                            @if ($comments->isEmpty())
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <svg viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M21 6h-2v9H7l-4 4V6c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="empty-title">No comments yet</div>
                                        <div class="empty-desc">Add the first comment to keep internal context.</div>
                                    </div>
                                </div>
                            @else
                                @foreach ($comments as $c)
                                    <div class="comment-item">
                                        <div class="comment-meta">
                                            <span class="comment-author">{{ $c->user->name ?? 'User' }}</span>
                                            <span class="comment-dot">•</span>
                                            <span
                                                class="comment-time">{{ optional($c->created_at)->diffForHumans() }}</span>
                                        </div>
                                        <div class="comment-body">{{ $c->comment ?? ($c->body ?? '') }}</div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @else
                        <div class="create-mode-message">
                            <div class="message-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M21 6h-2v9H7l-4 4V6c0-1.1.9-2 2-2h16c1.1 0 2 .9 2 2z" />
                                </svg>
                            </div>
                            <h4>Comments</h4>
                            <p>Save the product first — then you can add internal comments here.</p>
                        </div>
                    @endif
                </div>
            </div> --}}

        </div>
    </div>
</div>

{{-- =======================
    CSS (Same as yours)
======================= --}}
<style>
    /* Keep your full CSS as-is. No changes required. */
</style>

{{-- =======================
    JS (Namespaced / No conflicts)
======================= --}}
<script>
    (function() {
        "use strict";

        const PF = {};
        PF.root = document.querySelector("[data-pf-root]");
        if (!PF.root) return;

        PF.csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
        PF.productId = @json($product->id ?? null);

        PF.qs = (sel) => PF.root.querySelector(sel);
        PF.qsa = (sel) => PF.root.querySelectorAll(sel);

        PF.escapeHtml = (str) => String(str).replace(/[&<>"']/g, s => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        } [s]));

        PF.debounce = (fn, wait = 280) => {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), wait);
            };
        };

        PF.sleep = (ms) => new Promise(r => setTimeout(r, ms));

        // -------- Toasts
        PF.toastStack = document.getElementById("pf_toastStack");
        PF.ICONS = {
            success: `<svg class="toast-icon" viewBox="0 0 24 24"><path fill="currentColor" d="M9 16.2l-3.5-3.5L4 14.2l5 5 11-11-1.5-1.5z"/></svg>`,
            error: `<svg class="toast-icon" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm1 13h-2v-2h2v2zm0-4h-2V7h2v4z"/></svg>`,
            info: `<svg class="toast-icon" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>`,
            warning: `<svg class="toast-icon" viewBox="0 0 24 24"><path fill="currentColor" d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>`
        };

        PF.toast = (type, message, title) => {
            if (!PF.toastStack) return;
            const el = document.createElement("div");
            el.className = `toast toast-${type || "info"}`;
            el.innerHTML = `
      ${PF.ICONS[type] || PF.ICONS.info}
      <div>
        <div class="toast-title">${PF.escapeHtml(title || "Notice")}</div>
        <div class="toast-message">${PF.escapeHtml(message || "")}</div>
      </div>
      <button class="toast-close" type="button" aria-label="Close">
        <svg viewBox="0 0 24 24"><path d="M18.3 5.71L12 12l6.3 6.29-1.41 1.42L12 13.41l-6.89 6.3-1.41-1.42L10.59 12 3.7 5.71 5.11 4.29 12 10.59l6.89-6.3z"/></svg>
      </button>
    `;
            PF.toastStack.appendChild(el);
            el.querySelector(".toast-close")?.addEventListener("click", () => el.remove());
            setTimeout(() => {
                el.style.opacity = "0";
                setTimeout(() => el.remove(), 180);
            }, 5200);
        };

        if (Array.isArray(window.__PF_FLASH_TOASTS__)) {
            window.__PF_FLASH_TOASTS__.forEach(t => PF.toast(t.type || "info", t.message || "", t.title || ""));
            window.__PF_FLASH_TOASTS__ = [];
        }

        // -------- Tabs
        PF.tabBtns = PF.qsa(".tab-btn");
        PF.tabPanes = PF.qsa(".tab-pane");

        PF.setActiveTab = (tabId) => {
            PF.tabBtns.forEach(b => {
                const active = b.dataset.pfTab === tabId;
                b.classList.toggle("active", active);
                b.setAttribute("aria-selected", active ? "true" : "false");
            });
            PF.tabPanes.forEach(p => p.classList.toggle("active", p.id === tabId));
        };

        PF.tabBtns.forEach(btn => btn.addEventListener("click", () => PF.setActiveTab(btn.dataset.pfTab)));

        // -------- Autocomplete (Safe / non-conflicting)
        PF.autocompletes = new Map();
        PF.activeKey = null;

        PF.closeAll = (exceptKey = null) => {
            for (const [k, inst] of PF.autocompletes.entries()) {
                if (exceptKey && k === exceptKey) continue;
                inst.close();
            }
        };

        document.addEventListener("click", (e) => {
            if (!PF.activeKey) return;
            const inst = PF.autocompletes.get(PF.activeKey);
            if (!inst) return;
            if (inst.wrapper.contains(e.target)) return;
            inst.close();
            PF.activeKey = null;
        });

        PF.createAutocomplete = ({
            key,
            wrapperSel,
            inputSel,
            hiddenSel,
            pf_dropdownSel,
            mode,
            minLen = 1
        }) => {
            const wrapper = PF.qs(wrapperSel);
            const input = PF.qs(inputSel);
            const hidden = PF.qs(hiddenSel);
            const pf_dropdown = PF.qs(pf_dropdownSel);
            if (!wrapper || !input || !hidden || !pf_dropdown) return;

            let controller = null;
            let lastQuery = "";

            const close = () => {
                pf_dropdown.classList.remove("show");
                pf_dropdown.innerHTML = "";
            };

            const open = () => {
                PF.closeAll(key);
                pf_dropdown.classList.add("show");
                PF.activeKey = key;
            };

            const setSelection = ({
                id,
                name
            }) => {
                input.value = name || "";
                hidden.value = id || "";
                close();
            };

            pf_dropdown.addEventListener("pointerdown", (ev) => ev.preventDefault());

            input.addEventListener("keydown", (ev) => {
                if (ev.key === "Escape") close();
            });

            const fetchData = async (q) => {
                if (mode === "category") {
                    const res = await fetch(`/categories/search?q=${encodeURIComponent(q)}`, {
                        signal: controller.signal
                    });
                    if (!res.ok) throw new Error("Category fetch failed");
                    return await res.json();
                }
                if (mode === "brand") {
                    const url = @json(route('brands.search'));
                    const res = await fetch(url, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            ...(PF.csrf ? {
                                "X-CSRF-TOKEN": PF.csrf
                            } : {})
                        },
                        body: JSON.stringify({
                            q
                        }),
                        signal: controller.signal
                    });
                    if (!res.ok) throw new Error("Brand fetch failed");
                    const json = await res.json();
                    return Array.isArray(json?.data) ? json.data : [];
                }
                return [];
            };

            const render = (q, data) => {
                pf_dropdown.innerHTML = "";

                if (Array.isArray(data) && data.length) {
                    data.forEach(item => {
                        const name = item.name ?? "";
                        const id = item.id ?? "";
                        const div = document.createElement("div");
                        div.className = "pf_dropdown-item";
                        div.setAttribute("role", "option");
                        div.innerHTML =
                            `<span>${PF.escapeHtml(name)}</span><span class="muted">Select</span>`;
                        div.addEventListener("click", () => {
                            setSelection({
                                id,
                                name
                            });
                            PF.toast("success", `${mode} selected: ${name}`, "Selected");
                        });
                        pf_dropdown.appendChild(div);
                    });
                } else {
                    const div = document.createElement("div");
                    div.className = "pf_dropdown-item";
                    div.setAttribute("role", "option");
                    div.innerHTML =
                        `<span>Create “${PF.escapeHtml(q)}”</span><span class="muted">New</span>`;
                    div.addEventListener("click", () => {
                        input.value = q;
                        hidden.value = "";
                        close();
                        PF.toast("info", `Will create ${mode}: ${q}`, "New");
                    });
                    pf_dropdown.appendChild(div);
                }
                open();
            };

            const run = PF.debounce(async () => {
                const q = input.value.trim();
                lastQuery = q;
                hidden.value = "";

                if (!q || q.length < minLen) return close();

                try {
                    controller?.abort();
                    controller = new AbortController();

                    const data = await fetchData(q);
                    if (input.value.trim() !== lastQuery) return;

                    render(q, data);
                } catch (err) {
                    if (err?.name === "AbortError") return;
                    close();
                    PF.toast("error", `Could not load ${mode}s.`, "Search Error");
                    console.error(err);
                }
            }, 260);

            input.addEventListener("input", run);
            input.addEventListener("blur", () => setTimeout(() => close(), 140));

            PF.autocompletes.set(key, {
                wrapper,
                close
            });
        };

        PF.createAutocomplete({
            key: "pf_category",
            wrapperSel: '[data-pf-autocomplete="category"]',
            inputSel: "#pf_category_search",
            hiddenSel: "#pf_category_id",
            pf_dropdownSel: "#pf_category_results",
            mode: "category",
            minLen: 1
        });

        PF.createAutocomplete({
            key: "pf_brand",
            wrapperSel: '[data-pf-autocomplete="brand"]',
            inputSel: "#pf_brand_search",
            hiddenSel: "#pf_brand_id",
            pf_dropdownSel: "#pf_brand_results",
            mode: "brand",
            minLen: 2
        });

        // -------- Barcode + Scanner
        PF.barcodeInput = PF.qs("#pf_barcodeInput");
        PF.genBtn = PF.qs("[data-pf-generate-barcode]");
        PF.scanBtn = PF.qs("[data-pf-scan-barcode]");

        PF.overlay = document.getElementById("pf_scannerOverlay");
        PF.video = document.getElementById("pf_scannerVideo");
        PF.closeBtn = PF.overlay?.querySelector("[data-pf-scan-close]");
        PF.switchBtn = PF.overlay?.querySelector("[data-pf-scan-switch]");
        PF.torchBtn = PF.overlay?.querySelector("[data-pf-scan-torch]");

        PF.checkBarcodeUnique = async (barcode) => {
            const url = new URL(@json(route('products.barcode.check')), window.location.origin);
            url.searchParams.set("barcode", barcode);
            if (PF.productId) url.searchParams.set("product_id", PF.productId);

            const res = await fetch(url.toString(), {
                headers: {
                    "Accept": "application/json"
                }
            });
            if (!res.ok) throw new Error("Barcode check failed");
            return await res.json();
        };

        PF.validateBarcodeNow = async (label) => {
            const code = (PF.barcodeInput?.value || "").trim();
            if (code.length < 3) return;
            try {
                const result = await PF.checkBarcodeUnique(code);
                if (result.valid) PF.toast("success", result.message || "Barcode is available.", label ||
                    "Barcode");
                else PF.toast("warning", result.message || "Barcode already exists.", label || "Barcode");
            } catch (e) {
                PF.toast("error", "Could not validate barcode (server error).", "Barcode");
                console.error(e);
            }
        };

        PF.genBtn?.addEventListener("click", async () => {
            const ts = Date.now().toString().slice(-6);
            const rand = Math.floor(1000000 + Math.random() * 9000000);
            const code = `${rand}${ts}`;
            PF.barcodeInput.value = code;
            PF.barcodeInput.dispatchEvent(new Event("input", {
                bubbles: true
            }));
            await PF.validateBarcodeNow("Generated");
        });

        PF.barcodeInput?.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                PF.validateBarcodeNow("Scanned/Typed");
            }
        });

        PF.barcodeInput?.addEventListener("input", PF.debounce(() => {
            if ((PF.barcodeInput.value || "").trim().length >= 3) PF.validateBarcodeNow("Barcode");
        }, 500));

        // Scanner internals
        PF.stream = null;
        PF.scanning = false;
        PF.detector = null;
        PF.zxingReader = null;
        PF.currentFacing = "environment";
        PF.lastValue = "";
        PF.lastAt = 0;

        PF.openScanner = () => {
            if (!PF.overlay) return;
            PF.overlay.classList.add("show");
            PF.overlay.setAttribute("aria-hidden", "false");
        };

        PF.closeScanner = () => {
            if (!PF.overlay) return;
            PF.overlay.classList.remove("show");
            PF.overlay.setAttribute("aria-hidden", "true");
        };

        PF.stopCamera = async () => {
            PF.scanning = false;
            try {
                PF.zxingReader?.reset?.();
            } catch (_) {}
            if (PF.stream) {
                PF.stream.getTracks().forEach(t => t.stop());
                PF.stream = null;
            }
            if (PF.video) PF.video.srcObject = null;
        };

        PF.setBarcodeValue = (value) => {
            const now = Date.now();
            if (value === PF.lastValue && (now - PF.lastAt) < 1500) return;
            PF.lastValue = value;
            PF.lastAt = now;

            PF.barcodeInput.value = value;
            PF.barcodeInput.dispatchEvent(new Event("input", {
                bubbles: true
            }));

            PF.toast("success", `Scanned: ${value}`, "Scanner");
            PF.closeScanner();
            PF.stopCamera();
            PF.validateBarcodeNow("Scanned");
        };

        PF.scanLoopNative = async () => {
            while (PF.scanning && PF.detector) {
                try {
                    const codes = await PF.detector.detect(PF.video);
                    if (codes?.length) {
                        const raw = (codes[0].rawValue || "").trim();
                        if (raw) PF.setBarcodeValue(raw);
                    }
                } catch (_) {}
                await PF.sleep(120);
            }
        };

        PF.loadScriptOnce = async (src) => new Promise((resolve, reject) => {
            const exists = document.querySelector(`script[data-src="${src}"]`);
            if (exists) return resolve();
            const s = document.createElement("script");
            s.src = src;
            s.async = true;
            s.defer = true;
            s.dataset.src = src;
            s.onload = resolve;
            s.onerror = reject;
            document.head.appendChild(s);
        });

        PF.ensureZxing = async () => {
            if (window.ZXing?.BrowserMultiFormatReader) {
                PF.zxingReader = new window.ZXing.BrowserMultiFormatReader();
                return;
            }
            await PF.loadScriptOnce("https://unpkg.com/@zxing/library@0.20.0/umd/index.min.js");
            if (window.ZXing?.BrowserMultiFormatReader) {
                PF.zxingReader = new window.ZXing.BrowserMultiFormatReader();
            }
        };

        PF.scanLoopZxing = async () => {
            try {
                await PF.zxingReader.decodeFromVideoElementContinuously(PF.video, (result) => {
                    if (!PF.scanning) return;
                    if (result?.text) PF.setBarcodeValue(result.text.trim());
                });
            } catch (e) {
                PF.toast("error", "Failed to start scanner.", "Scanner");
                console.error(e);
                PF.closeScanner();
                PF.stopCamera();
            }
        };

        PF.startCamera = async () => {
            await PF.stopCamera();

            if (!navigator.mediaDevices?.getUserMedia) {
                PF.toast("error", "Camera not supported in this browser.", "Scanner");
                PF.closeScanner();
                return;
            }

            try {
                PF.stream = await navigator.mediaDevices.getUserMedia({
                    audio: false,
                    video: {
                        facingMode: {
                            ideal: PF.currentFacing
                        },
                        width: {
                            ideal: 1280
                        },
                        height: {
                            ideal: 720
                        }
                    }
                });
                PF.video.srcObject = PF.stream;
                await PF.video.play();
            } catch (err) {
                PF.toast("error", "Camera permission denied or not available.", "Scanner");
                console.error(err);
                PF.closeScanner();
                return;
            }

            if ("BarcodeDetector" in window) {
                try {
                    PF.detector = new BarcodeDetector({
                        formats: ["ean_13", "ean_8", "code_128", "code_39", "upc_a", "upc_e", "itf",
                            "qr_code"
                        ]
                    });
                    PF.scanning = true;
                    PF.toast("info", "Camera ready. Scan a barcode.", "Scanner");
                    PF.scanLoopNative();
                    return;
                } catch (_) {
                    PF.detector = null;
                }
            }

            await PF.ensureZxing();
            if (!PF.zxingReader) {
                PF.toast("error", "Scanner not supported on this browser.", "Scanner");
                PF.closeScanner();
                return;
            }

            PF.scanning = true;
            PF.toast("info", "Camera ready. Scan a barcode.", "Scanner");
            PF.scanLoopZxing();
        };

        PF.toggleTorch = async () => {
            if (!PF.stream) return;
            const track = PF.stream.getVideoTracks()[0];
            const caps = track.getCapabilities?.();
            if (!caps?.torch) {
                PF.toast("warning", "Torch not supported on this device.", "Scanner");
                return;
            }
            const torchOn = !!track.getSettings?.().torch;
            try {
                await track.applyConstraints({
                    advanced: [{
                        torch: !torchOn
                    }]
                });
                PF.toast("success", !torchOn ? "Torch on" : "Torch off", "Scanner");
            } catch (_) {
                PF.toast("error", "Could not toggle torch.", "Scanner");
            }
        };

        PF.scanBtn?.addEventListener("click", async () => {
            PF.openScanner();
            await PF.startCamera();
        });
        PF.closeBtn?.addEventListener("click", async () => {
            PF.closeScanner();
            await PF.stopCamera();
        });
        PF.switchBtn?.addEventListener("click", async () => {
            PF.currentFacing = (PF.currentFacing === "environment") ? "user" : "environment";
            PF.toast("info", "Switching camera…", "Scanner");
            await PF.startCamera();
        });
        PF.torchBtn?.addEventListener("click", PF.toggleTorch);

        PF.overlay?.addEventListener("click", async (e) => {
            if (e.target === PF.overlay) {
                PF.closeScanner();
                await PF.stopCamera();
            }
        });

        // -------- Confirm delete (scoped)
        PF.root.querySelectorAll("form[data-pf-confirm]").forEach(form => {
            form.addEventListener("submit", (e) => {
                e.preventDefault();
                const kind = form.getAttribute("data-pf-confirm");
                const msg = (kind === "delete-status") ?
                    "Delete this status? This cannot be undone." :
                    "Delete this batch? This cannot be undone.";

                if (confirm(msg)) form.submit();
                else PF.toast("info", "Cancelled.", "Okay");
            });
        });

    })();
</script>
