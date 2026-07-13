@extends('layouts.app')

@section('title', $privilege->name . ' - Privilege Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[var(--bg-secondary)]/50 to-[var(--bg-primary)] p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-sm text-[var(--text-secondary)]">
                <li>
                    <a href="" class="hover:text-[var(--text-primary)] transition-colors duration-300">
                        Dashboard
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="{{ route('privileges.index') }}" class="hover:text-[var(--text-primary)] transition-colors duration-300">
                        Privileges
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-[var(--text-primary)] font-medium">Privilege Details</span>
                </li>
            </ol>
        </nav>

        <!-- Header Section -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6 mb-8 shadow-[var(--card-shadow)]">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-[var(--accent-color)]/20 to-[var(--accent-color)]/10">
                            <svg class="w-7 h-7 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-3xl font-bold text-[var(--text-primary)]">{{ $privilege->name }}</h1>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-[var(--accent-color)]/10 text-[var(--accent-color)] font-mono">
                                    {{ $privilege->slug }}
                                </span>
                            </div>
                            <p class="text-[var(--text-secondary)] mt-2">Privilege details and usage information</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Used in Roles</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privilege->roles->count() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Created</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privilege->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Last Updated</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privilege->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Status</p>
                            <p class="text-sm font-medium text-[var(--success)] flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Active
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('privileges.edit', $privilege) }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl"
                       style="background: linear-gradient(135deg, var(--warning), var(--warning)/0.8); color: white;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Privilege
                    </a>
                    <a href="{{ route('privileges.index') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300 group">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Privileges
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Privilege Details Card -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] lg:col-span-2">
                <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                        <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Privilege Information
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="info-item">
                                <label class="info-label">Privilege Name</label>
                                <p class="info-value">{{ $privilege->name }}</p>
                            </div>

                            <div class="info-item">
                                <label class="info-label">Slug</label>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[var(--accent-color)]/10 text-[var(--accent-color)] border border-[var(--accent-color)]/20 font-mono">
                                        {{ $privilege->slug }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="info-item">
                                <label class="info-label">Created</label>
                                <p class="info-value">{{ $privilege->created_at->format('F d, Y \a\t h:i A') }}</p>
                            </div>

                            <div class="info-item">
                                <label class="info-label">Last Updated</label>
                                <p class="info-value">{{ $privilege->updated_at->format('F d, Y \a\t h:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($privilege->description)
                        <div class="mt-6 pt-6 border-t border-[var(--border-color)]/30">
                            <label class="info-label">Description</label>
                            <p class="info-value mt-2 text-[var(--text-secondary)] leading-relaxed">{{ $privilege->description }}</p>
                        </div>
                    @else
                        <div class="mt-6 pt-6 border-t border-[var(--border-color)]/30">
                            <div class="flex items-center gap-2 text-[var(--text-secondary)]">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm">No description provided</span>
                            </div>
                        </div>
                    @endif

                    <!-- Code Snippet -->

                </div>
            </div>

            <!-- Actions Card -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)]">
                <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                        <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Actions
                    </h2>
                </div>

                <div class="p-6 space-y-4">
                    <a href="{{ route('privileges.edit', $privilege) }}"
                       class="action-btn edit-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Privilege
                    </a>

                    <button type="button"
                            onclick="showDeleteModal()"
                            class="action-btn delete-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Privilege
                    </button>

                    <a href="{{ route('privileges.create') }}"
                       class="action-btn create-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create New Privilege
                    </a>

                    <a href="{{ route('privileges.index') }}"
                       class="action-btn outline-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to All Privileges
                    </a>
                </div>

                <!-- Quick Stats -->
                <div class="px-6 py-4 border-t border-[var(--border-color)]/30 bg-gradient-to-r from-[var(--bg-tertiary)]/30 to-transparent">
                    <p class="text-sm text-[var(--text-secondary)] mb-2">Usage Statistics</p>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-[var(--text-secondary)]">Total Roles</span>
                            <span class="font-medium text-[var(--text-primary)]">{{ $privilege->roles->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-[var(--text-secondary)]">Created</span>
                            <span class="font-medium text-[var(--text-primary)]">{{ $privilege->created_at->diffForHumans(null, true) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-[var(--text-secondary)]">Last Updated</span>
                            <span class="font-medium text-[var(--text-primary)]">{{ $privilege->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Associated Roles Section -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] mt-6">
            <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                        <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Associated Roles
                        <span class="px-3 py-1 text-sm font-medium bg-[var(--accent-color)]/10 text-[var(--accent-color)] rounded-full">
                            {{ $privilege->roles->count() }} roles
                        </span>
                    </h2>

                    <a href="{{ route('roles.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--accent-color)]/10 text-[var(--accent-color)] hover:bg-[var(--accent-color)]/20 transition-colors duration-300 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Role
                    </a>
                </div>
            </div>

            <div class="p-6">
                @if($privilege->roles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($privilege->roles as $role)
                            <a href="{{ route('roles.show', $role) }}" class="role-card group">
                                <div class="role-header">
                                    <div class="role-icon">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div class="role-badge">{{ $role->slug }}</div>
                                </div>
                                <div class="role-name">{{ $role->name }}</div>
                                @if($role->description)
                                    <div class="role-description">{{ Str::limit($role->description, 100) }}</div>
                                @endif
                                <div class="role-footer">
                                    <span class="text-xs text-[var(--text-secondary)]">
                                        {{ $role->privileges->count() }} permissions
                                    </span>
                                    <svg class="w-4 h-4 text-[var(--accent-color)] transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <!-- Roles Summary -->
                    <div class="mt-8 p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--border-color)]">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <p class="text-sm font-medium text-[var(--text-primary)]">Roles Summary</p>
                                <p class="text-xs text-[var(--text-secondary)] mt-1">
                                    This privilege is used in {{ count($privilege->roles) }} role{{ count($privilege->roles) !== 1 ? 's' : '' }}
                                </p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-[var(--text-primary)]">{{ count($privilege->roles) }}</p>
                                    <p class="text-xs text-[var(--text-secondary)]">Total</p>
                                </div>
                                <div class="w-px h-8 bg-[var(--border-color)]"></div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-[var(--text-primary)]">
                                        {{ $privilege->roles->where('is_active', true)->count() }}
                                    </p>
                                    <p class="text-xs text-[var(--text-secondary)]">Active</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="max-w-md mx-auto">
                            <div class="p-4 rounded-full bg-[var(--bg-tertiary)] inline-flex mb-4">
                                <svg class="w-12 h-12 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-[var(--text-primary)] mb-3">No Roles Assigned</h3>
                            <p class="text-[var(--text-secondary)] mb-6">This privilege isn't assigned to any roles yet. Assign it to roles to grant specific permissions.</p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <a href="{{ route('roles.index') }}"
                                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl"
                                   style="background: linear-gradient(135deg, var(--accent-color), var(--accent-hover)); color: var(--primary-foreground);">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Manage Roles
                                </a>
                                <a href="{{ route('privileges.index') }}"
                                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300">
                                    Browse Privileges
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] mt-6">
            <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                    <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Recent Activity
                </h2>
            </div>

            <div class="p-6">
                <div class="space-y-4">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg class="w-5 h-5 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <p class="activity-title">Privilege Updated</p>
                            <p class="activity-description">Details were last modified {{ $privilege->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="activity-time">{{ $privilege->updated_at->format('h:i A') }}</div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <svg class="w-5 h-5 text-[var(--info)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <p class="activity-title">Privilege Created</p>
                            <p class="activity-description">Privilege was created {{ $privilege->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="activity-time">{{ $privilege->created_at->format('M d, Y') }}</div>
                    </div>

                    @if($privilege->roles->count() > 0)
                        <div class="activity-item">
                            <div class="activity-icon">
                                <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 8.65a2 2 0 01-2.83 0M19 18a2 2 0 01-2 2"/>
                                </svg>
                            </div>
                            <div class="activity-content">
                                <p class="activity-title">Role Assignments</p>
                                <p class="activity-description">Currently assigned to {{ $privilege->roles->count() }} role(s)</p>
                            </div>
                            <div class="activity-time">Active</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden transition-opacity duration-300">
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 max-w-md w-full transform transition-all duration-300 scale-95 opacity-0"
             id="modalContent">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 rounded-xl bg-[var(--danger)]/10">
                        <svg class="w-6 h-6 text-[var(--danger)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-[var(--text-primary)]">Delete Privilege</h3>
                        <p class="text-sm text-[var(--text-secondary)]">This action cannot be undone</p>
                    </div>
                </div>

                <div class="mb-6 p-4 bg-[var(--danger)]/5 border border-[var(--danger)]/20 rounded-xl">
                    <p class="text-sm text-[var(--text-primary)]">
                        Are you sure you want to delete the privilege <span class="font-semibold">"{{ $privilege->name }}"</span>?
                    </p>
                    <ul class="mt-2 space-y-1 text-sm text-[var(--text-secondary)]">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[var(--danger)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            This will affect {{ $privilege->roles->count() }} roles
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[var(--danger)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Users may lose access to certain features
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[var(--danger)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            This action is permanent and cannot be reversed
                        </li>
                    </ul>
                </div>

                <form action="{{ route('privileges.destroy', $privilege) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button"
                                onclick="hideDeleteModal()"
                                class="flex-1 px-4 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-colors duration-300">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-[var(--danger)] to-[var(--danger)]/80 text-white font-medium hover:shadow-lg transition-all duration-300">
                            Delete Privilege
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
function showDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('modalContent');

    modal.classList.remove('hidden');

    // Trigger reflow for animation
    modal.offsetHeight;

    modal.classList.add('opacity-100');
    content.classList.remove('scale-95', 'opacity-0');
    content.classList.add('scale-100', 'opacity-100');
}

function hideDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('modalContent');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.remove('opacity-100');
        modal.classList.add('hidden');
    }, 300);
}

function copyCodeSnippet() {
    const code = document.getElementById('codeSnippet').textContent;
    navigator.clipboard.writeText(code)
        .then(() => {
            const btn = document.querySelector('.copy-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Copied!
            `;
            btn.classList.remove('text-[var(--text-secondary)]');
            btn.classList.add('text-[var(--success)]');

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('text-[var(--success)]');
                btn.classList.add('text-[var(--text-secondary)]');
            }, 2000);
        })
        .catch(err => {
            console.error('Failed to copy: ', err);
        });
}

// Close modal on outside click
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideDeleteModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) {
        hideDeleteModal();
    }
});

// Add loading state to delete form
document.getElementById('deleteForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.innerHTML = `
            <svg class="animate-spin w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Deleting...
        `;
        submitBtn.disabled = true;
    }
});

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + E for edit
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        window.location.href = "{{ route('privileges.edit', $privilege) }}";
    }

    // Ctrl/Cmd + Backspace for delete (with confirmation)
    if ((e.ctrlKey || e.metaKey) && e.key === 'Backspace') {
        e.preventDefault();
        if (confirm('Delete this privilege?')) {
            showDeleteModal();
        }
    }
});
</script>

<style>
    .glass-card {
        background: var(--glass-base);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* Info Item Styles */
    .info-item {
        padding-bottom: 12px;
    }

    .info-label {
        display: block;
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .info-value {
        font-size: 0.95rem;
        color: var(--text-primary);
        font-weight: 500;
    }

    /* Action Button Styles */
    .action-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        text-align: left;
    }

    .action-btn.edit-btn {
        background: linear-gradient(to right, var(--warning)/10, transparent);
        color: var(--warning);
        border: 1px solid var(--warning)/20;
    }

    .action-btn.edit-btn:hover {
        background: linear-gradient(to right, var(--warning)/20, transparent);
        border-color: var(--warning)/40;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--warning)/10;
    }

    .action-btn.delete-btn {
        background: linear-gradient(to right, var(--danger)/10, transparent);
        color: var(--danger);
        border: 1px solid var(--danger)/20;
    }

    .action-btn.delete-btn:hover {
        background: linear-gradient(to right, var(--danger)/20, transparent);
        border-color: var(--danger)/40;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--danger)/10;
    }

    .action-btn.create-btn {
        background: linear-gradient(to right, var(--success)/10, transparent);
        color: var(--success);
        border: 1px solid var(--success)/20;
    }

    .action-btn.create-btn:hover {
        background: linear-gradient(to right, var(--success)/20, transparent);
        border-color: var(--success)/40;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px var(--success)/10;
    }

    .action-btn.outline-btn {
        background: var(--bg-tertiary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .action-btn.outline-btn:hover {
        background: var(--bg-secondary);
        border-color: var(--accent-color);
        transform: translateY(-2px);
    }

    /* Role Card Styles */
    .role-card {
        background: var(--bg-tertiary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 16px;
        transition: all 0.3s ease;
        display: block;
        color: inherit;
        text-decoration: none;
    }

    .role-card:hover {
        border-color: var(--accent-color);
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px var(--accent-glow);
    }

    .role-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

    .role-icon {
        width: 32px;
        height: 32px;
        background: var(--accent-color)/10;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-color);
    }

    .role-badge {
        font-size: 0.7rem;
        padding: 4px 8px;
        background: var(--bg-secondary);
        color: var(--text-secondary);
        border-radius: 9999px;
        font-weight: 500;
        font-family: monospace;
    }

    .role-name {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .role-description {
        font-size: 0.85rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 12px;
    }

    .role-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.75rem;
        color: var(--text-muted);
        border-top: 1px solid var(--border-color);
        padding-top: 8px;
    }

    /* Code Snippet */
    pre {
        background: var(--bg-tertiary);
        border-radius: 8px;
        padding: 16px;
        overflow-x: auto;
        margin: 0;
    }

    code {
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 0.85rem;
        line-height: 1.5;
    }

    /* Activity Item */
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        transition: background-color 0.2s ease;
    }

    .activity-item:hover {
        background: var(--bg-tertiary);
    }

    .activity-icon {
        width: 32px;
        height: 32px;
        background: var(--bg-tertiary);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .activity-description {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .activity-time {
        font-size: 0.75rem;
        color: var(--text-muted);
        flex-shrink: 0;
    }

    /* Loading Animation */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Modal Animation */
    #deleteModal {
        transition: opacity 0.3s ease;
    }

    #modalContent {
        transition: all 0.3s ease;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .role-card {
            padding: 12px;
        }
    }

    @media (max-width: 640px) {
        .glass-card {
            padding: 1rem;
        }

        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .action-btn {
            padding: 10px 12px;
            font-size: 0.85rem;
        }
    }
</style>

@endsection
