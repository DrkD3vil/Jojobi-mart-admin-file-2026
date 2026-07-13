{{-- @extends('layouts.app')

@section('title','Access Key Mapping')

@section('content')
<div class="max-w-4xl mx-auto p-6">

    @if(session('success'))
        <div class="p-3 mb-4 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h2 class="text-lg font-semibold mb-4">Map Privilege → Module (access_key)</h2>

        <form method="POST" action="{{ route('access_keys.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @csrf

            <select name="privilege_id" class="border rounded p-2" required>
                <option value="">Select Privilege</option>
                @foreach($privileges as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->slug }})</option>
                @endforeach
            </select>

            <select name="access_key" class="border rounded p-2" required>
                <option value="">Select Module</option>
                @foreach($accessKeys as $key)
                    <option value="{{ $key }}">{{ $key }}</option>
                @endforeach
            </select>

            <button class="bg-black text-white rounded p-2">Save</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-semibold mb-4">Existing Mappings</h2>

        <div class="space-y-2">
            @forelse($mappings as $m)
                <div class="flex items-center justify-between border rounded p-3">
                    <div>
                        <div class="font-medium">Privilege ID: {{ $m->privilege_id }}</div>
                        <div class="text-sm text-gray-600">Access Key: {{ $m->access_key }}</div>
                    </div>
                    <form method="POST" action="{{ route('access_keys.destroy',$m->id) }}">
                        @csrf @method('DELETE')
                        <button class="text-red-600">Remove</button>
                    </form>
                </div>
            @empty
                <div class="text-gray-500">No mappings yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection --}}

@extends('layouts.app')

