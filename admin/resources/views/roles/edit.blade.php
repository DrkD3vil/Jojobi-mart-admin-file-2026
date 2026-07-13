@extends('layouts.app')

@section('title', 'Edit ' . $role->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[var(--bg-secondary)]/50 to-[var(--bg-primary)] p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
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
                    <a href="{{ route('roles.index') }}" class="hover:text-[var(--text-primary)] transition-colors duration-300">
                        Roles
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="{{ route('roles.show', $role) }}" class="hover:text-[var(--text-primary)] transition-colors duration-300">
                        {{ Str::limit($role->name, 20) }}
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-[var(--text-primary)] font-medium">Edit Role</span>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6 mb-8 shadow-[var(--card-shadow)]">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-[var(--warning)]/20 to-[var(--warning)]/10">
                            <svg class="w-7 h-7 text-[var(--warning)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <h1 class="text-3xl font-bold text-[var(--text-primary)]">Edit Role</h1>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-[var(--accent-color)]/10 text-[var(--accent-color)]">
                                    {{ $role->slug }}
                                </span>
                            </div>
                            <p class="text-[var(--text-secondary)] mt-2">Update role information and permissions</p>
                        </div>
                    </div>

                    <!-- Role Info Summary -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Created</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $role->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Last Updated</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $role->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Current Permissions</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ count($selectedPrivilegeIds) }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('roles.show', $role) }}"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300 group">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Details
                    </a>
                    <a href="{{ route('roles.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-[var(--bg-tertiary)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300">
                        All Roles
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('roles.update', $role) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information Card -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] hover:shadow-[var(--card-shadow-hover)] transition-all duration-500">
                <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                        <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Basic Information
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Name Field -->
                        <div class="space-y-3">
                            <label for="name" class="block">
                                <span class="text-sm font-medium text-[var(--text-primary)]">Role Name *</span>
                                <span class="text-xs text-[var(--text-secondary)] ml-1">Required</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                    <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $role->name) }}"
                                       class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                       placeholder="e.g., Administrator"
                                       required>
                            </div>
                            @error('name')
                                <p class="text-sm text-[var(--danger)] mt-1 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Slug Field -->
                        <div class="space-y-3">
                            <label for="slug" class="block">
                                <span class="text-sm font-medium text-[var(--text-primary)]">Slug</span>
                                <span class="text-xs text-[var(--text-secondary)] ml-1">Unique identifier</span>
                            </label>
                            <div class="relative">
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                    <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </div>
                                <input type="text"
                                       id="slug"
                                       name="slug"
                                       value="{{ old('slug', $role->slug) }}"
                                       class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                       placeholder="e.g., administrator">
                            </div>
                            <p class="text-xs text-[var(--text-secondary)]">Changing the slug may affect existing integrations</p>
                            @error('slug')
                                <p class="text-sm text-[var(--danger)] mt-1 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description Field -->
                    <div class="mt-6 space-y-3">
                        <label for="description" class="block">
                            <span class="text-sm font-medium text-[var(--text-primary)]">Description</span>
                            <span class="text-xs text-[var(--text-secondary)] ml-1">Optional</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="w-full px-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300 resize-none"
                                  placeholder="Describe the role's purpose and responsibilities...">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <p class="text-sm text-[var(--danger)] mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Permissions Card -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] hover:shadow-[var(--card-shadow-hover)] transition-all duration-500">
                <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                            <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Manage Permissions
                            <span class="px-2 py-1 text-xs font-medium bg-[var(--bg-tertiary)] rounded-full">
                                {{ count($selectedPrivilegeIds) }} selected
                            </span>
                        </h2>

                        <div class="flex items-center gap-3">
                            <button type="button"
                                    id="selectAll"
                                    class="text-sm px-4 py-2 rounded-lg border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300">
                                Select All
                            </button>
                            <button type="button"
                                    id="clearAll"
                                    class="text-sm px-4 py-2 rounded-lg border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-[var(--danger)] hover:border-[var(--danger)]/30 transition-all duration-300">
                                Clear All
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if($privileges->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($privileges as $privilege)
                                @php
                                    $isSelected = in_array($privilege->id, old('privileges', $selectedPrivilegeIds));
                                    $wasOriginallySelected = in_array($privilege->id, $selectedPrivilegeIds);
                                @endphp
                                <div class="relative">
                                    <input type="checkbox"
                                           id="privilege_{{ $privilege->id }}"
                                           name="privileges[]"
                                           value="{{ $privilege->id }}"
                                           {{ $isSelected ? 'checked' : '' }}
                                           class="hidden peer"
                                           data-original="{{ $wasOriginallySelected ? 'true' : 'false' }}">
                                    <label for="privilege_{{ $privilege->id }}"
                                           class="block p-4 border-2 rounded-xl cursor-pointer transition-all duration-300 hover:shadow-sm
                                                  {{ $isSelected
                                                     ? 'border-[var(--accent-color)] bg-[var(--accent-color)]/5 shadow-[0_0_0_3px_var(--accent-glow)]'
                                                     : 'border-[var(--border-color)] hover:border-[var(--accent-color)]' }}
                                                  {{ !$isSelected && $wasOriginallySelected ? 'border-[var(--warning)]/50 bg-[var(--warning)]/5' : '' }}">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-0.5 relative">
                                                <div class="w-5 h-5 rounded border flex items-center justify-center
                                                    {{ $isSelected
                                                       ? 'bg-[var(--accent-color)] border-[var(--accent-color)]'
                                                       : 'border-[var(--border-color)]' }}">
                                                    <svg class="w-3 h-3 text-white {{ $isSelected ? 'block' : 'hidden' }}"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                                @if($wasOriginallySelected && !$isSelected)
                                                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-[var(--warning)] rounded-full border border-[var(--bg-tertiary)]"></div>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-start justify-between">
                                                    <span class="font-medium text-[var(--text-primary)] block">{{ $privilege->name }}</span>
                                                    @if($wasOriginallySelected && !$isSelected)
                                                        <span class="text-xs px-2 py-0.5 bg-[var(--warning)]/20 text-[var(--warning)] rounded-full">Removed</span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-[var(--text-secondary)] block mt-1">{{ $privilege->slug }}</span>
                                                @if($privilege->description)
                                                    <p class="text-xs text-[var(--text-muted)] mt-2">{{ $privilege->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <!-- Permissions Summary -->
                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-gradient-to-r from-[var(--success)]/10 to-transparent rounded-xl border border-[var(--success)]/20">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-[var(--success)]/20">
                                        <svg class="w-5 h-5 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-[var(--text-primary)]">Selected</p>
                                        <p class="text-2xl font-bold text-[var(--text-primary)]">
                                            <span id="selectedCount">{{ count(array_intersect($selectedPrivilegeIds, old('privileges', $selectedPrivilegeIds))) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-[var(--info)]/10 to-transparent rounded-xl border border-[var(--info)]/20">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-[var(--info)]/20">
                                        <svg class="w-5 h-5 text-[var(--info)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-[var(--text-primary)]">Changed</p>
                                        <p class="text-2xl font-bold text-[var(--text-primary)]">
                                            <span id="changedCount">0</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--border-color)]">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-[var(--bg-tertiary)]">
                                        <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-[var(--text-primary)]">Total Available</p>
                                        <p class="text-2xl font-bold text-[var(--text-primary)]">{{ $privileges->count() }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="max-w-sm mx-auto">
                                <div class="p-4 rounded-full bg-[var(--bg-tertiary)] inline-flex mb-4">
                                    <svg class="w-10 h-10 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Permissions Available</h3>
                                <p class="text-[var(--text-secondary)] mb-4">You need to create permissions before assigning them to roles.</p>
                                <a href="{{ route('privileges.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--accent-color)]/10 text-[var(--accent-color)] hover:bg-[var(--accent-color)]/20 transition-colors duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Create Permission
                                </a>
                            </div>
                        </div>
                    @endif
                    @error('privileges')
                        <p class="text-sm text-[var(--danger)] mt-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">Save Changes</h3>
                        <p class="text-sm text-[var(--text-secondary)]">Review your changes before updating the role</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('roles.show', $role) }}"
                           class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] text-center transition-all duration-300 hover:shadow-sm">
                            Cancel
                        </a>
                        <button type="reset"
                                class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300 hover:shadow-sm">
                            Reset Changes
                        </button>
                        <button type="submit"
                                id="submitBtn"
                                class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Role
                        </button>
                    </div>
                </div>

                <!-- Changes Summary -->
                <div id="changesSummary" class="mt-6 pt-6 border-t border-[var(--border-color)]/30 hidden">
                    <h4 class="text-sm font-medium text-[var(--text-primary)] mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--info)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Summary of Changes
                    </h4>
                    <div class="space-y-3">
                        <div id="addedPermissions" class="text-sm text-[var(--success)] hidden">
                            <span class="font-medium">Added:</span>
                            <span id="addedCount">0</span> permissions
                        </div>
                        <div id="removedPermissions" class="text-sm text-[var(--danger)] hidden">
                            <span class="font-medium">Removed:</span>
                            <span id="removedCount">0</span> permissions
                        </div>
                        <div id="noChanges" class="text-sm text-[var(--text-secondary)]">
                            No changes detected
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    const selectAllBtn = document.getElementById('selectAll');
    const clearAllBtn = document.getElementById('clearAll');
    const selectedCountEl = document.getElementById('selectedCount');
    const changedCountEl = document.getElementById('changedCount');
    const privilegeCheckboxes = document.querySelectorAll('input[name="privileges[]"]');
    const submitBtn = document.getElementById('submitBtn');
    const changesSummary = document.getElementById('changesSummary');
    const addedPermissions = document.getElementById('addedPermissions');
    const removedPermissions = document.getElementById('removedPermissions');
    const noChanges = document.getElementById('noChanges');
    const addedCountEl = document.getElementById('addedCount');
    const removedCountEl = document.getElementById('removedCount');

    // Auto-generate slug from name if slug is empty
    nameInput.addEventListener('input', function() {
        if (!slugInput.dataset.manual) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/gi, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });

    // Mark slug as manually edited
    slugInput.addEventListener('input', function() {
        this.dataset.manual = 'true';
    });

    // Select All / Deselect All functionality
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            const allChecked = Array.from(privilegeCheckboxes).every(cb => cb.checked);
            privilegeCheckboxes.forEach(cb => {
                cb.checked = !allChecked;
                cb.dispatchEvent(new Event('change'));
            });
            this.textContent = allChecked ? 'Select All' : 'Deselect All';
            updateSummary();
        });
    }

    // Clear All functionality
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
            privilegeCheckboxes.forEach(cb => {
                cb.checked = false;
                cb.dispatchEvent(new Event('change'));
            });
            if (selectAllBtn) selectAllBtn.textContent = 'Select All';
            updateSummary();
        });
    }

    // Update counts and summary
    function updateCounts() {
        const selectedCount = Array.from(privilegeCheckboxes).filter(cb => cb.checked).length;
        if (selectedCountEl) {
            selectedCountEl.textContent = selectedCount;
        }

        // Update select all button text
        if (selectAllBtn) {
            const allChecked = selectedCount === privilegeCheckboxes.length;
            selectAllBtn.textContent = allChecked ? 'Deselect All' : 'Select All';
        }
    }

    // Update changed count
    function updateChangedCount() {
        let changedCount = 0;
        privilegeCheckboxes.forEach(cb => {
            const isChecked = cb.checked;
            const wasOriginal = cb.dataset.original === 'true';
            if (isChecked !== wasOriginal) {
                changedCount++;
            }
        });

        if (changedCountEl) {
            changedCountEl.textContent = changedCount;
        }

        // Show/hide changes summary
        if (changesSummary) {
            if (changedCount > 0) {
                changesSummary.classList.remove('hidden');
            } else {
                changesSummary.classList.add('hidden');
            }
        }

        return changedCount;
    }

    // Update summary details
    function updateSummary() {
        let added = 0;
        let removed = 0;

        privilegeCheckboxes.forEach(cb => {
            const isChecked = cb.checked;
            const wasOriginal = cb.dataset.original === 'true';

            if (isChecked && !wasOriginal) {
                added++;
            } else if (!isChecked && wasOriginal) {
                removed++;
            }
        });

        // Update summary display
        if (added > 0) {
            addedPermissions.classList.remove('hidden');
            addedCountEl.textContent = added;
        } else {
            addedPermissions.classList.add('hidden');
        }

        if (removed > 0) {
            removedPermissions.classList.remove('hidden');
            removedCountEl.textContent = removed;
        } else {
            removedPermissions.classList.add('hidden');
        }

        if (added === 0 && removed === 0) {
            noChanges.classList.remove('hidden');
        } else {
            noChanges.classList.add('hidden');
        }

        // Update submit button text if changes exist
        if (submitBtn && (added > 0 || removed > 0)) {
            submitBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Role (${added + removed} changes)
            `;
        }
    }

    // Add change event listeners to checkboxes
    privilegeCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateCounts();
            updateChangedCount();
            updateSummary();
        });
    });

    // Initialize counts
    updateCounts();
    updateChangedCount();
    updateSummary();

    // Form submission handling
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Add loading state to submit button
        if (submitBtn) {
            submitBtn.innerHTML = `
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Updating...
            `;
            submitBtn.disabled = true;
        }
    });

    // Real-time validation
    nameInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.style.borderColor = 'var(--danger)';
        } else {
            this.style.borderColor = '';
        }
    });

    // Keyboard shortcut for form submission
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            if (form && form.checkValidity()) {
                form.submit();
            }
        }
    });

    // Add tooltip for keyboard shortcut
    if (submitBtn) {
        submitBtn.title = 'Press Ctrl+Enter to save changes';
    }

    // Warn about unsaved changes
    let formChanged = false;
    const formInputs = form.querySelectorAll('input, textarea, select');

    formInputs.forEach(input => {
        input.addEventListener('input', () => {
            formChanged = true;
        });

        input.addEventListener('change', () => {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', (e) => {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
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

    /* Custom checkbox animation */
    input[type="checkbox"]:checked + label {
        animation: checkAnim 0.3s ease;
    }

    @keyframes checkAnim {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    /* Loading animation */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Change indicators */
    .removed-indicator {
        position: relative;
    }

    .removed-indicator::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 6px;
        height: 6px;
        background: var(--warning);
        border-radius: 50%;
        border: 2px solid var(--bg-tertiary);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
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
    }
</style>

@endsection
