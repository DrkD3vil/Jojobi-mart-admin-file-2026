@extends('layouts.app')

@section('title', 'Bulk Role Assignment')

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
                    <a href="" class="hover:text-[var(--text-primary)] transition-colors duration-300">
                        Users & Roles
                    </a>
                </li>
                <li class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-[var(--text-primary)] font-medium">Bulk Role Assignment</span>
                </li>
            </ol>
        </nav>

        <!-- Header Section -->
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
                            <h1 class="text-3xl font-bold text-[var(--text-primary)]">Bulk Role Assignment</h1>
                            <p class="text-[var(--text-secondary)] mt-2">Assign roles to multiple users simultaneously</p>
                        </div>
                    </div>

                    <!-- Progress Steps -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between max-w-lg">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-10 h-10 rounded-full bg-[var(--accent-color)] text-[var(--primary-foreground)] flex items-center justify-center font-semibold mb-2">
                                    1
                                </div>
                                <span class="text-sm font-medium text-[var(--text-primary)]">Select Users</span>
                            </div>
                            <div class="flex-1 h-1 bg-[var(--border-color)] mx-4"></div>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-10 h-10 rounded-full bg-[var(--bg-tertiary)] text-[var(--text-secondary)] flex items-center justify-center font-semibold mb-2">
                                    2
                                </div>
                                <span class="text-sm text-[var(--text-secondary)]">Select Roles</span>
                            </div>
                            <div class="flex-1 h-1 bg-[var(--border-color)] mx-4"></div>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-10 h-10 rounded-full bg-[var(--bg-tertiary)] text-[var(--text-secondary)] flex items-center justify-center font-semibold mb-2">
                                    3
                                </div>
                                <span class="text-sm text-[var(--text-secondary)]">Confirm</span>
                            </div>
                        </div>
                    </div>
                </div>

                <a href=""
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-[var(--border-color)] text-[var(--text-primary)] hover:bg-[var(--bg-secondary)] hover:shadow-sm transition-all duration-300 group">
                    <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Users
                </a>
            </div>
        </div>

        <!-- Form Container -->
        <form action="{{ route('user.roles.assign_multiple') }}" method="POST" id="bulkAssignForm" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Users Selection Card -->
                <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] hover:shadow-[var(--card-shadow-hover)] transition-all duration-500">
                    <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                                <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 8.65a2 2 0 01-2.83 0M19 18a2 2 0 01-2 2"/>
                                </svg>
                                Select Users
                            </h2>
                            <span class="text-sm text-[var(--text-secondary)]">{{ $users->count() }} users available</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- User Search -->
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--text-secondary)]"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text"
                                       id="userSearch"
                                       class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                       placeholder="Search users by name or email..."
                                       onkeyup="filterUsers()">
                            </div>

                            <!-- User Selection -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-[var(--text-secondary)]">
                                        <span id="selectedUsersCount">0</span> users selected
                                    </span>
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                                onclick="selectAllUsers()"
                                                class="text-[var(--accent-color)] hover:text-[var(--accent-hover)] transition-colors duration-300 text-sm">
                                            Select All
                                        </button>
                                        <button type="button"
                                                onclick="clearAllUsers()"
                                                class="text-[var(--danger)] hover:text-[var(--danger)]/80 transition-colors duration-300 text-sm">
                                            Clear All
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-[var(--input)] border border-[var(--border-color)] rounded-xl p-2 max-h-96 overflow-y-auto" id="usersContainer">
                                    @foreach($users as $user)
                                        <div class="user-option group" data-user-id="{{ $user->id }}" data-search="{{ strtolower($user->name . ' ' . $user->email) }}">
                                            <input type="checkbox"
                                                   id="user_{{ $user->id }}"
                                                   name="users[]"
                                                   value="{{ $user->id }}"
                                                   class="hidden user-checkbox"
                                                   data-email="{{ $user->email }}"
                                                   data-roles="{{ $user->roles->pluck('name')->join(', ') }}">
                                            <label for="user_{{ $user->id }}"
                                                   class="flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-300 border border-transparent hover:border-[var(--accent-color)] hover:bg-[var(--accent-color)]/5">
                                                <div class="flex items-center gap-3">
                                                    <div class="user-checkbox-display">
                                                        <svg class="checkbox-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                            <path d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-medium text-[var(--text-primary)]">{{ $user->name }}</span>
                                                            @if($user->roles->count() > 0)
                                                                <span class="text-xs px-2 py-0.5 bg-[var(--accent-color)]/10 text-[var(--accent-color)] rounded-full">
                                                                    {{ $user->roles->count() }} role{{ $user->roles->count() !== 1 ? 's' : '' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <p class="text-sm text-[var(--text-secondary)] mt-1">{{ $user->email }}</p>
                                                        @if($user->roles->count() > 0)
                                                            <p class="text-xs text-[var(--text-muted)] mt-1">
                                                                Current roles: {{ $user->roles->pluck('name')->join(', ') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="user-avatar">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <p class="text-xs text-[var(--text-secondary)] mt-2">
                                    Hold Ctrl/Cmd to select multiple users. Selected users will appear in the preview.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles Selection Card -->
                <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)] hover:shadow-[var(--card-shadow-hover)] transition-all duration-500">
                    <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                                <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Select Roles
                            </h2>
                            <span class="text-sm text-[var(--text-secondary)]">{{ $roles->count() }} roles available</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Role Search -->
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-[var(--text-secondary)]"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text"
                                       id="roleSearch"
                                       class="w-full pl-10 pr-4 py-3 bg-[var(--input)] border border-[var(--border-color)] rounded-xl focus:ring-2 focus:ring-[var(--accent-color)] focus:border-transparent outline-none transition-all duration-300"
                                       placeholder="Search roles by name..."
                                       onkeyup="filterRoles()">
                            </div>

                            <!-- Role Selection -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-[var(--text-secondary)]">
                                        <span id="selectedRolesCount">0</span> roles selected
                                    </span>
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                                onclick="selectAllRoles()"
                                                class="text-[var(--accent-color)] hover:text-[var(--accent-hover)] transition-colors duration-300 text-sm">
                                            Select All
                                        </button>
                                        <button type="button"
                                                onclick="clearAllRoles()"
                                                class="text-[var(--danger)] hover:text-[var(--danger)]/80 transition-colors duration-300 text-sm">
                                            Clear All
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-[var(--input)] border border-[var(--border-color)] rounded-xl p-2 max-h-96 overflow-y-auto" id="rolesContainer">
                                    @foreach($roles as $role)
                                        <div class="role-option group" data-role-id="{{ $role->id }}" data-search="{{ strtolower($role->name . ' ' . $role->slug) }}">
                                            <input type="checkbox"
                                                   id="role_{{ $role->id }}"
                                                   name="roles[]"
                                                   value="{{ $role->id }}"
                                                   class="hidden role-checkbox"
                                                   data-description="{{ $role->description ?? 'No description' }}"
                                                   data-permissions="{{ $role->privileges->count() }}">
                                            <label for="role_{{ $role->id }}"
                                                   class="flex items-center justify-between p-3 rounded-lg cursor-pointer transition-all duration-300 border border-transparent hover:border-[var(--accent-color)] hover:bg-[var(--accent-color)]/5">
                                                <div class="flex items-center gap-3">
                                                    <div class="role-checkbox-display">
                                                        <svg class="checkbox-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                                            <path d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-medium text-[var(--text-primary)]">{{ $role->name }}</span>
                                                            <span class="text-xs px-2 py-0.5 bg-[var(--accent-color)]/10 text-[var(--accent-color)] rounded-full">
                                                                {{ $role->privileges->count() }} permission{{ $role->privileges->count() !== 1 ? 's' : '' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-[var(--text-secondary)] mt-1">{{ $role->slug }}</p>
                                                        @if($role->description)
                                                            <p class="text-xs text-[var(--text-muted)] mt-1 truncate">{{ $role->description }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="role-users-count text-xs text-[var(--text-secondary)]">
                                                    {{ $role->users->count() }} users
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <p class="text-xs text-[var(--text-secondary)] mt-2">
                                    Selected roles will be assigned to all selected users.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 overflow-hidden shadow-[var(--card-shadow)]">
                <div class="border-b border-[var(--border-color)]/30 px-6 py-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-[var(--bg-tertiary)]/50">
                    <h2 class="text-xl font-semibold text-[var(--text-primary)] flex items-center gap-3">
                        <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Assignment Preview
                    </h2>
                </div>

                <div class="p-6">
                    <div id="emptyPreview" class="text-center py-8">
                        <div class="p-4 rounded-full bg-[var(--bg-tertiary)] inline-flex mb-4">
                            <svg class="w-10 h-10 text-[var(--text-secondary)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">No Selection Made</h3>
                        <p class="text-[var(--text-secondary)]">Select users and roles to see the assignment preview</p>
                    </div>

                    <div id="assignmentPreview" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Selected Users Preview -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-[var(--text-primary)] flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 8.65a2 2 0 01-2.83 0M19 18a2 2 0 01-2 2"/>
                                    </svg>
                                    Selected Users
                                    <span class="px-2 py-1 text-xs bg-[var(--accent-color)]/10 text-[var(--accent-color)] rounded-full" id="previewUsersCount">0</span>
                                </h3>
                                <div class="space-y-3" id="selectedUsersPreview">
                                    <!-- Selected users will appear here -->
                                </div>
                            </div>

                            <!-- Selected Roles Preview -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-[var(--text-primary)] flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[var(--accent-color)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Selected Roles
                                    <span class="px-2 py-1 text-xs bg-[var(--accent-color)]/10 text-[var(--accent-color)] rounded-full" id="previewRolesCount">0</span>
                                </h3>
                                <div class="space-y-3" id="selectedRolesPreview">
                                    <!-- Selected roles will appear here -->
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="mt-8 p-4 bg-gradient-to-r from-[var(--bg-tertiary)] to-transparent rounded-xl border border-[var(--border-color)]">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-[var(--text-primary)]">Assignment Summary</p>
                                    <p class="text-xs text-[var(--text-secondary)] mt-1">
                                        <span id="totalAssignments">0</span> total role assignments will be made
                                    </p>
                                </div>
                                <div class="text-center">
                                    <p class="text-2xl font-bold text-[var(--text-primary)]" id="assignmentsCount">0</p>
                                    <p class="text-xs text-[var(--text-secondary)]">Role Assignments</p>
                                </div>
                            </div>
                        </div>

                        <!-- Warning Message -->
                        <div id="warningMessage" class="mt-4 hidden">
                            <div class="p-4 bg-gradient-to-r from-[var(--warning)]/10 to-transparent rounded-xl border border-[var(--warning)]/30">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-[var(--warning)] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-[var(--text-primary)]">Warning: Existing Roles</p>
                                        <p class="text-xs text-[var(--text-secondary)] mt-1" id="warningText">
                                            Some selected users already have roles assigned. This operation will add new roles to existing ones.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="glass-card rounded-2xl border border-[var(--border-color)]/30 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[var(--text-primary)] mb-2">Complete Assignment</h3>
                        <p class="text-sm text-[var(--text-secondary)]">Review your selection before proceeding</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href=""
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
                                class="btn-primary px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:-translate-y-0.5 active:translate-y-0 shadow-lg hover:shadow-xl flex items-center justify-center gap-2"
                                disabled>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Assign Roles
                        </button>
                    </div>
                </div>

                <!-- Operation Mode -->
                <div class="mt-6 pt-6 border-t border-[var(--border-color)]/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[var(--text-primary)]">Operation Mode</p>
                            <p class="text-xs text-[var(--text-secondary)] mt-1">Choose how to handle existing roles</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="operation_mode" value="add" checked class="text-[var(--accent-color)]">
                                <span class="text-sm text-[var(--text-primary)]">Add to Existing</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="operation_mode" value="replace" class="text-[var(--accent-color)]">
                                <span class="text-sm text-[var(--text-primary)]">Replace Existing</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
class BulkRoleAssignment {
    constructor() {
        this.form = document.getElementById('bulkAssignForm');
        this.userCheckboxes = document.querySelectorAll('.user-checkbox');
        this.roleCheckboxes = document.querySelectorAll('.role-checkbox');
        this.userSearch = document.getElementById('userSearch');
        this.roleSearch = document.getElementById('roleSearch');
        this.resetBtn = document.getElementById('resetBtn');
        this.submitBtn = document.getElementById('submitBtn');
        this.emptyPreview = document.getElementById('emptyPreview');
        this.assignmentPreview = document.getElementById('assignmentPreview');
        this.warningMessage = document.getElementById('warningMessage');

        // Counter elements
        this.selectedUsersCount = document.getElementById('selectedUsersCount');
        this.selectedRolesCount = document.getElementById('selectedRolesCount');
        this.previewUsersCount = document.getElementById('previewUsersCount');
        this.previewRolesCount = document.getElementById('previewRolesCount');
        this.totalAssignments = document.getElementById('totalAssignments');
        this.assignmentsCount = document.getElementById('assignmentsCount');

        // Preview containers
        this.selectedUsersPreview = document.getElementById('selectedUsersPreview');
        this.selectedRolesPreview = document.getElementById('selectedRolesPreview');

        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateCounts();
        this.updatePreview();
        this.validateForm();
    }

    setupEventListeners() {
        // User checkbox changes
        this.userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateUserOption(checkbox);
                this.updateCounts();
                this.updatePreview();
                this.validateForm();
            });
        });

        // Role checkbox changes
        this.roleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.updateRoleOption(checkbox);
                this.updateCounts();
                this.updatePreview();
                this.validateForm();
            });
        });

        // User label clicks
        document.querySelectorAll('.user-option label').forEach(label => {
            label.addEventListener('click', (e) => {
                e.preventDefault();
                const checkbox = label.parentElement.querySelector('.user-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
        });

        // Role label clicks
        document.querySelectorAll('.role-option label').forEach(label => {
            label.addEventListener('click', (e) => {
                e.preventDefault();
                const checkbox = label.parentElement.querySelector('.role-checkbox');
                if (checkbox) {
                    checkbox.checked = !checkbox.checked;
                    checkbox.dispatchEvent(new Event('change'));
                }
            });
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

        // Operation mode changes
        document.querySelectorAll('input[name="operation_mode"]').forEach(radio => {
            radio.addEventListener('change', () => {
                this.updateWarningMessage();
            });
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter' && !this.submitBtn.disabled) {
                this.form.submit();
            }
            if (e.key === 'Escape') {
                window.location.href = "";
            }
        });
    }

    updateUserOption(checkbox) {
        const option = checkbox.closest('.user-option');
        const label = option.querySelector('label');

        if (checkbox.checked) {
            label.classList.add('border-[var(--accent-color)]', 'bg-[var(--accent-color)]/10');
            label.querySelector('.user-checkbox-display').classList.add('checked');
        } else {
            label.classList.remove('border-[var(--accent-color)]', 'bg-[var(--accent-color)]/10');
            label.querySelector('.user-checkbox-display').classList.remove('checked');
        }
    }

    updateRoleOption(checkbox) {
        const option = checkbox.closest('.role-option');
        const label = option.querySelector('label');

        if (checkbox.checked) {
            label.classList.add('border-[var(--accent-color)]', 'bg-[var(--accent-color)]/10');
            label.querySelector('.role-checkbox-display').classList.add('checked');
        } else {
            label.classList.remove('border-[var(--accent-color)]', 'bg-[var(--accent-color)]/10');
            label.querySelector('.role-checkbox-display').classList.remove('checked');
        }
    }

    updateCounts() {
        const selectedUsers = Array.from(this.userCheckboxes).filter(cb => cb.checked).length;
        const selectedRoles = Array.from(this.roleCheckboxes).filter(cb => cb.checked).length;

        if (this.selectedUsersCount) this.selectedUsersCount.textContent = selectedUsers;
        if (this.selectedRolesCount) this.selectedRolesCount.textContent = selectedRoles;
        if (this.previewUsersCount) this.previewUsersCount.textContent = selectedUsers;
        if (this.previewRolesCount) this.previewRolesCount.textContent = selectedRoles;

        const totalAssignments = selectedUsers * selectedRoles;
        if (this.totalAssignments) this.totalAssignments.textContent = totalAssignments;
        if (this.assignmentsCount) this.assignmentsCount.textContent = totalAssignments;
    }

    updatePreview() {
        const selectedUsers = Array.from(this.userCheckboxes).filter(cb => cb.checked);
        const selectedRoles = Array.from(this.roleCheckboxes).filter(cb => cb.checked);

        if (selectedUsers.length > 0 && selectedRoles.length > 0) {
            this.emptyPreview.classList.add('hidden');
            this.assignmentPreview.classList.remove('hidden');

            // Update users preview
            let usersHtml = '';
            selectedUsers.forEach(checkbox => {
                const name = checkbox.closest('.user-option').querySelector('.font-medium').textContent;
                const email = checkbox.dataset.email;
                const currentRoles = checkbox.dataset.roles;

                usersHtml += `
                    <div class="flex items-center justify-between p-3 bg-[var(--bg-tertiary)] rounded-lg">
                        <div>
                            <p class="font-medium text-[var(--text-primary)]">${name}</p>
                            <p class="text-sm text-[var(--text-secondary)]">${email}</p>
                            ${currentRoles ? `<p class="text-xs text-[var(--text-muted)] mt-1">Current: ${currentRoles}</p>` : ''}
                        </div>
                    </div>
                `;
            });
            this.selectedUsersPreview.innerHTML = usersHtml;

            // Update roles preview
            let rolesHtml = '';
            selectedRoles.forEach(checkbox => {
                const roleOption = checkbox.closest('.role-option');
                const name = roleOption.querySelector('.font-medium').textContent;
                const slug = roleOption.querySelector('.text-sm').textContent;
                const permissions = checkbox.dataset.permissions;

                rolesHtml += `
                    <div class="flex items-center justify-between p-3 bg-[var(--bg-tertiary)] rounded-lg">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-[var(--text-primary)]">${name}</p>
                                <span class="text-xs px-2 py-0.5 bg-[var(--accent-color)]/10 text-[var(--accent-color)] rounded-full">
                                    ${permissions} permission${permissions !== '1' ? 's' : ''}
                                </span>
                            </div>
                            <p class="text-sm text-[var(--text-secondary)]">${slug}</p>
                        </div>
                    </div>
                `;
            });
            this.selectedRolesPreview.innerHTML = rolesHtml;

            // Update warning message
            this.updateWarningMessage();
        } else {
            this.emptyPreview.classList.remove('hidden');
            this.assignmentPreview.classList.add('hidden');
        }
    }

    updateWarningMessage() {
        const selectedUsers = Array.from(this.userCheckboxes).filter(cb => cb.checked);
        const selectedRoles = Array.from(this.roleCheckboxes).filter(cb => cb.checked);
        const operationMode = document.querySelector('input[name="operation_mode"]:checked').value;

        if (selectedUsers.length === 0 || selectedRoles.length === 0) {
            this.warningMessage.classList.add('hidden');
            return;
        }

        // Check if any user already has roles
        const usersWithRoles = selectedUsers.filter(checkbox => checkbox.dataset.roles);

        if (usersWithRoles.length > 0) {
            this.warningMessage.classList.remove('hidden');
            const warningText = document.getElementById('warningText');

            if (operationMode === 'add') {
                warningText.textContent =
                    `${usersWithRoles.length} selected user${usersWithRoles.length !== 1 ? 's' : ''} already have roles. ` +
                    `This operation will add the selected roles to their existing ones.`;
            } else {
                warningText.textContent =
                    `${usersWithRoles.length} selected user${usersWithRoles.length !== 1 ? 's' : ''} already have roles. ` +
                    `This operation will replace their existing roles with the selected ones.`;
            }
        } else {
            this.warningMessage.classList.add('hidden');
        }
    }

    validateForm() {
        const selectedUsers = Array.from(this.userCheckboxes).filter(cb => cb.checked).length;
        const selectedRoles = Array.from(this.roleCheckboxes).filter(cb => cb.checked).length;
        const isValid = selectedUsers > 0 && selectedRoles > 0;

        if (this.submitBtn) {
            this.submitBtn.disabled = !isValid;
            if (isValid) {
                this.submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                this.submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }

        return isValid;
    }

    resetForm() {
        if (!confirm('Are you sure you want to reset the form? All selections will be lost.')) return;

        this.userCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            this.updateUserOption(checkbox);
        });

        this.roleCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
            this.updateRoleOption(checkbox);
        });

        this.updateCounts();
        this.updatePreview();
        this.validateForm();

        // Clear search filters
        if (this.userSearch) this.userSearch.value = '';
        if (this.roleSearch) this.roleSearch.value = '';

        // Show all options
        document.querySelectorAll('.user-option, .role-option').forEach(option => {
            option.classList.remove('hidden');
        });
    }

    handleSubmit(e) {
        if (!this.validateForm()) {
            e.preventDefault();
            return;
        }

        const selectedUsers = Array.from(this.userCheckboxes).filter(cb => cb.checked).length;
        const selectedRoles = Array.from(this.roleCheckboxes).filter(cb => cb.checked).length;
        const operationMode = document.querySelector('input[name="operation_mode"]:checked').value;

        let message = `Assign ${selectedRoles} role${selectedRoles !== 1 ? 's' : ''} to ${selectedUsers} user${selectedUsers !== 1 ? 's' : ''}?`;

        if (operationMode === 'replace') {
            message += '\n\nWarning: This will replace existing roles for selected users.';
        }

        if (!confirm(message)) {
            e.preventDefault();
            return;
        }

        // Add loading state
        if (this.submitBtn) {
            this.submitBtn.innerHTML = `
                <svg class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Assigning...
            `;
            this.submitBtn.disabled = true;
        }
    }
}

