@extends('layouts.app')

@section('title', 'Manage Privileges')

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
                    <span class="text-[var(--text-primary)] font-medium">Privileges</span>
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
                        <div>
                            <h1 class="text-3xl font-bold text-[var(--text-primary)]">Manage Privileges</h1>
                            <p class="text-[var(--text-secondary)] mt-2">Define and manage system permissions and access levels</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Total Privileges</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privileges->total() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Active</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privileges->count() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">This Page</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privileges->currentPage() }} of {{ $privileges->lastPage() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Per Page</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privileges->perPage() }}</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('privileges.create') }}"
                   class="btn-primary inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Privilege
                </a>
            </div>

            <!-- Search and Filter -->
            <div class="mt-6">
                <form method="GET" action="{{ route('privileges.index') }}" class="space-y-4">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--text-secondary)]"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text"
                                       name="q"
                                       value="{{ request('q') }}"
                                       class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                       placeholder="Search privileges by name or slug...">
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg active:scale-95">
                                Search
                            </button>
                            <a href="{{ route('privileges.index') }}"
                               class="px-6 py-3 border border-[var(--border-color)] rounded-xl font-medium text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300 hover:shadow active:scale-95">
                                Clear
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-[var(--text-secondary)]">Sort by:</label>
                            <select name="sort"
                                    class="bg-[var(--input)] border border-[var(--border-color)] rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Newest</option>
                                <option value="created_at_desc" {{ request('sort') == 'created_at_desc' ? 'selected' : '' }}>Oldest</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="text-sm text-[var(--text-secondary)]">Show:</label>
                            <select name="per_page"
                                    class="bg-[var(--input)] border border-[var(--border-color)] rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300">
                                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Privileges Table -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] hover:shadow-[var(--card-shadow-hover)] transition-all duration-500">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/80 border-b border-[var(--border-color)]">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Privilege</span>
                                    <button type="button" class="sort-btn" data-sort="name">
                                        <svg class="w-4 h-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </button>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Slug</span>
                                    <button type="button" class="sort-btn" data-sort="slug">
                                        <svg class="w-4 h-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </button>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">Description</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Created</span>
                                    <button type="button" class="sort-btn" data-sort="created_at">
                                        <svg class="w-4 h-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </button>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-color)]/30">
                        @forelse($privileges as $privilege)
                            <tr class="group hover:bg-[var(--accent-color)]/5 transition-all duration-300 hover:shadow-sm">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 rounded-lg bg-[var(--accent-color)]/10">
                                            <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-[var(--text-primary)] group-hover:text-[var(--accent-color)] transition-colors duration-300">{{ $privilege->name }}</p>
                                            <p class="text-xs text-[var(--text-secondary)] mt-1">ID: {{ $privilege->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[var(--bg-tertiary)] text-[var(--text-secondary)] font-mono">
                                        {{ $privilege->slug }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-[var(--text-secondary)] max-w-xs truncate">
                                        {{ $privilege->description ?? 'No description provided' }}
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-[var(--text-primary)]">{{ $privilege->created_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-[var(--text-secondary)]">{{ $privilege->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('privileges.show', $privilege->id) }}"
                                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--info)]/10 text-[var(--info)] hover:bg-[var(--info)]/20 transition-all duration-300 group/edit text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            View
                                        </a>


                                        <a href="{{ route('privileges.edit', $privilege->id) }}"
                                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--info)]/10 text-[var(--info)] hover:bg-[var(--info)]/20 transition-all duration-300 group/edit text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>

                                        <button type="button"
                                                onclick="showDeleteModal({{ $privilege->id }}, '{{ $privilege->name }}')"
                                                class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[var(--danger)]/10 text-[var(--danger)] hover:bg-[var(--danger)]/20 transition-all duration-300 group/delete text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <div class="p-4 rounded-full bg-[var(--bg-tertiary)] inline-flex mb-4">
                                            <svg class="w-10 h-10 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No privileges found</h3>
                                        <p class="text-[var(--text-secondary)] mb-4">Get started by creating your first privilege</p>
                                        <a href="{{ route('privileges.create') }}"
                                           class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add Privilege
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($privileges->hasPages())
                <div class="px-6 py-4 border-t border-[var(--border-color)]/30 bg-gradient-to-r from-[var(--bg-tertiary)]/30 to-transparent">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-[var(--text-secondary)]">
                            Showing <span class="font-semibold text-[var(--text-primary)]">{{ $privileges->firstItem() }}</span>
                            to <span class="font-semibold text-[var(--text-primary)]">{{ $privileges->lastItem() }}</span>
                            of <span class="font-semibold text-[var(--text-primary)]">{{ $privileges->total() }}</span> results
                        </div>

                        <div class="flex items-center space-x-2">
                            {{ $privileges->appends(request()->query())->links('vendor.pagination.custom') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[var(--info)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    About Privileges
                </h3>
                <div class="space-y-3">
                    <p class="text-sm text-[var(--text-secondary)]">
                        Privileges define specific actions or access levels that can be assigned to roles.
                    </p>
                    <ul class="space-y-2 text-sm text-[var(--text-secondary)]">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[var(--success)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Use clear, descriptive names for privileges
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[var(--success)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Slugs should be lowercase with hyphens
                        </li>
                    </ul>
                </div>
            </div>

            <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[var(--warning)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Best Practices
                </h3>
                <ul class="space-y-3 text-sm text-[var(--text-secondary)]">
                    <li class="flex items-start gap-2">
                        <div class="mt-0.5">•</div>
                        <span>Create granular privileges for better access control</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="mt-0.5">•</div>
                        <span>Add descriptions to explain privilege purposes</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="mt-0.5">•</div>
                        <span>Review privileges periodically for cleanup</span>
                    </li>
                </ul>
            </div>

            <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('privileges.create') }}"
                       class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg bg-[var(--accent-color)]/10 text-[var(--accent-color)] hover:bg-[var(--accent-color)]/20 transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Create New Privilege
                    </a>
                    <a href="{{ route('roles.index') }}"
                       class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Manage Roles
                    </a>
                    <a href="#"
                       class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export to CSV
                    </a>
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

                <div class="mb-6">
                    <p class="text-sm text-[var(--text-primary)] mb-4">
                        Are you sure you want to delete the privilege <span class="font-semibold" id="privilegeName"></span>?
                    </p>
                    <div class="p-4 bg-[var(--danger)]/5 border border-[var(--danger)]/20 rounded-xl">
                        <p class="text-xs text-[var(--text-secondary)]">
                            <span class="font-medium text-[var(--danger)]">Warning:</span> This may affect existing roles that use this privilege.
                        </p>
                    </div>
                </div>

                <form id="deleteForm" method="POST" class="space-y-4">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button"
                                onclick="hideDeleteModal()"
                                class="flex-1 px-4 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-colors duration-300">
                            Cancel
                        </button>
                        <button type="submit"
                                id="deleteSubmitBtn"
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
let currentPrivilegeId = null;

function showDeleteModal(id, name) {
    currentPrivilegeId = id;

    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('modalContent');
    const form = document.getElementById('deleteForm');
    const nameElement = document.getElementById('privilegeName');

    // Update modal content
    nameElement.textContent = `"${name}"`;
    form.action = `/privileges/${id}`;

    // Reset submit button
    const submitBtn = document.getElementById('deleteSubmitBtn');
    if (submitBtn) {
        submitBtn.innerHTML = 'Delete Privilege';
        submitBtn.disabled = false;
    }

    // Show modal with animation
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
    const submitBtn = document.getElementById('deleteSubmitBtn');
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

// Sort functionality
document.querySelectorAll('.sort-btn').forEach(button => {
    button.addEventListener('click', function() {
        const sortField = this.dataset.sort;
        const currentUrl = new URL(window.location.href);
        const currentSort = currentUrl.searchParams.get('sort');

        // Toggle sort direction
        let newSort = sortField;
        if (currentSort === sortField) {
            newSort = `${sortField}_desc`;
        } else if (currentSort === `${sortField}_desc`) {
            newSort = sortField;
        }

        currentUrl.searchParams.set('sort', newSort);
        window.location.href = currentUrl.toString();
    });
});

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N for new privilege
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = "{{ route('privileges.create') }}";
    }

    // Ctrl/Cmd + F for focus search
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
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

    .btn-primary {
        background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
        color: var(--primary-foreground);
        box-shadow: 0 4px 14px 0 var(--accent-glow);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--accent-hover), var(--accent-color));
        box-shadow: 0 6px 20px 0 var(--accent-glow);
    }

    /* Sort button */
    .sort-btn {
        opacity: 0.5;
        transition: opacity 0.2s ease;
    }

    .sort-btn:hover {
        opacity: 1;
    }

    /* Table row hover effects */
    tr {
        transition: all 0.3s ease;
    }

    tr:hover {
        background: var(--accent-color)/5 !important;
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

    /* Action buttons */
    .action-btn {
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-1px);
    }

    /* Modal Animation */
    #deleteModal {
        transition: opacity 0.3s ease;
    }

    #modalContent {
        transition: all 0.3s ease;
    }

    /* Loading animation */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 768px;
        }

        .grid-cols-3 {
            grid-template-columns: 1fr;
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

        .py-4 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }
</style>

@endsection
