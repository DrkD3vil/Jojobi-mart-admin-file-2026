@extends('layouts.app')

@section('content')
<div class="expense-container">
  <div class="page-header">
    <div class="header-content">
      <div class="header-title-section">
        <h1 class="page-title">Trash Bin</h1>
        <div class="header-stats">
          <div class="stat-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="3 6 5 6 21 6"></polyline>
              <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
            </svg>
            <span>{{ $expenses->total() }} {{ Str::plural('expense', $expenses->total()) }}</span>
          </div>
          <div class="stat-badge danger-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>Deleted Items</span>
          </div>
        </div>
      </div>
      
      <div class="header-actions">
        <div class="action-group">
          @if($expenses->isNotEmpty())
            <form method="POST" action="{{ route('expenses.emptyTrash') }}" 
                  onsubmit="return confirm('Are you sure you want to permanently delete ALL items in trash? This action cannot be undone.')"
                  class="d-inline">
              @csrf
              <button type="submit" class="action-btn danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="3 6 5 6 21 6"></polyline>
                  <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                  <line x1="10" y1="11" x2="10" y2="17"></line>
                  <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
                <span>Empty Trash</span>
              </button>
            </form>
          @endif
          <a href="{{ route('expenses.index') }}" class="action-btn secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            <span>Back to Expenses</span>
          </a>
        </div>
      </div>
    </div>
  </div>

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

  <div class="trash-card">
    <div class="trash-header">
      <div class="trash-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"></polyline>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
        <h2>Deleted Expenses</h2>
      </div>
      <div class="trash-stats">
        @if($expenses->isNotEmpty())
          <span class="deleted-count">{{ $expenses->total() }} items deleted</span>
        @endif
      </div>
    </div>

    @if($expenses->isEmpty())
      <div class="empty-trash">
        <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"></polyline>
          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
          <line x1="10" y1="11" x2="10" y2="17"></line>
          <line x1="14" y1="11" x2="14" y2="17"></line>
        </svg>
        <h3>Trash is empty</h3>
        <p>No deleted expenses found. Items you delete will appear here.</p>
        <a href="{{ route('expenses.index') }}" class="empty-action">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="3" y1="9" x2="21" y2="9"></line>
            <line x1="9" y1="21" x2="9" y2="9"></line>
          </svg>
          Back to Expenses
        </a>
      </div>
    @else
      <div class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th class="no-col">
                <span>Expense No</span>
              </th>
              <th class="date-col">
                <span>Date</span>
              </th>
              <th class="details-col">
                <span>Details</span>
              </th>
              <th class="amount-col">
                <span>Amount</span>
              </th>
              <th class="deleted-col">
                <span>Deleted</span>
              </th>
              <th class="actions-col">
                <span>Actions</span>
              </th>
            </tr>
          </thead>
          <tbody>
            @foreach($expenses as $e)
              <tr class="data-row deleted-row">
                <td class="no-cell">
                  <span class="expense-number">{{ $e->expense_no }}</span>
                </td>
                <td class="date-cell">
                  <div class="date-content">
                    <span class="date-day">{{ $e->expense_date?->format('d') }}</span>
                    <div class="date-info">
                      <span class="date-month">{{ $e->expense_date?->format('M') }}</span>
                      <span class="date-year">{{ $e->expense_date?->format('Y') }}</span>
                    </div>
                  </div>
                </td>
                <td class="details-cell">
                  <div class="expense-title">{{ $e->title }}</div>
                  <div class="expense-meta">
                    @if($e->vendor_name)
                      <span class="expense-vendor">{{ $e->vendor_name }}</span>
                    @endif
                    @if($e->category)
                      <span class="expense-category">• {{ $e->category?->name }}</span>
                    @endif
                  </div>
                </td>
                <td class="amount-cell">
                  <div class="amount-content">
                    <span class="amount-value">{{ number_format((float)$e->amount, 2) }}</span>
                    <span class="amount-currency">{{ $e->currency }}</span>
                  </div>
                </td>
                <td class="deleted-cell">
                  <div class="deleted-info">
                    <span class="deleted-date">{{ $e->deleted_at?->format('M d, Y') }}</span>
                    <span class="deleted-time">{{ $e->deleted_at?->format('H:i') }}</span>
                  </div>
                  <div class="deleted-age">
                    @php
                      $diff = $e->deleted_at->diff(now());
                      if ($diff->days > 30) {
                          $age = floor($diff->days / 30) . ' months ago';
                      } elseif ($diff->days > 0) {
                          $age = $diff->days . ' days ago';
                      } elseif ($diff->h > 0) {
                          $age = $diff->h . ' hours ago';
                      } else {
                          $age = $diff->i . ' minutes ago';
                      }
                    @endphp
                    <span class="deleted-badge">{{ $age }}</span>
                  </div>
                </td>
                <td class="actions-cell">
                  <div class="trash-actions">
                    <form method="POST" action="{{ route('expenses.restore', $e->id) }}" class="restore-form">
                      @csrf
                      <button type="submit" class="action-btn restore-btn" title="Restore">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M3 2v6h6"></path>
                          <path d="M3 13a9 9 0 1 0 3-7.7L3 8"></path>
                        </svg>
                        <span>Restore</span>
                      </button>
                    </form>
                    
                    <form method="POST" action="{{ route('expenses.forceDelete', $e->id) }}" 
                          class="delete-form"
                          onsubmit="return confirmPermanentDelete('{{ $e->title }}')">
                      @csrf @method('DELETE')
                      <button type="submit" class="action-btn delete-btn" title="Permanently delete">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="3 6 5 6 21 6"></polyline>
                          <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        <span>Delete</span>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($expenses->hasPages())
        <div class="table-footer">
          {{ $expenses->links() }}
        </div>
      @endif
    @endif
  </div>
