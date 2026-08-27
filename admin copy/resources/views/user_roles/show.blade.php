@extends('layouts.app')

@section('title', 'Assign Roles - ' . $user->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[var(--bg-secondary)]/50 to-[var(--bg-primary)] p-4 md:p-6">
    <div class="max-w-4xl mx-auto">

        {{-- Flash + Errors --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl border border-green-500/20 bg-green-500/10 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl border border-red-500/20 bg-red-500/10 text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Breadcrumb -->
        <nav class="mb-6" aria-label="Breadcrumb">
            <ol class="flex items-center gap-2 text-sm text-[var(--text-secondary)] flex-wrap">
                <li><a href="" class="hover:text-[var(--text-primary)] transition-colors duration-300">Dashboard</a></li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="" class="hover:text-[var(--text-primary)] transition-colors duration-300">Users</a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <a href="{{ route('me', $user->id) }}" class="hover:text-[var(--text-primary)] transition-colors duration-300">
                        {{ $user->name }}
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-[var(--text-primary)] font-medium">Assign Roles</span>
                </li>
            </ol>
        </nav>

        <!-- Header Section -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6 mb-8 shadow-[var(--card-shadow)]">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="relative">
                            <div class="p-3 rounded-xl bg-gradient-to-br from-[var(--accent-color)]/20 to-[var(--accent-color)]/10">
                                <svg class="w-7 h-7 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-gradient-to-br from-[var(--primary)] to-[var(--primary-hover)] text-white text-xs flex items-center justify-center border-2 border-[var(--bg-primary)]">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-[var(--text-primary)]">Assign Roles to {{ $user->name }}</h1>
                            <p class="text-[var(--text-secondary)] mt-2">Manage role assignments for this specific user</p>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--border-color)]/30">
                            <p class="text-sm font-medium text-[var(--text-primary)]">Email</p>
                            <p class="text-sm text-[var(--text-secondary)]">{{ $user->email }}</p>
                        </div>

                        <div class="p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--border-color)]/30">
                            <p class="text-sm font-medium text-[var(--text-primary)]">Joined</p>
                            <p class="text-sm text-[var(--text-secondary)]">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>

                        <div class="p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--border-color)]/30">
                            <p class="text-sm font-medium text-[var(--text-primary)]">Assigned Roles</p>
                            <p class="text-sm text-[var(--text-secondary)]">{{ $assignedRoles->count() }}</p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('me', $user->id) }}"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300 group">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Profile
                </a>
            </div>
        </div>

        <!-- Current Roles Summary -->
        <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] mb-8">
            <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                <h2 class="text-xl font-semibold text-[var(--text-primary)]">Current Role Assignment</h2>
            </div>

            <div class="p-6">
                @if($assignedRoles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($assignedRoles as $role)
                            <div class="p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--accent-color)]/20">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-semibold text-[var(--text-primary)]">{{ $role->name }}</h3>
                                            <span class="text-xs px-2 py-0.5 bg-[var(--accent-color)]/10 text-[var(--accent-color)] rounded-full">
                                                {{ $role->privileges_count }} permissions
                                            </span>
                                        </div>
                                        <p class="text-sm text-[var(--text-secondary)]">{{ $role->slug }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <p class="text-sm text-[var(--text-secondary)] mt-4">
                        {{ $user->name }} currently has {{ $assignedRoles->count() }} role{{ $assignedRoles->count() !== 1 ? 's' : '' }} assigned.
                    </p>
                @else
                    <p class="text-[var(--text-secondary)]">No roles assigned yet.</p>
                @endif
            </div>
        </div>

        <!-- Role Assignment Form -->
        <form action="{{ route('user.roles.store', $user->id) }}" method="POST" id="roleAssignmentForm">
            @csrf

            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)]">
                <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="text-xl font-semibold text-[var(--text-primary)]">Assign / Update Roles</h2>
                        <span class="text-sm text-[var(--text-secondary)]">{{ $allRoles->count() }} roles available</span>
                    </div>
                </div>

                <div class="p-6 space-y-6">

                    <!-- Tools Row -->
                    <div class="flex flex-col md:flex-row md:items-center gap-3 justify-between">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--text-secondary)]"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text"
                                   id="roleSearch"
                                   class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                   placeholder="Search roles..."
                                   onkeyup="filterRoleOptions()">
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" onclick="toggleHideAssigned()"
                                    class="px-3 py-2 rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-tertiary)] transition">
                                Hide Assigned
                            </button>

                            <button type="button" onclick="uncheckAssigned()"
                                    class="px-3 py-2 rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-tertiary)] transition">
                                Uncheck Assigned
                            </button>

                            <button type="button" onclick="selectVisible()"
                                    class="px-3 py-2 rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-tertiary)] transition">
                                Select Visible
                            </button>

                            <button type="button" onclick="clearAllRoles()"
                                    class="px-3 py-2 rounded-xl border border-[var(--border-color)] hover:bg-[var(--bg-tertiary)] transition">
                                Clear All
                            </button>
                        </div>
                    </div>

                    <!-- Operation Mode -->
                    <div class="p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--border-color)]">
                        <p class="text-sm font-medium text-[var(--text-primary)]">Operation Mode</p>
                        <p class="text-xs text-[var(--text-secondary)] mb-3">
                            If you want old roles removed automatically, use <b>Replace</b>.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="operation-mode-option cursor-pointer">
                                {{-- IMPORTANT: default = replace (fixes “keeps old + new”) --}}
                                <input type="radio" name="operation_mode" value="replace" checked class="sr-only">
                                <div class="p-4 rounded-xl border-2 border-transparent hover:border-[var(--accent-color)]/30 transition-all duration-300 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5">
                                            <div class="w-5 h-5 rounded-full border-2 border-[var(--border-color)] flex items-center justify-center">
                                                <div class="w-2.5 h-2.5 rounded-full bg-[var(--accent-color)] opacity-0 transition-opacity duration-200"></div>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-medium text-[var(--text-primary)]">Replace (Overwrite)</p>
                                            <p class="text-sm text-[var(--text-secondary)] mt-1">User will keep only the selected roles.</p>
                                        </div>
                                    </div>
                                </div>
                            </label>

                            <label class="operation-mode-option cursor-pointer">
                                <input type="radio" name="operation_mode" value="add" class="sr-only">
                                <div class="p-4 rounded-xl border-2 border-transparent hover:border-[var(--accent-color)]/30 transition-all duration-300 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5">
                                            <div class="w-5 h-5 rounded-full border-2 border-[var(--border-color)] flex items-center justify-center">
                                                <div class="w-2.5 h-2.5 rounded-full bg-[var(--accent-color)] opacity-0 transition-opacity duration-200"></div>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-medium text-[var(--text-primary)]">Add (Keep Existing)</p>
                                            <p class="text-sm text-[var(--text-secondary)] mt-1">Selected roles will be added to existing roles.</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Role Cards -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-[var(--text-primary)]">Select Roles</p>
                            <p class="text-xs text-[var(--text-secondary)]" id="selectedRolesCount">0 roles selected</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="rolesContainer">
                            @foreach($allRoles as $role)
                                @php
                                    $isAssigned = in_array((int)$role->id, $assignedRoleIds, true);
                                    $searchText = strtolower($role->name.' '.($role->description ?? '').' '.$role->slug);
                                @endphp

                                <div class="role-option group"
                                     data-role-id="{{ $role->id }}"
                                     data-search="{{ $searchText }}"
                                     data-assigned="{{ $isAssigned ? 'true' : 'false' }}">
                                    <input type="checkbox"
                                           id="role_{{ $role->id }}"
                                           name="roles[]"
                                           value="{{ $role->id }}"
                                           class="hidden role-checkbox"
                                           {{ $isAssigned ? 'checked' : '' }}
                                           data-permissions="{{ $role->privileges_count }}"
                                           data-users="{{ $role->users_count }}">

                                    <label for="role_{{ $role->id }}"
                                           class="block p-4 rounded-xl border border-[var(--border-color)] hover:border-[var(--accent-color)] hover:shadow-sm cursor-pointer transition-all duration-300
                                                  {{ $isAssigned ? 'border-[var(--accent-color)]/30 bg-gradient-to-r from-[var(--accent-color)]/5 to-transparent' : 'bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent' }}">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-[var(--text-primary)] role-name">{{ $role->name }}</span>
                                                    @if($isAssigned)
                                                        <span class="text-xs px-2 py-0.5 bg-gradient-to-r from-[var(--accent-color)] to-[var(--accent-hover)] text-white rounded-full">
                                                            Assigned
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-[var(--text-secondary)]">{{ $role->slug }}</p>
                                            </div>

                                            <div class="role-checkbox-display ml-2 flex-shrink-0">
                                                <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition-all duration-200
                                                    {{ $isAssigned ? 'border-[var(--accent-color)] bg-[var(--accent-color)]' : 'border-[var(--border-color)] bg-transparent' }}">
                                                    <svg class="w-3 h-3 text-white transition-opacity duration-200 {{ $isAssigned ? 'opacity-100' : 'opacity-0' }}"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                        @if(!empty($role->description))
                                            <p class="text-sm text-[var(--text-muted)] mb-2 line-clamp-2">{{ $role->description }}</p>
                                        @endif

                                        <div class="flex items-center justify-between text-xs text-[var(--text-secondary)]">
                                            <span>{{ $role->privileges_count }} permissions</span>
                                            <span>{{ $role->users_count }} users</span>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Selected Preview -->
                    <div class="p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--border-color)]">
                        <p class="text-sm font-medium text-[var(--text-primary)] mb-2">Selected Roles Preview</p>
                        <div id="selectedRolesPreview" class="space-y-2">
                            <div class="text-center py-4">
                                <p class="text-sm text-[var(--text-muted)]">No roles selected yet</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Actions -->
                <div class="border-t border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-[var(--text-primary)]">Save Changes</p>
                            <p class="text-xs text-[var(--text-secondary)]">Replace mode removes old roles automatically.</p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('me', $user->id) }}"
                               class="px-6 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] text-center transition-all duration-300 hover:shadow-sm">
                                Cancel
                            </a>

                            <button type="submit"
                                    id="submitBtn"
                                    class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Role Assignments
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>