// Global functions for search and selection
function filterUsers() {
    const searchTerm = document.getElementById('userSearch').value.toLowerCase();
    const userOptions = document.querySelectorAll('.user-option');

    userOptions.forEach(option => {
        const searchText = option.dataset.search;
        if (searchText.includes(searchTerm)) {
            option.classList.remove('hidden');
        } else {
            option.classList.add('hidden');
        }
    });
}

function filterRoles() {
    const searchTerm = document.getElementById('roleSearch').value.toLowerCase();
    const roleOptions = document.querySelectorAll('.role-option');

    roleOptions.forEach(option => {
        const searchText = option.dataset.search;
        if (searchText.includes(searchTerm)) {
            option.classList.remove('hidden');
        } else {
            option.classList.add('hidden');
        }
    });
}

function selectAllUsers() {
    const visibleUsers = Array.from(document.querySelectorAll('.user-option:not(.hidden)'));
    const allChecked = visibleUsers.every(option => {
        const checkbox = option.querySelector('.user-checkbox');
        return checkbox.checked;
    });

    visibleUsers.forEach(option => {
        const checkbox = option.querySelector('.user-checkbox');
        checkbox.checked = !allChecked;
        checkbox.dispatchEvent(new Event('change'));
    });
}

function clearAllUsers() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.dispatchEvent(new Event('change'));
    });
}