</div>

<style>
  /* Expense Container */
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

  .danger-badge {
    background: linear-gradient(135deg, var(--danger), rgba(var(--danger-rgb), 0.8));
    color: white;
  }

  .header-actions {
    flex-shrink: 0;
  }

  .action-group {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  /* Action Buttons */
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

  .action-btn.danger {
    background: var(--danger);
    color: white;
    border: none;
  }

  .action-btn.danger:hover {
    background: rgba(var(--danger-rgb), 0.9);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(var(--danger-rgb), 0.2);
  }

  /* Trash Card */
  .trash-card {
    background: var(--bg-secondary);
    border-radius: var(--radius);
    box-shadow: var(--card-shadow);
    border: 1px solid var(--border-color);
    overflow: hidden;
  }

  .trash-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .trash-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .trash-title h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
  }

  .trash-stats {
    font-size: 0.875rem;
    color: var(--text-secondary);
  }

  .deleted-count {
    font-weight: 500;
    color: var(--danger);
  }

  /* Empty Trash */
  .empty-trash {
    padding: 4rem 2rem;
    text-align: center;
    color: var(--text-muted);
  }

  .empty-trash h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 1.5rem 0 0.5rem;
  }

  .empty-trash p {
    font-size: 1rem;
    margin: 0 0 1.5rem;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
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

  /* Table Container */
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

  .deleted-row {
    opacity: 0.8;
    transition: opacity var(--transition-fast);
  }

  .deleted-row:hover {
    opacity: 1;
    background: var(--bg-tertiary);
  }

  /* Table Cells */
  .expense-number {
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', monospace;
    font-size: 0.875rem;
    color: var(--text-secondary);
    background: var(--bg-tertiary);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    display: inline-block;
  }

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

  .expense-title {
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
  }

  .expense-meta {
    font-size: 0.875rem;
    color: var(--text-secondary);
  }

  .expense-category {
    color: var(--text-muted);
  }

  .amount-content {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
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

  .deleted-cell {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .deleted-info {
    display: flex;
    flex-direction: column;
  }

  .deleted-date {
    font-size: 0.875rem;
    color: var(--text-primary);
  }

  .deleted-time {
    font-size: 0.75rem;
    color: var(--text-muted);
  }

  .deleted-age {
    display: inline-block;
  }

  .deleted-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: rgba(var(--danger-rgb), 0.1);
    color: var(--danger);
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 500;
  }

  /* Trash Actions */
  .trash-actions {
    display: flex;
    gap: 0.5rem;
  }

  .restore-form,
  .delete-form {
    margin: 0;
  }

  .action-btn.restore-btn {
    background: var(--info);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
  }

  .action-btn.restore-btn:hover {
    background: rgba(var(--info-rgb), 0.9);
    transform: translateY(-2px);
  }

  .action-btn.delete-btn {
    background: var(--danger);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
  }

  .action-btn.delete-btn:hover {
    background: rgba(var(--danger-rgb), 0.9);
    transform: translateY(-2px);
  }

  /* Table Footer */
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

    .trash-header {
      flex-direction: column;
      gap: 1rem;
      align-items: flex-start;
    }

    .trash-actions {
      flex-direction: column;
    }

    .action-btn.restore-btn,
    .action-btn.delete-btn {
      width: 100%;
      justify-content: center;
    }

    .data-table th,
    .data-table td {
      padding: 0.75rem 1rem;
    }

    .date-cell .date-content {
      flex-direction: column;
      align-items: flex-start;
      gap: 0.25rem;
    }

    .date-day {
      font-size: 1rem;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success message after 5 seconds
    const alertToast = document.querySelector('.alert-toast');
    if (alertToast) {
      setTimeout(() => {
        alertToast.style.opacity = '0';
        alertToast.style.transform = 'translateX(100%)';
        setTimeout(() => alertToast.remove(), 300);
      }, 5000);
    }

    // Confirm permanent delete with item details
    window.confirmPermanentDelete = function(itemTitle) {
      return confirm(`Are you sure you want to PERMANENTLY delete "${itemTitle}"?\n\nThis action cannot be undone and the expense will be lost forever.`);
    };

    // Add subtle animation to deleted rows
    const deletedRows = document.querySelectorAll('.deleted-row');
    deletedRows.forEach((row, index) => {
      row.style.animationDelay = `${index * 50}ms`;
      row.style.animation = 'fadeIn 0.3s ease-out forwards';
    });

    // Add CSS animation
    const style = document.createElement('style');
    style.textContent = `
      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(10px);
        }
        to {
          opacity: 0.8;
          transform: translateY(0);
        }
      }
    `;
    document.head.appendChild(style);

    // Handle bulk actions
    const emptyTrashBtn = document.querySelector('.action-btn.danger');
    if (emptyTrashBtn) {
      emptyTrashBtn.addEventListener('click', function(e) {
        if (!confirm('Are you sure you want to permanently delete ALL items in trash? This action cannot be undone.')) {
          e.preventDefault();
        }
      });
    }

    // Restore button loading state
    const restoreButtons = document.querySelectorAll('.restore-btn');
    restoreButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        this.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spin">
            <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
          </svg>
          <span>Restoring...</span>
        `;
        this.disabled = true;
        this.parentElement.submit();
      });
    });

    // Delete button loading state
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        this.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="spin">
            <path d="M21 12a9 9 0 1 1-6.219-8.56"></path>
          </svg>
          <span>Deleting...</span>
        `;
        this.disabled = true;
        this.parentElement.submit();
      });
    });

    // Add spin animation for loading icons
    const spinStyle = document.createElement('style');
    spinStyle.textContent = `
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
      .spin {
        animation: spin 1s linear infinite;
      }
    `;
    document.head.appendChild(spinStyle);
  });
</script>
@endsection