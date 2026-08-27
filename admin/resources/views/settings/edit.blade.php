@extends('layouts.app')

@section('content')
<style>
    .st-wrap { max-width: 720px; margin: 0 auto; padding: 24px; }

    .st-header { margin-bottom: 20px; }
    .st-header h1 { font-size: 22px; font-weight: 600; color: var(--text-primary); margin: 0 0 4px 0; }
    .st-header p { color: var(--text-secondary); font-size: 14px; margin: 0; }

    .st-card {
        background: var(--card); border: 1px solid var(--border-color); border-radius: 12px;
        overflow: hidden; margin-bottom: 20px; box-shadow: var(--card-shadow);
    }
    .st-card-header { padding: 14px 18px; border-bottom: 1px solid var(--border-color); font-weight: 600; color: var(--text-primary); font-size: 14px; }
    .st-card-body { padding: 18px; }

    .st-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 640px) { .st-grid { grid-template-columns: 1fr; } }

    .st-field { margin-bottom: 16px; }
    .st-field label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .st-field input, .st-field textarea {
        width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid var(--border-color);
        background: var(--bg-secondary); color: var(--text-primary); font-size: 14px; font-family: inherit;
    }
    .st-field textarea { min-height: 80px; resize: vertical; }
    .st-hint { font-size: 12px; color: var(--text-secondary); margin-top: 4px; }

    .st-actions { display: flex; justify-content: flex-end; }
    .st-btn { padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; background: var(--primary, #6366f1); color: #fff; }
    .st-btn:hover { opacity: .92; }

    .st-alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
    .st-alert-success { background: rgba(34,197,94,.12); color: #16a34a; border: 1px solid rgba(34,197,94,.3); }
</style>

<div class="st-wrap">
    <div class="st-header">
        <h1>Store Settings</h1>
        <p>Business identity and display settings used across invoices, exports, and money formatting.</p>
    </div>

    @if (session('success'))
        <div class="st-alert st-alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf

        <div class="st-card">
            <div class="st-card-header">Business Information</div>
            <div class="st-card-body">
                <div class="st-grid">
                    <div class="st-field">
                        <label for="store_name">Store Name</label>
                        <input type="text" id="store_name" name="store_name" value="{{ old('store_name', $settings['store_name'] ?? '') }}" placeholder="JOJOBI MART">
                    </div>
                    <div class="st-field">
                        <label for="store_phone">Phone</label>
                        <input type="text" id="store_phone" name="store_phone" value="{{ old('store_phone', $settings['store_phone'] ?? '') }}" placeholder="+880 1XXX-XXXXXX">
                    </div>
                </div>
                <div class="st-field">
                    <label for="store_email">Email</label>
                    <input type="email" id="store_email" name="store_email" value="{{ old('store_email', $settings['store_email'] ?? '') }}" placeholder="contact@example.com">
                </div>
                <div class="st-field">
                    <label for="store_address">Address</label>
                    <input type="text" id="store_address" name="store_address" value="{{ old('store_address', $settings['store_address'] ?? '') }}" placeholder="Street, City, Country">
                </div>
            </div>
        </div>

        <div class="st-card">
            <div class="st-card-header">Display</div>
            <div class="st-card-body">
                <div class="st-field">
                    <label for="currency_symbol">Currency Symbol</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol'] ?? '৳') }}" placeholder="৳" maxlength="5" style="max-width: 120px;">
                    <p class="st-hint">Used everywhere money is displayed — dashboards, reports, exports, invoices.</p>
                </div>
                <div class="st-field">
                    <label for="invoice_footer_note">Invoice Footer Note</label>
                    <textarea id="invoice_footer_note" name="invoice_footer_note" placeholder="Thank you for your business!">{{ old('invoice_footer_note', $settings['invoice_footer_note'] ?? '') }}</textarea>
                    <p class="st-hint">Shown at the bottom of printed invoices.</p>
                </div>
            </div>
        </div>

        <div class="st-actions">
            <button type="submit" class="st-btn">Save Settings</button>
        </div>
    </form>
</div>
@endsection
