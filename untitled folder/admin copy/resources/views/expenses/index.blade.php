@extends('layouts.app')

@section('content')
    <div class="expense-container" style="min-height: calc(100vh - var(--header-height));">

        @if (session('success'))
            <div class="alert-toast" role="alert">
                <div class="alert-content">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        @endif

        {{-- Header --}}
        <div class="page-header">
            <div class="header-content">
                <div class="header-title-section">
                    <h1 class="page-title">Expense Management</h1>
                    <div class="header-stats">
                        <div class="stat-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="9" y1="21" x2="9" y2="9"></line>
                            </svg>
                            <span>{{ $expenses->total() }} {{ Str::plural('expense', $expenses->total()) }}</span>
                        </div>
                        <div class="stat-badge total-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"></line>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <span>BDT {{ number_format((float) $filteredTotal, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="header-actions">
                    <div class="action-group">
                        <a href="{{ route('expenses.trash') }}" class="action-btn secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                </path>
                            </svg>
                            <span>Trash</span>
                        </a>
                        <a href="{{ route('expenses.export.csv', request()->query()) }}" class="action-btn secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>Export</span>
                        </a>
                        <a href="{{ route('expenses.create') }}" class="action-btn primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                            <span>Add Expense</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats Cards --}}
        <div class="quick-stats">
            <div class="stat-card stat-card-total">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Expenses</div>
                    <div class="stat-value">{{ $expenses->total() }}</div>
                    <div class="stat-change positive">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                        <span>{{ $expenses->total() > 0 ? 'Active' : 'No records' }}</span>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-amount">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total Amount</div>
                    <div class="stat-value">BDT {{ number_format((float) $filteredTotal, 2) }}</div>
                    <div class="stat-change positive">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                        <span>Filtered total</span>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-category">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z">
                            </path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                    </div>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Categories</div>
                    <div class="stat-value">{{ $categories->count() }}</div>
                    <div class="stat-change">
                        <span>{{ $categories->count() > 0 ? 'Active categories' : 'No categories' }}</span>
                    </div>
                </div>
            </div>

            <div class="stat-card stat-card-avg">
                <div class="stat-icon-wrapper">
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M12 2v4"></path>
                            <path d="M12 18v4"></path>
                            <path d="M4.93 4.93l2.83 2.83"></path>
                            <path d="M16.24 16.24l2.83 2.83"></path>
                            <path d="M2 12h4"></path>
                            <path d="M18 12h4"></path>
                            <path d="M4.93 19.07l2.83-2.83"></path>
                            <path d="M16.24 7.76l2.83-2.83"></path>
                        </svg>
                    </div>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Average Expense</div>
                    <div class="stat-value">BDT {{ $expenses->count() > 0 ? number_format($filteredTotal / $expenses->count(), 2) : '0.00' }}</div>
                    <div class="stat-change">
                        <span>Per expense</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Card --}}
        <div class="filter-card">
            <div class="filter-header">
                <div class="filter-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <h3>Filters & Search</h3>
                </div>
                <div class="filter-results">
                    <span class="results-count">{{ $expenses->total() }} results</span>
                </div>
            </div>

            <form method="GET" action="{{ route('expenses.index') }}" class="filter-form">
                <div class="filter-grid">
                    {{-- Search --}}
                    <div class="filter-group">
                        <label class="filter-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            Search
                        </label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            placeholder="Search title, vendor, reference..." class="filter-input">
                    </div>

                    {{-- Date Range --}}
                    <div class="filter-group">
                        <label class="filter-label">Date Range</label>
                        <div class="date-range">
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="date-input">
                            <span class="date-separator">to</span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="date-input">
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="filter-group">
                        <label class="filter-label">Category</label>
                        <select name="category_id" class="filter-select">
                            <option value="">All Categories</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Location --}}
                    <div class="filter-group">
                        <label class="filter-label">Location</label>
                        <select name="location_id" class="filter-select">
                            <option value="">All Locations</option>
                            @foreach ($locations as $l)
                                <option value="{{ $l->id }}" @selected(request('location_id') == $l->id)>{{ $l->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Payment Method --}}
                    <div class="filter-group">
                        <label class="filter-label">Payment Method</label>
                        <select name="payment_method" class="filter-select">
                            <option value="">All Methods</option>
                            @foreach ($paymentMethods as $m)
                                <option value="{{ $m }}" @selected(request('payment_method') == $m)>{{ $m }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Amount Range --}}
                    <div class="filter-group">
                        <label class="filter-label">Amount Range (BDT)</label>
                        <div class="amount-range">
                            <input type="number" step="0.01" name="min_amount" value="{{ request('min_amount') }}"
                                placeholder="Min" class="amount-input">
                            <span class="amount-separator">-</span>
                            <input type="number" step="0.01" name="max_amount" value="{{ request('max_amount') }}"
                                placeholder="Max" class="amount-input">
                        </div>
                    </div>

                    {{-- Sort Options --}}
                    <div class="filter-group">
                        <label class="filter-label">Sort By</label>
                        <div class="sort-options">
                            <select name="sort" class="sort-select">
                                <option value="expense_date" @selected(request('sort', 'expense_date') == 'expense_date')>Date</option>
                                <option value="amount" @selected(request('sort') == 'amount')>Amount</option>
                                <option value="created_at" @selected(request('sort') == 'created_at')>Created</option>
                                <option value="expense_no" @selected(request('sort') == 'expense_no')>Expense No</option>
                            </select>
                            <select name="dir" class="sort-direction">
                                <option value="desc" @selected(request('dir', 'desc') == 'desc')>↓ Desc</option>
                                <option value="asc" @selected(request('dir') == 'asc')>↑ Asc</option>
                            </select>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="filter-actions">
                        <button type="submit" class="filter-btn apply">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            Apply Filters
                        </button>
                        <a href="{{ route('expenses.index') }}" class="filter-btn reset">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                            Reset All
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Category Breakdown Section --}}
        <div class="category-breakdown">
            <div class="breakdown-header">
                <div class="breakdown-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 12v-2a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v2"></path>
                        <circle cx="12" cy="16" r="5"></circle>
                        <line x1="12" y1="11" x2="12" y2="16"></line>
                        <line x1="9" y1="13" x2="12" y2="16"></line>
                        <line x1="15" y1="13" x2="12" y2="16"></line>
                    </svg>
                    <h3>Category Breakdown</h3>
                </div>
                <div class="breakdown-total">
                    <span>Total: BDT {{ number_format((float) $filteredTotal, 2) }}</span>
                </div>
            </div>
            <div class="breakdown-grid">
                @php
                    $categoryTotals = [];
                    foreach($expenses as $e) {
                        $catName = $e->category?->name ?? 'Uncategorized';
                        if(!isset($categoryTotals[$catName])) {
                            $categoryTotals[$catName] = 0;
                        }
                        $categoryTotals[$catName] += (float)$e->amount;
                    }
                    arsort($categoryTotals);
                    $colors = ['#4F46E5', '#7C3AED', '#EC4899', '#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#F472B6', '#34D399'];
                @endphp
                @foreach($categoryTotals as $catName => $total)
                    <div class="breakdown-item">
                        <div class="breakdown-item-header">
                            <span class="breakdown-item-name">{{ $catName }}</span>
                            <span class="breakdown-item-amount">BDT {{ number_format($total, 2) }}</span>
                        </div>
                        <div class="breakdown-bar-container">
                            @php
                                $maxTotal = max($categoryTotals) ?: 1;
                                $percentage = ($total / $maxTotal) * 100;
                                $colorIndex = array_keys($categoryTotals)[$loop->index] ?? 0;
                            @endphp
                            <div class="breakdown-bar" style="width: {{ $percentage }}%; background-color: {{ $colors[$loop->index % count($colors)] }};">
                                <span class="breakdown-percentage">{{ round(($total / $filteredTotal) * 100, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Chart Card --}}
        <div class="chart-card">
            <div class="chart-header">
                <div class="chart-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <h3>Monthly Expense Summary</h3>
                </div>
                <div class="chart-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: var(--chart-1);"></span>
                        <span>Expenses (BDT)</span>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="expenseChart"></canvas>
            </div>
        </div>

        {{-- Recent Activity & Top Expenses --}}
        <div class="activity-grid">
            {{-- Recent Activities --}}
            <div class="activity-section">
                <div class="section-header">
                    <div class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                        <h3>Recent Activity</h3>
                    </div>
                </div>
                <div class="activity-list">
                    @forelse($expenses->take(5) as $e)
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div class="activity-content">
                                <div class="activity-title">{{ $e->title }}</div>
                                <div class="activity-meta">
                                    <span class="activity-vendor">{{ $e->vendor_name }}</span>
                                    <span class="activity-date">{{ $e->expense_date?->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="activity-amount">BDT {{ number_format((float) $e->amount, 2) }}</div>
                        </div>
                    @empty
                        <div class="activity-empty">
                            <p>No recent activities</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Top Expenses --}}
            <div class="top-expenses-section">
                <div class="section-header">
                    <div class="section-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline>
                            <polyline points="17 6 23 6 23 12"></polyline>
                        </svg>
                        <h3>Top Expenses</h3>
                    </div>
                </div>
                <div class="top-expenses-list">
                    @php
                        $sorted = $expenses->sortByDesc('amount')->take(5);
                    @endphp
                    @forelse($sorted as $e)
                        <div class="top-expense-item">
                            <div class="top-expense-rank">{{ $loop->iteration }}</div>
                            <div class="top-expense-info">
                                <div class="top-expense-title">{{ $e->title }}</div>
                                <div class="top-expense-category">{{ $e->category?->name ?? 'Uncategorized' }}</div>
                            </div>
                            <div class="top-expense-amount">BDT {{ number_format((float) $e->amount, 2) }}</div>
                        </div>
                    @empty
                        <div class="top-expense-empty">
                            <p>No expenses recorded</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="table-card">
            <div class="table-header">
                <div class="table-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                    <h3>Expense Records</h3>
                </div>
                <div class="table-summary">
                    <div class="summary-item">
                        <span class="summary-label">Filtered Total:</span>
                        <span class="summary-value">BDT {{ number_format((float) $filteredTotal, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="date-col">
                                <span>Date</span>
                            </th>
                            <th class="no-col">
                                <span>Expense No</span>
                            </th>
                            <th class="details-col">
                                <span>Details</span>
                            </th>
                            <th class="category-col">
                                <span>Category</span>
                            </th>
                            <th class="location-col">
                                <span>Location</span>
                            </th>
                            <th class="payment-col">
                                <span>Payment</span>
                            </th>
                            <th class="amount-col">
                                <span>Amount (BDT)</span>
                            </th>
                            <th class="receipt-col">
                                <span>Receipt</span>
                            </th>
                            <th class="actions-col">
                                <span>Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr class="data-row">
                                <td class="date-cell">
                                    <div class="date-content">
                                        <span class="date-day">{{ $e->expense_date?->format('d') }}</span>
                                        <div class="date-info">
                                            <span class="date-month">{{ $e->expense_date?->format('M') }}</span>
                                            <span class="date-year">{{ $e->expense_date?->format('Y') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="no-cell">
                                    <span class="expense-number">{{ $e->expense_no }}</span>
                                </td>
                                <td class="details-cell">
                                    <div class="expense-title">{{ $e->title }}</div>
                                    <div class="expense-meta">
                                        <span class="expense-vendor">{{ $e->vendor_name }}</span>
                                        @if ($e->reference_no)
                                            <span class="expense-ref">• Ref: {{ $e->reference_no }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="category-cell">
                                    <span class="category-badge">{{ $e->category?->name ?? '—' }}</span>
                                </td>
                                <td class="location-cell">
                                    <span class="location-text">{{ $e->location?->name ?? '—' }}</span>
                                </td>
                                <td class="payment-cell">
                                    <span class="payment-badge">{{ $e->payment_method }}</span>
                                </td>
                                <td class="amount-cell">
                                    <div class="amount-content">
                                        <span class="amount-value">BDT {{ number_format((float) $e->amount, 2) }}</span>
                                    </div>
                                </td>
                                <td class="receipt-cell">
                                    @if ($e->receipt_url)
                                        <a href="{{ $e->receipt_url }}" target="_blank" class="receipt-link">
                                            <div class="receipt-preview">
                                                <img src="{{ $e->receipt_url }}" alt="Receipt">
                                                <div class="receipt-overlay">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6">
                                                        </path>
                                                        <polyline points="15 3 21 3 21 9"></polyline>
                                                        <line x1="10" y1="14" x2="21"
                                                            y2="3"></line>
                                                    </svg>
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <span class="no-receipt">—</span>
                                    @endif
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="{{ route('expenses.edit', $e) }}" class="action-btn edit-btn"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('expenses.destroy', $e) }}"
                                            class="delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn delete-btn" title="Move to trash"
                                                onclick="return confirm('Are you sure you want to move this expense to trash?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path
                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="9">
                                    <div class="empty-state">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="8" x2="12" y2="12"></line>
                                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                        </svg>
                                        <h4>No expenses found</h4>
                                        <p>Try adjusting your filters or add a new expense</p>
                                        <a href="{{ route('expenses.create') }}" class="empty-action">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <line x1="12" y1="5" x2="12" y2="19">
                                                </line>
                                                <line x1="5" y1="12" x2="19" y2="12">
                                                </line>
                                            </svg>
                                            Add New Expense
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($expenses->hasPages())
                <div class="table-footer">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>

    </div>

    <style>
        /* Modern CSS Design with Animations */
        .expense-container {
            padding: 2rem;
            max-width: 100%;
            margin: 0 auto;
        }

        /* Animation Keyframes */
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

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes pulse {
            0%,
            100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }
            100% {
                background-position: 200% 0;
            }
        }

        @keyframes countUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Alert Toast */
        .alert-toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            background: var(--success);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--dropdown-shadow);
            animation: slideIn 0.3s ease-out;
            max-width: 400px;
        }

        .alert-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex: 1;
        }

        .alert-close {
            background: transparent;
            border: none;
            color: inherit;
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
            transition: opacity var(--transition-fast);
        }

        .alert-close:hover {
            opacity: 1;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
            animation: slideDown 0.6s ease-out;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .header-title-section {
            flex: 1;
            min-width: 300px;
        }

        .page-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 1rem 0;
            line-height: 1.2;
        }

        .header-stats {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            border-radius: calc(var(--radius) * 0.75);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .total-badge {
            background: linear-gradient(135deg, var(--accent-color), var(--chart-1));
            color: var(--accent-foreground);
        }

        .header-actions {
            flex-shrink: 0;
        }

        .action-group {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius);
            font-weight: 500;
            text-decoration: none;
            transition: all var(--transition-fast);
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .action-btn.secondary {
            background: var(--bg-secondary);
            color: var(--text-secondary);
            border-color: var(--border-color);
        }

        .action-btn.secondary:hover {
            background: var(--bg-tertiary);
            transform: translateY(-2px);
            box-shadow: var(--card-shadow);
        }

        .action-btn.primary {
            background: var(--accent-color);
            color: var(--accent-foreground);
            border: none;
        }

        .action-btn.primary:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        /* Quick Stats */
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-secondary);
            border-radius: var(--radius);
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out both;
        }

        .stat-card:nth-child(1) {
            animation-delay: 0.05s;
        }
        .stat-card:nth-child(2) {
            animation-delay: 0.1s;
        }
        .stat-card:nth-child(3) {
            animation-delay: 0.15s;
        }
        .stat-card:nth-child(4) {
            animation-delay: 0.2s;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            opacity: 0;
            transition: opacity 0.4s;
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card-total::before {
            background: linear-gradient(90deg, #3B82F6, #60A5FA);
        }
        .stat-card-amount::before {
            background: linear-gradient(90deg, #10B981, #34D399);
        }
        .stat-card-category::before {
            background: linear-gradient(90deg, #8B5CF6, #A78BFA);
        }
        .stat-card-avg::before {
            background: linear-gradient(90deg, #F59E0B, #FBBF24);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-icon-wrapper {
            flex-shrink: 0;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-tertiary);
            color: var(--accent-color);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover .stat-icon {
            transform: scale(1.1) rotate(-5deg);
        }

        .stat-card-total .stat-icon {
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
        }

        .stat-card-amount .stat-icon {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }

        .stat-card-category .stat-icon {
            background: rgba(139, 92, 246, 0.1);
            color: #8B5CF6;
        }

        .stat-card-avg .stat-icon {
            background: rgba(245, 158, 11, 0.1);
            color: #F59E0B;
        }

        .stat-info {
            flex: 1;
            min-width: 0;
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0.25rem 0;
            animation: countUp 0.6s ease-out;
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-muted);
        }

        .stat-change.positive {
            color: #10B981;
        }

        .stat-change.negative {
            color: #EF4444;
        }

        /* Filter Card */
        .filter-card {
            background: var(--bg-secondary);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            overflow: hidden;
            border: 1px solid var(--border-color);
            animation: slideDown 0.6s ease-out 0.1s both;
        }

        .filter-header {
            padding: 1.5rem 1.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .filter-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .filter-title h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .filter-results {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .results-count {
            font-weight: 500;
        }

        .filter-form {
            padding: 1.5rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-label {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-input,
        .filter-select,
        .date-input,
        .amount-input {
            width: 100%;
            padding: 0.625rem 0.875rem;
            background: var(--input);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-radius: calc(var(--radius) * 0.75);
            font-size: 0.875rem;
            transition: all var(--transition-fast);
        }

        .filter-input:focus,
        .filter-select:focus,
        .date-input:focus,
        .amount-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .date-range,
        .amount-range {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .date-separator,
        .amount-separator {
            color: var(--text-muted);
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .sort-options {
            display: flex;
            gap: 0.5rem;
        }

        .sort-select,
        .sort-direction {
            flex: 1;
        }

        .sort-direction {
            max-width: 120px;
        }

        .filter-actions {
            grid-column: 1 / -1;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        .filter-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: calc(var(--radius) * 0.75);
            font-weight: 500;
            text-decoration: none;
            transition: all var(--transition-fast);
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .filter-btn.apply {
            background: var(--accent-color);
            color: var(--accent-foreground);
        }

        .filter-btn.apply:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--accent-glow);
        }

        .filter-btn.reset {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .filter-btn.reset:hover {
            background: var(--bg-secondary);
            transform: translateY(-2px);
        }

        /* Category Breakdown */
        .category-breakdown {
            background: var(--bg-secondary);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            animation: fadeInUp 0.6s ease-out 0.15s both;
        }

        .breakdown-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .breakdown-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .breakdown-title h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .breakdown-total {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .breakdown-grid {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .breakdown-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .breakdown-item-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.875rem;
        }

        .breakdown-item-name {
            font-weight: 500;
            color: var(--text-primary);
        }

        .breakdown-item-amount {
            font-weight: 600;
            color: var(--text-primary);
        }

        .breakdown-bar-container {
            position: relative;
            background: var(--bg-tertiary);
            border-radius: 999px;
            height: 24px;
            overflow: hidden;
            transition: all 0.4s;
        }

        .breakdown-bar {
            height: 100%;
            border-radius: 999px;
            transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 0.75rem;
            position: relative;
            min-width: 40px;
        }

        .breakdown-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        .breakdown-percentage {
            font-size: 0.7rem;
            font-weight: 600;
            color: white;
            z-index: 1;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Chart Card */
        .chart-card {
            background: var(--bg-secondary);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .chart-header {
            padding: 1.5rem 1.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .chart-title h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .chart-legend {
            display: flex;
            gap: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        .chart-container {
            padding: 1.5rem;
            height: 300px;
        }

        /* Activity Grid */
        .activity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .activity-section,
        .top-expenses-section {
            background: var(--bg-secondary);
            border-radius: var(--radius);
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--card-shadow);
            animation: fadeInUp 0.6s ease-out 0.25s both;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-radius: calc(var(--radius) * 0.75);
            background: var(--bg-tertiary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .activity-item:hover {
            transform: translateX(4px);
            background: var(--bg-secondary);
            box-shadow: var(--card-shadow);
        }

        .activity-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-color);
            flex-shrink: 0;
            animation: pulse 2s infinite;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-title {
            font-weight: 500;
            color: var(--text-primary);
        }

        .activity-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            gap: 0.5rem;
        }

        .activity-amount {
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
        }

        .activity-empty,
        .top-expense-empty {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
        }

        /* Top Expenses */
        .top-expenses-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .top-expense-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem;
            border-radius: calc(var(--radius) * 0.75);
            background: var(--bg-tertiary);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .top-expense-item:hover {
            transform: translateX(4px);
            background: var(--bg-secondary);
            box-shadow: var(--card-shadow);
        }

        .top-expense-rank {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            color: white;
            flex-shrink: 0;
        }

        .top-expense-item:nth-child(1) .top-expense-rank {
            background: linear-gradient(135deg, #F59E0B, #FBBF24);
        }
        .top-expense-item:nth-child(2) .top-expense-rank {
            background: linear-gradient(135deg, #6B7280, #9CA3AF);
        }
        .top-expense-item:nth-child(3) .top-expense-rank {
            background: linear-gradient(135deg, #D97706, #F59E0B);
        }
        .top-expense-item:nth-child(4) .top-expense-rank {
            background: linear-gradient(135deg, #4B5563, #6B7280);
        }
        .top-expense-item:nth-child(5) .top-expense-rank {
            background: linear-gradient(135deg, #92400E, #D97706);
        }

        .top-expense-info {
            flex: 1;
            min-width: 0;
        }

        .top-expense-title {
            font-weight: 500;
            color: var(--text-primary);
        }

        .top-expense-category {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .top-expense-amount {
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
        }

        /* Table Card */
        .table-card {
            background: var(--bg-secondary);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border-color);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }

        .table-header {
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .table-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .table-title h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .table-summary {
            display: flex;
            gap: 1.5rem;
        }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .summary-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .summary-value {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--bg-tertiary);
        }

        .data-table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .data-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
        }

        .data-row {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .data-row:hover {
            background: var(--bg-tertiary);
            transform: scale(1.002);
        }

        .empty-row td {
            padding: 4rem 1.5rem;
            text-align: center;
        }

        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            color: var(--text-muted);
        }

        .empty-state h4 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .empty-state p {
            margin: 0;
            font-size: 0.875rem;
        }

        .empty-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: var(--accent-color);
            color: var(--accent-foreground);
            border-radius: var(--radius);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-fast);
        }

        .empty-action:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
        }

        /* Table Cells */
        .date-cell .date-content {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .date-day {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .date-info {
            display: flex;
            flex-direction: column;
        }

        .date-month {
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--text-primary);
        }

        .date-year {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .expense-number {
            font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
            font-size: 0.875rem;
            color: var(--text-secondary);
            background: var(--bg-tertiary);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }

        .expense-title {
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .expense-meta {
            font-size: 0.875rem;
            color: var(--text-secondary);
        }

        .expense-ref {
            color: var(--chart-1);
        }

        .category-badge,
        .payment-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .category-badge {
            background: var(--bg-tertiary);
            color: var(--text-secondary);
        }

        .payment-badge {
            background: var(--chart-3);
            color: white;
        }

        .amount-content {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .amount-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .receipt-link {
            display: inline-block;
        }

        .receipt-preview {
            position: relative;
            width: 48px;
            height: 48px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: transform 0.3s;
        }

        .receipt-preview:hover {
            transform: scale(1.1);
        }

        .receipt-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .receipt-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity var(--transition-fast);
        }

        .receipt-link:hover .receipt-overlay {
            opacity: 1;
        }

        .no-receipt {
            color: var(--text-muted);
            font-style: italic;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .action-btn.edit-btn,
        .action-btn.delete-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all var(--transition-fast);
        }

        .action-btn.edit-btn {
            background: var(--info);
            color: white;
        }

        .action-btn.edit-btn:hover {
            background: var(--info);
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .action-btn.delete-btn {
            background: var(--danger);
            color: white;
        }

        .action-btn.delete-btn:hover {
            background: var(--danger);
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .delete-form {
            margin: 0;
        }

        .table-footer {
            padding: 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: center;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .activity-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .expense-container {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .header-title-section {
                min-width: 100%;
            }

            .action-group {
                width: 100%;
            }

            .action-btn {
                flex: 1;
                justify-content: center;
            }

            .quick-stats {
                grid-template-columns: 1fr 1fr;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .table-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .data-table th,
            .data-table td {
                padding: 0.75rem 1rem;
            }

            .breakdown-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .quick-stats {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-value {
                font-size: 1.25rem;
            }

            .activity-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .activity-amount {
                align-self: flex-end;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Chart
            const labels = @json($chartLabels);
            const values = @json($chartValues);

            const chartColors = [
                'var(--chart-1)',
                'var(--chart-2)',
                'var(--chart-3)',
                'var(--chart-4)',
                'var(--chart-5)'
            ];

            const ctx = document.getElementById('expenseChart').getContext('2d');
            const expenseChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Monthly Expense (BDT)',
                        data: values,
                        backgroundColor: chartColors,
                        borderColor: chartColors,
                        borderWidth: 0,
                        borderRadius: 6,
                        hoverBackgroundColor: chartColors.map(color =>
                            color.replace('rgb', 'rgba').replace(')', ', 0.8)')
                        ),
                        barPercentage: 0.7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'var(--popover)',
                            titleColor: 'var(--popover-foreground)',
                            bodyColor: 'var(--popover-foreground)',
                            borderColor: 'var(--border)',
                            borderWidth: 1,
                            borderRadius: 6,
                            padding: 12,
                            boxShadow: 'var(--card-shadow)',
                            callbacks: {
                                label: function(context) {
                                    return `BDT ${context.parsed.y.toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'var(--border)',
                                drawBorder: false
                            },
                            ticks: {
                                color: 'var(--text-secondary)',
                                callback: function(value) {
                                    return 'BDT ' + value;
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'var(--text-secondary)'
                            }
                        }
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    }
                }
            });

            // Auto-hide success message after 5 seconds
            const alertToast = document.querySelector('.alert-toast');
            if (alertToast) {
                setTimeout(() => {
                    alertToast.style.opacity = '0';
                    alertToast.style.transform = 'translateX(100%)';
                    setTimeout(() => alertToast.remove(), 300);
                }, 5000);
            }

            // Add receipt hover effect
            document.querySelectorAll('.receipt-link').forEach(link => {
                link.addEventListener('mouseenter', function() {
                    this.querySelector('.receipt-overlay').style.opacity = '1';
                });
                link.addEventListener('mouseleave', function() {
                    this.querySelector('.receipt-overlay').style.opacity = '0';
                });
            });

            // Filter form enhancements
            const filterForm = document.querySelector('.filter-form');
            if (filterForm) {
                const searchInput = filterForm.querySelector('input[name="keyword"]');
                let searchTimeout;
                searchInput?.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        this.form.submit();
                    }, 500);
                });

                const dateFrom = filterForm.querySelector('input[name="date_from"]');
                const dateTo = filterForm.querySelector('input[name="date_to"]');

                dateFrom?.addEventListener('change', function() {
                    if (dateTo.value && this.value > dateTo.value) {
                        dateTo.value = this.value;
                    }
                });

                dateTo?.addEventListener('change', function() {
                    if (dateFrom.value && this.value < dateFrom.value) {
                        dateFrom.value = this.value;
                    }
                });

                const minAmount = filterForm.querySelector('input[name="min_amount"]');
                const maxAmount = filterForm.querySelector('input[name="max_amount"]');

                minAmount?.addEventListener('change', function() {
                    if (maxAmount.value && parseFloat(this.value) > parseFloat(maxAmount.value)) {
                        maxAmount.value = this.value;
                    }
                });

                maxAmount?.addEventListener('change', function() {
                    if (minAmount.value && parseFloat(this.value) < parseFloat(minAmount.value)) {
                        minAmount.value = this.value;
                    }
                });
            }

            // Animate category breakdown bars on load
            setTimeout(() => {
                document.querySelectorAll('.breakdown-bar').forEach((bar, index) => {
                    setTimeout(() => {
                        bar.style.width = bar.style.width;
                    }, index * 100);
                });
            }, 500);

            // Add shimmer effect to stat cards on load
            document.querySelectorAll('.stat-card').forEach((card, index) => {
                setTimeout(() => {
                    card.style.animation = 'none';
                    card.offsetHeight;
                    card.style.animation = `fadeInUp 0.6s ease-out ${index * 0.05}s both`;
                }, 100);
            });
        });
    </script>
@endsection
