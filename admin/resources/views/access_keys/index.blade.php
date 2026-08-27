

@extends('layouts.app')

@section('title', 'Access Key Management')
@section('page_title', 'Access Key Mapping')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card reveal">
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

        <div class="stat-card reveal">
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

        <div class="stat-card reveal">
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

        <div class="stat-card reveal">
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

    @if($aiAccessRequests->isNotEmpty())
        <!-- Pending AI Assistant Access Requests -->
        <div class="card reveal">
            <div class="card-header">
                <h3 class="text-lg font-semibold">Pending AI Assistant Requests</h3>
                <p class="text-sm text-muted-foreground mt-1">These users asked for AI Assistant access from within the app and are waiting on you.</p>
            </div>
            <div class="p-6 space-y-3">
                @foreach($aiAccessRequests as $req)
                    <div class="flex items-center justify-between gap-4 p-3 rounded-lg border border-[var(--border-color)]">
                        <div>
                            <p class="font-medium">{{ $req->user->name ?? 'Unknown user' }}</p>
                            <p class="text-sm text-muted-foreground">{{ $req->user->email ?? '' }} &middot; requested {{ $req->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('access_keys.ai_requests.approve', $req->id) }}">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('access_keys.ai_requests.deny', $req->id) }}">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm">Deny</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- User Access Lookup -->
    <div class="card reveal">
        <div class="card-header">
            <h3 class="text-lg font-semibold">Who Has Access?</h3>
            <p class="text-sm text-muted-foreground mt-1">Pick a user to see every access key they hold — assigned directly or inherited through a role</p>
        </div>

        <div class="p-6">
            <div class="form-group user-access-picker">
                <label class="form-label" for="userAccessSelect">Select User</label>
                <select id="userAccessSelect" class="form-select">
                    <option value="">Choose a user…</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <p id="userAccessPlaceholder" class="text-sm text-muted-foreground user-access-placeholder">
                Select a user above to view their access breakdown.
            </p>

            <div id="userAccessResult" class="user-access-result hidden"></div>
        </div>
    </div>

    @if(auth()->user()->canAssignAccessKey())
        <!-- Assignment Form Card -->
        <div class="card overflow-hidden reveal">
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
                                           placeholder="Type to filter the list below..."
                                           autocomplete="off">
                                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <select name="user_id" id="userId" class="form-select mt-2">
                                    <option value="">Select a user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
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
    <div class="card reveal">
        <div class="card-header">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold">Existing Mappings</h3>
                    <p class="text-sm text-muted-foreground">Manage existing access key assignments</p>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <!-- Filters -->
                    <select id="filterAccessKey" class="form-select filter-select" aria-label="Filter by access key">
                        <option value="">All Access Keys</option>
                        @foreach($accessKeys as $key)
                            <option value="{{ $key }}">{{ $accessKeyLabels[$key] ?? ucfirst($key) }}</option>
                        @endforeach
                    </select>

                    <select id="filterAssignmentType" class="form-select filter-select" aria-label="Filter by assignment type">
                        <option value="">All Types</option>
                        <option value="user">User</option>
                        <option value="role">Role</option>
                    </select>

                    <select id="sortMappings" class="form-select filter-select" aria-label="Sort mappings">
                        <option value="latest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name_asc">Name (A–Z)</option>
                        <option value="name_desc">Name (Z–A)</option>
                        <option value="access_key_asc">Access Key (A–Z)</option>
                        <option value="access_key_desc">Access Key (Z–A)</option>
                    </select>

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

    <!-- Recent Activity -->
    <div class="card reveal">
        <div class="card-header">
            <h3 class="text-lg font-semibold">Recent Activity</h3>
            <p class="text-sm text-muted-foreground mt-1">A running audit trail of every grant and revoke, most recent first</p>
        </div>

        @if ($recentActivity->isEmpty())
            <div class="p-6">
                <p class="text-sm text-muted-foreground">No activity recorded yet.</p>
            </div>
        @else
            <div class="activity-table-wrap">
                <table class="activity-table">
                    <thead>
                        <tr>
                            <th class="w-10"></th>
                            <th>What happened</th>
                            <th class="activity-col-by">By</th>
                            <th class="activity-col-when">When</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentActivity as $entry)
                            <tr>
                                <td>
                                    <div class="activity-icon {{ $entry->event === 'revoked' ? 'is-revoked' : 'is-granted' }}">
                                        @if ($entry->event === 'revoked')
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <p class="activity-title">{{ $entry->description ?? $entry->title }}</p>
                                    <p class="activity-meta activity-meta-mobile">
                                        by <strong>{{ $entry->causer->name ?? 'System' }}</strong>
                                        &middot; <span title="{{ $entry->created_at }}">{{ $entry->created_at->diffForHumans() }}</span>
                                    </p>
                                </td>
                                <td class="activity-col-by"><strong>{{ $entry->causer->name ?? 'System' }}</strong></td>
                                <td class="activity-col-when" title="{{ $entry->created_at }}">{{ $entry->created_at->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($recentActivity->hasPages())
                <div class="pagination-wrapper">
                    {{ $recentActivity->onEachSide(1)->links('vendor.pagination.custom') }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast hidden">
    <div class="toast-content">
        <span class="toast-message"></span>
    </div>
</div>

<!-- Mapping Details Modal -->
<div id="mappingDetailsModal" class="modal hidden">
    <div class="modal-overlay" id="mappingDetailsOverlay"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="text-lg font-semibold">Mapping Details</h3>
            <button type="button" class="modal-close" id="mappingDetailsClose">&times;</button>
        </div>
        <div class="modal-body" id="mappingDetailsBody">
            <p class="text-sm text-muted-foreground">Loading…</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" id="mappingDetailsCloseFooter">Close</button>
        </div>
    </div>
</div>


<script>
    // Total counts for the "assign to all users/roles" confirmation below --
    // these bulk actions loop every user/role with no undo, so the modal
    // needs the real row count before it fires.
    const TOTAL_USERS_COUNT = {{ $users->count() }};
    const TOTAL_ROLES_COUNT = {{ $roles->count() }};

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

    // The user picker is a plain, always-visible <select> (like the Role
    // picker next to it) so it's actually clickable/selectable on its own --
    // the search box above it just filters which of its <option>s show,
    // entirely client-side, so nothing depends on a network round trip.
    function initializeUserSearch() {
        const searchInput = document.getElementById('userSearch');
        const userIdSelect = document.getElementById('userId');

        if (!searchInput || !userIdSelect) return;

        const options = Array.from(userIdSelect.options).filter(o => o.value !== '');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();

            options.forEach(option => {
                const matches = !query
                    || (option.dataset.name || '').includes(query)
                    || (option.dataset.email || '').includes(query);
                option.hidden = !matches;
            });

            // If the currently selected option just got filtered out, clear it.
            if (userIdSelect.value && userIdSelect.selectedOptions[0]?.hidden) {
                userIdSelect.value = '';
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

            if (assignmentType === 'all_users' || assignmentType === 'all_roles') {
                const targetCount = assignmentType === 'all_users' ? TOTAL_USERS_COUNT : TOTAL_ROLES_COUNT;
                const targetLabel = assignmentType === 'all_users' ? 'user' : 'role';
                const rowCount = targetCount * accessKeys.length;
                const confirmed = confirm(
                    `This will grant ${accessKeys.length} access key(s) to all ${targetCount} ${targetLabel}(s) ` +
                    `-- ${rowCount} mapping(s) in total -- with no undo besides removing them one by one. Continue?`
                );
                if (!confirmed) {
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
        const accessKeyFilter = document.getElementById('filterAccessKey');
        const typeFilter = document.getElementById('filterAssignmentType');
        const sortSelect = document.getElementById('sortMappings');

        if (!searchInput) return;

        let searchTimeout;

        function fetchMappings() {
            const params = new URLSearchParams();
            params.set('query', searchInput.value);
            if (accessKeyFilter && accessKeyFilter.value) params.set('access_key', accessKeyFilter.value);
            if (typeFilter && typeFilter.value) params.set('assignment_type', typeFilter.value);
            if (sortSelect && sortSelect.value) params.set('sort', sortSelect.value);

            fetch(`{{ route("access_keys.search") }}?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    updateTable(data.data);
                });
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(fetchMappings, 300);
        });

        [accessKeyFilter, typeFilter, sortSelect].forEach(select => {
            if (select) select.addEventListener('change', fetchMappings);
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
                                <button type="submit" class="btn-icon btn-sm btn-danger">
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

        // This modal only renders for users who canAssignAccessKey() -- for
        // everyone else `modal` is null. Bail out here rather than letting
        // the (unscoped) selectors below reach past this modal and grab
        // .modal-close/.modal-overlay elements belonging to OTHER modals
        // on the page (e.g. the mapping-details modal, which always
        // renders), which would wire them to this null `modal` and throw
        // on click.
        if (!modal) return;

        const closeButtons = modal.querySelectorAll('.modal-close');
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
        const overlay = modal.querySelector('.modal-overlay');
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
        const modal = document.getElementById('mappingDetailsModal');
        const body = document.getElementById('mappingDetailsBody');

        body.innerHTML = '<p class="text-sm text-muted-foreground">Loading…</p>';
        modal.classList.remove('hidden');

        fetch(`/access-keys/${id}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    body.innerHTML = `<p class="text-sm text-muted-foreground">${escapeHtml(data.message || 'Mapping not found.')}</p>`;
                    return;
                }
                renderMappingDetails(data.mapping, data.timeline);
            })
            .catch(() => {
                body.innerHTML = '<p class="text-sm text-muted-foreground">Failed to load mapping details. Please try again.</p>';
            });
    };

    function renderMappingDetails(mapping, timeline) {
        const body = document.getElementById('mappingDetailsBody');

        let html = `
            <div class="space-y-4">
                <div class="preview-item">
                    <span class="preview-label">Access Key:</span>
                    <span class="preview-value"><span class="badge badge-primary">${escapeHtml(mapping.access_key_label)}</span></span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Privilege:</span>
                    <span class="preview-value">${escapeHtml(mapping.privilege)}</span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Assigned To:</span>
                    <span class="preview-value">${escapeHtml(mapping.target_name)}${mapping.target_email ? ' &middot; ' + escapeHtml(mapping.target_email) : ''}
                        <span class="badge ${mapping.target_type === 'user' ? 'badge-info' : 'badge-warning'}">${escapeHtml(mapping.target_type)}</span>
                    </span>
                </div>
                <div class="preview-item">
                    <span class="preview-label">Created:</span>
                    <span class="preview-value" title="${escapeHtml(mapping.created_at)}">${escapeHtml(mapping.created_at_human)}</span>
                </div>
            </div>
        `;

        html += '<h4 class="user-access-group-title" style="margin-top:1.5rem;">History</h4>';

        if (!timeline.length) {
            html += '<p class="text-sm text-muted-foreground">No history recorded for this mapping.</p>';
        } else {
            html += '<div class="space-y-3" style="margin-top:.75rem;">';
            timeline.forEach(entry => {
                html += `
                    <div class="activity-item">
                        <div class="activity-icon ${entry.event === 'revoked' ? 'is-revoked' : 'is-granted'}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${entry.event === 'revoked' ? 'M6 18L18 6M6 6l12 12' : 'M5 13l4 4L19 7'}" /></svg>
                        </div>
                        <div class="activity-body">
                            <p class="activity-title">${escapeHtml(entry.title)}</p>
                            ${entry.description ? `<p class="activity-desc">${escapeHtml(entry.description)}</p>` : ''}
                            <p class="activity-meta">by <strong>${escapeHtml(entry.causer)}</strong> &middot; ${escapeHtml(entry.created_at)}</p>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
        }

        body.innerHTML = html;
    }

    (function initializeMappingDetailsModal() {
        const modal = document.getElementById('mappingDetailsModal');
        const close = () => modal.classList.add('hidden');

        document.getElementById('mappingDetailsClose')?.addEventListener('click', close);
        document.getElementById('mappingDetailsCloseFooter')?.addEventListener('click', close);
        document.getElementById('mappingDetailsOverlay')?.addEventListener('click', close);
    })();

    // Single-row delete is a real <form method="POST"> (method-spoofed to
    // DELETE) so it still works if JS fails to load, but the controller
    // returns JSON, not a redirect -- if the browser submits it natively
    // the user lands on a raw JSON "page" instead of staying on the table.
    // Intercepting via delegation (rather than per-row wiring) means this
    // also covers rows injected later by updateTable()'s search results.
    document.addEventListener('submit', function(event) {
        const form = event.target;
        if (!form.classList.contains('delete-form')) return;

        event.preventDefault();

        if (!confirm('Are you sure you want to remove this mapping?')) return;

        const row = form.closest('tr');
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                if (row) row.remove();
            } else {
                showToast(data.message || 'Failed to remove mapping.', 'error');
                if (submitBtn) submitBtn.disabled = false;
            }
        })
        .catch(() => {
            showToast('An error occurred. Please try again.', 'error');
            if (submitBtn) submitBtn.disabled = false;
        });
    });

    // --- User Access Lookup ("Who Has Access?") ---
    document.addEventListener('DOMContentLoaded', function() {
        initializeUserAccessLookup();
        initializeScrollReveal();
    });

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value).replace(/[&<>"']/g, function(char) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
        });
    }

    function initializeUserAccessLookup() {
        const select = document.getElementById('userAccessSelect');
        const resultBox = document.getElementById('userAccessResult');
        const placeholder = document.getElementById('userAccessPlaceholder');

        if (!select) return;

        let requestController = null;

        select.addEventListener('change', function() {
            const userId = this.value;

            if (requestController) requestController.abort();

            if (!userId) {
                resultBox.classList.add('hidden');
                placeholder.classList.remove('hidden');
                return;
            }

            placeholder.classList.add('hidden');
            resultBox.classList.remove('hidden');
            resultBox.innerHTML = '<p class="text-sm text-muted-foreground">Loading access…</p>';

            requestController = new AbortController();

            fetch(`/access-keys/users/${userId}/access`, { signal: requestController.signal })
                .then(response => response.json())
                .then(data => renderUserAccess(data))
                .catch(error => {
                    if (error.name === 'AbortError') return;
                    resultBox.innerHTML = '<p class="text-sm text-muted-foreground">Failed to load access. Please try again.</p>';
                });
        });
    }

    function renderUserAccess(data) {
        const resultBox = document.getElementById('userAccessResult');
        const totalDirect = data.direct.length;
        const totalViaRole = data.via_role.reduce((sum, group) => sum + group.access.length, 0);

        if (totalDirect === 0 && totalViaRole === 0) {
            resultBox.innerHTML = `
                <div class="user-access-summary">
                    <span class="badge badge-secondary">No access assigned to ${escapeHtml(data.user.name)}</span>
                </div>
            `;
            return;
        }

        let html = `
            <div class="user-access-summary">
                <span class="badge badge-primary">${totalDirect} direct</span>
                <span class="badge badge-info">${totalViaRole} via role</span>
            </div>
        `;

        if (totalDirect > 0) {
            html += `
                <div class="user-access-group">
                    <h4 class="user-access-group-title">Direct Assignment</h4>
                    <div class="user-access-chips">
                        ${data.direct.map(item => `<span class="badge badge-primary" title="${escapeHtml(item.privilege || '')}">${escapeHtml(item.label)}</span>`).join(' ')}
                    </div>
                </div>
            `;
        }

        data.via_role.forEach(group => {
            html += `
                <div class="user-access-group">
                    <h4 class="user-access-group-title">Via Role: ${escapeHtml(group.role)}</h4>
                    <div class="user-access-chips">
                        ${group.access.map(item => `<span class="badge badge-warning" title="${escapeHtml(item.privilege || '')}">${escapeHtml(item.label)}</span>`).join(' ')}
                    </div>
                </div>
            `;
        });

        resultBox.innerHTML = html;
    }

    // --- Scroll-triggered reveal animation ---
    function initializeScrollReveal() {
        const targets = document.querySelectorAll('.reveal');
        if (!targets.length) return;

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduceMotion || !('IntersectionObserver' in window)) {
            targets.forEach(el => el.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -10% 0px' });

        targets.forEach(el => observer.observe(el));
    }
</script>

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
    /* NOTE: an unscoped `.hidden { display: none !important; }` rule used to
       live here. Tailwind's CDN build already generates a correct `.hidden`
       utility on its own -- this hand-written one was redundant, but worse,
       being `!important` and inline (not scoped to this page), it beat
       Tailwind's `lg:block`/`lg:hidden` responsive variants for *every*
       element with class="hidden" on the page, including ones belonging to
       the shared header partial: the desktop sidebar toggle and the page
       title (`hidden lg:block`) were being forced invisible at every
       viewport width, permanently, only on this page. Removed. */

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

    /* ==========================================================
       Color utility fixes
       Tailwind's CDN build has no "primary/success/warning/info/
       muted-foreground" colors configured, so bg-primary/10,
       text-primary, text-muted-foreground etc. were rendering with
       no color at all. These give them real definitions using the
       app's existing design tokens.
       ========================================================== */
    .bg-primary\/10 { background-color: color-mix(in srgb, var(--primary) 14%, transparent); }
    .bg-success\/10 { background-color: color-mix(in srgb, var(--success) 16%, transparent); }
    .bg-warning\/10 { background-color: color-mix(in srgb, var(--warning) 18%, transparent); }
    .bg-info\/10 { background-color: color-mix(in srgb, var(--info) 16%, transparent); }
    .text-primary { color: var(--primary); }
    .text-success { color: var(--success); }
    .text-warning { color: var(--warning); }
    .text-info { color: var(--info); }
    .text-muted-foreground { color: var(--text-secondary); }

    /* Preview / mapping-details content (was referenced by JS but never styled) */
    .preview-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        padding-bottom: 0.875rem;
        border-bottom: 1px solid var(--border-color);
    }

    .preview-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .preview-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
    }

    .preview-value {
        font-size: 0.9375rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    .preview-modules {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
        margin-top: 0.25rem;
    }

    .toast.info {
        border-left: 4px solid var(--info);
    }

    /* --- Scroll-triggered reveal animation --- */
    .reveal {
        opacity: 0;
        transform: translateY(18px);
        transition: opacity 0.55s cubic-bezier(0.4, 0, 0.2, 1), transform 0.55s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
        .reveal {
            transition: none;
            opacity: 1;
            transform: none;
        }
    }

    /* --- User Access Lookup ("Who Has Access?") --- */
    .user-access-picker {
        max-width: 28rem;
    }

    .user-access-placeholder {
        margin-top: 1rem;
    }

    .user-access-result {
        margin-top: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .user-access-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .user-access-group-title {
        font-size: 0.8125rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .user-access-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.375rem;
    }

    /* --- Mappings table filter/sort dropdowns --- */
    .filter-select {
        width: auto;
        min-width: 9.5rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    @media (max-width: 640px) {
        .filter-select {
            width: 100%;
        }
    }

    /* --- Recent Activity feed --- */
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.65rem 0;
        border-bottom: 1px solid var(--border);
    }
    .activity-item:last-child { border-bottom: none; }

    .activity-icon {
        width: 30px;
        height: 30px;
        border-radius: calc(var(--radius) - 2px);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .activity-icon.is-granted {
        background: color-mix(in oklch, var(--success) 16%, var(--card));
        color: var(--success);
    }
    .activity-icon.is-revoked {
        background: color-mix(in oklch, var(--danger) 16%, var(--card));
        color: var(--danger);
    }

    .activity-body { flex: 1; min-width: 0; }
    .activity-title { font-size: 0.875rem; font-weight: 600; color: var(--foreground); }
    .activity-desc { font-size: 0.8rem; color: var(--muted-foreground); margin-top: 0.15rem; }
    .activity-meta { font-family: var(--font-mono); font-size: 0.72rem; color: var(--muted-foreground); margin-top: 0.3rem; }

    /* --- Recent Activity table (index page card, distinct from the
       item-list styles above which the mapping-details modal still uses) --- */
    .activity-table-wrap { overflow-x: auto; }
    .activity-table { width: 100%; border-collapse: collapse; }
    .activity-table thead th {
        text-align: left;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--muted-foreground);
        padding: 0.6rem 1.5rem;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .activity-table tbody td {
        padding: 0.65rem 1.5rem;
        border-bottom: 1px solid var(--border);
        vertical-align: top;
    }
    .activity-table tbody tr:last-child td { border-bottom: none; }
    .activity-table tbody tr:hover { background: var(--accent); }
    .activity-table .activity-icon { flex-shrink: 0; }
    .activity-table .activity-title { font-weight: 500; line-height: 1.5; }

    .activity-col-by,
    .activity-col-when {
        white-space: nowrap;
        font-size: 0.82rem;
        color: var(--muted-foreground);
    }
    .activity-meta-mobile { display: none; }

    /* Below this width, drop the By/When columns in favour of the inline
       meta line under the message -- keeps the message itself full-width
       and readable instead of forcing a horizontal scroll for two narrow
       columns. */
    @media (max-width: 640px) {
        .activity-col-by,
        .activity-col-when {
            display: none;
        }
        .activity-meta-mobile { display: block; }
        .activity-table thead th,
        .activity-table tbody td {
            padding: 0.6rem 1rem;
        }
    }
</style>
@endsection
