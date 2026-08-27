@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        .customer-edit-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 24px;
        }

        .customer-edit-card {
            background: var(--card);
            border-radius: 16px;
            padding: 32px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .customer-edit-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--foreground);
            margin-bottom: 8px;
        }

        .customer-edit-subtitle {
            color: var(--muted-foreground);
            margin-bottom: 24px;
        }

        .customer-form-group {
            margin-bottom: 20px;
        }

        .customer-form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--foreground);
            margin-bottom: 6px;
        }

        .customer-form-group label .required {
            color: #DC2626;
            margin-left: 2px;
        }

        .customer-form-control {
            width: 100%;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--foreground);
            font-size: 14px;
            transition: all 0.3s;
        }

        .customer-form-control:focus {
            outline: none;
            border-color: #4F46E5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .customer-form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .customer-form-actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .customer-btn {
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .customer-btn-primary {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            color: white;
        }

        .customer-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.4);
        }

        .customer-btn-ghost {
            background: var(--muted);
            color: var(--foreground);
            border: 1px solid var(--border);
        }

        .customer-btn-ghost:hover {
            background: var(--border);
        }

        @media (max-width: 768px) {
            .customer-edit-container {
                padding: 16px;
            }

            .customer-edit-card {
                padding: 20px;
            }

            .customer-form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="customer-edit-container">
        <div class="customer-edit-card">
            <h1 class="customer-edit-title">Edit Customer</h1>
            <p class="customer-edit-subtitle">Update customer information</p>

            <form action="{{ route('customers.update', $customer) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="customer-form-group">
                    <label>Full Name <span class="required">*</span></label>
                    <input type="text" class="customer-form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name', $customer->name) }}" required>
                    @error('name')
                        <div style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="customer-form-row">
                    <div class="customer-form-group">
                        <label>Phone</label>
                        <input type="text" class="customer-form-control @error('phone') is-invalid @enderror"
                               name="phone" value="{{ old('phone', $customer->phone) }}">
                        @error('phone')
                            <div style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="customer-form-group">
                        <label>Email</label>
                        <input type="email" class="customer-form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email', $customer->email) }}">
                        @error('email')
                            <div style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="customer-form-row">
                    <div class="customer-form-group">
                        <label>Type</label>
                        <select class="customer-form-control @error('type') is-invalid @enderror" name="type">
                            <option value="regular" {{ old('type', $customer->type) == 'regular' ? 'selected' : '' }}>Regular</option>
                            <option value="premium" {{ old('type', $customer->type) == 'premium' ? 'selected' : '' }}>Premium</option>
                            <option value="vip" {{ old('type', $customer->type) == 'vip' ? 'selected' : '' }}>VIP</option>
                        </select>
                        @error('type')
                            <div style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="customer-form-group">
                        <label>Status</label>
                        <select class="customer-form-control @error('is_active') is-invalid @enderror" name="is_active">
                            <option value="1" {{ old('is_active', $customer->is_active) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active', $customer->is_active) ? '' : 'selected' }}>Inactive</option>
                        </select>
                        @error('is_active')
                            <div style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="customer-form-group">
                    <label>Address</label>
                    <input type="text" class="customer-form-control @error('address') is-invalid @enderror"
                           name="address" value="{{ old('address', $customer->address) }}">
                    @error('address')
                        <div style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="customer-form-group">
                    <label>Notes</label>
                    <textarea class="customer-form-control @error('notes') is-invalid @enderror"
                              name="notes" rows="4">{{ old('notes', $customer->notes) }}</textarea>
                    @error('notes')
                        <div style="color: #DC2626; font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="customer-form-actions">
                    <a href="{{ route('customers.index') }}" class="customer-btn customer-btn-ghost">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="customer-btn customer-btn-primary">
                        <i class="fas fa-save"></i> Update Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
