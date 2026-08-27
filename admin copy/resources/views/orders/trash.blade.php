@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        :root {
            --trash-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --trash-danger: #ef4444;
            --trash-danger-hover: #dc2626;
            --trash-warning: #f59e0b;
            --trash-warning-hover: #d97706;
            --trash-success: #10b981;
            --trash-success-hover: #059669;
            --trash-info: #3b82f6;
            --trash-info-hover: #2563eb;
            --trash-bg-light: #f8fafc;
            --trash-border-light: #e2e8f0;
            --trash-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            --trash-shadow-hover: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Trash specific styles */
        .trash-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .trash-header {
            background: var(--card);
            border-radius: 16px;
            padding: 24px 32px;
            margin-bottom: 32px;
            border: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            animation: slideDown 0.6s ease-out;
            box-shadow: var(--trash-shadow);
        }

        .trash-header-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .trash-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            position: relative;
            overflow: hidden;
        }

        .trash-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
            border-radius: 50%;
            transform: rotate(25deg);
        }

        .trash-title {
            margin: 0;
            font-size: 26px;
            font-weight: 800;
            color: var(--foreground);
            letter-spacing: -0.02em;
        }

        .trash-title span {
            background: var(--trash-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
        }

        .trash-subtitle {
            font-size: 14px;
            color: var(--muted-foreground);
            margin: 4px 0 0 0;
        }

        .trash-badge {
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .trash-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .trash-btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .trash-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.1);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .trash-btn:hover::after {
            opacity: 1;
        }

        .trash-btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .trash-btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.4);
        }

        .trash-btn-warning {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        }

        .trash-btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.4);
        }

        .trash-btn-primary {
            background: var(--trash-gradient);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .trash-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        }

        .trash-btn-ghost {
            background: var(--muted);
            color: var(--foreground);
            border: 1px solid var(--border);
        }

        .trash-btn-ghost:hover {
            background: var(--border);
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .trash-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
            animation: fadeUp 0.6s ease-out 0.1s both;
        }

        .trash-stat-card {
            background: var(--card);
            border-radius: 14px;
            padding: 18px 22px;
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--trash-shadow);
            position: relative;
            overflow: hidden;
        }

        .trash-stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--trash-gradient);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .trash-stat-card:hover::before {
            opacity: 1;
        }

        .trash-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--trash-shadow-hover);
        }

        .trash-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .trash-stat-icon.danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #dc2626;
        }

        .trash-stat-icon.warning {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #d97706;
        }

        .trash-stat-icon.info {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #2563eb;
        }

        .trash-stat-content {
            flex: 1;
            min-width: 0;
        }

        .trash-stat-number {
            font-size: 26px;
            font-weight: 800;
            display: block;
            color: var(--foreground);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .trash-stat-label {
            font-size: 12px;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 600;
        }

        /* Bulk Actions Bar */
        .trash-bulk-actions {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            border-radius: 12px;
            padding: 14px 24px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            display: none;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            animation: slideUp 0.3s ease-out;
            box-shadow: var(--trash-shadow);
        }

        .trash-bulk-actions.show {
            display: flex;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .trash-bulk-info {
            font-size: 14px;
            color: var(--muted-foreground);
        }

        .trash-bulk-info strong {
            color: var(--foreground);
            font-size: 16px;
        }

        .trash-bulk-buttons {
            display: flex;
            gap: 8px;
        }

        .trash-bulk-btn {
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .trash-bulk-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--trash-shadow-hover);
        }

        .trash-bulk-btn-restore {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
        }

        .trash-bulk-btn-restore:hover {
            background: linear-gradient(135deg, #a7f3d0, #6ee7b7);
        }

        .trash-bulk-btn-delete {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }

        .trash-bulk-btn-delete:hover {
            background: linear-gradient(135deg, #fecaca, #fca5a5);
        }

        /* Table Styles */
        .trash-table-wrap {
            overflow-x: auto;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: var(--card);
            box-shadow: var(--trash-shadow);
        }

        .trash-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .trash-table thead {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        }

        .trash-table th {
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
        }

        .trash-table td {
            padding: 14px 18px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 14px;
            color: var(--foreground);
        }

        .trash-table tbody tr {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--card);
        }

        .trash-table tbody tr:hover {
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            transform: scale(1.002);
        }

        .trash-table tbody tr:last-child td {
            border-bottom: none;
        }

        .trash-table .checkbox-cell {
            width: 40px;
            text-align: center;
        }

        .trash-table input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #667eea;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .trash-table input[type="checkbox"]:hover {
            transform: scale(1.1);
        }

        .trash-deleted-at {
            font-size: 13px;
            color: var(--muted-foreground);
        }

        .trash-deleted-by {
            font-size: 13px;
            color: var(--muted-foreground);
        }

        .trash-deleted-by i {
            opacity: 0.6;
        }

        .trash-delete-reason {
            max-width: 200px;
            font-size: 13px;
            color: var(--muted-foreground);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding: 4px 10px;
            background: var(--muted);
            border-radius: 6px;
        }

        .trash-action-btns {
            display: flex;
            gap: 6px;
        }

        .trash-action-btn {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .trash-action-btn-restore {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
        }

        .trash-action-btn-restore:hover {
            background: linear-gradient(135deg, #a7f3d0, #6ee7b7);
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .trash-action-btn-delete {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }

        .trash-action-btn-delete:hover {
            background: linear-gradient(135deg, #fecaca, #fca5a5);
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .trash-empty {
            padding: 60px 24px;
            text-align: center;
        }

        .trash-empty .empty-icon {
            font-size: 72px;
            margin-bottom: 16px;
            display: block;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%,
            100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .trash-empty .empty-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--foreground);
            margin-bottom: 8px;
        }

        .trash-empty .empty-desc {
            color: var(--muted-foreground);
            font-size: 15px;
            margin-bottom: 16px;
        }

        /* Modal */
        .trash-modal-overlay {
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .trash-modal-overlay.show {
            display: flex;
        }

        .trash-modal {
            background: var(--card);
            border-radius: 20px;
            max-width: 500px;
            width: 90%;
            padding: 32px;
            animation: modalSlide 0.3s ease-out;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
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

        .trash-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .trash-modal-header h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--foreground);
        }

        .trash-modal-header h3::before {
            content: '⚠️ ';
            font-size: 20px;
        }

        .trash-modal-close {
            background: var(--muted);
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--muted-foreground);
            transition: all 0.3s;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .trash-modal-close:hover {
            color: var(--foreground);
            background: var(--border);
            transform: rotate(90deg);
        }

        .trash-modal-body {
            margin-bottom: 24px;
        }

        .trash-modal-body p {
            font-size: 15px;
            color: var(--muted-foreground);
            line-height: 1.6;
            margin: 0;
        }

        .trash-modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .trash-modal-footer .trash-btn {
            min-width: 100px;
            justify-content: center;
        }

        /* Pagination custom styles */
        .trash-pagination {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }

        .trash-pagination .pagination {
            gap: 4px;
        }

        .trash-pagination .page-link {
            border-radius: 8px;
            border: 1px solid var(--border);
            color: var(--foreground);
            transition: all 0.3s;
            padding: 8px 16px;
        }

        .trash-pagination .page-link:hover {
            background: var(--trash-gradient);
            color: white;
            border-color: transparent;
        }

        .trash-pagination .active .page-link {
            background: var(--trash-gradient);
            border-color: transparent;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .trash-container {
                padding: 16px;
            }

            .trash-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px 20px;
            }

            .trash-header-left {
                width: 100%;
            }

            .trash-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .trash-stats {
                grid-template-columns: 1fr 1fr;
            }

            .trash-table td,
            .trash-table th {
                padding: 10px 12px;
                font-size: 13px;
            }

            .trash-stat-card {
                padding: 14px 16px;
            }

            .trash-stat-number {
                font-size: 22px;
            }

            .trash-bulk-actions {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .trash-bulk-buttons {
                justify-content: center;
            }

            .trash-modal {
                padding: 24px;
            }
        }

        @media (max-width: 480px) {
            .trash-stats {
                grid-template-columns: 1fr;
            }

            .trash-table-wrap {
                border-radius: 10px;
            }

            .trash-table td,
            .trash-table th {
                padding: 8px 10px;
                font-size: 12px;
            }

            .trash-action-btn {
                padding: 4px 8px;
                font-size: 11px;
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

        /* Scrollbar styling */
        .trash-table-wrap::-webkit-scrollbar {
            height: 6px;
        }

        .trash-table-wrap::-webkit-scrollbar-track {
            background: var(--muted);
            border-radius: 10px;
        }

        .trash-table-wrap::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }

        .trash-table-wrap::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2, #667eea);
        }
    </style>

    <div class="trash-container">
        <!-- Header -->
        <div class="trash-header">
            <div class="trash-header-left">
                <div class="trash-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <div>
                    <h1 class="trash-title">Order <span>Trash</span></h1>
                    <p class="trash-subtitle">Manage permanently deleted orders</p>
                </div>
            </div>
            <div class="trash-actions">
                <span class="trash-badge">
                    <i class="fas fa-trash"></i> {{ $trashedOrders->total() }} items
                </span>
                @if ($trashedOrders->isNotEmpty())
                    <button class="trash-btn trash-btn-danger" onclick="confirmEmptyTrash()">
                        <i class="fas fa-bomb"></i> Empty Trash
                    </button>
                @endif
                <a href="{{ route('orders.index') }}" class="trash-btn trash-btn-ghost">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="trash-stats">
            <div class="trash-stat-card">
                <div class="trash-stat-icon danger">
                    <i class="fas fa-trash"></i>
                </div>
                <div class="trash-stat-content">
                    <span class="trash-stat-number">{{ $trashedOrders->total() }}</span>
                    <span class="trash-stat-label">Trashed Orders</span>
                </div>
            </div>
            <div class="trash-stat-card">
                <div class="trash-stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="trash-stat-content">
                    <span class="trash-stat-number">
                        {{ $trashedOrders->first() ? $trashedOrders->first()->deleted_at->diffForHumans() : '-' }}
                    </span>
                    <span class="trash-stat-label">Latest Deletion</span>
                </div>
            </div>
            <div class="trash-stat-card">
                <div class="trash-stat-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <div class="trash-stat-content">
                    <span class="trash-stat-number">
                        {{ $trashedOrders->pluck('deleted_by')->unique()->count() }}
                    </span>
                    <span class="trash-stat-label">Users Who Deleted</span>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="trash-bulk-actions" id="bulkActions">
            <div class="trash-bulk-info">
                <strong id="selectedCount">0</strong> orders selected
            </div>
            <div class="trash-bulk-buttons">
                <button class="trash-bulk-btn trash-bulk-btn-restore" onclick="bulkRestore()">
                    <i class="fas fa-undo"></i> Restore Selected
                </button>
                <button class="trash-bulk-btn trash-bulk-btn-delete" onclick="bulkDelete()">
                    <i class="fas fa-times"></i> Delete Selected
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="trash-table-wrap">
            <table class="trash-table">
                <thead>
                    <tr>
                        <th class="checkbox-cell">
                            <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes()">
                        </th>
                        <th>ID</th>
                        <th>Order No</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Deleted At</th>
                        <th>Deleted By</th>
                        <th>Reason</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trashedOrders as $order)
                        <tr>
                            <td class="checkbox-cell">
                                <input type="checkbox" class="order-checkbox" value="{{ $order->id }}"
                                    onchange="updateBulkActions()">
                            </td>
                            <td><strong>#{{ $order->id }}</strong></td>
                            <td>{{ $order->order_no ?? 'ORD-' . $order->id }}</td>
                            <td>
                                <div><strong>{{ $order->customer?->name ?? 'Guest' }}</strong></div>
                                <div class="trash-deleted-by">{{ $order->customer?->phone ?? '' }}</div>
                            </td>
                            <td><strong>tk.{{ number_format($order->payable_total, 2) }}</strong></td>
                            <td>
                                <div class="trash-deleted-at">
                                    <i class="fas fa-calendar-alt" style="font-size: 10px;"></i>
                                    {{ $order->deleted_at->format('M d, Y H:i') }}
                                </div>
                                <div class="trash-deleted-at" style="font-size: 11px; margin-top: 2px;">
                                    ({{ $order->deleted_at->diffForHumans() }})
                                </div>
                            </td>
                            <td>
                                <span class="trash-deleted-by">
                                    <i class="fas fa-user" style="font-size: 10px;"></i>
                                    {{ $order->deletedBy?->name ?? 'System' }}
                                </span>
                            </td>
                            <td>
                                <span class="trash-delete-reason" title="{{ $order->delete_reason ?? 'No reason provided' }}">
                                    <i class="fas fa-info-circle" style="font-size: 10px; opacity: 0.6;"></i>
                                    {{ $order->delete_reason ?? 'No reason provided' }}
                                </span>
                            </td>
                            <td>
                                <div class="trash-action-btns">
                                    <button class="trash-action-btn trash-action-btn-restore"
                                        onclick="restoreOrder('{{ $order->id }}')">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                    <button class="trash-action-btn trash-action-btn-delete"
                                        onclick="confirmDelete('{{ $order->id }}')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="trash-empty">
                                    <span class="empty-icon">🗑️</span>
                                    <div class="empty-title">Trash is empty</div>
                                    <p class="empty-desc">No orders have been moved to trash yet</p>
                                    <a href="{{ route('orders.index') }}" class="trash-btn trash-btn-primary"
                                        style="margin-top: 12px;">
                                        <i class="fas fa-arrow-left"></i> Back to Orders
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($trashedOrders->hasPages())
            <div class="trash-pagination">
                {{ $trashedOrders->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    <!-- Confirm Modal -->
    <div class="trash-modal-overlay" id="confirmModal">
        <div class="trash-modal">
            <div class="trash-modal-header">
                <h3 id="modalTitle">Confirm Action</h3>
                <button class="trash-modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="trash-modal-body" id="modalBody">
                <p id="modalMessage">Are you sure you want to perform this action?</p>
            </div>
            <div class="trash-modal-footer">
                <button class="trash-btn trash-btn-ghost" onclick="closeModal()">Cancel</button>
                <button class="trash-btn trash-btn-danger" id="modalConfirmBtn"
                    onclick="executeModalAction()">Confirm</button>
            </div>
        </div>
    </div>

    <script>
        let modalAction = null;
        let modalData = null;

        // Toggle all checkboxes
        function toggleAllCheckboxes() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateBulkActions();
        }

        // Update bulk actions visibility
        function updateBulkActions() {
            const checked = document.querySelectorAll('.order-checkbox:checked').length;
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            if (checked > 0) {
                bulkActions.classList.add('show');
                selectedCount.textContent = checked;
            } else {
                bulkActions.classList.remove('show');
            }
        }

        // Show modal
        function showModal(title, message, action, data = null) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            modalAction = action;
            modalData = data;
            document.getElementById('confirmModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Close modal
        function closeModal() {
            document.getElementById('confirmModal').classList.remove('show');
            modalAction = null;
            modalData = null;
            document.body.style.overflow = '';
        }

        // Execute modal action
        function executeModalAction() {
            if (modalAction) {
                modalAction(modalData);
            }
            closeModal();
        }

        // Helper function to submit form
        function submitForm(action, method = 'POST', data = {}) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = action;

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add method spoofing if needed
            if (method !== 'POST') {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = method;
                form.appendChild(methodInput);
            }

            // Add additional data
            for (const [key, value] of Object.entries(data)) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        }

        // Restore single order - FIXED with form submission
        function restoreOrder(id) {
            showModal(
                'Restore Order',
                'Are you sure you want to restore this order? It will be moved back to the main orders list.',
                function() {
                    const url = '{{ route('orders.restore', ['id' => '__ID__']) }}'.replace('__ID__', id);
                    submitForm(url, 'POST');
                }
            );
        }

        // Confirm delete - FIXED with form submission
        function confirmDelete(id) {
            showModal(
                'Permanently Delete Order',
                'Are you sure you want to permanently delete this order? This action cannot be undone.',
                function() {
                    const url = '{{ route('orders.force-delete', ['id' => '__ID__']) }}'.replace('__ID__', id);
                    submitForm(url, 'DELETE');
                }
            );
        }

        // Bulk restore
        function bulkRestore() {
            const ids = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
            if (ids.length === 0) return;

            showModal(
                'Restore Selected Orders',
                `Are you sure you want to restore tk.{ids.length} selected order(s)?`,
                function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('orders.restore-multiple') }}';

                    // CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    // Order IDs
                    const idsInput = document.createElement('input');
                    idsInput.type = 'hidden';
                    idsInput.name = 'order_ids[]';
                    idsInput.value = ids.join(',');
                    form.appendChild(idsInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            );
        }

        // Bulk delete
        function bulkDelete() {
            const ids = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
            if (ids.length === 0) return;

            showModal(
                'Permanently Delete Selected Orders',
                `Are you sure you want to permanently delete tk.{ids.length} selected order(s)? This action cannot be undone.`,
                function() {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('orders.force-delete-multiple') }}';

                    // CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    // Method spoofing for DELETE
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    // Order IDs
                    const idsInput = document.createElement('input');
                    idsInput.type = 'hidden';
                    idsInput.name = 'order_ids[]';
                    idsInput.value = ids.join(',');
                    form.appendChild(idsInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            );
        }

        // Confirm empty trash - FIXED with form submission
        function confirmEmptyTrash() {
            showModal(
                'Empty Trash',
                'Are you sure you want to permanently delete all orders in the trash? This action cannot be undone.',
                function() {
                    submitForm('{{ route('orders.empty-trash') }}', 'DELETE');
                }
            );
        }

        // Close modal on overlay click
        document.getElementById('confirmModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Initialize bulk actions
        document.addEventListener('DOMContentLoaded', function() {
            updateBulkActions();
        });
    </script>
@endsection
