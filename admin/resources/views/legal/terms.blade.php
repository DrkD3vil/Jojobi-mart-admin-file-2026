@extends('layouts.app')

@section('content')
<style>
    .tc-wrap { max-width: 780px; margin: 0 auto; padding: 24px; }
    .tc-header { margin-bottom: 20px; }
    .tc-header h1 { font-size: 22px; font-weight: 600; color: var(--text-primary); margin: 0 0 4px 0; }
    .tc-header p { color: var(--text-secondary); font-size: 14px; margin: 0; }

    .tc-notice {
        display: flex; gap: 12px; align-items: flex-start;
        background: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.35);
        border-radius: 10px; padding: 14px 16px; margin-bottom: 24px; color: var(--text-primary); font-size: 13px;
    }
    .tc-notice i { color: #eab308; margin-top: 2px; }

    .tc-card {
        background: var(--card); border: 1px solid var(--border-color); border-radius: 12px;
        padding: 22px 26px; box-shadow: var(--card-shadow); color: var(--text-primary); font-size: 14px; line-height: 1.7;
    }
    .tc-card h2 { font-size: 15px; font-weight: 600; margin: 22px 0 8px 0; color: var(--text-primary); }
    .tc-card h2:first-child { margin-top: 0; }
    .tc-card p { margin: 0 0 10px 0; color: var(--text-secondary); }
    .tc-card ul { margin: 0 0 10px 18px; padding: 0; color: var(--text-secondary); }
    .tc-card li { margin-bottom: 4px; }
    .tc-updated { font-size: 12px; color: var(--text-secondary); margin-top: 20px; text-align: right; }
</style>

<div class="tc-wrap">
    <div class="tc-header">
        <h1>Terms & Conditions</h1>
        <p>Terms of use for {{ \App\Models\Setting::get('store_name') ?: 'this' }} admin panel.</p>
    </div>

    <div class="tc-notice">
        <i class="fas fa-triangle-exclamation"></i>
        <span>This is a starting template, not finished legal advice. Have it reviewed by a qualified legal professional and adjusted for your business, jurisdiction, and actual data practices before relying on it.</span>
    </div>

    <div class="tc-card">
        <h2>1. Acceptance of Terms</h2>
        <p>By logging into and using this admin panel, you agree to these terms. Access is granted only to authorized staff and representatives of the business operating this system.</p>

        <h2>2. Authorized Use</h2>
        <p>This system is for legitimate business operations only: managing orders, inventory, customers, expenses, and related records. You agree to:</p>
        <ul>
            <li>Use your own account credentials and not share them with others.</li>
            <li>Access only the sections and data your role permits.</li>
            <li>Report any suspected unauthorized access or security issue immediately.</li>
        </ul>

        <h2>3. Data Accuracy</h2>
        <p>Reports, dashboards, and exports reflect data as recorded in the system at the time of viewing. While the system is built to keep financial figures (sales, refunds, profit) internally consistent, business decisions based on this data remain the responsibility of the user.</p>

        <h2>4. Confidentiality</h2>
        <p>Customer information, financial records, and business data accessible through this panel are confidential. They may not be exported, shared, or used outside the scope of your assigned duties.</p>

        <h2>5. Account Responsibility</h2>
        <p>You are responsible for all actions taken under your account. Administrators may suspend or revoke access at any time, for any legitimate business reason.</p>

        <h2>6. Changes to These Terms</h2>
        <p>These terms may be updated as the business's policies or this system's capabilities change. Continued use after an update constitutes acceptance of the revised terms.</p>

        <h2>7. Limitation of Liability</h2>
        <p>This system is provided for internal business use as-is. The business operating it is not liable for indirect or incidental issues arising from its use, to the extent permitted by applicable law.</p>

        <div class="tc-updated">Last updated: {{ now()->format('F Y') }}</div>
    </div>
</div>
@endsection
