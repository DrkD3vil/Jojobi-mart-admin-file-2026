@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        :root {
            --customer-gradient: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
        }

        .customer-detail-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .customer-detail-header {
            background: var(--card);
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 32px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .customer-profile {
            display: flex;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .customer-profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: var(--customer-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.3);
        }

        .customer-profile-info {
            flex: 1;
        }

        .customer-profile-info h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            color: var(--foreground);
        }

        .customer-profile-info .subtitle {
            color: var(--muted-foreground);
            font-size: 14px;
        }

        .customer-profile-meta {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .customer-profile-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            color: var(--muted-foreground);
        }

        .customer-profile-meta-item i {
            color: #4F46E5;
        }

        .customer-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .customer-stat-box {
            background: var(--bg);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            border: 1px solid var(--border);
        }

        .customer-stat-box .number {
            font-size: 24px;
            font-weight: 800;
            color: var(--foreground);
        }

        .customer-stat-box .label {
            font-size: 12px;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .customer-section {
            background: var(--card);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
        }

        .customer-section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--foreground);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .customer-section-title i {
            color: #4F46E5;
        }

        .customer-order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .customer-order-item:last-child {
            border-bottom: none;
        }

        .customer-activity-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .customer-activity-item:last-child {
            border-bottom: none;
        }

        .customer-activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .customer-activity-icon.credit {
            background: #D1FAE5;
            color: #059669;
        }

        .customer-activity-icon.debit {
            background: #FEE2E2;
            color: #DC2626;
        }

        .customer-activity-icon.reward {
            background: #FEF3C7;
            color: #D97706;
        }

        .customer-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted-foreground);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .customer-back-btn:hover {
            color: var(--foreground);
        }

        .customer-badge {
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .customer-badge-success {
            background: #D1FAE5;
            color: #065F46;
        }

        .customer-badge-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        .customer-badge-warning {
            background: #FEF3C7;
            color: #92400E;
        }

        @media (max-width: 768px) {
            .customer-detail-container {
                padding: 16px;
            }

            .customer-profile {
                flex-direction: column;
                text-align: center;
            }

            .customer-profile-meta {
                justify-content: center;
            }

            .customer-stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    <div class="customer-detail-container">
        <a href="{{ route('customers.index') }}" class="customer-back-btn">
            <i class="fas fa-arrow-left"></i> Back to Customers
        </a>

        <!-- Profile Header -->
        <div class="customer-detail-header">
            <div class="customer-profile">
                <div class="customer-profile-avatar">
                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                </div>
                <div class="customer-profile-info">
                    <h1>{{ $customer->name }}</h1>
                    <div class="subtitle">
                        <span class="customer-badge
                            {{ $customer->type == 'vip' ? 'customer-badge-warning' :
                               ($customer->type == 'premium' ? 'customer-badge-warning' :
                               'customer-badge-success') }}">
                            {{ ucfirst($customer->type ?? 'Regular') }}
                        </span>
                        <span class="customer-badge {{ $customer->is_active ? 'customer-badge-success' : 'customer-badge-danger' }}">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="customer-profile-meta">
                        <span class="customer-profile-meta-item">
                            <i class="fas fa-phone"></i> {{ $customer->phone ?? 'N/A' }}
                        </span>
                        <span class="customer-profile-meta-item">
                            <i class="fas fa-envelope"></i> {{ $customer->email ?? 'N/A' }}
                        </span>
                        <span class="customer-profile-meta-item">
                            <i class="fas fa-map-marker-alt"></i> {{ $customer->address ?? 'No address' }}
                        </span>
                        <span class="customer-profile-meta-item">
                            <i class="fas fa-calendar"></i> Joined {{ $customer->created_at->format('M d, Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="customer-stats-grid">
                <div class="customer-stat-box">
                    <div class="number">tk.{{ number_format($customer->due_balance, 2) }}</div>
                    <div class="label">Due Balance</div>
                </div>
                <div class="customer-stat-box">
                    <div class="number">tk.{{ number_format($customer->advance_balance, 2) }}</div>
                    <div class="label">Advance Balance</div>
                </div>
                <div class="customer-stat-box">
                    <div class="number">{{ number_format($customer->reward_points) }}</div>
                    <div class="label">Reward Points</div>
                </div>
                <div class="customer-stat-box">
                    <div class="number">{{ $orderStats['total'] }}</div>
                    <div class="label">Total Orders</div>
                </div>
                <div class="customer-stat-box">
                    <div class="number">tk.{{ number_format($orderStats['total_amount'], 2) }}</div>
                    <div class="label">Total Spent</div>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="customer-section">
            <div class="customer-section-title">
                <i class="fas fa-shopping-bag"></i> Recent Orders
            </div>
            @if($customer->orders->count() > 0)
                @foreach($customer->orders as $order)
                    <div class="customer-order-item">
                        <div>
                            <strong>#{{ $order->id }}</strong>
                            <span style="color: var(--muted-foreground); font-size: 14px;">
                                {{ $order->created_at->format('M d, Y H:i') }}
                            </span>
                        </div>
                        <div>
                            <span class="customer-badge
                                {{ $order->status == 'completed' ? 'customer-badge-success' :
                                   ($order->status == 'cancelled' ? 'customer-badge-danger' :
                                   'customer-badge-warning') }}">
                                {{ ucfirst($order->status ?? 'Pending') }}
                            </span>
                            <strong style="margin-left: 12px;">
                                tk.{{ number_format($order->payable_total, 2) }}
                            </strong>
                        </div>
                    </div>
                @endforeach
            @else
                <p style="color: var(--muted-foreground); text-align: center; padding: 20px;">
                    No orders found
                </p>
            @endif
        </div>

        <!-- Balance Ledger -->
        <div class="customer-section">
            <div class="customer-section-title">
                <i class="fas fa-coins"></i> Balance Transactions
            </div>
            @if($customer->balanceLedgers->count() > 0)
                @foreach($customer->balanceLedgers as $ledger)
                    <div class="customer-activity-item">
                        <div class="customer-activity-icon {{ $ledger->direction == 'credit' ? 'credit' : 'debit' }}">
                            <i class="fas {{ $ledger->direction == 'credit' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600;">
                                {{ ucfirst($ledger->kind) }} - {{ ucfirst($ledger->direction) }}
                            </div>
                            <div style="font-size: 13px; color: var(--muted-foreground);">
                                {{ $ledger->note ?? 'No note' }}
                                @if($ledger->ref_type)
                                    <span style="margin-left: 8px; opacity: 0.6;">
                                        ({{ $ledger->ref_type }} #{{ $ledger->ref_id }})
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span style="font-weight: 700;
                                {{ $ledger->direction == 'credit' ? 'color: #059669;' : 'color: #DC2626;' }}">
                                {{ $ledger->direction == 'credit' ? '-' : '+' }}
                                tk.{{ number_format($ledger->amount, 2) }}
                            </span>
                            <div style="font-size: 12px; color: var(--muted-foreground);">
                                {{ $ledger->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p style="color: var(--muted-foreground); text-align: center; padding: 20px;">
                    No balance transactions found
                </p>
            @endif
        </div>

        <!-- Reward Ledger -->
        <div class="customer-section">
            <div class="customer-section-title">
                <i class="fas fa-gem"></i> Reward Transactions
            </div>
            @if($customer->rewardLedgers->count() > 0)
                @foreach($customer->rewardLedgers as $ledger)
                    <div class="customer-activity-item">
                        <div class="customer-activity-icon reward">
                            <i class="fas fa-star"></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 600;">
                                {{ ucfirst($ledger->action) }} - {{ ucfirst($ledger->direction) }}
                            </div>
                            <div style="font-size: 13px; color: var(--muted-foreground);">
                                {{ $ledger->note ?? 'No note' }}
                            </div>
                        </div>
                        <div>
                            <span style="font-weight: 700; color: #D97706;">
                                {{ $ledger->direction == 'add' ? '+' : '-' }}
                                {{ number_format($ledger->points) }} pts
                            </span>
                            <div style="font-size: 12px; color: var(--muted-foreground);">
                                {{ $ledger->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p style="color: var(--muted-foreground); text-align: center; padding: 20px;">
                    No reward transactions found
                </p>
            @endif
        </div>
    </div>
@endsection
