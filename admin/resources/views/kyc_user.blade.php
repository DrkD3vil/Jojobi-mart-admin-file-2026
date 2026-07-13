@extends('layouts.app')

@section('title', 'Profile Update')

@section('content')

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Using your provided color palette */
        :root {
            --radius: 0.625rem;
            --transition-fast: 150ms;
            --transition-normal: 250ms;
            --transition-slow: 350ms;
            --background: oklch(0.145 0 0);
            --foreground: oklch(0.985 0 0);
            --card: oklch(0.205 0 0);
            --card-foreground: oklch(0.985 0 0);
            --primary: oklch(0.922 0 0);
            --primary-foreground: oklch(0.205 0 0);
            --secondary: oklch(0.269 0 0);
            --secondary-foreground: oklch(0.985 0 0);
            --muted: oklch(0.269 0 0);
            --muted-foreground: oklch(0.708 0 0);
            --accent: oklch(0.269 0 0);
            --accent-foreground: oklch(0.985 0 0);
            --destructive: oklch(0.704 0.191 22.216);
            --border: oklch(1 0 0 / 15%);
            --input: oklch(1 0 0 / 15%);
            --ring: oklch(0.556 0 0);
            --sidebar-primary: oklch(0.488 0.243 264.376);
            --success: oklch(0.696 0.17 162.48);
            --warning: oklch(0.769 0.188 70.08);
            --info: oklch(0.488 0.243 264.376);
            --danger: oklch(0.704 0.191 22.216);
            --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.25);
            --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.35), 0 3px 6px -2px rgb(0 0 0 / 0.25);
            --accent-color: var(--sidebar-primary);
            --text-primary: var(--foreground);
            --text-secondary: var(--muted-foreground);
            --border-color: var(--border);
        }

        html[data-theme='light'] {
            --background: oklch(0.99 0 0);
            --foreground: oklch(0.12 0 0);
            --card: oklch(1 0 0);
            --card-foreground: oklch(0.12 0 0);
            --primary: oklch(0.15 0 0);
            --primary-foreground: oklch(0.99 0 0);
            --secondary: oklch(0.97 0 0);
            --secondary-foreground: oklch(0.15 0 0);
            --muted: oklch(0.96 0 0);
            --muted-foreground: oklch(0.5 0 0);
            --accent: oklch(0.96 0 0);
            --accent-foreground: oklch(0.15 0 0);
            --destructive: oklch(0.577 0.245 27.325);
            --border: oklch(0.9 0 0);
            --input: oklch(0.96 0 0);
            --ring: oklch(0.65 0 0);
            --sidebar-primary: oklch(0.646 0.222 41.116);
            --success: oklch(0.627 0.194 149.214);
            --warning: oklch(0.769 0.188 70.08);
            --info: oklch(0.623 0.214 259.815);
            --danger: oklch(0.577 0.245 27.325);
            --card-shadow: 0 2px 4px 0 rgb(0 0 0 / 0.08);
            --card-shadow-hover: 0 6px 12px -1px rgb(0 0 0 / 0.12), 0 3px 6px -2px rgb(0 0 0 / 0.08);
        }

        /* Base Styles */
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background);
            color: var(--foreground);
            transition: all var(--transition-normal) ease;
            min-height: 100vh;
            padding: 1rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes shimmer {
            0% { background-position: -468px 0; }
            100% { background-position: 468px 0; }
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Form Container */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            animation: fadeIn 0.5s ease-out;
        }

        /* Header */
        .form-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border);
        }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--sidebar-primary), var(--info));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Alert Messages */
        .alert {
            padding: 1rem 1.25rem;
            border-radius: var(--radius);
            margin-bottom: 1.5rem;
            border: 1px solid transparent;
            animation: fadeInUp 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background-color: var(--success) / 0.1;
            border-color: var(--success) / 0.3;
            color: var(--success);
        }

        .alert-warning {
            background-color: var(--warning) / 0.1;
            border-color: var(--warning) / 0.3;
            color: var(--warning);
        }

        /* Fieldset Styling */
        .form-section {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            background: var(--card);
            transition: all var(--transition-normal) ease;
            animation: fadeInUp 0.4s ease-out;
        }

        .form-section:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
            border-color: var(--sidebar-primary);
        }

        .section-legend {
            font-size: 1.125rem;
            font-weight: 600;
            padding: 0 0.75rem;
            margin-bottom: 1rem;
            color: var(--sidebar-primary);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 1.25rem;
            animation: fadeIn 0.5s ease-out;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--foreground);
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            background-color: var(--input);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--foreground);
            font-size: 0.95rem;
            transition: all var(--transition-fast) ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--sidebar-primary);
            box-shadow: 0 0 0 3px var(--sidebar-primary) / 0.2;
            transform: translateY(-1px);
        }

        .form-input:hover:not(:focus) {
            border-color: var(--accent-foreground) / 0.3;
        }

        /* Readonly Email Input */
        .email-readonly {
            background-color: var(--muted);
            color: var(--muted-foreground);
            cursor: not-allowed;
            border: 1px solid var(--border);
        }

        .email-readonly:focus {
            box-shadow: none;
            transform: none;
        }

        .email-tooltip {
            position: relative;
            display: inline-block;
        }

        .email-tooltip:hover::after {
            content: 'Email cannot be changed for security reasons';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--accent);
            color: var(--foreground);
            padding: 0.5rem 0.75rem;
            border-radius: var(--radius);
            font-size: 0.75rem;
            white-space: nowrap;
            z-index: 10;
            margin-bottom: 0.5rem;
            animation: fadeIn 0.2s ease-out;
        }

        /* File Upload */
        .file-upload-container {
            border: 2px dashed var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            text-align: center;
            transition: all var(--transition-normal) ease;
            cursor: pointer;
        }

        .file-upload-container:hover {
            border-color: var(--sidebar-primary);
            background-color: var(--sidebar-primary) / 0.05;
            transform: translateY(-2px);
        }

        .file-upload-container.dragover {
            border-color: var(--success);
            background-color: var(--success) / 0.1;
            animation: pulse 0.5s ease;
        }

        .profile-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .avatar-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--sidebar-primary);
            position: relative;
            animation: float 3s ease-in-out infinite;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .avatar-preview:hover img {
            transform: scale(1.1);
        }

        .file-input {
            display: none;
        }

        .file-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background-color: var(--sidebar-primary);
            color: white;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all var(--transition-fast) ease;
            font-weight: 500;
        }

        .file-label:hover {
            background-color: var(--sidebar-primary) / 0.9;
            transform: translateY(-2px);
        }

        /* Textarea */
        .form-textarea {
            width: 100%;
            min-height: 80px;
            padding: 0.875rem 1rem;
            background-color: var(--input);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--foreground);
            font-size: 0.95rem;
            resize: vertical;
            transition: all var(--transition-fast) ease;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--sidebar-primary);
            box-shadow: 0 0 0 3px var(--sidebar-primary) / 0.2;
            min-height: 100px;
        }

        /* Select */
        .form-select {
            width: 100%;
            padding: 0.875rem 1rem;
            background-color: var(--input);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--foreground);
            font-size: 0.95rem;
            transition: all var(--transition-fast) ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
        }

        .form-select:focus {
            outline: none;
            border-color: var(--sidebar-primary);
            box-shadow: 0 0 0 3px var(--sidebar-primary) / 0.2;
        }

        /* JSON Metadata */
        .json-container {
            background-color: var(--accent);
            border-radius: var(--radius);
            padding: 1rem;
            margin-top: 1rem;
        }

        .json-preview {
            background-color: var(--background);
            border-radius: calc(var(--radius) - 2px);
            padding: 1rem;
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
            max-height: 200px;
            overflow-y: auto;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--sidebar-primary), var(--info));
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-normal) ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px var(--sidebar-primary) / 0.3;
        }

        .submit-btn:active {
            transform: translateY(-1px);
        }

        .submit-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .submit-btn.loading::after {
            content: '';
            width: 20px;
            height: 20px;
            border: 3px solid transparent;
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Error Messages */
        .error-message {
            color: var(--danger);
            font-size: 0.875rem;
            margin-top: 0.5rem;
            animation: fadeIn 0.2s ease-out;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Verified Badge */
        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            background-color: var(--success) / 0.1;
            color: var(--success);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: 0.5rem;
            border: 1px solid var(--success) / 0.3;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .form-container {
                padding: 1rem;
            }

            .form-section {
                padding: 1rem;
            }

            .form-header {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }

            .json-container {
                overflow-x: auto;
            }
        }

        @media (max-width: 480px) {
            .form-input,
            .form-select,
            .form-textarea {
                padding: 0.75rem;
                font-size: 16px; /* Prevents zoom on iOS */
            }

            .submit-btn {
                padding: 0.875rem 1rem;
                font-size: 0.95rem;
            }
        }

        /* Custom Animations */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes checkmark {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Ripple Effect */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: scale(0);
            animation: ripple 0.6s linear;
            pointer-events: none;
        }
    </style>

        <!-- Header -->
        <div class="form-header">
            <i data-lucide="user-circle" class="w-8 h-8" style="color: var(--sidebar-primary);"></i>
            <h2>User Profile & KYC Update</h2>
        </div>

        <!-- Status Message -->
        @if (session('success'))
            <div class="alert alert-success">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- Email Verification Alert -->
        @if (! $user->hasVerifiedEmail())
            <div class="alert alert-warning">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>⚠️ <strong>Your email address is unverified.</strong> If you changed your email, please check your inbox for the verification link.</span>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
            @csrf

            <!-- Section 1: Basic User Data -->
            <fieldset class="form-section" style="animation-delay: 0.1s">
                <legend class="section-legend">
                    <i data-lucide="user"></i>
                    User Account Details
                </legend>

                <div class="form-group">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $user->name) }}"
                           required class="form-input"
                           placeholder="Enter your full name">
                    @error('name')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="email-tooltip">
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               readonly class="form-input email-readonly"
                               placeholder="Your email address">
                    </div>
                    <small style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Email cannot be changed for security reasons
                    </small>
                    @error('email')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </fieldset>

            <!-- Section 2: KYC & Profile Data -->
            <fieldset class="form-section" style="animation-delay: 0.2s">
                <legend class="section-legend">
                    <i data-lucide="shield-check"></i>
                    KYC and Personal Information
                </legend>

                <!-- Profile Image -->
                <div class="form-group">
                    <label class="form-label">Profile Image</label>

                    <div class="profile-preview">
                        @if($user->kycDetail?->profile_image)
                            <div class="avatar-preview">
                                <img src="{{ asset('storage/' . $user->kycDetail->profile_image) }}"
                                     alt="Profile Picture"
                                     id="avatar-preview">
                            </div>
                        @else
                            <div class="avatar-preview" style="background: linear-gradient(135deg, var(--sidebar-primary), var(--info));">
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 600;">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="file-upload-container" id="file-upload-area">
                        <input type="file" id="profile_image" name="profile_image" class="file-input" accept="image/*">
                        <label for="profile_image" class="file-label">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            Choose New Profile Picture
                        </label>
                        <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.5rem;">
                            Maximum file size: 2MB. Supported formats: JPG, PNG, GIF
                        </p>
                    </div>

                    @error('profile_image')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" id="phone" name="phone"
                           value="{{ old('phone', $user->kycDetail?->phone) }}"
                           class="form-input"
                           placeholder="+1 (555) 123-4567">
                    @error('phone')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    @if ($user->kycDetail?->phone_verified_at)
                        <span class="verified-badge">
                            <i data-lucide="check" class="w-3 h-3"></i>
                            Verified
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-select">
                        @php $selectedGender = old('gender', $user->kycDetail?->gender); @endphp
                        <option value="">Select Gender</option>
                        <option value="male" @selected($selectedGender == 'male')>Male</option>
                        <option value="female" @selected($selectedGender == 'female')>Female</option>
                        <option value="other" @selected($selectedGender == 'other')>Other</option>
                    </select>
                    @error('gender')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                           value="{{ old('date_of_birth', $user->kycDetail?->date_of_birth?->format('Y-m-d')) }}"
                           class="form-input">
                    @error('date_of_birth')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city"
                           value="{{ old('city', $user->kycDetail?->city) }}"
                           class="form-input"
                           placeholder="Enter your city">
                    @error('city')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_1" class="form-label">Address Line 1</label>
                    <textarea id="address_1" name="address_1"
                              class="form-textarea"
                              placeholder="Enter your primary address">{{ old('address_1', $user->kycDetail?->address_1) }}</textarea>
                    @error('address_1')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address_2" class="form-label">Address Line 2 (Optional)</label>
                    <textarea id="address_2" name="address_2"
                              class="form-textarea"
                              placeholder="Apartment, suite, unit, building, floor, etc.">{{ old('address_2', $user->kycDetail?->address_2) }}</textarea>
                    @error('address_2')
                        <div class="error-message">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            </fieldset>


            <!-- Section: Access Keys -->
