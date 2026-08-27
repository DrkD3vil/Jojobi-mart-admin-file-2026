@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[var(--bg-secondary)]/50 to-[var(--bg-primary)] p-4 md:p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-[var(--text-primary)]">Manage Roles</h1>
                    <p class="text-[var(--text-secondary)] mt-2">Create, edit, and manage user permissions and access levels</p>
                </div>

                <a href="{{ route('roles.create') }}"
                   class="btn-primary inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Role
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[var(--text-secondary)] text-sm font-medium">Total Roles</p>
                            <p class="text-2xl font-bold text-[var(--text-primary)] mt-1">{{ $roles->total() }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-[var(--accent-color)]/10">
                            <svg class="w-6 h-6 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[var(--text-secondary)] text-sm font-medium">Active Roles</p>
                            <p class="text-2xl font-bold text-[var(--text-primary)] mt-1">{{ $roles->count() }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-[var(--success)]/10">
                            <svg class="w-6 h-6 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[var(--text-secondary)] text-sm font-medium">This Page</p>
                            <p class="text-2xl font-bold text-[var(--text-primary)] mt-1">{{ $roles->currentPage() }} of {{ $roles->lastPage() }}</p>
                        </div>
                        <div class="p-3 rounded-lg bg-[var(--info)]/10">
                            <svg class="w-6 h-6 text-[var(--info)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30 mb-6">
                <form method="GET" action="{{ route('roles.index') }}" class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--text-secondary)]"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text"
                                       name="q"
                                       value="{{ $q ?? '' }}"
                                       class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                       placeholder="Search roles by name or slug...">
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg active:scale-95">
                                Search
                            </button>
                            <a href="{{ route('roles.index') }}"
                               class="px-6 py-3 border border-[var(--border-color)] rounded-xl font-medium text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300 hover:shadow active:scale-95">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Roles Table -->
        <div class="glass-card rounded-xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] hover:shadow-[var(--card-shadow-hover)] transition-all duration-500">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/80 border-b border-[var(--border-color)]">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Role Name</span>
                                    <svg class="w-4 h-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Slug</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Actions</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]/30">
                        @forelse($roles as $role)
                            <tr class="group hover:bg-[var(--accent-color)]/5 transition-all duration-300 hover:shadow-sm">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg bg-[var(--accent-color)]/10">
                                            <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804L13 20.48V9a1 1 0 00-1-1H4.5A2.5 2.5 0 012 5.5v-1A2.5 2.5 0 014.5 2H8a1 1 0 011 1v10a1 1 0 001 1h1a1 1 0 011 1v1a1 1 0 01-1 1H6.5a2.5 2.5 0 01-2.5-2.5v-1A2.5 2.5 0 016.5 9H10a1 1 0 001-1V6.5A2.5 2.5 0 0113.5 4H18a1 1 0 011 1v10a1 1 0 01-1 1h-1a1 1 0 00-1 1v1a1 1 0 001 1h1a2.5 2.5 0 002.5-2.5v-1A2.5 2.5 0 0119 15h-3a1 1 0 01-1-1v-1a1 1 0 011-1h3a2.5 2.5 0 002.5-2.5v-1A2.5 2.5 0 0119 7h-3a1 1 0 01-1-1V5.5A2.5 2.5 0 0114.5 3H11a1 1 0 00-1 1v10a1 1 0 01-1 1H8a1 1 0 00-1 1v1a1 1 0 001 1h2a1 1 0 001-1v-1a1 1 0 00-1-1H9a1 1 0 01-1-1V8a1 1 0 00-1-1H5.5A2.5 2.5 0 013 5.5v-1A2.5 2.5 0 015.5 2H9a1 1 0 011 1v10a1 1 0 001 1h1a1 1 0 011 1v1a1 1 0 01-1 1H8a1 1 0 00-1 1v1a1 1 0 001 1h2a2.5 2.5 0 002.5-2.5v-1A2.5 2.5 0 0110 14H7a1 1 0 01-1-1V9a1 1 0 00-1-1H3a1 1 0 00-1 1v6a1 1 0 001 1h1a1 1 0 011 1v1a1 1 0 001 1h1a2.5 2.5 0 002.5-2.5v-1A2.5 2.5 0 016 15H3a1 1 0 01-1-1V9a1 1 0 011-1h2a1 1 0 001-1V6a1 1 0 00-1-1H3a1 1 0 01-1-1V3a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 001-1V6a1 1 0 00-1-1H8a1 1 0 01-1-1V3a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011-1V6a1 1 0 00-1-1h-1a1 1 0 01-1-1V3a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011-1V6a1 1 0 00-1-1h-1a1 1 0 01-1-1V3a1 1 0 011-1h2a2.5 2.5 0 012.5 2.5v1A2.5 2.5 0 0119 8h2a1 1 0 011 1v6a1 1 0 01-1 1h-1a1 1 0 00-1 1v1a1 1 0 001 1h1a2.5 2.5 0 002.5-2.5v-1A2.5 2.5 0 0121 15h-2a1 1 0 01-1-1V9a1 1 0 00-1-1h-1a1 1 0 01-1-1V6a1 1 0 011-1h1a1 1 0 001-1V3a1 1 0 00-1-1h-2a1 1 0 01-1-1V0a1 1 0 00-1-1H13a1 1 0 00-1 1v3a1 1 0 01-1 1H9a1 1 0 00-1 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 00-1-1H1a1 1 0 00-1 1v12a1 1 0 001 1h2a1 1 0 011 1v1a1 1 0 001 1h2a2.5 2.5 0 002.5-2.5v-1A2.5 2.5 0 018 16H5a1 1 0 01-1-1V9a1 1 0 011-1h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 01-1-1V3a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011-1V6a1 1 0 00-1-1h-1a1 1 0 01-1-1V3a1 1 0 011-1h2a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011 1v1a1 1 0 001 1h1a1 1 0 011-1V6a1 1 0 00-1-1h-1a1 1 0 01-1-1V3a1 1 0 011-1h2a2.5 2.5 0 012.5 2.5v1A2.5 2.5 0 0119 8h2a1 1 0 011 1v6a1 1 0 01-1 1h-1a1 1 0 00-1 1v1a1 1 0 001 1h1a2.5 2.5 0 002.5-2.5v-1A2.5 2.5 0 0121 15h-2a1 1 0 01-1-1V9a1 1 0 00-1-1h-1a1 1 0 01-1-1V6a1 1 0 011-1h1a1 1 0 001-1V3a1 1 0 00-1-1h-2a1 1 0 01-1-1V0a1 1 0 00-1-1H13a1 1 0 00-1 1v3a1 1 0 01-1 1H9a1 1 0 00-1 1v1a1 1 0 01-1 1H5a1 1 0 01-1-1V5a1 1 0 00-1-1H1z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-[var(--text-primary)] group-hover:text-[var(--accent-color)] transition-colors duration-300">{{ $role->name }}</p>
                                            <p class="text-sm text-[var(--text-secondary)] mt-1">Created {{ $role->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[var(--bg-tertiary)] text-[var(--text-secondary)]">
                                        {{ $role->slug }}
                                    </span>
                                </td>


                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('roles.show', $role) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--info)]/10 text-[var(--info)] hover:bg-[var(--info)]/20 transition-all duration-300 group/edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Show
                                        </a>

                                        <a href="{{ route('roles.edit', $role) }}"
                                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--info)]/10 text-[var(--info)] hover:bg-[var(--info)]/20 transition-all duration-300 group/edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        <form action="{{ route('roles.destroy', $role) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--danger)]/10 text-[var(--danger)] hover:bg-[var(--danger)]/20 transition-all duration-300 group/delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <div class="p-4 rounded-full bg-[var(--bg-tertiary)] inline-flex mb-4">
                                            <svg class="w-8 h-8 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No roles found</h3>
                                        <p class="text-[var(--text-secondary)] mb-4">Get started by creating your first role</p>
                                        <a href="{{ route('roles.create') }}"
                                           class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Create Role
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($roles->hasPages())
                <div class="px-6 py-4 border-t border-[var(--border-color)]/30 bg-gradient-to-r from-[var(--bg-tertiary)]/30 to-transparent">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-[var(--text-secondary)]">
                            Showing <span class="font-semibold text-[var(--text-primary)]">{{ $roles->firstItem() }}</span>
                            to <span class="font-semibold text-[var(--text-primary)]">{{ $roles->lastItem() }}</span>
                            of <span class="font-semibold text-[var(--text-primary)]">{{ $roles->total() }}</span> results
                        </div>

                        <div class="flex items-center space-x-2">
                            {{ $roles->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4">Quick Tips</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[var(--success)] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-[var(--text-secondary)]">Use descriptive names for better role identification</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-[var(--warning)] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span class="text-sm text-[var(--text-secondary)]">Be cautious when deleting roles as it may affect user permissions</span>
                    </li>
                </ul>
            </div>

            <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4">Need Help?</h3>
                <p class="text-sm text-[var(--text-secondary)] mb-4">Learn more about role management and best practices</p>
                <div class="flex gap-3">
                    <a href="#" class="text-sm px-4 py-2 rounded-lg bg-[var(--bg-tertiary)] text-[var(--text-primary)] hover:bg-[var(--accent-color)]/10 transition-colors duration-300">
                        View Documentation
                    </a>
                    <a href="#" class="text-sm px-4 py-2 rounded-lg border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors duration-300">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Pagination View -->
@if(!View::exists('vendor.pagination.custom'))
    @php
        $customPaginationExists = false;
    @endphp
@endif

<style>
    .glass-card {
        background: var(--glass-base);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
        color: var(--primary-foreground);
        box-shadow: 0 4px 14px 0 var(--accent-glow);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--accent-hover), var(--accent-color));
        box-shadow: 0 6px 20px 0 var(--accent-glow);
    }

    /* Smooth scrollbar */
    .overflow-x-auto {
        scrollbar-width: thin;
        scrollbar-color: var(--border-color) transparent;
    }

    .overflow-x-auto::-webkit-scrollbar {
        height: 6px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: var(--bg-tertiary);
        border-radius: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }

    /* Table row animation */
    tr {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
        .glass-card {
            padding: 1rem;
        }

        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }
</style>

@endsection
