@extends('layouts.app')

@section('title', 'Create Privilege')

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
                    <span class="text-[var(--text-primary)] font-medium">Create Privilege</span>
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6 mb-8 shadow-[var(--card-shadow)]">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="p-2 rounded-xl bg-gradient-to-br from-[var(--success)]/20 to-[var(--success)]/10">
                            <svg class="w-7 h-7 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-[var(--text-primary)]">Create New Privilege</h1>
                            <p class="text-[var(--text-secondary)] mt-2">Define a new system permission or access level</p>
                        </div>
                    </div>

                    <!-- Progress Steps -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between max-w-md">
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
                                <span class="text-sm text-[var(--text-secondary)]">Advanced</span>
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

                <a href="{{ route('privileges.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300 group">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Privileges
                </a>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('privileges.store') }}" method="POST" id="privilegeForm" class="space-y-8">
            @csrf

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
                            <span class="text-xs text-[var(--text-secondary)]" id="nameCounter">0/50</span>
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
                                   value="{{ old('name') }}"
                                   class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                   placeholder="e.g., Create Users"
                                   maxlength="50"
                                   required
                                   autofocus>
                        </div>
                        <p class="text-xs text-[var(--text-secondary)]">Use a clear, descriptive name that explains the permission (max 50 characters)</p>
                        <div class="flex items-center gap-2 mt-2">
                            <svg class="w-4 h-4 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-xs text-[var(--text-secondary)]">Examples: "View Reports", "Manage Settings", "Delete Users"</span>
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
                        <div class="flex items-center justify-between">
                            <label for="slug" class="block">
                                <span class="text-sm font-medium text-[var(--text-primary)]">Slug</span>
                                <span class="text-xs text-[var(--text-secondary)] ml-1">Auto-generated</span>
                            </label>
                            <span class="text-xs text-[var(--text-secondary)]" id="slugCounter">0/30</span>
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
                                   value="{{ old('slug') }}"
                                   class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300 font-mono"
                                   placeholder="Will be auto-generated from name"
                                   maxlength="30">
                        </div>
                        <p class="text-xs text-[var(--text-secondary)]">Unique identifier used in code. Lowercase with hyphens (e.g., "create-users")</p>
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
                            <span class="text-xs text-[var(--text-secondary)]" id="descriptionCounter">0/200</span>
                        </div>
                        <textarea id="description"
                                  name="description"
                                  rows="4"
                                  class="w-full px-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300 resize-none"
                                  placeholder="Describe what this privilege allows... (e.g., Allows users to create new user accounts in the system)"
                                  maxlength="200">{{ old('description') }}</textarea>
                        <p class="text-xs text-[var(--text-secondary)]">Provide a clear description of what this privilege allows (max 200 characters)</p>
                        @error('description')
                            <p class="text-sm text-[var(--danger)] mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Preview Section -->
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
                                    <span class="font-medium text-[var(--text-primary)]" id="namePreview">Privilege Name</span>
                                </div>
                                <span class="px-2 py-1 text-xs font-mono bg-[var(--bg-secondary)] text-[var(--text-secondary)] rounded" id="slugPreview">privilege-slug</span>
                            </div>
                            <p class="text-sm text-[var(--text-secondary)]" id="descriptionPreview">
                                Description will appear here...
                            </p>
                        </div>
                    </div>

                    <!-- Code Preview -->

                </div>
            </div>

            <!-- Best Practices Card -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)]">
                <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                        <svg class="w-5 h-5 text-[var(--info)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Best Practices
                    </h2>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-[var(--text-primary)] flex items-center gap-2">
                                <svg class="w-4 h-4 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Do's
                            </h3>
                            <ul class="space-y-2 text-sm text-[var(--text-secondary)]">
                                <li class="flex items-start gap-2">
                                    <div class="mt-0.5">✓</div>
                                    <span>Use specific, action-oriented names</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <div class="mt-0.5">✓</div>
                                    <span>Keep slugs lowercase with hyphens</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <div class="mt-0.5">✓</div>
                                    <span>Add clear descriptions for documentation</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <div class="mt-0.5">✓</div>
                                    <span>Create granular privileges for better control</span>
                                </li>
                            </ul>
                        </div>

                        <div class="space-y-4">
                            <h3 class="text-sm font-medium text-[var(--text-primary)] flex items-center gap-2">
                                <svg class="w-4 h-4 text-[var(--danger)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Don'ts
                            </h3>
                            <ul class="space-y-2 text-sm text-[var(--text-secondary)]">
                                <li class="flex items-start gap-2">
                                    <div class="mt-0.5">✗</div>
                                    <span>Use vague or generic names</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <div class="mt-0.5">✗</div>
                                    <span>Use spaces or special characters in slugs</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <div class="mt-0.5">✗</div>
                                    <span>Create overlapping privileges</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <div class="mt-0.5">✗</div>
                                    <span>Forget to assign privileges to roles</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">Create Privilege</h3>
                        <p class="text-sm text-[var(--text-secondary)]">Review information before creating</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('privileges.index') }}"
                           class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] text-center transition-all duration-300 hover:shadow-sm">
                            Cancel
                        </a>
                        <button type="reset"
                                id="resetBtn"
                                class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] transition-all duration-300 hover:shadow-sm">
                            Reset Form
                        </button>
                        <button type="submit"
                                id="submitBtn"
                                class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Create Privilege
                        </button>
                    </div>
                </div>

                <!-- Validation Summary -->
                <div id="validationSummary" class="mt-6 pt-6 border-t border-[var(--border-color)]/30 hidden">
                    <h4 class="text-sm font-medium text-[var(--text-primary)] mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-[var(--warning)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Please check the following:
                    </h4>
                    <ul class="space-y-2 text-sm" id="validationList">
                        <!-- Validation messages will appear here -->
                    </ul>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
class CreatePrivilegeForm {
    constructor() {
        this.form = document.getElementById('privilegeForm');
        this.nameInput = document.getElementById('name');
        this.slugInput = document.getElementById('slug');
        this.descriptionInput = document.getElementById('description');
        this.resetBtn = document.getElementById('resetBtn');
        this.submitBtn = document.getElementById('submitBtn');
        this.validationSummary = document.getElementById('validationSummary');
        this.validationList = document.getElementById('validationList');

        // Counter elements
        this.nameCounter = document.getElementById('nameCounter');
        this.slugCounter = document.getElementById('slugCounter');
        this.descriptionCounter = document.getElementById('descriptionCounter');

        // Preview elements
        this.namePreview = document.getElementById('namePreview');
        this.slugPreview = document.getElementById('slugPreview');
        this.descriptionPreview = document.getElementById('descriptionPreview');
        this.codeSnippet = document.getElementById('codeSnippet');

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateCounters();
        this.updatePreview();
    }

    setupEventListeners() {
        // Auto-generate slug from name
        this.nameInput.addEventListener('input', () => {
            this.updateCounter(this.nameInput, this.nameCounter);
            this.updatePreview();
            if (!this.slugInput.dataset.manual) {
                this.generateSlug();
            }
            this.validateForm();
        });

        // Manual slug editing
        this.slugInput.addEventListener('input', () => {
            this.slugInput.dataset.manual = 'true';
            this.updateCounter(this.slugInput, this.slugCounter);
            this.updatePreview();
            this.validateForm();
        });

        // Description updates
        this.descriptionInput.addEventListener('input', () => {
            this.updateCounter(this.descriptionInput, this.descriptionCounter);
            this.updatePreview();
            this.validateForm();
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

        // Real-time validation
        this.form.addEventListener('input', () => this.validateForm());

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && this.form.checkValidity()) {
                this.form.submit();
            }
            if (e.key === 'Escape') {
                window.location.href = "{{ route('privileges.index') }}";
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
        this.updateCounter(this.slugInput, this.slugCounter);
    }

    updateCounter(input, counter) {
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

    updateCounters() {
        this.updateCounter(this.nameInput, this.nameCounter);
        this.updateCounter(this.slugInput, this.slugCounter);
        this.updateCounter(this.descriptionInput, this.descriptionCounter);
    }

    updatePreview() {
        // Update name preview
        if (this.namePreview) {
            this.namePreview.textContent = this.nameInput.value || 'Privilege Name';
        }

        // Update slug preview
        if (this.slugPreview) {
            const slug = this.slugInput.value || 'privilege-slug';
            this.slugPreview.textContent = slug;

            // Update code snippet
            if (this.codeSnippet) {
                this.codeSnippet.textContent = `// Check if user has this privilege
if (auth()->user()->hasPrivilege('${slug}')) {
    // User has permission
}`;
            }
        }

        // Update description preview
        if (this.descriptionPreview) {
            this.descriptionPreview.textContent = this.descriptionInput.value || 'Description will appear here...';
        }
    }

    validateForm() {
        const errors = [];

        // Check name
        if (!this.nameInput.value.trim()) {
            errors.push('Privilege name is required');
        } else if (this.nameInput.value.length < 3) {
            errors.push('Privilege name should be at least 3 characters');
        }

        // Check slug format if provided
        if (this.slugInput.value) {
            const slugRegex = /^[a-z0-9]+(?:-[a-z0-9]+)*$/;
            if (!slugRegex.test(this.slugInput.value)) {
                errors.push('Slug must contain only lowercase letters, numbers, and hyphens');
            }
        }

        // Update validation summary
        this.updateValidationSummary(errors);

        return errors.length === 0;
    }

    updateValidationSummary(errors) {
        if (!this.validationSummary || !this.validationList) return;

        if (errors.length > 0) {
            this.validationSummary.classList.remove('hidden');

            let html = '';
            errors.forEach(error => {
                html += `
                    <li class="flex items-center gap-2 text-[var(--danger)]">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span>${error}</span>
                    </li>
                `;
            });

            this.validationList.innerHTML = html;

            // Update submit button
            if (this.submitBtn) {
                this.submitBtn.disabled = true;
                this.submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        } else {
            this.validationSummary.classList.add('hidden');
            this.validationList.innerHTML = '';

            // Enable submit button
            if (this.submitBtn) {
                this.submitBtn.disabled = false;
                this.submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
    }

    resetForm() {
        if (!confirm('Are you sure you want to reset the form? All entered data will be lost.')) return;

        this.nameInput.value = '';
        this.slugInput.value = '';
        this.descriptionInput.value = '';

        // Reset manual flag
        delete this.slugInput.dataset.manual;

        this.updateCounters();
        this.updatePreview();
        this.validateForm();
        this.nameInput.focus();
    }

    handleSubmit(e) {
        // Final validation
        if (!this.validateForm()) {
            e.preventDefault();
            return;
        }

        // Validate slug format if provided
        if (this.slugInput.value) {
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
                Creating...
            `;
            this.submitBtn.disabled = true;
        }
    }
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

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CreatePrivilegeForm();
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

    .btn-primary:hover:not(:disabled) {
        background: linear-gradient(135deg, var(--accent-hover), var(--accent-color));
        box-shadow: 0 6px 20px 0 var(--accent-glow);
    }

    /* Input focus effects */
    input:focus, textarea:focus {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px var(--accent-glow);
    }

    /* Character counter styling */
    .char-counter {
        transition: color 0.2s ease;
    }

    /* Preview styling */
    #namePreview, #slugPreview, #descriptionPreview {
        transition: all 0.3s ease;
    }

    /* Code snippet */
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

    /* Do's and Don'ts styling */
    ul li div {
        color: var(--accent-color);
    }

    ul li div:first-child {
        width: 20px;
        flex-shrink: 0;
    }

    /* Loading animation */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Validation styling */
    #validationList li {
        padding: 4px 0;
        border-bottom: 1px solid var(--border-color);
    }

    #validationList li:last-child {
        border-bottom: none;
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

        .grid-cols-2 {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
