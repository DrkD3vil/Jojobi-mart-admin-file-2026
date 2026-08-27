@extends('layouts.app')

@section('title', 'Edit ' . $privilege->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[var(--bg-secondary)]/50 to-[var(--bg-primary)] p-4 md:p-6">
    <div class="max-w-4xl mx-auto">
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
                    <a href="{{ route('privileges.show', $privilege) }}" class="hover:text-[var(--text-primary)] transition-colors duration-300">
                        {{ Str::limit($privilege->name, 20) }}
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-[var(--text-primary)] font-medium">Edit</span>
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
                                <h1 class="text-3xl font-bold text-[var(--text-primary)]">Edit Privilege</h1>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-[var(--accent-color)]/10 text-[var(--accent-color)] font-mono">
                                    {{ $privilege->slug }}
                                </span>
                            </div>
                            <p class="text-[var(--text-secondary)] mt-2">Update privilege information and settings</p>
                        </div>
                    </div>

                    <!-- Privilege Info -->
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Created</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privilege->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Last Updated</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privilege->updated_at->diffForHumans() }}</p>
                        </div>
                        <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl">
                            <p class="text-xs text-[var(--text-secondary)]">Used in Roles</p>
                            <p class="text-sm font-medium text-[var(--text-primary)]">{{ $privilege->roles->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('privileges.show', $privilege) }}"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300 group">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        View Details
                    </a>
                    <a href="{{ route('privileges.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-[var(--bg-tertiary)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300">
                        All Privileges
                    </a>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('privileges.update', $privilege) }}" method="POST" id="privilegeForm" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information Card -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] hover:shadow-[var(--card-shadow-hover)] transition-all duration-500">
                <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                        <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Privilege Information
                    </h2>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Name Field -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label for="name" class="block">
                                <span class="text-sm font-medium text-[var(--text-primary)]">Privilege Name *</span>
                                <span class="text-xs text-[var(--text-secondary)] ml-1">Required</span>
                            </label>
                            <span class="text-xs text-[var(--text-secondary)]">{{ strlen(old('name', $privilege->name)) }}/50</span>
                        </div>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $privilege->name) }}"
                                   class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                   placeholder="e.g., Create Users"
                                   maxlength="50"
                                   required>
                        </div>
                        <p class="text-xs text-[var(--text-secondary)]">Use a clear, descriptive name that explains the permission</p>
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
                        <div class="flex items-center justify-between">
                            <label for="slug" class="block">
                                <span class="text-sm font-medium text-[var(--text-primary)]">Slug</span>
                                <span class="text-xs text-[var(--text-secondary)] ml-1">Auto-generated</span>
                            </label>
                            <span class="text-xs text-[var(--text-secondary)]">{{ strlen(old('slug', $privilege->slug)) }}/30</span>
                        </div>
                        <div class="relative">
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                                <svg class="w-5 h-5 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                            </div>
                            <input type="text"
                                   id="slug"
                                   name="slug"
                                   value="{{ old('slug', $privilege->slug) }}"
                                   class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300 font-mono"
                                   placeholder="Will be auto-generated"
                                   maxlength="30">
                        </div>
                        <p class="text-xs text-[var(--text-secondary)]">Unique identifier used in code. Changing may affect existing integrations.</p>
                        @error('slug')
                            <p class="text-sm text-[var(--danger)] mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description Field -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label for="description" class="block">
                                <span class="text-sm font-medium text-[var(--text-primary)]">Description</span>
                                <span class="text-xs text-[var(--text-secondary)] ml-1">Optional</span>
                            </label>
                            <span class="text-xs text-[var(--text-secondary)]">{{ strlen(old('description', $privilege->description ?? '')) }}/200</span>
                        </div>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full px-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300 resize-none"
                                  placeholder="Describe what this privilege allows... (e.g., Allows users to create new user accounts in the system)"
                                  maxlength="200">{{ old('description', $privilege->description) }}</textarea>
                        <p class="text-xs text-[var(--text-secondary)]">Provide a clear description of what this privilege allows</p>
                        @error('description')
                            <p class="text-sm text-[var(--danger)] mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Preview -->
                    <div class="mt-6 pt-6 border-t border-[var(--border-color)]/30">
                        <label class="text-sm font-medium text-[var(--text-primary)] mb-3 block">Preview</label>
                        <div class="p-4 bg-[var(--bg-tertiary)] rounded-xl border border-[var(--border-color)]">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 rounded-lg bg-[var(--accent-color)]/10">
                                        <svg class="w-4 h-4 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <span class="font-medium text-[var(--text-primary)]" id="namePreview">{{ old('name', $privilege->name) }}</span>
                                </div>
                                <span class="px-2 py-1 text-xs font-mono bg-[var(--bg-secondary)] text-[var(--text-secondary)] rounded" id="slugPreview">{{ old('slug', $privilege->slug) }}</span>
                            </div>
                            <p class="text-sm text-[var(--text-secondary)]" id="descriptionPreview">
                                {{ old('description', $privilege->description) ?: 'No description provided' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Impact Analysis -->
            @if($privilege->roles->count() > 0)
                <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)]">
                    <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                        <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                            <svg class="w-5 h-5 text-[var(--warning)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            Impact Analysis
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 rounded-lg bg-[var(--warning)]/10">
                                <svg class="w-5 h-5 text-[var(--warning)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-[var(--text-primary)]">This privilege is used in {{ $privilege->roles->count() }} role(s)</p>
                                <p class="text-xs text-[var(--text-secondary)]">Changes may affect user permissions</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-sm font-medium text-[var(--text-primary)]">Used in these roles:</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($privilege->roles as $role)
                                    <div class="p-3 bg-[var(--bg-tertiary)] rounded-xl border border-[var(--border-color)]">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                                </svg>
                                                <span class="text-sm font-medium text-[var(--text-primary)]">{{ $role->name }}</span>
                                            </div>
                                            <span class="text-xs px-2 py-1 bg-[var(--accent-color)]/10 text-[var(--accent-color)] rounded-full">{{ $role->slug }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Form Actions -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">Save Changes</h3>
                        <p class="text-sm text-[var(--text-secondary)]">Review your changes before updating</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('privileges.show', $privilege) }}"
                           class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] text-center transition-all duration-300 hover:shadow-sm">
                            Cancel
                        </a>
                        <button type="reset"
                                id="resetBtn"
                                class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300 hover:shadow-sm">
                            Reset
                        </button>
                        <button type="submit"
                                id="submitBtn"
                                class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Privilege
                        </button>
                    </div>
                </div>

                <!-- Change Summary -->
                <div id="changeSummary" class="mt-6 pt-6 border-t border-[var(--border-color)]/30 hidden">
                    <h4 class="text-sm font-medium text-[var(--text-primary)] mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--info)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Changes Detected
                    </h4>
                    <div class="space-y-2 text-sm" id="changesList">
                        <!-- Changes will be listed here -->
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
class PrivilegeEditForm {
    constructor() {
        this.form = document.getElementById('privilegeForm');
        this.nameInput = document.getElementById('name');
        this.slugInput = document.getElementById('slug');
        this.descriptionInput = document.getElementById('description');
        this.resetBtn = document.getElementById('resetBtn');
        this.submitBtn = document.getElementById('submitBtn');
        this.changeSummary = document.getElementById('changeSummary');
        this.changesList = document.getElementById('changesList');

        // Preview elements
        this.namePreview = document.getElementById('namePreview');
        this.slugPreview = document.getElementById('slugPreview');
        this.descriptionPreview = document.getElementById('descriptionPreview');

        // Original values
        this.originalValues = {
            name: "{{ $privilege->name }}",
            slug: "{{ $privilege->slug }}",
            description: "{{ $privilege->description ?? '' }}"
        };

        this.isModified = false;

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updatePreview();
        this.checkForChanges();

        // Set slug as manually edited if it already has a value
        if (this.slugInput.value) {
            this.slugInput.dataset.manual = 'true';
        }
    }