@section('title', 'Access Key Management')
@section('page_title', 'Access Key Mapping')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary/10">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Total Mappings</h3>
                <p class="stat-value">{{ $stats['total_mappings'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success/10">
                <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Users with Access</h3>
                <p class="stat-value">{{ $stats['total_users_with_access'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-warning/10">
                <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Roles with Access</h3>
                <p class="stat-value">{{ $stats['total_roles_with_access'] }}</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-info/10">
                <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <div class="stat-content">
                <h3 class="stat-label">Most Assigned</h3>
                <p class="stat-value">{{ $stats['most_assigned_key']?->access_key ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    @if(auth()->user()->canAssignAccessKey())
        <!-- Assignment Form Card -->
        <div class="card overflow-hidden">
            <div class="card-header">
                <h3 class="text-lg font-semibold">Assign New Access Keys</h3>
                <p class="text-sm text-muted-foreground mt-1">Configure access permissions for users or roles</p>
            </div>

            <div class="p-6">
                <form id="accessKeyForm" method="POST" action="{{ route('access_keys.store') }}" class="space-y-6">
                    @csrf

                    <!-- Assignment Type Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="form-label">Assignment Type</label>
                            <div class="assignment-type-grid">
                                <label class="assignment-type-option">
                                    <input type="radio" name="assignment_type" value="user" class="hidden peer" checked>
                                    <div class="assignment-card">
                                        <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Specific User</span>
                                    </div>
                                </label>

                                <label class="assignment-type-option">
                                    <input type="radio" name="assignment_type" value="role" class="hidden peer">
                                    <div class="assignment-card">
                                        <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Specific Role</span>
                                    </div>
                                </label>

                                <label class="assignment-type-option">
                                    <input type="radio" name="assignment_type" value="all_users" class="hidden peer">
                                    <div class="assignment-card">
                                        <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span>All Users</span>
                                    </div>
                                </label>

                                <label class="assignment-type-option">
                                    <input type="radio" name="assignment_type" value="all_roles" class="hidden peer">
                                    <div class="assignment-card">
                                        <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>All Roles</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic Selection Area -->
                        <div class="space-y-4">
                            <!-- User Selection (shown for 'user' type) -->
                            <div id="userSelection" class="selection-field">
                                <label class="form-label">Select User</label>
                                <div class="relative">
                                    <input type="text"
                                           id="userSearch"
                                           class="form-input pl-10"
                                           placeholder="Search users..."
                                           autocomplete="off">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <select name="user_id" id="userId" class="form-select hidden">
                                    <option value="">Select a user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <div id="userSearchResults" class="search-results mt-2"></div>
                            </div>

                            <!-- Role Selection (shown for 'role' type) -->
                            <div id="roleSelection" class="selection-field hidden">
                                <label class="form-label">Select Role</label>
                                <select name="role_id" id="roleId" class="form-select">
                                    <option value="">Choose a role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}">
                                            {{ $role->name }} ({{ $role->users_count }} users)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Privilege Selection -->
                    <div class="form-group">
                        <label class="form-label">Select Privilege</label>
                        <select name="privilege_id" id="privilegeId" class="form-select">
                            <option value="">Choose a privilege</option>
                            @foreach($privileges as $privilege)
                                <option value="{{ $privilege->id }}">{{ $privilege->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Access Keys Selection -->
                    <div class="form-group">
                        <div class="flex items-center justify-between mb-4">
                            <label class="form-label mb-0">Select Modules</label>
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" id="selectAll" class="checkbox">
                                    <span class="text-sm">Select All</span>
                                </label>
                                <button type="button" id="resetSelection" class="btn-secondary btn-sm">
                                    Reset
                                </button>
                            </div>
                        </div>

                        <div class="modules-grid">
                            @foreach($accessKeys as $key)
                                <label class="module-checkbox">
                                    <input type="checkbox"
                                           name="access_keys[]"
                                           value="{{ $key }}"
                                           class="hidden peer">
                                    <div class="module-card">
                                        <span class="module-icon">
                                            @switch($key)
                                                @case('profile')
                                                    👤
                                                    @break
                                                @case('rbac')
                                                    🔐
                                                    @break
                                                @case('categories')
                                                    📁
                                                    @break
                                                @case('brands')
                                                    🏷️
                                                    @break
                                                @case('products')
                                                    📦
                                                    @break
                                                @case('pos')
                                                    💳
                                                    @break
                                                @case('customers')
                                                    👥
                                                    @break
                                                @case('orders')
                                                    📋
                                                    @break
                                                @case('stock')
                                                    📊
                                                    @break
                                                @case('reports_financial')
                                                    📈
                                                    @break
                                                @default
                                                    🔑
                                            @endswitch
                                        </span>
                                        <span class="module-name">{{ $accessKeyLabels[$key] ?? ucfirst($key) }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" id="previewBtn" class="btn-secondary">
                            Preview Assignment
                        </button>
                        <button type="submit" class="btn-primary" id="submitBtn">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Assign Access Keys
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview Modal -->
        <div id="previewModal" class="modal hidden">
            <div class="modal-overlay"></div>
            <div class="modal-container">
                <div class="modal-header">
                    <h3 class="text-lg font-semibold">Preview Assignment</h3>
                    <button type="button" class="modal-close">&times;</button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Preview content will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary modal-close">Cancel</button>
                    <button type="button" class="btn-primary" id="confirmAssign">Confirm Assignment</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Mappings Table Card -->
    <div class="card">
        <div class="card-header">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold">Existing Mappings</h3>
                    <p class="text-sm text-muted-foreground">Manage existing access key assignments</p>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Search -->
                    <div class="relative">
                        <input type="text"
                               id="tableSearch"
                               class="form-input pl-9 pr-4 py-2 w-full sm:w-64"
                               placeholder="Search mappings...">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Bulk Actions -->
                    <button type="button"
                            id="bulkDeleteBtn"
                            class="btn-danger btn-sm hidden"
                            disabled>
                        Delete Selected
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" id="selectAllRows" class="checkbox">
                        </th>
                        <th>User / Role</th>
                        <th>Access Key</th>
                        <th>Privilege</th>
                        <th>Assignment Type</th>
                        <th>Assigned At</th>
                        <th class="w-24">Actions</th>
                    </tr>
                </thead>
                <tbody id="mappingsTableBody">
                    @forelse($mappings as $mapping)
                        <tr data-id="{{ $mapping->id }}">
                            <td>
                                <input type="checkbox" class="row-checkbox checkbox" value="{{ $mapping->id }}">
                            </td>
                            <td>
                                @if($mapping->user)
                                    <div class="flex items-center">
                                        <div class="avatar avatar-sm bg-primary/10 text-primary">
                                            {{ substr($mapping->user->name, 0, 2) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium">{{ $mapping->user->name }}</p>
                                            <p class="text-xs text-muted-foreground">{{ $mapping->user->email }}</p>
                                        </div>
                                    </div>
                                @elseif($mapping->role)
                                    <div class="flex items-center">
                                        <div class="avatar avatar-sm bg-warning/10 text-warning">
                                            👥
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium">{{ $mapping->role->name }}</p>
                                            <p class="text-xs text-muted-foreground">Role</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $accessKeyLabels[$mapping->access_key] ?? $mapping->access_key }}
                                </span>
                            </td>
                            <td>{{ $mapping->privilege->name ?? 'N/A' }}</td>
                            <td>
                                @if($mapping->user)
                                    <span class="badge badge-info">User</span>
                                @elseif($mapping->role)
                                    <span class="badge badge-warning">Role</span>
                                @else
                                    <span class="badge badge-secondary">Unknown</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-sm" title="{{ $mapping->created_at }}">
                                    {{ $mapping->created_at->diffForHumans() }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button type="button"
                                            class="btn-icon btn-sm btn-secondary"
                                            onclick="showMappingDetails({{ $mapping->id }})"
                                            title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <form class="delete-form" method="POST" action="{{ route('access_keys.destroy', $mapping->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-icon btn-sm btn-danger"
                                                onclick="return confirmDelete(event)"
                                                title="Remove">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8">
                                <div class="empty-state">
                                    <svg class="w-16 h-16 mx-auto mb-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-muted-foreground">No mappings found</p>
                                    <p class="text-sm text-muted-foreground mt-1">Get started by creating a new mapping above</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        {{-- @if($mappings->hasPages())
            <div class="card-footer">
                {{ $mappings->links() }}
            </div>
        @endif --}}

        @if ($mappings->hasPages())
            <div class="pagination-wrapper">
                {{ $mappings->onEachSide(1)->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast hidden">
    <div class="toast-content">
        <span class="toast-message"></span>
    </div>
</div>

<style>
    /* Assignment Type Grid */
    .assignment-type-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .assignment-card {
        padding: 1rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        cursor: pointer;
        text-align: center;
        transition: all var(--transition-fast);
    }

    .assignment-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: var(--card-shadow-hover);
    }

    .peer:checked + .assignment-card {
        border-color: var(--primary);
        background: var(--primary);
        color: var(--primary-foreground);
        box-shadow: 0 0 0 2px var(--primary), var(--card-shadow);
    }

    /* Modules Grid */
    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 0.75rem;
    }

    .module-card {
        padding: 0.75rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        cursor: pointer;
        text-align: center;
        transition: all var(--transition-fast);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .module-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: var(--card-shadow-hover);
    }

    .peer:checked + .module-card {
        border-color: var(--primary);
        background: var(--primary);
        color: var(--primary-foreground);
        box-shadow: 0 0 0 2px var(--primary), var(--card-shadow);
    }

    .module-icon {
        font-size: 1.5rem;
    }

    .module-name {
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Search Results */
    .search-results {
        max-height: 200px;
        overflow-y: auto;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        display: none;
    }

    .search-results.active {
        display: block;
    }

    .search-result-item {
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: background var(--transition-fast);
    }

    .search-result-item:hover {
        background: var(--bg-tertiary);
    }

    .search-result-item.selected {
        background: var(--primary);
        color: var(--primary-foreground);
    }

    /* Modal */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal.hidden {
        display: none;
    }

    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
    }

    .modal-container {
        position: relative;
        background: var(--bg-secondary);
        border-radius: var(--radius);
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--dropdown-shadow);
        animation: modalSlideIn var(--transition-normal);
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: var(--text-secondary);
    }

    .modal-close:hover {
        color: var(--text-primary);
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 1.5rem;
        right: 1.5rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        padding: 1rem;
        box-shadow: var(--dropdown-shadow);
        z-index: 1100;
        animation: toastSlideIn var(--transition-normal);
    }

    .toast.hidden {
        display: none;
    }

    @keyframes toastSlideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .toast.success {
        border-left: 4px solid var(--success);
    }

    .toast.error {
        border-left: 4px solid var(--danger);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .assignment-type-grid {
            grid-template-columns: 1fr;
        }

        .modules-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .modal-container {
            width: 95%;
            margin: 1rem;
        }

        .toast {
            left: 1rem;
            right: 1rem;
            bottom: 1rem;
        }
    }
</style>

<script>
    // Form Handling
    document.addEventListener('DOMContentLoaded', function() {
        initializeAssignmentTypes();
        initializeUserSearch();
        initializeCheckboxHandlers();
        initializeFormSubmission();
        initializeTableSearch();
        initializeBulkActions();
        initializePreviewModal();
    });

    function initializeAssignmentTypes() {
        const typeRadios = document.querySelectorAll('input[name="assignment_type"]');
        const userSelection = document.getElementById('userSelection');
        const roleSelection = document.getElementById('roleSelection');

        typeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                userSelection.classList.add('hidden');
                roleSelection.classList.add('hidden');

                switch(this.value) {
                    case 'user':
                        userSelection.classList.remove('hidden');
                        break;
                    case 'role':
                        roleSelection.classList.remove('hidden');
                        break;
                }
            });
        });
    }

    function initializeUserSearch() {
        const searchInput = document.getElementById('userSearch');
        const userIdSelect = document.getElementById('userId');
        const resultsDiv = document.getElementById('userSearchResults');

        if (!searchInput) return;

        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                resultsDiv.classList.remove('active');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`{{ route("access_keys.search") }}?type=users&query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(users => {
                        resultsDiv.innerHTML = '';
                        if (users.length > 0) {
                            users.forEach(user => {
                                const div = document.createElement('div');
                                div.className = 'search-result-item';
                                div.innerHTML = `
                                    <div class="font-medium">${user.name}</div>
                                    <div class="text-xs text-muted-foreground">${user.email}</div>
                                `;
                                div.addEventListener('click', () => selectUser(user));
                                resultsDiv.appendChild(div);
                            });
                            resultsDiv.classList.add('active');
                        } else {
                            resultsDiv.innerHTML = '<div class="search-result-item">No users found</div>';
                            resultsDiv.classList.add('active');
                        }
                    });
            }, 300);
        });

        function selectUser(user) {
            searchInput.value = user.name;
            userIdSelect.value = user.id;
            resultsDiv.classList.remove('active');
        }

        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                resultsDiv.classList.remove('active');
            }
        });
    }

    function initializeCheckboxHandlers() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('input[name="access_keys[]"]');
        const resetBtn = document.getElementById('resetSelection');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                if (selectAll) selectAll.checked = false;
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (selectAll) {
                    selectAll.checked = Array.from(checkboxes).every(c => c.checked);
                }
            });
        });
    }

    function initializeFormSubmission() {
        const form = document.getElementById('accessKeyForm');
        const submitBtn = document.getElementById('submitBtn');

        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate form
            const assignmentType = document.querySelector('input[name="assignment_type"]:checked')?.value;
            const privilegeId = document.getElementById('privilegeId').value;
            const accessKeys = document.querySelectorAll('input[name="access_keys[]"]:checked');

            if (!assignmentType) {
                showToast('Please select an assignment type', 'error');
                return;
            }

            if (!privilegeId) {
                showToast('Please select a privilege', 'error');
                return;
            }

            if (accessKeys.length === 0) {
                showToast('Please select at least one module', 'error');
                return;
            }

            if (assignmentType === 'user') {
                const userId = document.getElementById('userId').value;
                if (!userId) {
                    showToast('Please select a user', 'error');
                    return;
                }
            }

            if (assignmentType === 'role') {
                const roleId = document.getElementById('roleId').value;
                if (!roleId) {
                    showToast('Please select a role', 'error');
                    return;
                }
            }

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';

            // Submit form
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    form.reset();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>Assign Access Keys';
            });
        });
    }

    function initializeTableSearch() {
        const searchInput = document.getElementById('tableSearch');
        if (!searchInput) return;

        let searchTimeout;

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const query = this.value;
                fetch(`{{ route("access_keys.search") }}?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        updateTable(data.data);
                    });
            }, 300);
        });
    }

    function updateTable(mappings) {
        const tbody = document.getElementById('mappingsTableBody');
        if (!tbody) return;

        if (mappings.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-8">
                        <div class="empty-state">
                            <svg class="w-16 h-16 mx-auto mb-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-muted-foreground">No mappings found</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        // Build table HTML (simplified for brevity)
        let html = '';
        mappings.forEach(mapping => {
            html += `
                <tr data-id="${mapping.id}">
                    <td><input type="checkbox" class="row-checkbox checkbox" value="${mapping.id}"></td>
                    <td>
                        ${mapping.user ?
                            `<div class="flex items-center">
                                <div class="avatar avatar-sm bg-primary/10 text-primary">${mapping.user.name.substring(0, 2)}</div>
                                <div class="ml-3">
                                    <p class="font-medium">${mapping.user.name}</p>
                                    <p class="text-xs text-muted-foreground">${mapping.user.email}</p>
                                </div>
                            </div>` :
                            mapping.role ?
                            `<div class="flex items-center">
                                <div class="avatar avatar-sm bg-warning/10 text-warning">👥</div>
                                <div class="ml-3">
                                    <p class="font-medium">${mapping.role.name}</p>
                                    <p class="text-xs text-muted-foreground">Role</p>
                                </div>
                            </div>` :
                            '<span class="badge badge-secondary">N/A</span>'
                        }
                    </td>
                    <td><span class="badge badge-primary">${mapping.access_key}</span></td>
                    <td>${mapping.privilege?.name ?? 'N/A'}</td>
                    <td>
                        <span class="badge badge-${mapping.user ? 'info' : (mapping.role ? 'warning' : 'secondary')}">
                            ${mapping.user ? 'User' : (mapping.role ? 'Role' : 'Unknown')}
                        </span>
                    </td>
                    <td><span class="text-sm">${new Date(mapping.created_at).toLocaleDateString()}</span></td>
                    <td>
                        <div class="flex items-center space-x-2">
                            <button type="button" class="btn-icon btn-sm btn-secondary" onclick="showMappingDetails(${mapping.id})">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <form class="delete-form" method="POST" action="/access-keys/${mapping.id}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-icon btn-sm btn-danger" onclick="return confirmDelete(event)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        reinitializeRowCheckboxes();
    }

    function initializeBulkActions() {
        const selectAllRows = document.getElementById('selectAllRows');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        if (selectAllRows) {
            selectAllRows.addEventListener('change', function() {
                const rowCheckboxes = document.querySelectorAll('.row-checkbox');
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBulkDeleteButton();
            });
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('row-checkbox')) {
                updateBulkDeleteButton();

                if (selectAllRows) {
                    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
                    selectAllRows.checked = Array.from(rowCheckboxes).every(c => c.checked);
                    selectAllRows.indeterminate = !selectAllRows.checked &&
                        Array.from(rowCheckboxes).some(c => c.checked);
                }
            }
        });

        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', function() {
                const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                    .map(cb => cb.value);

                if (selectedIds.length === 0) return;

                if (confirm(`Are you sure you want to delete ${selectedIds.length} mapping(s)?`)) {
                    fetch('{{ route("access_keys.bulk_destroy") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ ids: selectedIds })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showToast(data.message, 'error');
                        }
                    });
                }
            });
        }
    }

    function updateBulkDeleteButton() {
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const selectedCount = document.querySelectorAll('.row-checkbox:checked').length;

        if (bulkDeleteBtn) {
            if (selectedCount > 0) {
                bulkDeleteBtn.classList.remove('hidden');
                bulkDeleteBtn.disabled = false;
                bulkDeleteBtn.textContent = `Delete Selected (${selectedCount})`;
            } else {
                bulkDeleteBtn.classList.add('hidden');
                bulkDeleteBtn.disabled = true;
            }
        }
    }

    function reinitializeRowCheckboxes() {
        const selectAllRows = document.getElementById('selectAllRows');
        if (selectAllRows) {
            selectAllRows.checked = false;
        }
        updateBulkDeleteButton();
    }

    function initializePreviewModal() {
        const previewBtn = document.getElementById('previewBtn');
        const modal = document.getElementById('previewModal');
        const closeButtons = document.querySelectorAll('.modal-close');
        const confirmBtn = document.getElementById('confirmAssign');

        if (previewBtn) {
            previewBtn.addEventListener('click', showPreview);
        }

        closeButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        });

        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
                document.getElementById('accessKeyForm').dispatchEvent(new Event('submit'));
            });
        }

        // Close modal on overlay click
        const overlay = document.querySelector('.modal-overlay');
        if (overlay) {
            overlay.addEventListener('click', () => {
                modal.classList.add('hidden');
            });
        }
    }

    function showPreview() {
        const modal = document.getElementById('previewModal');
        const previewContent = document.getElementById('previewContent');

        const assignmentType = document.querySelector('input[name="assignment_type"]:checked')?.value;
        const privilege = document.getElementById('privilegeId').selectedOptions[0]?.text;
        const selectedKeys = Array.from(document.querySelectorAll('input[name="access_keys[]"]:checked'))
            .map(cb => cb.nextElementSibling?.querySelector('.module-name')?.textContent || cb.value);

        if (!assignmentType || !privilege || selectedKeys.length === 0) {
            showToast('Please complete the form before preview', 'error');
            return;
        }

        let targetInfo = '';
        if (assignmentType === 'user') {
            const userId = document.getElementById('userId').value;
            const userOption = document.querySelector(`#userId option[value="${userId}"]`);
            targetInfo = userOption ? userOption.textContent : 'Selected User';
        } else if (assignmentType === 'role') {
            const roleId = document.getElementById('roleId').value;
            const roleOption = document.querySelector(`#roleId option[value="${roleId}"]`);
            targetInfo = roleOption ? roleOption.textContent : 'Selected Role';
        } else {
            targetInfo = assignmentType === 'all_users' ? 'All Users' : 'All Roles';
        }

        previewContent.innerHTML = `
            <div class="space-y-4">
                <div class="preview-item">
                    <span class="preview-label">Assignment Type:</span>
                    <span class="preview-value">${assignmentType.replace('_', ' ').toUpperCase()}</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Target:</span>
                    <span class="preview-value">${targetInfo}</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Privilege:</span>
                    <span class="preview-value">${privilege}</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Modules (${selectedKeys.length}):</span>
                    <div class="preview-modules">
                        ${selectedKeys.map(key => `<span class="badge badge-primary">${key}</span>`).join(' ')}
                    </div>
                </div>
            </div>
        `;

        modal.classList.remove('hidden');
    }

    function showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        const toastMessage = toast.querySelector('.toast-message');

        toast.className = `toast ${type}`;
        toastMessage.textContent = message;
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    window.showMappingDetails = function(id) {
        // Implement view details functionality
        showToast('View details feature coming soon', 'info');
    };

    window.confirmDelete = function(event) {
        if (!confirm('Are you sure you want to remove this mapping?')) {
            event.preventDefault();
            return false;
        }
        return true;
    };
</script>

<style>
    /* Form Elements */
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: var(--text-primary);
    }

    .form-input, .form-select {
        width: 100%;
        padding: 0.625rem 1rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        color: var(--text-primary);
        font-size: 0.875rem;
        transition: all var(--transition-fast);
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--ring);
        box-shadow: 0 0 0 2px var(--ring);
    }

    .checkbox {
        width: 1rem;
        height: 1rem;
        border-radius: 0.25rem;
        border: 1px solid var(--border-color);
        background: var(--bg-secondary);
        cursor: pointer;
    }

    /* Buttons */
    .btn-primary, .btn-secondary, .btn-danger {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.625rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: var(--radius);
        border: none;
        cursor: pointer;
        transition: all var(--transition-fast);
    }

    .btn-primary {
        background: var(--primary);
        color: var(--primary-foreground);
    }

    .btn-primary:hover:not(:disabled) {
        opacity: 0.9;
        transform: translateY(-1px);
        box-shadow: var(--card-shadow);
    }

    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: var(--secondary);
        color: var(--secondary-foreground);
    }

    .btn-secondary:hover:not(:disabled) {
        background: var(--accent);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
    }

    .btn-danger:hover:not(:disabled) {
        opacity: 0.9;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    .btn-icon {
        padding: 0.5rem;
        border-radius: 9999px;
    }

    .btn-icon.btn-sm {
        padding: 0.375rem;
    }

    /* Cards */
    .card {
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        overflow: hidden;
        transition: all var(--transition-normal);
    }

    .card:hover {
        box-shadow: var(--card-shadow-hover);
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-tertiary);
    }

    .card-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border-color);
        background: var(--bg-tertiary);
    }

    /* Stats Cards */
    .stat-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: var(--bg-secondary);
        border: 1px solid var(--border-color);
        border-radius: var(--radius);
        transition: all var(--transition-fast);
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--card-shadow-hover);
    }

    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: var(--radius);
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.2;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 9999px;
    }

    .badge-primary {
        background: var(--primary);
        color: var(--primary-foreground);
    }

    .badge-secondary {
        background: var(--secondary);
        color: var(--secondary-foreground);
    }

    .badge-success {
        background: var(--success);
        color: white;
    }

    .badge-warning {
        background: var(--warning);
        color: black;
    }

    .badge-danger {
        background: var(--danger);
        color: white;
    }

    .badge-info {
        background: var(--info);
        color: white;
    }

    /* Avatars */
    .avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        font-weight: 500;
    }

    .avatar-sm {
        width: 2rem;
        height: 2rem;
        font-size: 0.75rem;
    }

    .avatar-md {
        width: 2.5rem;
        height: 2.5rem;
        font-size: 0.875rem;
    }

    .avatar-lg {
        width: 3rem;
        height: 3rem;
        font-size: 1rem;
    }

    /* Data Table */
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        text-align: left;
        padding: 0.75rem 1rem;
        background: var(--bg-tertiary);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-secondary);
        border-bottom: 1px solid var(--border-color);
    }

    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-primary);
    }

    .data-table tbody tr:hover {
        background: var(--bg-tertiary);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
    }

    /* Animations */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    .animate-spin {
        animation: spin 1s linear infinite;
    }

    /* Utilities */
    .hidden {
        display: none !important;
    }

    .space-y-2 > * + * {
        margin-top: 0.5rem;
    }

    .space-y-4 > * + * {
        margin-top: 1rem;
    }

    .space-y-6 > * + * {
        margin-top: 1.5rem;
    }

    .space-x-2 > * + * {
        margin-left: 0.5rem;
    }

    .space-x-3 > * + * {
        margin-left: 0.75rem;
    }

    .space-x-4 > * + * {
        margin-left: 1rem;
    }

    /* Responsive Grid */
    @media (min-width: 640px) {
        .grid-cols-2 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 768px) {
        .grid-cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 1024px) {
        .grid-cols-4 {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>
@endsection
