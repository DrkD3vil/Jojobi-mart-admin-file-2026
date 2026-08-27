@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --gradient-warning: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-danger: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
            --gradient-info: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-dark: linear-gradient(135deg, #2d3436 0%, #000000 100%);
            --gradient-glass: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        }

        /* Enhanced Base Styles */
        .order-wrap {
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px;
            font-family: 'Inter', sans-serif;
        }

        /* Animated Background */
        .order-bg-particles {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        .order-bg-particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: var(--gradient-primary);
            border-radius: 50%;
            opacity: 0.1;
            animation: floatParticle 20s infinite linear;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.1;
            }

            90% {
                opacity: 0.1;
            }

            100% {
                transform: translateY(-100vh) rotate(720deg);
                opacity: 0;
            }
        }

        /* Stats Cards with Glassmorphism */
        .order-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
            position: relative;
            z-index: 1;
        }

        .order-stat-card {
            background: var(--card);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px 24px;
            border: 1px solid var(--border);
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: var(--foreground);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .order-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--gradient-glass);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .order-stat-card:hover::before {
            left: 0;
        }

        .order-stat-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
            border-color: transparent;
        }

        .order-stat-card.active {
            border-color: transparent;
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-4px);
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
        }

        .order-stat-card.active .order-stat-label {
            color: rgba(255, 255, 255, 0.8);
        }

        .order-stat-card.active .order-stat-number {
            color: white;
        }

        .order-stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 22px;
            transition: all 0.3s;
        }

        .order-stat-card:not(.active) .order-stat-icon-wrapper {
            background: var(--muted);
        }

        .order-stat-card.active .order-stat-icon-wrapper {
            background: rgba(255, 255, 255, 0.2);
        }

        .order-stat-number {
            font-size: 28px;
            font-weight: 800;
            display: block;
            line-height: 1.2;
            transition: all 0.3s;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-stat-card.active .order-stat-number {
            -webkit-text-fill-color: white;
            background: none;
        }

        .order-stat-number.green {
            background: var(--gradient-success);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 4px;
            transition: all 0.3s;
        }

        .order-stat-trend {
            display: inline-block;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 999px;
            margin-top: 4px;
            font-weight: 600;
        }

        .order-stat-trend.up {
            background: #d1fae5;
            color: #065f46;
        }

        .order-stat-trend.down {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Page Header */
        .order-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding: 20px 28px;
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            position: relative;
            z-index: 1;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .order-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .order-header-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }

        .order-header-title {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-header-subtitle {
            font-size: 14px;
            color: var(--muted-foreground);
            margin: 0;
        }

        .order-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .order-header-badge {
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .order-header-badge.status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .order-header-badge.status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .order-header-badge.status-paid {
            background: #dbeafe;
            color: #1e40af;
        }

        .order-header-badge.status-refunded {
            background: #e0e7ff;
            color: #3730a3;
        }

        .order-header-badge.status-returned {
            background: #f3e8ff;
            color: #5b21b6;
        }

        .order-header-badge.status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Enhanced Search Card */
        .order-search-card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            padding: 20px 24px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.6s ease-out 0.1s both;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .order-search-grid {
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 12px;
            align-items: end;
        }

        @media (max-width: 768px) {
            .order-search-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }

        .order-search-input {
            position: relative;
        }

        .order-search-input .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted-foreground);
            font-size: 14px;
        }

        .order-search-input input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            background: var(--input);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--foreground);
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .order-search-input input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .order-search-input input::placeholder {
            color: var(--muted-foreground);
        }

        .order-search-field {
            min-width: 140px;
        }

        .order-search-field input {
            width: 100%;
            padding: 12px 14px;
            background: var(--input);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--foreground);
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .order-search-field input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .order-search-field input::placeholder {
            color: var(--muted-foreground);
        }

        .order-btn-clear {
            padding: 12px 20px;
            background: var(--muted);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--foreground);
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .order-btn-clear:hover {
            background: var(--border);
            transform: translateY(-2px);
        }

        .order-search-count {
            padding: 12px 18px;
            background: var(--muted);
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted-foreground);
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Enhanced Table */
        .order-table-card {
            background: var(--card);
            border-radius: 16px;
            border: 1px solid var(--border);
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.6s ease-out 0.2s both;
        }

        .order-table-wrap {
            overflow-x: auto;
        }

        .order-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .order-table thead {
            background: var(--muted);
        }

        .order-table th {
            padding: 14px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted-foreground);
            border-bottom: 2px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 2;
        }

        .order-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 14px;
        }

        .order-table tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }

        .order-table tbody tr::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--gradient-primary);
            opacity: 0;
            transition: all 0.3s;
        }

        .order-table tbody tr:hover {
            background: var(--muted);
            transform: scale(1.001);
        }

        .order-table tbody tr:hover::before {
            opacity: 1;
        }

        .order-table tbody tr:last-child td {
            border-bottom: none;
        }

        .order-id {
            font-weight: 700;
            font-size: 15px;
            color: var(--foreground);
        }

        .order-link {
            text-decoration: none;
            color: var(--foreground);
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
        }

        .order-link:hover {
            color: #667eea;
        }

        .order-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            transition: all 0.3s;
        }

        .order-link:hover::after {
            width: 100%;
        }

        .order-customer-name {
            font-weight: 600;
            font-size: 14px;
        }

        .order-customer-phone {
            font-size: 12px;
            color: var(--muted-foreground);
        }

        .order-total {
            font-weight: 700;
            font-size: 15px;
            color: var(--foreground);
        }

        .order-date {
            font-size: 13px;
            color: var(--muted-foreground);
        }

        /* Enhanced Status Badges */
        .order-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            transition: all 0.3s;
        }

        .order-status-badge .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .order-status-badge.status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .order-status-badge.status-pending .status-dot {
            background: #eab308;
        }

        .order-status-badge.status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .order-status-badge.status-completed .status-dot {
            background: #22c55e;
        }

        .order-status-badge.status-paid {
            background: #dbeafe;
            color: #1e40af;
        }

        .order-status-badge.status-paid .status-dot {
            background: #3b82f6;
        }

        .order-status-badge.status-refunded {
            background: #e0e7ff;
            color: #3730a3;
        }

        .order-status-badge.status-refunded .status-dot {
            background: #8b5cf6;
        }

        .order-status-badge.status-returned {
            background: #f3e8ff;
            color: #5b21b6;
        }

        .order-status-badge.status-returned .status-dot {
            background: #6d28d9;
        }

        .order-status-badge.status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .order-status-badge.status-cancelled .status-dot {
            background: #ef4444;
        }

        /* Action Buttons */
        .order-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            color: var(--muted-foreground);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: transparent;
            border: none;
            cursor: pointer;
        }

        .order-action-btn:hover {
            background: var(--gradient-primary);
            color: white;
            transform: scale(1.1) rotate(-5deg);
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        /* Footer */
        .order-table-footer {
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
            gap: 12px;
        }

        .order-table-footer .order-meta {
            font-size: 13px;
            color: var(--muted-foreground);
        }

        .order-table-footer .order-meta .highlight {
            font-weight: 600;
            color: var(--foreground);
        }

        /* Loading State */
        .order-loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid var(--border);
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Empty State */
        .order-empty {
            padding: 48px 24px;
            text-align: center;
            color: var(--muted-foreground);
        }

        .order-empty .empty-icon {
            font-size: 48px;
            color: var(--border);
            margin-bottom: 16px;
            display: block;
        }

        .order-empty .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--foreground);
            margin-bottom: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .order-wrap {
                padding: 16px;
            }

            .order-stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 10px;
            }

            .order-stat-card {
                padding: 14px 16px;
            }

            .order-stat-number {
                font-size: 22px;
            }

            .order-page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 16px 20px;
            }

            .order-header-right {
                width: 100%;
                justify-content: flex-start;
            }

            .order-table-footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        /* Scrollbar Styling */
        .order-table-wrap::-webkit-scrollbar {
            height: 6px;
        }

        .order-table-wrap::-webkit-scrollbar-track {
            background: var(--muted);
            border-radius: 3px;
        }

        .order-table-wrap::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        .order-table-wrap::-webkit-scrollbar-thumb:hover {
            background: #667eea;
        }

        /* Fade In Animation for Rows */
        .order-table tbody tr {
            animation: fadeInRow 0.4s ease-out;
            animation-fill-mode: both;
        }

        @keyframes fadeInRow {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .order-table tbody tr:nth-child(1) {
            animation-delay: 0.05s;
        }

        .order-table tbody tr:nth-child(2) {
            animation-delay: 0.1s;
        }

        .order-table tbody tr:nth-child(3) {
            animation-delay: 0.15s;
        }

        .order-table tbody tr:nth-child(4) {
            animation-delay: 0.2s;
        }

        .order-table tbody tr:nth-child(5) {
            animation-delay: 0.25s;
        }

        .order-table tbody tr:nth-child(6) {
            animation-delay: 0.3s;
        }

        .order-table tbody tr:nth-child(7) {
            animation-delay: 0.35s;
        }

        .order-table tbody tr:nth-child(8) {
            animation-delay: 0.4s;
        }

        .order-table tbody tr:nth-child(9) {
            animation-delay: 0.45s;
        }

        .order-table tbody tr:nth-child(10) {
            animation-delay: 0.5s;
        }

        /* Status Color Gradients for Cards */
        .order-stat-card[data-status="pending"] .order-stat-number {
            background: var(--gradient-warning);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-stat-card[data-status="completed"] .order-stat-number {
            background: var(--gradient-success);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-stat-card[data-status="paid"] .order-stat-number {
            background: var(--gradient-info);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-stat-card[data-status="refunded"] .order-stat-number {
            background: var(--gradient-dark);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-stat-card[data-status="returned"] .order-stat-number {
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .order-stat-card[data-status="cancelled"] .order-stat-number {
            background: var(--gradient-danger);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>

    <div class="order-wrap">
        <!-- Animated Background Particles -->
        <div class="order-bg-particles" id="bgParticles"></div>

        <!-- Statistics Grid -->
        @isset($stats)
            <div class="order-stats-grid">
                <a href="{{ route('orders.index') }}" class="order-stat-card {{ !isset($status) ? 'active' : '' }}">
                    <div class="order-stat-icon-wrapper">📦</div>
                    <span class="order-stat-number">{{ $stats['total'] }}</span>
                    <span class="order-stat-label">All Orders</span>
                    <span class="order-stat-trend up">+12%</span>
                </a>

                <a href="{{ route('orders.pending') }}"
                    class="order-stat-card {{ isset($status) && $status === 'pending' ? 'active' : '' }}" data-status="pending">
                    <div class="order-stat-icon-wrapper">⏳</div>
                    <span class="order-stat-number">{{ $stats['pending'] }}</span>
                    <span class="order-stat-label">Pending</span>
                    <span class="order-stat-trend up">+3%</span>
                </a>

                <a href="{{ route('orders.completed') }}"
                    class="order-stat-card {{ isset($status) && $status === 'completed' ? 'active' : '' }}"
                    data-status="completed">
                    <div class="order-stat-icon-wrapper">✅</div>
                    <span class="order-stat-number">{{ $stats['completed'] }}</span>
                    <span class="order-stat-label">Completed</span>
                    <span class="order-stat-trend up">+8%</span>
                </a>

                <a href="{{ route('orders.paid') }}"
                    class="order-stat-card {{ isset($status) && $status === 'paid' ? 'active' : '' }}" data-status="paid">
                    <div class="order-stat-icon-wrapper">💰</div>
                    <span class="order-stat-number">{{ $stats['paid'] }}</span>
                    <span class="order-stat-label">Paid</span>
                    <span class="order-stat-trend up">+15%</span>
                </a>

                <a href="{{ route('orders.refunded') }}"
                    class="order-stat-card {{ isset($status) && $status === 'refunded' ? 'active' : '' }}"
                    data-status="refunded">
                    <div class="order-stat-icon-wrapper">🔄</div>
                    <span class="order-stat-number">{{ $stats['refunded'] }}</span>
                    <span class="order-stat-label">Refunded</span>
                    <span class="order-stat-trend down">-2%</span>
                </a>

                <a href="{{ route('orders.returned') }}"
                    class="order-stat-card {{ isset($status) && $status === 'returned' ? 'active' : '' }}"
                    data-status="returned">
                    <div class="order-stat-icon-wrapper">↩️</div>
                    <span class="order-stat-number">{{ $stats['returned'] }}</span>
                    <span class="order-stat-label">Returned</span>
                    <span class="order-stat-trend up">+5%</span>
                </a>

                <a href="{{ route('orders.cancelled') }}"
                    class="order-stat-card {{ isset($status) && $status === 'cancelled' ? 'active' : '' }}"
                    data-status="cancelled">
                    <div class="order-stat-icon-wrapper">❌</div>
                    <span class="order-stat-number">{{ $stats['cancelled'] }}</span>
                    <span class="order-stat-label">Cancelled</span>
                    <span class="order-stat-trend down">-1%</span>
                </a>

                <a href="{{ route('orders.index') }}" class="order-stat-card">
                    <div class="order-stat-icon-wrapper">💵</div>
                    <span class="order-stat-number green">tk. {{ number_format($stats['total_revenue'] ?? 0, 0) }}</span>
                    <span class="order-stat-label">Revenue</span>
                    <span class="order-stat-trend up">+22%</span>
                </a>
            </div>
        @endisset

        <!-- Page Header -->
        <div class="order-page-header">
            <div class="order-header-left">
                <div class="order-header-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h1 class="order-header-title">
                        {{ $title ?? 'Orders' }}
                    </h1>
                    <p class="order-header-subtitle">
                        Manage and track all your orders in one place
                    </p>
                </div>
            </div>
            <div class="order-header-right">
                @if (isset($status))
                    <span class="order-header-badge status-{{ $status }}">
                        <i class="fas fa-filter"></i> {{ ucfirst($status) }}
                    </span>
                @endif
                <span style="font-size: 14px; color: var(--muted-foreground);">
                    <i class="fas fa-list"></i> {{ $orders->total() }} total
                </span>
            </div>
        </div>

        <!-- Search Card -->
        {{-- <div class="order-search-card">
            <div class="order-search-grid">
                <div class="order-search-input">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="ordxQ"
                        placeholder="Search orders by ID, customer name, phone or order number..." />
                </div>

                <div class="order-search-field">
                    <input type="number" id="ordxMin" step="0.01" placeholder="Min total" />
                </div>

                <div class="order-search-field">
                    <input type="number" id="ordxMax" step="0.01" placeholder="Max total" />
                </div>

                <button type="button" class="order-btn-clear" id="ordxClear">
                    <i class="fas fa-eraser"></i> Clear
                </button>

                <span class="order-search-count" id="ordxCount">
                    <i class="fas fa-file-alt"></i> {{ $orders->count() }} results
                </span>
            </div>
        </div> --}}

         <!-- Advanced Search Card -->
        <div class="order-search-card">
            <div class="order-search-grid">
                <div class="order-search-input" style="grid-column: span 2;">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="ordxQ" placeholder="Search by order ID, customer name, phone, or order number..." value="{{ request('q') }}" />
                </div>

                <div class="order-search-field">
                    <input type="number" id="ordxMin" step="0.01" placeholder="Min total" value="{{ request('min_total') }}" />
                </div>

                <div class="order-search-field">
                    <input type="number" id="ordxMax" step="0.01" placeholder="Max total" value="{{ request('max_total') }}" />
                </div>

                <div class="order-search-field">
                    <input type="date" id="ordxDateFrom" placeholder="Date from" value="{{ request('date_from') }}" />
                </div>

                <div class="order-search-field">
                    <input type="date" id="ordxDateTo" placeholder="Date to" value="{{ request('date_to') }}" />
                </div>

                <div class="order-search-field">
                    <select id="ordxStatus">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="paid">Paid</option>
                        <option value="refunded">Refunded</option>
                        <option value="returned">Returned</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="order-search-field">
                    <select id="ordxSortBy">
                        <option value="created_at">Date Created</option>
                        <option value="payable_total">Total Amount</option>
                        <option value="order_no">Order Number</option>
                        <option value="status">Status</option>
                    </select>
                    <select id="ordxSortDirection" style="width: 80px; margin-left: 5px;">
                        <option value="desc">↓ Newest</option>
                        <option value="asc">↑ Oldest</option>
                    </select>
                </div>

                <button type="button" class="order-btn-search" id="ordxSearchBtn">
                    <i class="fas fa-search"></i> Search
                </button>

                <button type="button" class="order-btn-clear" id="ordxClear">
                    <i class="fas fa-eraser"></i> Clear
                </button>

                <span class="order-search-count" id="ordxCount">
                    <i class="fas fa-file-alt"></i> {{ $orders->count() }} results
                </span>
            </div>
        </div>


        <!-- Orders Table -->
        <div class="order-table-card">
            <div class="order-table-wrap">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th style="width:80px;">ID</th>
                            <th>Order No</th>
                            <th>Customer</th>
                            <th style="width:140px;">Total</th>
                            <th style="width:200px;">Date</th>
                            <th style="width:140px;">Status</th>
                            <th style="width:120px;">Action</th> <!-- Increased width -->
                        </tr>
                    </thead>
                    <tbody id="ordxBody">
                        @foreach ($orders as $order)
                            <tr class="order-tr" data-href="{{ route('orders.show', $order) }}">
                                <td><span class="order-id">#{{ $order->id }}</span></td>
                                <td>
                                    <a class="order-link" href="{{ route('orders.show', $order) }}"
                                        onclick="event.stopPropagation()">
                                        {{ $order->order_no ?? 'ORD-' . $order->id }}
                                    </a>
                                </td>
                                <td>
                                    <div class="order-customer-name">{{ $order->customer?->name ?? 'Guest' }}</div>
                                    <div class="order-customer-phone">
                                        <i class="fas fa-phone" style="font-size: 10px;"></i>
                                        {{ $order->customer?->phone ?? 'N/A' }}
                                    </div>
                                </td>
                                <td><span class="order-total">tk. {{ number_format($order->payable_total, 2) }}</span>
                                </td>
                                <td class="order-date">
                                    <i class="far fa-calendar-alt" style="margin-right: 4px;"></i>
                                    {{ $order->created_at->format('M d, Y') }}
                                    <br>
                                    <span style="font-size: 11px; color: var(--muted-foreground);">
                                        {{ $order->created_at->format('H:i A') }}
                                    </span>
                                </td>
                                <td>

                                    <span class="order-status-badge status-{{ $order->status ?? 'pending' }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($order->status ?? 'Pending') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="order-action-group"
                                        style="display:flex; gap:4px; justify-content:center;">
                                        <a href="{{ route('orders.show', $order) }}" class="order-action-btn"
                                            title="View Order Details" onclick="event.stopPropagation()">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                        <!-- Only show trash button if using soft deletes and not already trashed -->
                                        @if (method_exists($order, 'trashed') && !$order->trashed())
                                            <button onclick="event.stopPropagation(); confirmTrash('{{ $order->id }}')"
                                                class="order-action-btn" title="Move to Trash" style="color: #ef4444;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif

                                        {{-- Edit Button --}}
                                        @if (in_array($order->status, ['pending', 'processing', 'cancel']))
                                            <a href="{{ route('orders.edit', $order->id) }}" onclick="event.stopPropagation();" class="order-action-btn"
                                                title="Edit Order" style="color: #2b78f5;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        {{-- Split Button --}}
                                        {{-- @if (in_array($order->status, ['completed']))
                                            <a href="{{ route('orders.split', $order) }}"
                                                onclick="event.stopPropagation();" class="order-action-btn"
                                                title="Split Order" style="color: #1ba100;">
                                                <i class="fas fa-code-branch"></i>
                                            </a>
                                        @endif --}}

                                        @if($order->canSplit())
    <a href="{{ route('orders.split', $order) }}"
        onclick="event.stopPropagation();"
        class="order-action-btn"
        title="Split Order"
        style="color: #1ba100;">
        <i class="fas fa-code-branch"></i>
    </a>
@endif


@if($order->isSplitParent())
    <a href="{{ route('orders.split.history', $order) }}"
        onclick="event.stopPropagation();"
        class="order-action-btn"
        title="Split History"
        style="color:#007bff;">
        <i class="fas fa-history"></i>
    </a>
@endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="order-table-footer">
                <div class="order-meta" id="ordxMeta">
                    <span class="highlight">{{ $orders->firstItem() ?? 0 }}</span> -
                    <span class="highlight">{{ $orders->lastItem() ?? 0 }}</span>
                    of <span class="highlight">{{ $orders->total() }}</span> orders
                </div>
                <div id="ordxPaginateWrap">
                    @if ($orders->hasPages())
                        {{ $orders->onEachSide(1)->links('vendor.pagination.custom') }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate background particles
            const particlesContainer = document.getElementById('bgParticles');
            if (particlesContainer) {
                const colors = ['#667eea', '#764ba2', '#11998e', '#38ef7d', '#f093fb', '#f5576c', '#4facfe',
                    '#00f2fe'
                ];
                for (let i = 0; i < 30; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'order-bg-particle';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animationDelay = Math.random() * 20 + 's';
                    particle.style.animationDuration = (15 + Math.random() * 20) + 's';
                    particle.style.width = (4 + Math.random() * 8) + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                    particlesContainer.appendChild(particle);
                }
            }
        });

        // Row click redirect
        function bindRowClicks() {
            document.querySelectorAll('.order-tr[data-href]').forEach(tr => {
                tr.addEventListener('click', () => {
                    window.location.href = tr.dataset.href;
                });
            });
        }
        bindRowClicks();

        // AJAX search
        const URL_AJAX = @json(route('orders.ajax.index'));
        const qEl = document.getElementById('ordxQ');
        const minEl = document.getElementById('ordxMin');
        const maxEl = document.getElementById('ordxMax');
        const bodyEl = document.getElementById('ordxBody');
        const countEl = document.getElementById('ordxCount');
        const metaEl = document.getElementById('ordxMeta');
        const clearBtn = document.getElementById('ordxClear');
        const paginateWrap = document.getElementById('ordxPaginateWrap');

        let lastController = null;

        const debounce = (fn, d = 250) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), d);
            }
        };

        function esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [m]));
        }

        function money(n) {
            return Number(n || 0).toFixed(2);
        }

        async function getJSON(url) {
            if (lastController) lastController.abort();
            lastController = new AbortController();

            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                },
                signal: lastController.signal
            });
            if (!res.ok) throw new Error(await res.text());
            return res.json();
        }

        async function runSearch(pageUrl = null) {
            const q = (qEl.value || '').trim();
            const min = (minEl.value || '').trim();
            const max = (maxEl.value || '').trim();

            if (q.length > 0 && q.length < 2 && !min && !max) {
                metaEl.innerHTML = `<span class="order-meta">Type at least 2 characters...</span>`;
                return;
            }

            metaEl.innerHTML = `<span class="order-loading"></span> Loading...`;

            const base = pageUrl ? new URL(pageUrl, window.location.origin) : new URL(URL_AJAX, window.location.origin);
            if (q) base.searchParams.set('q', q);
            if (min) base.searchParams.set('min_total', min);
            if (max) base.searchParams.set('max_total', max);

            @if (isset($status))
                base.searchParams.set('status', '{{ $status }}');
            @endif

            try {
                const data = await getJSON(base.toString());

                bodyEl.innerHTML = (data.rows || []).map((o, index) => `
            <tr class="order-tr" data-href="tk. {esc(o.show_url)}" style="animation-delay: tk. {index * 0.05}s">
                <td><span class="order-id">#tk. {esc(o.id)}</span></td>
                <td>
                    <a class="order-link" href="tk. {esc(o.show_url)}" onclick="event.stopPropagation()">
                        tk. {esc(o.order_no || ('ORD-'+o.id))}
                    </a>
                </td>
                <td>
                    <div class="order-customer-name">tk. {esc(o.customer_name || 'Guest')}</div>
                    <div class="order-customer-phone">
                        <i class="fas fa-phone" style="font-size: 10px;"></i>
                        tk. {esc(o.customer_phone || 'N/A')}
                    </div>
                </td>
                <td><span class="order-total">tk.tk. {money(o.payable_total)}</span></td>
                <td class="order-date">
                    <i class="far fa-calendar-alt" style="margin-right: 4px;"></i>
                    tk. {esc(o.created_at_formatted || o.created_at)}
                </td>
                <td>
                    <span class="order-status-badge status-tk. {esc(o.status || 'pending')}">
                        <span class="status-dot"></span>
                        tk. {esc(o.status || 'Pending')}
                    </span>
                </td>
                <td>
                    <a href="tk. {esc(o.show_url)}" class="order-action-btn" title="View Order Details" onclick="event.stopPropagation()">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </td>
            </tr>
        `).join('') || `
            <tr>
                <td colspan="7">
                    <div class="order-empty">
                        <span class="empty-icon">📭</span>
                        <div class="empty-title">No orders found</div>
                        <p style="color: var(--muted-foreground); font-size: 14px;">Try adjusting your search criteria</p>
                    </div>
                </td>
            </tr>
        `;

                paginateWrap.innerHTML = data.pagination_html || '';
                countEl.innerHTML = `<i class="fas fa-file-alt"></i> tk. {data.count_on_page || 0} results`;
                metaEl.innerHTML = `
            <span class="highlight">tk. {data.first_item || 0}</span> -
            <span class="highlight">tk. {data.last_item || 0}</span>
            of <span class="highlight">tk. {data.total || 0}</span> orders
        `;

                bindRowClicks();
                bindPaginationClicks();

            } catch (e) {
                if (String(e).includes('AbortError')) return;
                console.error(e);
                metaEl.innerHTML = `<span style="color: #ef4444;">Search failed. Please try again.</span>`;
            }
        }

        // Add this to your index.blade.php
        function confirmTrash(orderId) {
            if (confirm('Are you sure you want to move this order to trash?')) {
                const reason = prompt('Please provide a reason for moving this order to trash (optional):');

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ url('orders') }}/' + orderId + '/trash';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                if (reason !== null) {
                    const reasonInput = document.createElement('input');
                    reasonInput.type = 'hidden';
                    reasonInput.name = 'reason';
                    reasonInput.value = reason;
                    form.appendChild(reasonInput);
                }

                document.body.appendChild(form);
                form.submit();
            }
        }

        function bindPaginationClicks() {
            paginateWrap.querySelectorAll('a').forEach(a => {
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    runSearch(a.getAttribute('href'));
                });
            });
        }
        bindPaginationClicks();

        const run = debounce(() => runSearch(), 260);
        qEl.addEventListener('input', run);
        minEl.addEventListener('input', run);
        maxEl.addEventListener('input', run);

        clearBtn.addEventListener('click', () => {
            qEl.value = '';
            minEl.value = '';
            maxEl.value = '';
            runSearch();
        });
    </script> --}}



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate background particles
        const particlesContainer = document.getElementById('bgParticles');
        if (particlesContainer) {
            const colors = ['#667eea', '#764ba2', '#11998e', '#38ef7d', '#f093fb', '#f5576c', '#4facfe',
                '#00f2fe'
            ];
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'order-bg-particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 20 + 's';
                particle.style.animationDuration = (15 + Math.random() * 20) + 's';
                particle.style.width = (4 + Math.random() * 8) + 'px';
                particle.style.height = particle.style.width;
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                particlesContainer.appendChild(particle);
            }
        }

        // Initialize search functionality
        initializeAdvancedSearch();
    });

    function initializeAdvancedSearch() {
        // Row click redirect
        function bindRowClicks() {
            document.querySelectorAll('.order-tr[data-href]').forEach(tr => {
                tr.addEventListener('click', () => {
                    window.location.href = tr.dataset.href;
                });
            });
        }
        bindRowClicks();

        // AJAX search - Updated route name
        const URL_AJAX = @json(route('orders.ajax.search'));

        // Get all search elements
        const qEl = document.getElementById('ordxQ');
        const minEl = document.getElementById('ordxMin');
        const maxEl = document.getElementById('ordxMax');
        const dateFromEl = document.getElementById('ordxDateFrom');
        const dateToEl = document.getElementById('ordxDateTo');
        const statusEl = document.getElementById('ordxStatus');
        const sortByEl = document.getElementById('ordxSortBy');
        const sortDirectionEl = document.getElementById('ordxSortDirection');
        const bodyEl = document.getElementById('ordxBody');
        const countEl = document.getElementById('ordxCount');
        const metaEl = document.getElementById('ordxMeta');
        const clearBtn = document.getElementById('ordxClear');
        const searchBtn = document.getElementById('ordxSearchBtn');
        const paginateWrap = document.getElementById('ordxPaginateWrap');

        let lastController = null;
        let currentFilters = {};

        const debounce = (fn, d = 300) => {
            let t;
            return (...a) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...a), d);
            }
        };

        function esc(s) {
            return String(s ?? '').replace(/[&<>"']/g, m => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            } [m]));
        }

        function money(n) {
            return Number(n || 0).toFixed(2);
        }

        function getFilters() {
            const filters = {
                q: qEl ? qEl.value.trim() : '',
                min_total: minEl ? minEl.value.trim() : '',
                max_total: maxEl ? maxEl.value.trim() : '',
                date_from: dateFromEl ? dateFromEl.value.trim() : '',
                date_to: dateToEl ? dateToEl.value.trim() : '',
                status: statusEl ? statusEl.value : '',
                sort_by: sortByEl ? sortByEl.value : 'created_at',
                sort_direction: sortDirectionEl ? sortDirectionEl.value : 'desc',
            };

            // Remove empty filters
            return Object.fromEntries(
                Object.entries(filters).filter(([_, v]) => v !== '' && v !== null && v !== undefined)
            );
        }

        function buildSearchURL(baseUrl, filters, pageUrl = null) {
            // If pageUrl is provided, use it directly with filters
            let url;
            if (pageUrl) {
                url = new URL(pageUrl, window.location.origin);
            } else {
                url = new URL(baseUrl, window.location.origin);
            }

            // Remove any existing pagination parameters and add our filters
            const searchParams = new URLSearchParams();

            Object.entries(filters).forEach(([key, value]) => {
                if (value && value !== '') {
                    searchParams.set(key, value);
                }
            });

            // Preserve the page parameter if it exists in the URL
            const pageParam = url.searchParams.get('page');
            if (pageParam) {
                searchParams.set('page', pageParam);
            }

            url.search = searchParams.toString();

            // Add status from blade if exists (for status filter context)
            @if (isset($status))
                if (!searchParams.has('status')) {
                    url.searchParams.set('status', '{{ $status }}');
                }
            @endif

            return url.toString();
        }

        async function getJSON(url) {
            if (lastController) lastController.abort();
            lastController = new AbortController();

            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: lastController.signal
            });
            if (!res.ok) throw new Error(await res.text());
            return res.json();
        }

        async function runSearch(pageUrl = null) {
            const filters = getFilters();
            currentFilters = filters;

            // Validate search term length
            if (filters.q && filters.q.length > 0 && filters.q.length < 2 && !filters.min_total && !filters.max_total) {
                if (metaEl) {
                    metaEl.innerHTML = `<span class="order-meta">Type at least 2 characters...</span>`;
                }
                return;
            }

            if (metaEl) {
                metaEl.innerHTML = `<span class="order-loading"></span> Loading...`;
            }

            const url = buildSearchURL(URL_AJAX, filters, pageUrl);

            try {
                const data = await getJSON(url);
                updateTable(data);
            } catch (e) {
                if (String(e).includes('AbortError')) return;
                console.error('Search error:', e);
                if (metaEl) {
                    metaEl.innerHTML = `<span style="color: #ef4444;">Search failed. Please try again.</span>`;
                }
            }
        }

        function updateTable(data) {
            // Update table rows
            if (data.rows && data.rows.length > 0) {
                if (bodyEl) {
                    bodyEl.innerHTML = data.rows.map((o, index) => `
                        <tr class="order-tr" data-href="${esc(o.show_url)}" style="animation-delay: ${index * 0.05}s">
                            <td><span class="order-id">#${esc(o.id)}</span></td>
                            <td>
                                <a class="order-link" href="${esc(o.show_url)}" onclick="event.stopPropagation()">
                                    ${esc(o.order_no || ('ORD-'+o.id))}
                                </a>
                            </td>
                            <td>
                                <div class="order-customer-name">${esc(o.customer_name || 'Guest')}</div>
                                <div class="order-customer-phone">
                                    <i class="fas fa-phone" style="font-size: 10px;"></i>
                                    ${esc(o.customer_phone || 'N/A')}
                                </div>
                            </td>
                            <td><span class="order-total">tk. ${money(o.payable_total)}</span></td>
                            <td class="order-date">
                                <i class="far fa-calendar-alt" style="margin-right: 4px;"></i>
                                ${esc(o.created_at_formatted || o.created_at)}
                            </td>
                            <td>
                                <span class="order-status-badge status-${esc(o.status || 'pending')}">
                                    <span class="status-dot"></span>
                                    ${esc(o.status || 'Pending')}
                                </span>
                            </td>
                            <td>
                                <div class="order-action-group" style="display:flex; gap:4px; justify-content:center;">
                                    <a href="${esc(o.show_url)}" class="order-action-btn" title="View Order Details" onclick="event.stopPropagation()">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                    ${o.can_edit ? `<a href="${esc(o.edit_url)}" onclick="event.stopPropagation();" class="order-action-btn" title="Edit Order" style="color: #2b78f5;">
                                        <i class="fas fa-edit"></i>
                                    </a>` : ''}
                                    ${o.can_split ? `<a href="${esc(o.show_url)}/split" onclick="event.stopPropagation();" class="order-action-btn" title="Split Order" style="color: #1ba100;">
                                        <i class="fas fa-code-branch"></i>
                                    </a>` : ''}
                                    ${o.is_split_parent ? `<a href="${esc(o.split_history_url)}" onclick="event.stopPropagation();" class="order-action-btn" title="Split History" style="color:#007bff;">
                                        <i class="fas fa-history"></i>
                                    </a>` : ''}
                                    ${o.can_trash ? `<button onclick="event.stopPropagation(); confirmTrash('${o.id}')" class="order-action-btn" title="Move to Trash" style="color: #ef4444;">
                                        <i class="fas fa-trash"></i>
                                    </button>` : ''}
                                </div>
                            </td>
                        </tr>
                    `).join('');
                }
            } else {
                if (bodyEl) {
                    bodyEl.innerHTML = `
                        <tr>
                            <td colspan="7">
                                <div class="order-empty">
                                    <span class="empty-icon">📭</span>
                                    <div class="empty-title">No orders found</div>
                                    <p style="color: var(--muted-foreground); font-size: 14px;">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            }

            // Update pagination
            if (paginateWrap) {
                paginateWrap.innerHTML = data.pagination_html || '';
                // Re-bind pagination clicks after updating
                bindPaginationClicks();
            }

            // Update count
            if (countEl) {
                countEl.innerHTML = `<i class="fas fa-file-alt"></i> ${data.count_on_page || 0} results`;
            }

            // Update meta
            if (metaEl) {
                metaEl.innerHTML = `
                    <span class="highlight">${data.first_item || 0}</span> -
                    <span class="highlight">${data.last_item || 0}</span>
                    of <span class="highlight">${data.total || 0}</span> orders
                `;
            }

            bindRowClicks();
        }

        function bindPaginationClicks() {
            if (paginateWrap) {
                // Remove existing event listeners to avoid duplicates
                const links = paginateWrap.querySelectorAll('a');
                links.forEach(a => {
                    // Remove any previously attached listeners by cloning and replacing
                    const newA = a.cloneNode(true);
                    a.parentNode.replaceChild(newA, a);

                    // Add new click handler
                    newA.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Get the page URL from the href
                        const href = this.getAttribute('href');
                        if (href) {
                            // Extract page parameter
                            const urlParams = new URLSearchParams(href.split('?')[1] || '');
                            const page = urlParams.get('page');

                            // Build new URL with current filters and page
                            const filters = getFilters();
                            const baseUrl = URL_AJAX;
                            const url = new URL(baseUrl, window.location.origin);

                            // Add all filters
                            Object.entries(filters).forEach(([key, value]) => {
                                if (value && value !== '') {
                                    url.searchParams.set(key, value);
                                }
                            });

                            // Set the page
                            if (page) {
                                url.searchParams.set('page', page);
                            }

                            // Add status from blade if exists
                            @if (isset($status))
                                if (!url.searchParams.has('status')) {
                                    url.searchParams.set('status', '{{ $status }}');
                                }
                            @endif

                            // Perform search with the new URL
                            runSearch(url.toString());
                        }
                    });
                });
            }
        }

        // Event listeners with debounce
        const debouncedSearch = debounce(() => runSearch(), 300);

        if (qEl) qEl.addEventListener('input', debouncedSearch);
        if (minEl) minEl.addEventListener('input', debouncedSearch);
        if (maxEl) maxEl.addEventListener('input', debouncedSearch);
        if (dateFromEl) dateFromEl.addEventListener('change', debouncedSearch);
        if (dateToEl) dateToEl.addEventListener('change', debouncedSearch);
        if (statusEl) statusEl.addEventListener('change', debouncedSearch);
        if (sortByEl) sortByEl.addEventListener('change', debouncedSearch);
        if (sortDirectionEl) sortDirectionEl.addEventListener('change', debouncedSearch);

        // Manual search button
        if (searchBtn) {
            searchBtn.addEventListener('click', () => runSearch());
        }

        // Clear all filters
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (qEl) qEl.value = '';
                if (minEl) minEl.value = '';
                if (maxEl) maxEl.value = '';
                if (dateFromEl) dateFromEl.value = '';
                if (dateToEl) dateToEl.value = '';
                if (statusEl) statusEl.value = '';
                if (sortByEl) sortByEl.value = 'created_at';
                if (sortDirectionEl) sortDirectionEl.value = 'desc';
                runSearch();
            });
        }

        // Initial bind
        bindRowClicks();
        bindPaginationClicks();
    }

    // Confirm trash function
    function confirmTrash(orderId) {
        if (confirm('Are you sure you want to move this order to trash?')) {
            const reason = prompt('Please provide a reason for moving this order to trash (optional):');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url('orders') }}/' + orderId + '/trash';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            if (reason !== null && reason.trim() !== '') {
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = 'reason';
                reasonInput.value = reason.trim();
                form.appendChild(reasonInput);
            }

            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection
