@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        :root {
            --customer-gradient: linear-gradient(135deg, var(--sidebar-primary) 0%, color-mix(in oklch, var(--sidebar-primary) 80%, black) 100%);
            --customer-success: var(--success);
            --customer-danger: var(--danger);
            --customer-warning: var(--warning);
            --customer-info: var(--info);
            --customer-bg-light: var(--muted);
            --customer-shadow: var(--card-shadow);
            --customer-shadow-hover: var(--card-shadow-hover);
        }

        .customer-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* Header */
        .customer-header {
            background: var(--card);
            border-radius: calc(var(--radius) + 4px);
            padding: 24px 32px;
            margin-bottom: 32px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            box-shadow: var(--customer-shadow);
            animation: slideDown 0.6s ease-out;
        }

        .customer-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .customer-icon {
            width: 52px;
            height: 52px;
            border-radius: calc(var(--radius) + 4px);
            background: var(--customer-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 12px var(--accent-glow);
            position: relative;
            overflow: hidden;
        }

        .customer-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: rotate(25deg);
        }

        .customer-title {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            color: var(--foreground);
            letter-spacing: -0.02em;
        }

        .customer-title span {
            background: var(--customer-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .customer-subtitle {
            font-size: 14px;
            color: var(--muted-foreground);
            margin: 4px 0 0 0;
        }

        .customer-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .customer-btn {
            padding: 10px 20px;
            border-radius: calc(var(--radius) - 2px);
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .customer-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: opacity var(--transition-normal);
        }

        .customer-btn:hover::after {
            opacity: 1;
        }

        .customer-btn-primary {
            background: var(--customer-gradient);
            color: white;
            box-shadow: 0 2px 8px var(--accent-glow);
        }

        .customer-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px var(--accent-glow);
        }

        .customer-btn-success {
            background: linear-gradient(135deg, var(--success), color-mix(in oklch, var(--success) 80%, black));
            color: white;
            box-shadow: 0 2px 8px color-mix(in oklch, var(--success) 35%, transparent);
        }

        .customer-btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px color-mix(in oklch, var(--success) 45%, transparent);
        }

        .customer-btn-danger {
            background: linear-gradient(135deg, var(--danger), color-mix(in oklch, var(--danger) 80%, black));
            color: white;
            box-shadow: 0 2px 8px color-mix(in oklch, var(--danger) 35%, transparent);
        }

        .customer-btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px color-mix(in oklch, var(--danger) 45%, transparent);
        }

        .customer-btn-ghost {
            background: var(--muted);
            color: var(--foreground);
            border: 1px solid var(--border);
        }

        .customer-btn-ghost:hover {
            background: var(--border);
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .customer-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
            animation: fadeUp 0.6s ease-out 0.1s both;
        }

        .customer-stat-card {
            background: var(--card);
            border-radius: calc(var(--radius) + 4px);
            padding: 16px 20px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--customer-shadow);
            position: relative;
            overflow: hidden;
        }

        .customer-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--customer-gradient);
            opacity: 0;
            transition: opacity var(--transition-normal);
        }

        .customer-stat-card:hover::before {
            opacity: 1;
        }

        .customer-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--customer-shadow-hover);
        }

        .customer-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .customer-stat-icon.primary {
            background: color-mix(in oklch, var(--sidebar-primary) 18%, var(--card));
            color: var(--sidebar-primary);
        }

        .customer-stat-icon.success {
            background: color-mix(in oklch, var(--success) 18%, var(--card));
            color: var(--success);
        }

        .customer-stat-icon.danger {
            background: color-mix(in oklch, var(--danger) 18%, var(--card));
            color: var(--danger);
        }

        .customer-stat-icon.warning {
            background: color-mix(in oklch, var(--warning) 18%, var(--card));
            color: var(--warning);
        }

        .customer-stat-icon.info {
            background: color-mix(in oklch, var(--info) 18%, var(--card));
            color: var(--info);
        }

        .customer-stat-content {
            flex: 1;
            min-width: 0;
        }

        .customer-stat-number {
            font-size: 22px;
            font-weight: 800;
            display: block;
            color: var(--foreground);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .customer-stat-label {
            font-size: 11px;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
        }

        /* Search & Filter */
        .customer-toolbar {
            background: var(--card);
            border-radius: calc(var(--radius) + 4px);
            padding: 16px 24px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            align-items: center;
            box-shadow: var(--customer-shadow);
        }

        .customer-search {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .customer-search input {
            width: 100%;
            padding: 10px 16px 10px 42px;
            border-radius: calc(var(--radius) - 2px);
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--foreground);
            font-size: 14px;
            transition: all var(--transition-normal);
        }

        .customer-search input:focus {
            outline: none;
            border-color: var(--sidebar-primary);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .customer-search .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted-foreground);
        }

        .customer-filter-select {
            padding: 10px 16px;
            border-radius: calc(var(--radius) - 2px);
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--foreground);
            font-size: 14px;
            cursor: pointer;
            min-width: 140px;
            transition: all var(--transition-normal);
        }

        .customer-filter-select:focus {
            outline: none;
            border-color: var(--sidebar-primary);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        /* Table */
        .customer-table-wrap {
            overflow-x: auto;
            border-radius: calc(var(--radius) + 4px);
            border: 1px solid var(--border);
            background: var(--card);
            box-shadow: var(--customer-shadow);
        }

        .customer-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .customer-table thead {
            background: var(--muted);
        }

        .customer-table th {
            padding: 14px 18px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted-foreground);
            border-bottom: 2px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 10;
            cursor: pointer;
            user-select: none;
            transition: color var(--transition-normal);
        }

        .customer-table th:hover {
            color: var(--foreground);
        }

        .customer-table th .sort-icon {
            margin-left: 4px;
            opacity: 0.5;
        }

        .customer-table th.active .sort-icon {
            opacity: 1;
        }

        .customer-table td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 14px;
            color: var(--foreground);
        }

        .customer-table tbody tr {
            transition: all var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--card);
        }

        .customer-table tbody tr:hover {
            background: var(--muted);
            transform: scale(1.002);
        }

        .customer-table tbody tr:last-child td {
            border-bottom: none;
        }

        .customer-name-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .customer-avatar {
            width: 40px;
            height: 40px;
            border-radius: calc(var(--radius) - 2px);
            background: var(--customer-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        .customer-name {
            font-weight: 600;
            color: var(--foreground);
        }

        .customer-phone {
            font-size: 13px;
            color: var(--muted-foreground);
        }

        .customer-badge {
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
        }

        .customer-badge-regular {
            background: var(--secondary);
            color: var(--secondary-foreground);
        }

        .customer-badge-premium {
            background: color-mix(in oklch, var(--warning) 18%, var(--card));
            color: var(--warning);
        }

        .customer-badge-vip {
            background: color-mix(in oklch, var(--sidebar-primary) 18%, var(--card));
            color: var(--sidebar-primary);
        }

        .customer-badge-active {
            background: color-mix(in oklch, var(--success) 18%, var(--card));
            color: var(--success);
        }

        .customer-badge-inactive {
            background: color-mix(in oklch, var(--danger) 18%, var(--card));
            color: var(--danger);
        }

        .customer-balance {
            font-weight: 600;
        }

        .customer-balance.due {
            color: var(--danger);
        }

        .customer-balance.advance {
            color: var(--success);
        }

        .customer-action-btns {
            display: flex;
            gap: 6px;
        }

        .customer-action-btn {
            padding: 6px 10px;
            border-radius: calc(var(--radius) - 2px);
            font-size: 12px;
            border: none;
            cursor: pointer;
            transition: all var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .customer-action-btn:hover {
            transform: scale(1.1);
        }

        .customer-action-btn-view {
            background: color-mix(in oklch, var(--info) 18%, var(--card));
            color: var(--info);
        }

        .customer-action-btn-view:hover {
            background: color-mix(in oklch, var(--info) 30%, var(--card));
        }

        .customer-action-btn-edit {
            background: color-mix(in oklch, var(--success) 18%, var(--card));
            color: var(--success);
        }

        .customer-action-btn-edit:hover {
            background: color-mix(in oklch, var(--success) 30%, var(--card));
        }

        .customer-action-btn-delete {
            background: color-mix(in oklch, var(--danger) 18%, var(--card));
            color: var(--danger);
        }

        .customer-action-btn-delete:hover {
            background: color-mix(in oklch, var(--danger) 30%, var(--card));
        }

        /* Modal */
        .customer-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
        }

        .customer-modal-overlay.show {
            display: flex;
        }

        .customer-modal {
            background: var(--card);
            border-radius: calc(var(--radius) + 4px);
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 32px;
            animation: modalSlide 0.3s ease-out;
            box-shadow: var(--dropdown-shadow);
            border: 1px solid var(--border);
        }

        @keyframes modalSlide {
            from {
                transform: scale(0.9) translateY(20px);
                opacity: 0;
            }
            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .customer-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .customer-modal-header h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--foreground);
        }

        .customer-modal-close {
            background: var(--muted);
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--muted-foreground);
            transition: all var(--transition-normal);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .customer-modal-close:hover {
            color: var(--foreground);
            background: var(--border);
            transform: rotate(90deg);
        }

        /* Form */
        .customer-form-group {
            margin-bottom: 18px;
        }

        .customer-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--foreground);
            margin-bottom: 6px;
        }

        .customer-form-group label .required {
            color: var(--danger);
            margin-left: 2px;
        }

        .customer-form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: calc(var(--radius) - 2px);
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--foreground);
            font-size: 14px;
            transition: all var(--transition-normal);
        }

        .customer-form-control:focus {
            outline: none;
            border-color: var(--sidebar-primary);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .customer-form-control:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .customer-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .customer-container {
                padding: 16px;
            }

            .customer-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 20px;
            }

            .customer-actions {
                width: 100%;
            }

            .customer-stats {
                grid-template-columns: 1fr 1fr;
            }

            .customer-toolbar {
                flex-direction: column;
            }

            .customer-search {
                width: 100%;
            }

            .customer-form-row {
                grid-template-columns: 1fr;
            }

            .customer-table td,
            .customer-table th {
                padding: 10px 12px;
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .customer-stats {
                grid-template-columns: 1fr;
            }

            .customer-stat-number {
                font-size: 20px;
            }
        }

        /* Animations */
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
    </style>

    <div class="customer-container">
        <!-- Header -->
        <div class="customer-header" data-reveal>
            <div class="customer-header-left">
                <div class="customer-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h1 class="customer-title">Customer <span>Management</span></h1>
                    <p class="customer-subtitle">Manage your customer relationships</p>
                </div>
            </div>
            <div class="customer-actions">
                <button class="customer-btn customer-btn-primary" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Add Customer
                </button>
                <a href="{{ route('customers.export') }}" class="customer-btn customer-btn-success">
                    <i class="fas fa-download"></i> Export
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="customer-stats" data-reveal>
            <div class="customer-stat-card">
                <div class="customer-stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="customer-stat-content">
                    <span class="customer-stat-number">{{ $stats['total'] }}</span>
                    <span class="customer-stat-label">Total Customers</span>
                </div>
            </div>
            <div class="customer-stat-card">
                <div class="customer-stat-icon success">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="customer-stat-content">
                    <span class="customer-stat-number">{{ $stats['active'] }}</span>
                    <span class="customer-stat-label">Active</span>
                </div>
            </div>
            <div class="customer-stat-card">
                <div class="customer-stat-icon danger">
                    <i class="fas fa-user-times"></i>
                </div>
                <div class="customer-stat-content">
                    <span class="customer-stat-number">{{ $stats['inactive'] }}</span>
                    <span class="customer-stat-label">Inactive</span>
                </div>
            </div>
            <div class="customer-stat-card">
                <div class="customer-stat-icon warning">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="customer-stat-content">
                    <span class="customer-stat-number">tk.{{ number_format($stats['total_due'], 2) }}</span>
                    <span class="customer-stat-label">Total Due</span>
                </div>
            </div>
            <div class="customer-stat-card">
                <div class="customer-stat-icon info">
                    <i class="fas fa-gem"></i>
                </div>
                <div class="customer-stat-content">
                    <span class="customer-stat-number">{{ number_format($stats['total_rewards']) }}</span>
                    <span class="customer-stat-label">Reward Points</span>
                </div>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="customer-toolbar" data-reveal>
            <div class="customer-search">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search customers by name, phone, email..."
                    value="{{ request('q') }}" oninput="applyFilters()">
            </div>
            <select class="customer-filter-select" id="typeFilter" onchange="applyFilters()">
                <option value="">All Types</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
            <select class="customer-filter-select" id="statusFilter" onchange="applyFilters()">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <!-- Table -->
        <div class="customer-table-wrap" data-reveal>
            <table class="customer-table">
                <thead>
                    <tr>
                        <th onclick="sortTable('id')">
                            ID <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th onclick="sortTable('name')">
                            Customer <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th onclick="sortTable('type')">
                            Type <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th onclick="sortTable('phone')">
                            Contact <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th onclick="sortTable('due_balance')" class="text-right">
                            Balance <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th onclick="sortTable('reward_points')" class="text-right">
                            Rewards <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th onclick="sortTable('is_active')">
                            Status <i class="fas fa-sort sort-icon"></i>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                        <tr>
                            <td>#{{ $customer->id }}</td>
                            <td>
                                <div class="customer-name-cell">
                                    <div class="customer-avatar">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="customer-name">{{ $customer->name }}</div>
                                        <div class="customer-phone">
                                            <i class="fas fa-envelope" style="font-size: 10px;"></i>
                                            {{ $customer->email ?? 'No email' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="customer-badge
                                    {{ $customer->type == 'vip' ? 'customer-badge-vip' :
                                       ($customer->type == 'premium' ? 'customer-badge-premium' :
                                       'customer-badge-regular') }}">
                                    {{ ucfirst($customer->type ?? 'Regular') }}
                                </span>
                            </td>
                            <td>
                                <div><i class="fas fa-phone" style="font-size: 12px; opacity: 0.6;"></i>
                                    {{ $customer->phone ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="text-right">
                                @if($customer->due_balance > 0)
                                    <span class="customer-balance due">
                                        <i class="fas fa-arrow-up" style="font-size: 10px;"></i>
                                        tk.{{ number_format($customer->due_balance, 2) }}
                                    </span>
                                @elseif($customer->advance_balance > 0)
                                    <span class="customer-balance advance">
                                        <i class="fas fa-arrow-down" style="font-size: 10px;"></i>
                                        tk.{{ number_format($customer->advance_balance, 2) }}
                                    </span>
                                @else
                                    <span style="color: var(--muted-foreground);">tk 0.00</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <span style="font-weight: 600; color: #7C3AED;">
                                    {{ number_format($customer->reward_points) }}
                                </span>
                            </td>
                            <td>
                                <span class="customer-badge {{ $customer->is_active ? 'customer-badge-active' : 'customer-badge-inactive' }}">
                                    {{ $customer->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="customer-action-btns">
                                    <a href="{{ route('customers.show', $customer) }}"
                                       class="customer-action-btn customer-action-btn-view" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}"
                                       class="customer-action-btn customer-action-btn-edit" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="customer-action-btn customer-action-btn-delete"
                                            onclick="deleteCustomer({{ $customer->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div style="padding: 60px 24px; text-align: center;">
                                    <i class="fas fa-users" style="font-size: 48px; color: var(--muted-foreground); display: block; margin-bottom: 16px;"></i>
                                    <h3 style="font-size: 20px; font-weight: 600; color: var(--foreground); margin-bottom: 8px;">
                                        No Customers Found
                                    </h3>
                                    <p style="color: var(--muted-foreground);">
                                        Start by adding your first customer
                                    </p>
                                    <button class="customer-btn customer-btn-primary" onclick="openCreateModal()" style="margin-top: 16px;">
                                        <i class="fas fa-plus"></i> Add Customer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($customers->hasPages())
            <div style="margin-top: 24px;">
                {{ $customers->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    <div class="customer-modal-overlay" id="customerModal">
        <div class="customer-modal">
            <div class="customer-modal-header">
                <h3 id="modalTitle">Add Customer</h3>
                <button class="customer-modal-close" onclick="closeModal()">×</button>
            </div>
            <form id="customerForm" onsubmit="saveCustomer(event)">
                @csrf
                <input type="hidden" id="customerId" name="id">

                <div class="customer-form-group">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" class="customer-form-control" id="name" name="name" required>
                </div>

                <div class="customer-form-row">
                    <div class="customer-form-group">
                        <label>Phone</label>
                        <input type="text" class="customer-form-control" id="phone" name="phone">
                    </div>
                    <div class="customer-form-group">
                        <label>Email</label>
                        <input type="email" class="customer-form-control" id="email" name="email">
                    </div>
                </div>

                <div class="customer-form-row">
                    <div class="customer-form-group">
                        <label>Type</label>
                        <select class="customer-form-control" id="type" name="type">
                            <option value="regular">Regular</option>
                            <option value="premium">Premium</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div class="customer-form-group">
                        <label>Status</label>
                        <select class="customer-form-control" id="is_active" name="is_active">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="customer-form-group">
                    <label>Address</label>
                    <input type="text" class="customer-form-control" id="address" name="address">
                </div>

                <div class="customer-form-group">
                    <label>Notes</label>
                    <textarea class="customer-form-control" id="notes" name="notes" rows="3"></textarea>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 20px;">
                    <button type="button" class="customer-btn customer-btn-ghost" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="customer-btn customer-btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="customer-modal-overlay" id="deleteModal">
        <div class="customer-modal" style="max-width: 450px;">
            <div class="customer-modal-header">
                <h3>Delete Customer</h3>
                <button class="customer-modal-close" onclick="closeDeleteModal()">×</button>
            </div>
            <div style="margin-bottom: 24px;">
                <p style="font-size: 15px; color: var(--muted-foreground);">
                    Are you sure you want to delete this customer? This action cannot be undone.
                </p>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button class="customer-btn customer-btn-ghost" onclick="closeDeleteModal()">Cancel</button>
                <button class="customer-btn customer-btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>

    <script>
        let deleteCustomerId = null;

        // Apply filters
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const type = document.getElementById('typeFilter').value;
            const status = document.getElementById('statusFilter').value;

            let url = new URL(window.location.href);
            if (search) url.searchParams.set('q', search);
            else url.searchParams.delete('q');
            if (type) url.searchParams.set('type', type);
            else url.searchParams.delete('type');
            if (status) url.searchParams.set('status', status);
            else url.searchParams.delete('status');

            window.location.href = url.toString();
        }

        // Sort table
        function sortTable(field) {
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort');
            const currentDirection = url.searchParams.get('direction');

            if (currentSort === field) {
                url.searchParams.set('direction', currentDirection === 'asc' ? 'desc' : 'asc');
            } else {
                url.searchParams.set('sort', field);
                url.searchParams.set('direction', 'asc');
            }

            window.location.href = url.toString();
        }

        // Open create modal
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add Customer';
            document.getElementById('customerId').value = '';
            document.getElementById('customerForm').reset();
            document.getElementById('customerModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Open edit modal
        function openEditModal(id) {
            fetch(`/customers/tk.{id}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalTitle').textContent = 'Edit Customer';
                    document.getElementById('customerId').value = data.customer.id;
                    document.getElementById('name').value = data.customer.name;
                    document.getElementById('phone').value = data.customer.phone || '';
                    document.getElementById('email').value = data.customer.email || '';
                    document.getElementById('type').value = data.customer.type || 'regular';
                    document.getElementById('is_active').value = data.customer.is_active ? '1' : '0';
                    document.getElementById('address').value = data.customer.address || '';
                    document.getElementById('notes').value = data.customer.notes || '';
                    document.getElementById('customerModal').classList.add('show');
                    document.body.style.overflow = 'hidden';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load customer data');
                });
        }

        // Close modal
        function closeModal() {
            document.getElementById('customerModal').classList.remove('show');
            document.body.style.overflow = '';
        }

        // Close delete modal
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('show');
            document.body.style.overflow = '';
        }

        // Save customer
        function saveCustomer(event) {
            event.preventDefault();
            const form = document.getElementById('customerForm');
            const id = document.getElementById('customerId').value;
            const formData = new FormData(form);

            const url = id ? `/customers/tk.{id}` : '/customers';
            const method = id ? 'PUT' : 'POST';

            // Convert FormData to object
            const data = {};
            formData.forEach((value, key) => {
                if (key !== '_token') {
                    data[key] = value;
                }
            });

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the customer');
            });
        }

        // Delete customer
        function deleteCustomer(id) {
            deleteCustomerId = id;
            document.getElementById('deleteModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Confirm delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (!deleteCustomerId) return;

            fetch(`/customers/${deleteCustomerId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                closeDeleteModal();
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the customer');
                closeDeleteModal();
            });
        });

        // Close modals on overlay click
        document.querySelectorAll('.customer-modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        });

        // Close modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.customer-modal-overlay.show').forEach(modal => {
                    modal.classList.remove('show');
                });
                document.body.style.overflow = '';
            }
        });

        // Debounce search
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });
    </script>
@endsection