<script>
class RoleAssignment {
    constructor() {
        this.form = document.getElementById('roleAssignmentForm');
        this.roleCheckboxes = document.querySelectorAll('.role-checkbox');
        this.submitBtn = document.getElementById('submitBtn');
        this.selectedRolesCount = document.getElementById('selectedRolesCount');
        this.selectedRolesPreview = document.getElementById('selectedRolesPreview');
        this.operationModeOptions = document.querySelectorAll('.operation-mode-option');

        this.hideAssigned = false;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateSelectedCount();
        this.updatePreview();
        this.setupOperationMode();
    }

    setupEventListeners() {
        this.roleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateRoleOption(checkbox);
                this.updateSelectedCount();
                this.updatePreview();
            });
        });

        this.operationModeOptions.forEach(option => {
            option.addEventListener('click', () => this.updateOperationMode(option));
        });

        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    updateRoleOption(checkbox) {
        const option = checkbox.closest('.role-option');
        const label = option.querySelector('label');
        const isAssigned = option.dataset.assigned === 'true';

        const box = label.querySelector('.role-checkbox-display div');
        const tick = label.querySelector('.role-checkbox-display svg');

        if (checkbox.checked) {
            label.classList.add('border-[var(--accent-color)]', 'bg-gradient-to-r', 'from-[var(--accent-color)]/5', 'to-transparent');
            box.classList.add('border-[var(--accent-color)]', 'bg-[var(--accent-color)]');
            tick.classList.add('opacity-100');
        } else {
            if (!isAssigned) {
                label.classList.remove('border-[var(--accent-color)]', 'bg-gradient-to-r', 'from-[var(--accent-color)]/5', 'to-transparent');
            }
            box.classList.remove('border-[var(--accent-color)]', 'bg-[var(--accent-color)]');
            tick.classList.remove('opacity-100');
        }
    }

    updateSelectedCount() {
        const selectedCount = Array.from(this.roleCheckboxes).filter(cb => cb.checked).length;
        this.selectedRolesCount.textContent = `${selectedCount} role${selectedCount !== 1 ? 's' : ''} selected`;
    }

    updatePreview() {
        const selected = Array.from(this.roleCheckboxes).filter(cb => cb.checked);

        if (!selected.length) {
            this.selectedRolesPreview.innerHTML = `
                <div class="text-center py-4">
                    <p class="text-sm text-[var(--text-muted)]">No roles selected yet</p>
                </div>
            `;
            return;
        }

        let html = '';
        selected.forEach(cb => {
            const option = cb.closest('.role-option');
            const name = option.querySelector('.role-name')?.textContent?.trim() ?? 'Role';
            html += `
                <div class="flex items-center justify-between p-3 bg-[var(--bg-secondary)] rounded-lg">
                    <div>
                        <p class="font-medium text-[var(--text-primary)]">${name}</p>
                        <p class="text-xs text-[var(--text-secondary)] mt-1">${cb.dataset.permissions} permissions • ${cb.dataset.users} users</p>
                    </div>
                    <button type="button"
                            onclick="removeFromSelection('${cb.value}')"
                            class="p-1 text-[var(--danger)] hover:bg-[var(--danger)]/10 rounded-lg transition-colors duration-300">
                        ✕
                    </button>
                </div>
            `;
        });

        this.selectedRolesPreview.innerHTML = html;
    }

    setupOperationMode() {
        this.operationModeOptions.forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            if (radio.checked) {
                option.querySelector('div').classList.add('border-[var(--accent-color)]');
                option.querySelector('.w-2\\.5').classList.add('opacity-100');
            }
        });
    }

    updateOperationMode(selectedOption) {
        const radio = selectedOption.querySelector('input[type="radio"]');
        radio.checked = true;

        this.operationModeOptions.forEach(option => {
            const optionDiv = option.querySelector('div');
            const indicator = option.querySelector('.w-2\\.5');

            if (option === selectedOption) {
                optionDiv.classList.add('border-[var(--accent-color)]');
                indicator.classList.add('opacity-100');
            } else {
                optionDiv.classList.remove('border-[var(--accent-color)]');
                indicator.classList.remove('opacity-100');
            }
        });
    }

    handleSubmit(e) {
        const selectedCount = Array.from(this.roleCheckboxes).filter(cb => cb.checked).length;

        if (selectedCount === 0) {
            e.preventDefault();
            alert('Please select at least one role.');
            return;
        }

        this.submitBtn.disabled = true;
        this.submitBtn.innerHTML = 'Saving...';
    }
}