<fieldset class="form-section" style="animation-delay: 0.3s">
    <legend class="section-legend">
        <i data-lucide="key"></i>
        Access Permissions
    </legend>

    <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1.5rem;">
        View the access permissions granted to this user.
    </p>

    <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--foreground);">
        Current Access Keys:
    </h4>

    <div class="access-key-container">
        <ul>
            @forelse($accessKeys as $mapping)
                <li>
                    <strong>{{ $mapping->access_key }}</strong>
                    (Privilege: {{ $mapping->privilege->name }})
                </li>
            @empty
                <li>No access keys assigned</li>
            @endforelse
        </ul>
    </div>
</fieldset>


            <!-- Section 3: JSON Metadata -->
            <fieldset class="form-section" style="animation-delay: 0.3s">
                <legend class="section-legend">
                    <i data-lucide="database"></i>
                    Extra Data (JSON/Metadata)
                </legend>

                <p style="color: var(--text-secondary); font-size: 0.875rem; margin-bottom: 1.5rem;">
                    Add one custom key-value pair to the flexible JSON column (existing data below).
                </p>

                <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="flex: 1;">
                        <label for="custom_json_key" class="form-label">Custom Key</label>
                        <input type="text" id="custom_json_key" name="custom_json_key"
                               value="{{ old('custom_json_key') }}"
                               class="form-input"
                               placeholder="e.g., favorite_color, occupation, hobby">
                        @error('custom_json_key')
                            <div class="error-message">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div style="flex: 1;">
                        <label for="custom_json_value" class="form-label">Custom Value</label>
                        <input type="text" id="custom_json_value" name="custom_json_value"
                               value="{{ old('custom_json_value') }}"
                               class="form-input"
                               placeholder="e.g., blue, software_developer, hiking">
                        @error('custom_json_value')
                            <div class="error-message">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <h4 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--foreground);">
                    Current Metadata:
                </h4>
                <div class="json-container">
                    <pre class="json-preview" id="json-preview">
                        {{ json_encode($user->kycDetail?->metadata, JSON_PRETTY_PRINT) }}
                    </pre>
                </div>
            </fieldset>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submit-btn">
                <i data-lucide="save" class="w-5 h-5"></i>
                Update Profile
            </button>
        </form>


    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Ripple effect for buttons
        function createRipple(event) {
            const button = event.currentTarget;
            const circle = document.createElement("span");
            const diameter = Math.max(button.clientWidth, button.clientHeight);
            const radius = diameter / 2;

            circle.style.width = circle.style.height = `${diameter}px`;
            circle.style.left = `${event.clientX - button.getBoundingClientRect().left - radius}px`;
            circle.style.top = `${event.clientY - button.getBoundingClientRect().top - radius}px`;
            circle.classList.add("ripple");

            const ripple = button.getElementsByClassName("ripple")[0];
            if (ripple) {
                ripple.remove();
            }

            button.appendChild(circle);
        }

        // Add ripple effect to submit button
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.addEventListener('click', createRipple);
        }

        // File upload preview
        const fileInput = document.getElementById('profile_image');
        const avatarPreview = document.getElementById('avatar-preview');
        const fileUploadArea = document.getElementById('file-upload-area');

        if (fileInput && avatarPreview) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) { // 10MB limit
                        alert('File size must be less than 10MB');
                        this.value = '';
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        avatarPreview.src = e.target.result;
                        avatarPreview.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            avatarPreview.style.transform = 'scale(1)';
                        }, 300);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // Drag and drop for file upload
        if (fileUploadArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                fileUploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                fileUploadArea.classList.add('dragover');
            }

            function unhighlight() {
                fileUploadArea.classList.remove('dragover');
            }

            fileUploadArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        }

        // Form submission animation
        const form = document.getElementById('profile-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Updating Profile...';
                    lucide.createIcons();
                }

                // Allow form submission to proceed
                return true;
            });
        }

        // Animate form sections on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.form-section').forEach(section => {
            section.style.animationPlayState = 'paused';
            observer.observe(section);
        });

        // Input focus effects
        const inputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });

        // Auto-resize textareas
        const textareas = document.querySelectorAll('.form-textarea');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        });

        // Theme detection and toggle
        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        const currentTheme = localStorage.getItem('theme') ||
                           (prefersDarkScheme.matches ? 'dark' : 'light');

        document.documentElement.setAttribute('data-theme', currentTheme);

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                submitBtn.click();
            }

            // Escape to blur input
            if (e.key === 'Escape') {
                document.activeElement.blur();
            }
        });

        // Initialize JSON syntax highlighting
        const jsonPreview = document.getElementById('json-preview');
        if (jsonPreview) {
            const jsonText = jsonPreview.textContent;
            if (jsonText.trim() !== 'null') {
                try {
                    const jsonObj = JSON.parse(jsonText);
                    const highlighted = JSON.stringify(jsonObj, null, 2)
                        .replace(/(".*?"):/g, '<span style="color: var(--sidebar-primary);">$1</span>:')
                        .replace(/: ".*?"/g, ': <span style="color: var(--success);">$&</span>')
                        .replace(/: \d+/g, ': <span style="color: var(--warning);">$&</span>')
                        .replace(/true|false/g, '<span style="color: var(--info);">$&</span>');
                    jsonPreview.innerHTML = highlighted;
                } catch (e) {
                    // Leave as plain text if invalid JSON
                }
            }
        }

        // Real-time validation for phone number
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                const phoneValue = this.value.replace(/\D/g, '');
                if (phoneValue.length > 10) {
                    this.style.borderColor = 'var(--danger)';
                } else {
                    this.style.borderColor = '';
                }
            });
        }

        // Add character counter for textareas
        const addressTextareas = document.querySelectorAll('#address_1, #address_2');
        addressTextareas.forEach(textarea => {
            const counter = document.createElement('div');
            counter.style.cssText = `
                font-size: 0.75rem;
                color: var(--text-secondary);
                text-align: right;
                margin-top: 0.25rem;
            `;
            textarea.parentNode.insertBefore(counter, textarea.nextSibling);

            function updateCounter() {
                const count = textarea.value.length;
                counter.textContent = `${count} characters`;
                if (count > 200) {
                    counter.style.color = 'var(--warning)';
                } else {
                    counter.style.color = 'var(--text-secondary)';
                }
            }

            textarea.addEventListener('input', updateCounter);
            updateCounter();
        });
    </script>
@endsection
