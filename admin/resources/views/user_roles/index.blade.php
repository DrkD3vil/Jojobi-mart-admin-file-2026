@extends('layouts.app')

@section('title', 'Users and Roles Management')

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
                    <span class="text-[var(--text-primary)] font-medium">Users & Roles</span>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 8.65a2 2 0 01-2.83 0M19 18a2 2 0 01-2 2"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-[var(--text-primary)]">Users and Roles</h1>
                            <p class="text-[var(--text-secondary)] mt-2">Manage user accounts and their role assignments</p>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Total Users</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $users->count() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Active Today</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">-</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">With Roles</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $users->filter(fn($user) => $user->roles->count() > 0)->count() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">This Page</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $users->currentPage() }} of {{ $users->lastPage() }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('user.roles.assign_multiple') }}"
                       class="btn-primary inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Bulk Assign Roles
                    </a>
                    <a href=""
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        Add User
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="mt-6">
                <form method="GET" action="" class="space-y-4">
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
                                       placeholder="Search users by name or email...">
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit"
                                    class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg active:scale-95">
                                Search
                            </button>
                            <a href=""
                               class="px-6 py-3 border border-[var(--border-color)] rounded-xl font-medium text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300 hover:shadow active:scale-95">
                                Clear
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div class="flex flex-wrap gap-4 items-center">
                        <div class="flex items-center gap-2">
                            <label class="text-sm text-[var(--text-secondary)]">Role:</label>
                            <select name="role"
                                    class="bg-[var(--input)] border border-[var(--border-color)] rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300">
                                <option value="">All Users</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->slug }}" {{ request('role') == $role->slug ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

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

        <!-- Users Table -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] hover:shadow-[var(--card-shadow-hover)] transition-all duration-500">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/80 border-b border-[var(--border-color)]">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>User</span>
                                    <button type="button" class="sort-btn" data-sort="name">
                                        <svg class="w-4 h-4 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                        </svg>
                                    </button>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Contact</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Roles</span>
                                </div>
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-[var(--text-primary)]">
                                <div class="flex items-center gap-2">
                                    <span>Member Since</span>
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
                        @forelse($users as $user)
                            <tr class="group hover:bg-[var(--accent-color)]/5 transition-all duration-300 hover:shadow-sm">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[var(--accent-color)] to-[var(--accent-hover)] flex items-center justify-center text-white font-semibold">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            @if($user->roles->count() > 0)
                                                <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-[var(--success)] border-2 border-[var(--bg-tertiary)]"></div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-[var(--text-primary)] group-hover:text-[var(--accent-color)] transition-colors duration-300">{{ $user->name }}</p>
                                            <p class="text-xs text-[var(--text-secondary)] mt-1">ID: {{ $user->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <a href="mailto:{{ $user->email }}" class="text-sm text-[var(--text-primary)] hover:text-[var(--accent-color)] transition-colors duration-300">
                                            {{ $user->email }}
                                        </a>
                                        @if($user->phone)
                                            <span class="text-xs text-[var(--text-secondary)] mt-1">{{ $user->phone }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2 max-w-xs">
                                        @forelse($user->roles as $role)
                                            <a href="{{ route('roles.show', $role) }}"
                                               class="role-badge group/role">
                                                <span class="role-name">{{ $role->name }}</span>
                                                <span class="role-count">{{ $role->privileges->count() }}</span>
                                            </a>
                                        @empty
                                            <span class="text-sm text-[var(--text-secondary)] italic">No roles assigned</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-[var(--text-primary)]">{{ $user->created_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-[var(--text-secondary)]">{{ $user->created_at->diffForHumans() }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('user.roles.show', $user->id) }}"
                                           class="action-btn manage-btn group/action">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 8.65a2 2 0 01-2.83 0M19 18a2 2 0 01-2 2"/>
                                            </svg>
                                            <span class="hidden sm:inline">Manage Roles</span>
                                        </a>

                                        <a href="{{ route('me', $user) }}"
                                           class="action-btn view-btn group/action">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="hidden sm:inline">View</span>
                                        </a>

                                        <a href="{{ route('me', $user) }}"
                                           class="action-btn edit-btn group/action">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span class="hidden sm:inline">Edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="max-w-sm mx-auto">
                                        <div class="p-4 rounded-full bg-[var(--bg-tertiary)] inline-flex mb-4">
                                            <svg class="w-10 h-10 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No users found</h3>
                                        <p class="text-[var(--text-secondary)] mb-4">Try adjusting your search or filter criteria</p>
                                        <a href=""
                                           class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Add User
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-[var(--border-color)]/30 bg-gradient-to-r from-[var(--bg-tertiary)]/30 to-transparent">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-[var(--text-secondary)]">
                            Showing <span class="font-semibold text-[var(--text-primary)]">{{ $users->firstItem() }}</span>
                            to <span class="font-semibold text-[var(--text-primary)]">{{ $users->lastItem() }}</span>
                            of <span class="font-semibold text-[var(--text-primary)]">{{ $users->total() }}</span> results
                        </div>

                        <div class="flex items-center space-x-2">
                            {{ $users->appends(request()->query())->links('vendor.pagination.custom') }}
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
                    Role Statistics
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[var(--text-secondary)]">Users with roles</span>
                        <span class="font-medium text-[var(--text-primary)]">{{ $users->filter(fn($user) => $user->roles->count() > 0)->count() }}/{{ $users->total() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[var(--text-secondary)]">Most common role</span>
                        <span class="font-medium text-[var(--text-primary)]">-</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[var(--text-secondary)]">Avg roles per user</span>
                        <span class="font-medium text-[var(--text-primary)]">{{ number_format($users->avg(fn($user) => $user->roles->count()), 1) }}</span>
                    </div>
                </div>
            </div>

            <div class="glass-card p-5 rounded-xl border border-[var(--border-color)]/30">
                <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[var(--warning)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Quick Tips
                </h3>
                <ul class="space-y-2 text-sm text-[var(--text-secondary)]">
                    <li class="flex items-start gap-2">
                        <div class="mt-0.5">•</div>
                        <span>Regularly review user role assignments</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="mt-0.5">•</div>
                        <span>Use bulk assignment for efficiency</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <div class="mt-0.5">•</div>
                        <span>Follow principle of least privilege</span>
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
                    <a href="{{ route('user.roles.assign_multiple') }}"
                       class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg bg-[var(--accent-color)]/10 text-[var(--accent-color)] hover:bg-[var(--accent-color)]/20 transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Bulk Assign Roles
                    </a>
                    <a href="{{ route('roles.index') }}"
                       class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Manage All Roles
                    </a>
                    <a href=""
                       class="flex items-center gap-2 text-sm px-4 py-2 rounded-lg border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-colors duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export Users
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
class UsersRolesManager {
    constructor() {
        this.initEventListeners();
    }

    initEventListeners() {
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

        // Role badge hover effects
        document.querySelectorAll('.role-badge').forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 12px var(--accent-glow)';
            });

            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });

        // Action button hover effects
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-1px)';
            });

            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + N for new user
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                window.location.href = "";
            }

            // Ctrl/Cmd + B for bulk assign
            if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
                e.preventDefault();
                window.location.href = "{{ route('user.roles.assign_multiple') }}";
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

        // Filter role selection
        const roleFilter = document.querySelector('select[name="role"]');
        if (roleFilter) {
            roleFilter.addEventListener('change', function() {
                this.form.submit();
            });
        }

        // Quick statistics calculation
        this.calculateStatistics();
    }

    calculateStatistics() {
        // Calculate most common role
        const roleCounts = {};
        document.querySelectorAll('.role-name').forEach(role => {
            const roleName = role.textContent;
            roleCounts[roleName] = (roleCounts[roleName] || 0) + 1;
        });

        let mostCommonRole = null;
        let maxCount = 0;

        for (const [roleName, count] of Object.entries(roleCounts)) {
            if (count > maxCount) {
                maxCount = count;
                mostCommonRole = roleName;
            }
        }

        if (mostCommonRole) {
            const mostCommonElement = document.querySelector('.statistics .most-common');
            if (mostCommonElement) {
                mostCommonElement.textContent = mostCommonRole;
            }
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new UsersRolesManager();
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

    /* User avatar */
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: white;
        font-size: 1rem;
    }

    /* Role badge styling */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        background: var(--accent-color)/10;
        border: 1px solid var(--accent-color)/20;
        border-radius: 9999px;
        font-size: 0.75rem;
        color: var(--text-primary);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .role-badge:hover {
        background: var(--accent-color)/20;
        border-color: var(--accent-color);
    }

    .role-name {
        font-weight: 500;
    }

    .role-count {
        background: var(--accent-color);
        color: white;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        font-weight: 600;
    }

    /* Action buttons */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .action-btn.manage-btn {
        background: var(--accent-color)/10;
        color: var(--accent-color);
        border: 1px solid var(--accent-color)/20;
    }

    .action-btn.manage-btn:hover {
        background: var(--accent-color)/20;
        border-color: var(--accent-color);
    }

    .action-btn.view-btn {
        background: var(--info)/10;
        color: var(--info);
        border: 1px solid var(--info)/20;
    }

    .action-btn.view-btn:hover {
        background: var(--info)/20;
        border-color: var(--info);
    }

    .action-btn.edit-btn {
        background: var(--warning)/10;
        color: var(--warning);
        border: 1px solid var(--warning)/20;
    }

    .action-btn.edit-btn:hover {
        background: var(--warning)/20;
        border-color: var(--warning);
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

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .grid-cols-3 {
            grid-template-columns: 1fr;
        }

        .action-btn span {
            display: none;
        }

        .action-btn {
            padding: 8px;
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

        table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
</style>

@endsection