let HIDE_ASSIGNED = false;

function filterRoleOptions() {
    const searchTerm = document.getElementById('roleSearch').value.toLowerCase();
    const options = document.querySelectorAll('.role-option');

    options.forEach(option => {
        const searchText = option.dataset.search;
        const isAssigned = option.dataset.assigned === 'true';

        const matchSearch = searchText.includes(searchTerm);
        const matchAssigned = !HIDE_ASSIGNED || !isAssigned;

        option.classList.toggle('hidden', !(matchSearch && matchAssigned));
    });
}

function toggleHideAssigned() {
    HIDE_ASSIGNED = !HIDE_ASSIGNED;
    filterRoleOptions();
}

function selectVisible() {
    document.querySelectorAll('.role-option:not(.hidden) .role-checkbox').forEach(cb => {
        cb.checked = true;
        cb.dispatchEvent(new Event('change'));
    });
}

function clearAllRoles() {
    document.querySelectorAll('.role-checkbox').forEach(cb => {
        cb.checked = false;
        cb.dispatchEvent(new Event('change'));
    });
}

function uncheckAssigned() {
    document.querySelectorAll('.role-option[data-assigned="true"] .role-checkbox').forEach(cb => {
        cb.checked = false;
        cb.dispatchEvent(new Event('change'));
    });
}

function removeFromSelection(roleId) {
    const cb = document.getElementById(`role_${roleId}`);
    if (cb) {
        cb.checked = false;
        cb.dispatchEvent(new Event('change'));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new RoleAssignment();
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
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    #rolesContainer {
        max-height: 420px;
        overflow-y: auto;
        padding-right: 4px;
    }
</style>
@endsection
