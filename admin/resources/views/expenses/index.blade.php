@extends('layouts.app')

@section('content')
<div class="expense-container" style="min-height: calc(100vh - var(--header-height));">

  @if(session('success'))
    <div class="alert-toast" role="alert">
      <div class="alert-content">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>{{ session('success') }}</span>
      </div>
      <button type="button" class="alert-close" onclick="this.parentElement.remove()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="3" y1="9" x2="21" y2="9"></line>
              <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
            <span>{{ $expenses->total() }} {{ Str::plural('expense', $expenses->total()) }}</span>
          </div>
          <div class="stat-badge total-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="1" x2="12" y2="23"></line>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
            <span>{{ number_format((float)$filteredTotal, 2) }} {{ config('app.currency', 'USD') }}</span>
          </div>
        </div>
      </div>
      
      <div class="header-actions">
        <div class="action-group">
          <a href="{{ route('expenses.trash') }}" class="action-btn secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
            <span>Trash</span>
          </a>
          <a href="{{ route('expenses.export.csv', request()->query()) }}" class="action-btn secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>Export</span>
          </a>
          <a href="{{ route('expenses.create') }}" class="action-btn primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="5" x2="12" y2="19"></line>
              <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Add Expense</span>
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Filters Card --}}
  <div class="filter-card">
    <div class="filter-header">
      <div class="filter-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="date-input">
            <span class="date-separator">to</span>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="date-input">
          </div>
        </div>

        {{-- Category --}}
        <div class="filter-group">
          <label class="filter-label">Category</label>
          <select name="category_id" class="filter-select">
            <option value="">All Categories</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Location --}}
        <div class="filter-group">
          <label class="filter-label">Location</label>
          <select name="location_id" class="filter-select">
            <option value="">All Locations</option>
            @foreach($locations as $l)
              <option value="{{ $l->id }}" @selected(request('location_id') == $l->id)>{{ $l->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Payment Method --}}
        <div class="filter-group">
          <label class="filter-label">Payment Method</label>
          <select name="payment_method" class="filter-select">
            <option value="">All Methods</option>
            @foreach($paymentMethods as $m)
              <option value="{{ $m }}" @selected(request('payment_method') == $m)>{{ $m }}</option>
            @endforeach
          </select>
        </div>

        {{-- Amount Range --}}
        <div class="filter-group">
          <label class="filter-label">Amount Range</label>
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
              <option value="expense_date" @selected(request('sort','expense_date')=='expense_date')>Date</option>
              <option value="amount" @selected(request('sort')=='amount')>Amount</option>
              <option value="created_at" @selected(request('sort')=='created_at')>Created</option>
              <option value="expense_no" @selected(request('sort')=='expense_no')>Expense No</option>
            </select>
            <select name="dir" class="sort-direction">
              <option value="desc" @selected(request('dir','desc')=='desc')>↓ Desc</option>
              <option value="asc" @selected(request('dir')=='asc')>↑ Asc</option>
            </select>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="filter-actions">
          <button type="submit" class="filter-btn apply">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            Apply Filters
          </button>
          <a href="{{ route('expenses.index') }}" class="filter-btn reset">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

  {{-- Chart Card --}}
  <div class="chart-card">
    <div class="chart-header">
      <div class="chart-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="20" x2="18" y2="10"></line>
          <line x1="12" y1="20" x2="12" y2="4"></line>
          <line x1="6" y1="20" x2="6" y2="14"></line>
        </svg>
        <h3>Monthly Expense Summary</h3>
      </div>
      <div class="chart-legend">
        <div class="legend-item">
          <span class="legend-color" style="background-color: var(--chart-1);"></span>
          <span>Expenses</span>
        </div>
      </div>
    </div>
    <div class="chart-container">
      <canvas id="expenseChart"></canvas>
    </div>
  </div>

  {{-- Table Card --}}
  <div class="table-card">
    <div class="table-header">
      <div class="table-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="3" y1="9" x2="21" y2="9"></line>
          <line x1="9" y1="21" x2="9" y2="9"></line>
        </svg>
        <h3>Expense Records</h3>
      </div>
      <div class="table-summary">
        <div class="summary-item">
          <span class="summary-label">Filtered Total:</span>
          <span class="summary-value">{{ number_format((float)$filteredTotal, 2) }} {{ config('app.currency', 'USD') }}</span>
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
              <span>Amount</span>
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
                  @if($e->reference_no)
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
                  <span class="amount-value">{{ number_format((float)$e->amount, 2) }}</span>
                  <span class="amount-currency">{{ $e->currency }}</span>
                </div>
              </td>
              <td class="receipt-cell">
                @if($e->receipt_url)
                  <a href="{{ $e->receipt_url }}" target="_blank" class="receipt-link">
                    <div class="receipt-preview">
                      <img src="{{ $e->receipt_url }}" alt="Receipt">
                      <div class="receipt-overlay">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                          <polyline points="15 3 21 3 21 9"></polyline>
                          <line x1="10" y1="14" x2="21" y2="3"></line>
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
                  <a href="{{ route('expenses.edit',$e) }}" class="action-btn edit-btn" title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                      <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                  </a>
                  <form method="POST" action="{{ route('expenses.destroy',$e) }}" class="delete-form">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn delete-btn" title="Move to trash"
                            onclick="return confirm('Are you sure you want to move this expense to trash?')">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
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
                  <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                  </svg>
                  <h4>No expenses found</h4>
                  <p>Try adjusting your filters or add a new expense</p>
                  <a href="{{ route('expenses.create') }}" class="empty-action">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="12" y1="5" x2="12" y2="19"></line>
                      <line x1="5" y1="12" x2="19" y2="12"></line>
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

    @if($expenses->hasPages())
      <div class="table-footer">
        {{ $expenses->links() }}
      </div>
    @endif
  </div>

</div>

<style>
  /* Modern CSS Design */
  .expense-container {
    padding: 2rem;
    max-width: 100%;
    margin: 0 auto;
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

  /* Filter Card */
  .filter-card {
    background: var(--bg-secondary);
    border-radius: var(--radius);
    box-shadow: var(--card-shadow);
    margin-bottom: 2rem;
    overflow: hidden;
    border: 1px solid var(--border-color);
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

  /* Chart Card */
  .chart-card {
    background: var(--bg-secondary);
    border-radius: var(--radius);
    box-shadow: var(--card-shadow);
    margin-bottom: 2rem;
    border: 1px solid var(--border-color);
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

  /* Table Card */
  .table-card {
    background: var(--bg-secondary);
    border-radius: var(--radius);
    box-shadow: var(--card-shadow);
    border: 1px solid var(--border-color);
    overflow: hidden;
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

  .data-row:hover {
    background: var(--bg-tertiary);
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

  .amount-currency {
    font-size: 0.75rem;
    color: var(--text-muted);
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

  /* Animations */
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

  /* Responsive Design */
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
          label: 'Monthly Expense',
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
                return `$${context.parsed.y.toFixed(2)}`;
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
                return '$' + value;
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
      // Add debounced search
      const searchInput = filterForm.querySelector('input[name="keyword"]');
      let searchTimeout;
      searchInput?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          this.form.submit();
        }, 500);
      });

      // Date range validation
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

      // Amount range validation
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
  });
</script>
@endsection