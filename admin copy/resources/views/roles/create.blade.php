@extends('layouts.app')

@section('title', 'Create Role')

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
                    <span class="text-[var(--text-primary)] font-medium">Create Role</span>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6 mb-8 shadow-[var(--card-shadow)]">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 rounded-xl bg-[var(--accent-color)]/10">
                            <svg class="w-7 h-7 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-[var(--text-primary)]">Create New Role</h1>
                            <p class="text-[var(--text-secondary)] mt-2">Define a new role with specific permissions and access levels</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('roles.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300 group">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Roles
                </a>
            </div>

            <!-- Progress Steps -->
            <div class="mt-8">
                <div class="flex items-center justify-between max-w-2xl">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-full bg-[var(--accent-color)] text-[var(--primary-foreground)] flex items-center justify-center font-semibold mb-2">
                            1
                        </div>
                        <span class="text-sm font-medium text-[var(--text-primary)]">Basic Info</span>
                    </div>
                    <div class="flex-1 h-1 bg-[var(--border-color)] mx-4"></div>
                    <div class="flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-full bg-[var(--bg-tertiary)] text-[var(--text-secondary)] flex items-center justify-center font-semibold mb-2">
                            2
                        </div>
                        <span class="text-sm text-[var(--text-secondary)]">Permissions</span>
                    </div>
                    <div class="flex-1 h-1 bg-[var(--border-color)] mx-4"></div>
                    <div class="flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-full bg-[var(--bg-tertiary)] text-[var(--text-secondary)] flex items-center justify-center font-semibold mb-2">
                            3
                        </div>
                        <span class="text-sm text-[var(--text-secondary)]">Review</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('roles.store') }}" method="POST" class="space-y-8">
            @csrf

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
                                       value="{{ old('name') }}"
                                       class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                       placeholder="e.g., Administrator"
                                       required>
                            </div>
                            <p class="text-xs text-[var(--text-secondary)]">Use a descriptive name that clearly identifies the role's purpose</p>
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
                                <span class="text-xs text-[var(--text-secondary)] ml-1">Auto-generated</span>
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
                                       value="{{ old('slug') }}"
                                       class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                       placeholder="Will be auto-generated">
                            </div>
                            <p class="text-xs text-[var(--text-secondary)]">Unique identifier for the role. Leave empty for auto-generation</p>
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
                                  placeholder="Describe the role's purpose and responsibilities...">{{ old('description') }}</textarea>
                        <p class="text-xs text-[var(--text-secondary)]">Provide a brief description of what this role entails</p>
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
                            Assign Permissions
                        </h2>

                        <div class="flex items-center gap-3">
                            <button type="button"
                                    id="selectAll"
                                    class="text-sm px-4 py-2 rounded-lg border border-[var(--border-color)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300">
                                Select All
                            </button>
                            <div class="relative group">
                                <button type="button"
                                        class="text-sm px-4 py-2 rounded-lg bg-[var(--bg-tertiary)] text-[var(--text-secondary)] hover:text-[var(--text-primary)] transition-colors duration-300">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Info
                                </button>
                                <div class="absolute right-0 top-full mt-2 w-64 p-3 bg-[var(--popover)] border border-[var(--border-color)] rounded-lg shadow-[var(--dropdown-shadow)] opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-10">
                                    <p class="text-xs text-[var(--text-secondary)]">Select permissions that this role should have. Be cautious when assigning administrative privileges.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if($privileges->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($privileges as $privilege)
                                <div class="relative">
                                    <input type="checkbox"
                                           id="privilege_{{ $privilege->id }}"
                                           name="privileges[]"
                                           value="{{ $privilege->id }}"
                                           {{ in_array($privilege->id, old('privileges', [])) ? 'checked' : '' }}
                                           class="hidden peer">
                                    <label for="privilege_{{ $privilege->id }}"
                                           class="block p-4 border-2 border-[var(--border-color)] rounded-xl cursor-pointer transition-all duration-300 hover:border-[var(--accent-color)] hover:shadow-sm peer-checked:border-[var(--accent-color)] peer-checked:bg-[var(--accent-color)]/5 peer-checked:shadow-[0_0_0_3px_var(--accent-glow)]">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-0.5">
                                                <div class="w-5 h-5 rounded border border-[var(--border-color)] flex items-center justify-center peer-checked:bg-[var(--accent-color)] peer-checked:border-[var(--accent-color)]">
                                                    <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1">
                                                <span class="font-medium text-[var(--text-primary)] block">{{ $privilege->name }}</span>
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

                        <div class="mt-6 p-4 bg-[var(--bg-tertiary)] rounded-xl">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <svg class="w-5 h-5 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm text-[var(--text-primary)]">
                                        <span id="selectedCount">0</span> of {{ $privileges->count() }} permissions selected
                                    </span>
                                </div>
                                <button type="button"
                                        id="clearAll"
                                        class="text-sm px-3 py-1 rounded-lg text-[var(--text-secondary)] hover:text-[var(--danger)] transition-colors duration-300">
                                    Clear All
                                </button>
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
                                <a href="{{ route('roles.create') }}"
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
                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">Ready to Create?</h3>
                        <p class="text-sm text-[var(--text-secondary)]">Review your settings before creating the new role</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('roles.index') }}"
                           class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] text-center transition-all duration-300 hover:shadow-sm">
                            Cancel
                        </a>
                        <button type="reset"
                                class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300 hover:shadow-sm">
                            Reset Form
                        </button>
                        <button type="submit"
                                class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Role
                        </button>
                    </div>
                </div>

                <!-- Quick Tips -->
                <div class="mt-6 pt-6 border-t border-[var(--border-color)]/30">
                    <h4 class="text-sm font-medium text-[var(--text-primary)] mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--warning)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Important Notes
                    </h4>
                    <ul class="space-y-2 text-sm text-[var(--text-secondary)]">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-[var(--accent-color)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Role names should be unique and descriptive</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-[var(--accent-color)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Assign only necessary permissions following the principle of least privilege</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 text-[var(--accent-color)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>You can always edit the role and its permissions later</span>
                        </li>
                    </ul>
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
    const privilegeCheckboxes = document.querySelectorAll('input[name="privileges[]"]');
    const submitBtn = document.querySelector('button[type="submit"]');

    // Auto-generate slug from name
    nameInput.addEventListener('input', function() {
        if (!slugInput.value.trim()) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/gi, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });

    // Allow manual slug editing without auto-override
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
        });
    }

    // Update selected count
    function updateSelectedCount() {
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

    // Add change event listeners to checkboxes
    privilegeCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });

    // Initialize count
    updateSelectedCount();

    // Form validation and feedback
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        // Add loading state to submit button
        if (submitBtn) {
            submitBtn.innerHTML = `
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Creating...
            `;
            submitBtn.disabled = true;
        }
    });

    // Real-time validation for name field
    nameInput.addEventListener('blur', function() {
        if (!this.value.trim()) {
            this.style.borderColor = 'var(--danger)';
        } else {
            this.style.borderColor = '';
        }
    });

    // Add keyboard shortcut for form submission
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            if (form && form.checkValidity()) {
                form.submit();
            }
        }
    });

    // Add tooltip for keyboard shortcut
    if (submitBtn) {
        submitBtn.title = 'Press Ctrl+Enter to submit';
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

    /* Smooth focus styles */
    input:focus, textarea:focus, select:focus {
        transform: translateY(-1px);
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

    /* Loading animation for submit button */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
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