    setupEventListeners() {
        // Auto-generate slug from name
        this.nameInput.addEventListener('input', () => {
            this.updatePreview();
            if (!this.slugInput.dataset.manual) {
                this.generateSlug();
            }
            this.checkForChanges();
        });

        // Manual slug editing
        this.slugInput.addEventListener('input', () => {
            this.slugInput.dataset.manual = 'true';
            this.updatePreview();
            this.checkForChanges();
        });

        // Description updates
        this.descriptionInput.addEventListener('input', () => {
            this.updatePreview();
            this.checkForChanges();
        });

        // Character counters
        [this.nameInput, this.slugInput, this.descriptionInput].forEach(input => {
            input.addEventListener('input', () => this.updateCharacterCount(input));
        });

        // Reset button
        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.resetForm();
            });
        }

        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Prevent leaving with unsaved changes
        window.addEventListener('beforeunload', (e) => {
            if (this.isModified) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && this.form.checkValidity()) {
                this.form.submit();
            }
            if (e.key === 'Escape') {
                window.location.href = "{{ route('privileges.show', $privilege) }}";
            }
        });
    }

    generateSlug() {
        const name = this.nameInput.value;
        const slug = name
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim()
            .substring(0, 30);
        this.slugInput.value = slug;
    }

    updatePreview() {
        if (this.namePreview) {
            this.namePreview.textContent = this.nameInput.value || 'Privilege Name';
        }

        if (this.slugPreview) {
            this.slugPreview.textContent = this.slugInput.value || 'privilege-slug';
        }

        if (this.descriptionPreview) {
            this.descriptionPreview.textContent = this.descriptionInput.value || 'No description provided';
        }
    }

    updateCharacterCount(input) {
        const counter = input.parentElement?.querySelector('.text-xs.text-\\[var\\(--text-secondary\\)\\]');
        if (counter) {
            const maxLength = input.getAttribute('maxlength');
            const currentLength = input.value.length;
            counter.textContent = `${currentLength}/${maxLength}`;

            // Change color if approaching limit
            if (currentLength > maxLength * 0.9) {
                counter.classList.add('text-[var(--warning)]');
                counter.classList.remove('text-[var(--text-secondary)]');
            } else {
                counter.classList.remove('text-[var(--warning)]');
                counter.classList.add('text-[var(--text-secondary)]');
            }
        }
    }

    checkForChanges() {
        const currentValues = {
            name: this.nameInput.value,
            slug: this.slugInput.value,
            description: this.descriptionInput.value
        };

        const changes = [];

        if (currentValues.name !== this.originalValues.name) {
            changes.push({
                field: 'Name',
                from: this.originalValues.name,
                to: currentValues.name
            });
        }

        if (currentValues.slug !== this.originalValues.slug) {
            changes.push({
                field: 'Slug',
                from: this.originalValues.slug,
                to: currentValues.slug
            });
        }

        if (currentValues.description !== this.originalValues.description) {
            changes.push({
                field: 'Description',
                from: this.originalValues.description || '(empty)',
                to: currentValues.description || '(empty)'
            });
        }

        this.isModified = changes.length > 0;
        this.updateChangeSummary(changes);
        this.updateSubmitButton(changes.length);
    }

    updateChangeSummary(changes) {
        if (!this.changeSummary || !this.changesList) return;

        if (changes.length > 0) {
            this.changeSummary.classList.remove('hidden');

            let html = '';
            changes.forEach(change => {
                html += `
                    <div class="p-3 bg-[var(--bg-tertiary)] rounded-lg mb-2">
                        <div class="font-medium text-[var(--text-primary)] mb-1">${change.field}</div>
                        <div class="flex items-start gap-2 text-xs">
                            <div class="flex-1">
                                <div class="text-[var(--text-secondary)] mb-1">From:</div>
                                <div class="text-[var(--text-primary)] font-mono bg-[var(--bg-secondary)] p-2 rounded">${change.from}</div>
                            </div>
                            <svg class="w-4 h-4 text-[var(--accent-color)] mt-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <div class="flex-1">
                                <div class="text-[var(--text-secondary)] mb-1">To:</div>
                                <div class="text-[var(--text-primary)] font-mono bg-[var(--success)]/10 p-2 rounded">${change.to}</div>
                            </div>
                        </div>
                    </div>
                `;
            });

            this.changesList.innerHTML = html;
        } else {
            this.changeSummary.classList.add('hidden');
            this.changesList.innerHTML = '';
        }
    }

    updateSubmitButton(changeCount) {
        if (!this.submitBtn) return;

        if (changeCount > 0) {
            this.submitBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Privilege (${changeCount} change${changeCount !== 1 ? 's' : ''})
            `;
        } else {
            this.submitBtn.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Privilege
            `;
        }
    }

    resetForm() {
        if (!confirm('Are you sure you want to reset all changes?')) return;

        this.nameInput.value = this.originalValues.name;
        this.slugInput.value = this.originalValues.slug;
        this.descriptionInput.value = this.originalValues.description;

        // Reset slug manual flag if it matches original
        if (this.slugInput.value === this.originalValues.slug) {
            delete this.slugInput.dataset.manual;
        }

        this.updatePreview();
        this.checkForChanges();
        this.updateCharacterCount(this.nameInput);
        this.updateCharacterCount(this.slugInput);
        this.updateCharacterCount(this.descriptionInput);
    }

    handleSubmit(e) {
        // Validate slug format if changed
        if (this.slugInput.value !== this.originalValues.slug) {
            const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
            if (!slugRegex.test(this.slugInput.value)) {
                e.preventDefault();
                alert('Slug must contain only lowercase letters, numbers, and hyphens, and cannot start or end with a hyphen.');
                this.slugInput.focus();
                return;
            }
        }

        // Add loading state
        if (this.submitBtn) {
            this.submitBtn.innerHTML = `
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Saving...
            `;
            this.submitBtn.disabled = true;
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new PrivilegeEditForm();
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

    /* Input focus effects */
    input:focus, textarea:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px var(--accent-glow);
    }

    /* Character counter */
    .char-counter {
        transition: color 0.2s ease;
    }

    /* Preview styling */
    #namePreview, #slugPreview, #descriptionPreview {
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

    /* Responsive */
    @media (max-width: 640px) {
        .glass-card {
            padding: 1rem;
        }

        .px-6 {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }
    }
</style>

@endsection
