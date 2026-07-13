@extends('layouts.app')

@section('content')
<div class="expense-container">
  <div class="page-header">
    <div class="header-content">
      <div class="header-title-section">
        <h1 class="page-title">Add New Expense</h1>
        <div class="header-stats">
          <div class="stat-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="2" y="6" width="20" height="12" rx="2"></rect>
              <path d="M12 6v12"></path>
              <path d="M17 10h-5"></path>
              <path d="M7 10h5"></path>
            </svg>
            <span>Create New Record</span>
          </div>
        </div>
      </div>
      
      <div class="header-actions">
        <a href="{{ route('expenses.index') }}" class="action-btn secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
          </svg>
          <span>Back to Expenses</span>
        </a>
      </div>
    </div>
  </div>

  <div class="form-card">
    <div class="form-header">
      <div class="form-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
        <h2>Expense Details</h2>
      </div>
      <div class="form-help">
        <span class="help-text">Fill in all required fields</span>
      </div>
    </div>

    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="expense-form">
      @csrf

      <div class="form-grid">
        {{-- Date --}}
        <div class="form-group">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            Date
            <span class="required">*</span>
          </label>
          <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}"
                 class="form-input @error('expense_date') error @enderror"
                 required>
          @error('expense_date')
            <div class="form-error">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span>{{ $message }}</span>
            </div>
          @enderror
        </div>

        {{-- Category --}}
        <div class="form-group">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
              <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            Category
          </label>
          <select name="expense_category_id" class="form-select">
            <option value="">— Select Category —</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" @selected(old('expense_category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Location --}}
        <div class="form-group">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Location
          </label>
          <select name="location_id" class="form-select">
            <option value="">— Select Location —</option>
            @foreach($locations as $l)
              <option value="{{ $l->id }}" @selected(old('location_id') == $l->id)>{{ $l->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Payment Method --}}
        <div class="form-group">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
              <line x1="1" y1="10" x2="23" y2="10"></line>
            </svg>
            Payment Method
          </label>
          <div class="payment-methods">
            <div class="method-options">
              <button type="button" class="method-option" data-value="cash">Cash</button>
              <button type="button" class="method-option" data-value="bkash">bKash</button>
              <button type="button" class="method-option" data-value="bank">Bank</button>
              <button type="button" class="method-option" data-value="card">Card</button>
            </div>
            <input type="text" name="payment_method" value="{{ old('payment_method') }}" 
                   class="form-input method-input" placeholder="Or enter custom method">
          </div>
        </div>

        {{-- Title --}}
        <div class="form-group full-width">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"></path>
            </svg>
            Title
            <span class="required">*</span>
          </label>
          <input name="title" value="{{ old('title') }}"
                 class="form-input @error('title') error @enderror" 
                 placeholder="e.g. Office rent, Client meeting, Software subscription"
                 required>
          @error('title')
            <div class="form-error">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span>{{ $message }}</span>
            </div>
          @enderror
        </div>

        {{-- Vendor --}}
        <div class="form-group">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            Vendor
          </label>
          <input name="vendor_name" value="{{ old('vendor_name') }}" 
                 class="form-input" 
                 placeholder="Company or person name">
        </div>

        {{-- Reference No --}}
        <div class="form-group">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 7V4h16v3"></path>
              <path d="M5 20h6"></path>
              <path d="M13 4 8 20"></path>
              <path d="m15 4 5 16"></path>
              <path d="M20 15H8"></path>
            </svg>
            Reference No
          </label>
          <input name="reference_no" value="{{ old('reference_no') }}" 
                 class="form-input" 
                 placeholder="Transaction ID, voucher, invoice no">
        </div>

        {{-- Amount --}}
        <div class="form-group">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="12" y1="1" x2="12" y2="23"></line>
              <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
            Amount
            <span class="required">*</span>
          </label>
          <div class="amount-input-group">
            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}"
                   class="form-input amount-input @error('amount') error @enderror"
                   placeholder="0.00"
                   required>
            <span class="amount-suffix">BDT</span>
          </div>
          @error('amount')
            <div class="form-error">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span>{{ $message }}</span>
            </div>
          @enderror
        </div>

        {{-- Currency --}}
        <div class="form-group">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
              <path d="M12 18V6"></path>
            </svg>
            Currency
          </label>
          <select name="currency" class="form-select currency-select">
            <option value="BDT" @selected(old('currency', 'BDT') == 'BDT')>BDT - Bangladeshi Taka</option>
            <option value="USD" @selected(old('currency') == 'USD')>USD - US Dollar</option>
            <option value="EUR" @selected(old('currency') == 'EUR')>EUR - Euro</option>
            <option value="GBP" @selected(old('currency') == 'GBP')>GBP - British Pound</option>
            <option value="custom">Custom Currency</option>
          </select>
          <input type="text" name="custom_currency" value="{{ old('custom_currency') }}" 
                 class="form-input custom-currency-input" 
                 placeholder="Enter custom currency code"
                 style="display: none;">
        </div>

        {{-- Receipt Image --}}
        <div class="form-group full-width">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <circle cx="8.5" cy="8.5" r="1.5"></circle>
              <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
            Receipt Image
          </label>
          <div class="file-upload-area @error('receipt_image') error @enderror" 
               onclick="document.getElementById('receipt-upload').click()">
            <input type="file" name="receipt_image" id="receipt-upload" 
                   class="file-input" 
                   accept="image/*,.pdf,.doc,.docx">
            <div class="upload-content">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="upload-icon">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="17 8 12 3 7 8"></polyline>
                <line x1="12" y1="3" x2="12" y2="15"></line>
              </svg>
              <div class="upload-text">
                <p class="upload-title">Click to upload receipt</p>
                <p class="upload-subtitle">Supports JPG, PNG, PDF (Max 5MB)</p>
              </div>
            </div>
            <div class="upload-preview" id="upload-preview"></div>
          </div>
          @error('receipt_image')
            <div class="form-error">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span>{{ $message }}</span>
            </div>
          @enderror
        </div>

        {{-- Description --}}
        <div class="form-group full-width">
          <label class="form-label">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
              <polyline points="14 2 14 8 20 8"></polyline>
              <line x1="16" y1="13" x2="8" y2="13"></line>
              <line x1="16" y1="17" x2="8" y2="17"></line>
              <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            Description
          </label>
          <textarea name="description" rows="4" 
                    class="form-textarea" 
                    placeholder="Additional details about this expense...">{{ old('description') }}</textarea>
        </div>

        {{-- Form Actions --}}
        <div class="form-actions full-width">
          <div class="action-buttons">
            <button type="button" class="action-btn secondary" onclick="window.location.href='{{ route('expenses.index') }}'">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
              </svg>
              Cancel
            </button>
            <button type="submit" class="action-btn primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
              </svg>
              Save Expense
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<style>
  /* Expense Container */
  .expense-container {
    padding: 2rem;
    max-width: 100%;
    margin: 0 auto;
  }

  /* Form Card */
  .form-card {
    background: var(--bg-secondary);
    border-radius: var(--radius);
    box-shadow: var(--card-shadow);
    border: 1px solid var(--border-color);
    overflow: hidden;
  }

  .form-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .form-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .form-title h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
  }

  .form-help {
    font-size: 0.875rem;
    color: var(--text-secondary);
  }

  .help-text {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }

  /* Form */
  .expense-form {
    padding: 1.5rem;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .form-group.full-width {
    grid-column: 1 / -1;
  }

  .form-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .required {
    color: var(--danger);
    margin-left: 0.25rem;
  }

  .form-input,
  .form-select,
  .form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    background: var(--input);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    border-radius: calc(var(--radius) * 0.75);
    font-size: 0.875rem;
    transition: all var(--transition-fast);
  }

  .form-input:focus,
  .form-select:focus,
  .form-textarea:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 0 0 3px var(--accent-glow);
  }

  .form-input.error,
  .form-select.error {
    border-color: var(--danger);
  }

  .form-input.error:focus {
    box-shadow: 0 0 0 3px rgba(var(--danger-rgb), 0.1);
  }

  .form-textarea {
    resize: vertical;
    min-height: 100px;
    line-height: 1.5;
  }

  /* Payment Methods */
  .payment-methods {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .method-options {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .method-option {
    padding: 0.5rem 1rem;
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
    border-radius: calc(var(--radius) * 0.5);
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast);
  }

  .method-option:hover {
    background: var(--bg-secondary);
    transform: translateY(-1px);
  }

  .method-option.active {
    background: var(--accent-color);
    color: var(--accent-foreground);
    border-color: var(--accent-color);
  }

  .method-input {
    margin-top: 0.5rem;
  }

  /* Amount Input */
  .amount-input-group {
    position: relative;
  }

  .amount-input {
    padding-right: 3.5rem;
  }

  .amount-suffix {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 500;
  }

  /* File Upload */
  .file-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: var(--radius);
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all var(--transition-fast);
    background: var(--input);
    position: relative;
  }

  .file-upload-area:hover {
    border-color: var(--accent-color);
    background: var(--bg-tertiary);
  }

  .file-upload-area.error {
    border-color: var(--danger);
  }

  .file-input {
    display: none;
  }

  .upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    pointer-events: none;
  }

  .upload-icon {
    color: var(--text-secondary);
  }

  .upload-text {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }

  .upload-title {
    font-size: 1rem;
    font-weight: 500;
    color: var(--text-primary);
    margin: 0;
  }

  .upload-subtitle {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
  }

  .upload-preview {
    margin-top: 1rem;
    display: none;
  }

  .preview-image {
    max-width: 200px;
    max-height: 200px;
    border-radius: calc(var(--radius) * 0.75);
    border: 1px solid var(--border-color);
  }

  /* Form Error */
  .form-error {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--danger);
    margin-top: 0.25rem;
  }

  /* Form Actions */
  .form-actions {
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
  }

  .action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
  }

  /* Custom Currency Input */
  .custom-currency-input {
    margin-top: 0.5rem;
  }

  /* Page Header (reuse from previous) */
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

  .header-actions {
    flex-shrink: 0;
  }

  /* Action Buttons (reuse from previous) */
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

    .form-grid {
      grid-template-columns: 1fr;
    }

    .action-buttons {
      flex-direction: column;
    }

    .action-btn {
      width: 100%;
      justify-content: center;
    }

    .form-header {
      flex-direction: column;
      gap: 1rem;
      align-items: flex-start;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Payment method buttons
    const methodOptions = document.querySelectorAll('.method-option');
    const methodInput = document.querySelector('.method-input');

    methodOptions.forEach(option => {
      option.addEventListener('click', function() {
        methodOptions.forEach(opt => opt.classList.remove('active'));
        this.classList.add('active');
        methodInput.value = this.dataset.value;
        methodInput.focus();
      });
    });

    // Set initial active payment method if exists
    if (methodInput.value) {
      methodOptions.forEach(option => {
        if (option.dataset.value === methodInput.value) {
          option.classList.add('active');
        }
      });
    }

    // Currency select with custom input
    const currencySelect = document.querySelector('.currency-select');
    const customCurrencyInput = document.querySelector('.custom-currency-input');

    currencySelect.addEventListener('change', function() {
      if (this.value === 'custom') {
        customCurrencyInput.style.display = 'block';
        customCurrencyInput.focus();
      } else {
        customCurrencyInput.style.display = 'none';
        customCurrencyInput.value = '';
      }
    });

    // Initialize currency select
    if (currencySelect.value === 'custom' && customCurrencyInput.value) {
      customCurrencyInput.style.display = 'block';
    }

    // File upload preview
    const fileInput = document.getElementById('receipt-upload');
    const uploadPreview = document.getElementById('upload-preview');

    fileInput.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        
        // Check file size (5MB limit)
        if (file.size > 5 * 1024 * 1024) {
          alert('File size must be less than 5MB');
          this.value = '';
          return;
        }

        // Show preview for images
        if (file.type.startsWith('image/')) {
          const reader = new FileReader();
          
          reader.onload = function(e) {
            uploadPreview.innerHTML = `
              <img src="${e.target.result}" class="preview-image" alt="Preview">
              <p style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
                ${file.name} (${(file.size / 1024).toFixed(1)} KB)
              </p>
            `;
            uploadPreview.style.display = 'block';
          };
          
          reader.readAsDataURL(file);
        } else {
          // Show file info for non-image files
          uploadPreview.innerHTML = `
            <div style="padding: 1rem; background: var(--bg-tertiary); border-radius: var(--radius);">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-secondary);">
                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                <polyline points="13 2 13 9 20 9"></polyline>
              </svg>
              <p style="margin-top: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
                ${file.name} (${(file.size / 1024).toFixed(1)} KB)
              </p>
            </div>
          `;
          uploadPreview.style.display = 'block';
        }
      } else {
        uploadPreview.innerHTML = '';
        uploadPreview.style.display = 'none';
      }
    });

    // Form validation
    const form = document.querySelector('.expense-form');
    
    form.addEventListener('submit', function(e) {
      const requiredFields = form.querySelectorAll('[required]');
      let isValid = true;

      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.classList.add('error');
          isValid = false;
          
          // Show error message if not already shown
          if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('form-error')) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'form-error';
            errorDiv.innerHTML = `
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span>This field is required</span>
            `;
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
          }
        }
      });

      if (!isValid) {
        e.preventDefault();
        // Scroll to first error
        const firstError = form.querySelector('.error');
        if (firstError) {
          firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });

    // Clear error on input
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
      input.addEventListener('input', function() {
        this.classList.remove('error');
        const errorDiv = this.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('form-error')) {
          errorDiv.remove();
        }
      });
    });

    // Set today's date by default if empty
    const dateInput = document.querySelector('input[name="expense_date"]');
    if (dateInput && !dateInput.value) {
      dateInput.value = new Date().toISOString().split('T')[0];
    }

    // Focus first field
    const firstField = form.querySelector('input, select, textarea');
    if (firstField) {
      firstField.focus();
    }
  });
</script>
@endsection