function selectAllRoles() {
    const visibleRoles = Array.from(document.querySelectorAll('.role-option:not(.hidden)'));
    const allChecked = visibleRoles.every(option => {
        const checkbox = option.querySelector('.role-checkbox');
        return checkbox.checked;
    });

    visibleRoles.forEach(option => {
        const checkbox = option.querySelector('.role-checkbox');
        checkbox.checked = !allChecked;
        checkbox.dispatchEvent(new Event('change'));
    });
}

function clearAllRoles() {
    document.querySelectorAll('.role-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.dispatchEvent(new Event('change'));
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new BulkRoleAssignment();
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

    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* User/Role option styling */
    .user-option, .role-option {
        transition: all 0.3s ease;
    }

    .user-option label, .role-option label {
        transition: all 0.3s ease;
    }

    .user-option:hover label:not(.border-[var(--accent-color)]) {
        border-color: var(--border-color);
        background: var(--bg-tertiary);
    }

    .role-option:hover label:not(.border-[var(--accent-color)]) {
        border-color: var(--border-color);
        background: var(--bg-tertiary);
    }

    /* Custom checkbox styling */
    .user-checkbox-display, .role-checkbox-display {
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .user-checkbox-display.checked, .role-checkbox-display.checked {
        background: var(--accent-color);
        border-color: var(--accent-color);
    }

    .checkbox-icon {
        width: 12px;
        height: 12px;
        color: white;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.2s ease;
    }

    .user-checkbox-display.checked .checkbox-icon,
    .role-checkbox-display.checked .checkbox-icon {
        opacity: 1;
        transform: scale(1);
    }

    /* User avatar */
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-color), var(--accent-hover));
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    /* Role users count */
    .role-users-count {
        min-width: 60px;
        text-align: right;
    }

    /* Scrollable containers */
    #usersContainer, #rolesContainer {
        scrollbar-width: thin;
        scrollbar-color: var(--border-color) transparent;
    }

    #usersContainer::-webkit-scrollbar,
    #rolesContainer::-webkit-scrollbar {
        width: 6px;
    }

    #usersContainer::-webkit-scrollbar-track,
    #rolesContainer::-webkit-scrollbar-track {
        background: var(--bg-tertiary);
        border-radius: 3px;
    }

    #usersContainer::-webkit-scrollbar-thumb,
    #rolesContainer::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }

    /* Loading animation */
    .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Radio button styling */
    input[type="radio"] {
        accent-color: var(--accent-color);
        width: 16px;
        height: 16px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .grid-cols-2 {
            grid-template-columns: 1fr;
        }

        .operation-mode {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .operation-mode .flex {
            width: 100%;
            justify-content: space-between;
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

        .py-3 {
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
        }

        .user-option label, .role-option label {
            padding: 12px;
        }
    }
</style>

@endsection
