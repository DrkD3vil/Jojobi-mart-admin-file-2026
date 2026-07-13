@extends('layouts.app')

@section('content')
    <div style="
        max-width: 28rem;
        margin: 2rem auto;
        padding: 0 1rem;
    ">
        <!-- Page Header -->
        <div style="
            text-align: center;
            margin-bottom: 2.5rem;
        ">
            <h1 style="
                font-size: 1.875rem;
                font-weight: 700;
                color: var(--foreground);
                margin-bottom: 0.5rem;
            ">
                Change Password
            </h1>
            <p style="
                color: var(--muted-foreground);
                font-size: 0.875rem;
            ">
                Update your account password securely
            </p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div style="
                padding: 1rem;
                margin-bottom: 1.5rem;
                border-radius: var(--radius);
                background-color: var(--success);
                color: white;
                border: 1px solid var(--success);
                display: flex;
                align-items: center;
                gap: 0.75rem;
            ">
                <i class="fas fa-check-circle" style="font-size: 1rem;"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Error Message -->
        @if (session('error'))
            <div style="
                padding: 1rem;
                margin-bottom: 1.5rem;
                border-radius: var(--radius);
                background-color: var(--danger);
                color: white;
                border: 1px solid var(--danger);
                display: flex;
                align-items: center;
                gap: 0.75rem;
            ">
                <i class="fas fa-exclamation-circle" style="font-size: 1rem;"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Form Container -->
        <div style="
            background-color: var(--card);
            border-radius: calc(var(--radius) + 0.25rem);
            padding: 2rem;
            box-shadow: var(--card-shadow);
            border: 1px solid var(--border);
        ">
            <form action="{{ route('password.change') }}" method="POST" style="
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            ">
                @csrf

                <!-- Current Password -->
                <div style="
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                ">
                    <label for="current_password" style="
                        font-size: 0.875rem;
                        font-weight: 500;
                        color: var(--foreground);
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                    ">
                        <i class="fas fa-lock" style="
                            color: var(--muted-foreground);
                            font-size: 0.75rem;
                        "></i>
                        Current Password
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               required
                               style="
                                   width: 100%;
                                   padding: 0.75rem 1rem 0.75rem 2.5rem;
                                   border-radius: var(--radius);
                                   border: 1px solid var(--border);
                                   background-color: var(--input);
                                   color: var(--foreground);
                                   font-size: 0.875rem;
                                   outline: none;
                                   transition: all var(--transition-fast);
                               "
                               onfocus="
                                   this.style.borderColor = 'var(--ring)';
                                   this.style.boxShadow = '0 0 0 3px var(--ring)';
                               "
                               onblur="
                                   this.style.borderColor = 'var(--border)';
                                   this.style.boxShadow = 'none';
                               ">
                        <i class="fas fa-key" style="
                            position: absolute;
                            left: 1rem;
                            top: 50%;
                            transform: translateY(-50%);
                            color: var(--muted-foreground);
                            font-size: 0.875rem;
                        "></i>
                    </div>
                    @error('current_password')
                        <div style="
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            margin-top: 0.25rem;
                            color: var(--danger);
                            font-size: 0.75rem;
                        ">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- New Password -->
                <div style="
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                ">
                    <label for="new_password" style="
                        font-size: 0.875rem;
                        font-weight: 500;
                        color: var(--foreground);
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                    ">
                        <i class="fas fa-lock" style="
                            color: var(--muted-foreground);
                            font-size: 0.75rem;
                        "></i>
                        New Password
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="new_password"
                               name="new_password"
                               required
                               style="
                                   width: 100%;
                                   padding: 0.75rem 1rem 0.75rem 2.5rem;
                                   border-radius: var(--radius);
                                   border: 1px solid var(--border);
                                   background-color: var(--input);
                                   color: var(--foreground);
                                   font-size: 0.875rem;
                                   outline: none;
                                   transition: all var(--transition-fast);
                               "
                               onfocus="
                                   this.style.borderColor = 'var(--ring)';
                                   this.style.boxShadow = '0 0 0 3px var(--ring)';
                               "
                               onblur="
                                   this.style.borderColor = 'var(--border)';
                                   this.style.boxShadow = 'none';
                               ">
                        <i class="fas fa-lock" style="
                            position: absolute;
                            left: 1rem;
                            top: 50%;
                            transform: translateY(-50%);
                            color: var(--muted-foreground);
                            font-size: 0.875rem;
                        "></i>
                    </div>
                    <div style="
                        margin-top: 0.25rem;
                        color: var(--text-muted);
                        font-size: 0.75rem;
                        line-height: 1.4;
                    ">
                        Password must be at least 8 characters long with uppercase, lowercase, numbers, and special characters.
                    </div>
                    @error('new_password')
                        <div style="
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            margin-top: 0.25rem;
                            color: var(--danger);
                            font-size: 0.75rem;
                        ">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div style="
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                ">
                    <label for="new_password_confirmation" style="
                        font-size: 0.875rem;
                        font-weight: 500;
                        color: var(--foreground);
                        display: flex;
                        align-items: center;
                        gap: 0.5rem;
                    ">
                        <i class="fas fa-lock" style="
                            color: var(--muted-foreground);
                            font-size: 0.75rem;
                        "></i>
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input type="password"
                               id="new_password_confirmation"
                               name="new_password_confirmation"
                               required
                               style="
                                   width: 100%;
                                   padding: 0.75rem 1rem 0.75rem 2.5rem;
                                   border-radius: var(--radius);
                                   border: 1px solid var(--border);
                                   background-color: var(--input);
                                   color: var(--foreground);
                                   font-size: 0.875rem;
                                   outline: none;
                                   transition: all var(--transition-fast);
                               "
                               onfocus="
                                   this.style.borderColor = 'var(--ring)';
                                   this.style.boxShadow = '0 0 0 3px var(--ring)';
                               "
                               onblur="
                                   this.style.borderColor = 'var(--border)';
                                   this.style.boxShadow = 'none';
                               ">
                        <i class="fas fa-check-circle" style="
                            position: absolute;
                            left: 1rem;
                            top: 50%;
                            transform: translateY(-50%);
                            color: var(--muted-foreground);
                            font-size: 0.875rem;
                        "></i>
                    </div>
                    @error('new_password_confirmation')
                        <div style="
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            margin-top: 0.25rem;
                            color: var(--danger);
                            font-size: 0.75rem;
                        ">
                            <i class="fas fa-exclamation-circle"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" style="
                    width: 100%;
                    padding: 0.875rem 1.5rem;
                    margin-top: 1rem;
                    background-color: var(--primary);
                    color: var(--primary-foreground);
                    border: none;
                    border-radius: var(--radius);
                    font-size: 0.875rem;
                    font-weight: 500;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.75rem;
                    transition: all var(--transition-fast);
                "
                onmouseover="
                    this.style.backgroundColor = 'var(--primary)';
                    this.style.opacity = '0.9';
                    this.style.transform = 'translateY(-1px)';
                "
                onmouseout="
                    this.style.opacity = '1';
                    this.style.transform = 'translateY(0)';
                "
                onfocus="
                    this.style.boxShadow = '0 0 0 3px var(--ring)';
                "
                onblur="
                    this.style.boxShadow = 'none';
                ">
                    <i class="fas fa-key"></i>
                    Change Password
                </button>
            </form>
        </div>

        <!-- Security Tips -->
        <div style="
            margin-top: 2rem;
            padding: 1.25rem;
            background-color: var(--accent);
            border-radius: var(--radius);
            border: 1px solid var(--border);
        ">
            <div style="
                display: flex;
                align-items: center;
                gap: 0.75rem;
                margin-bottom: 0.75rem;
            ">
                <i class="fas fa-shield-alt" style="
                    color: var(--info);
                    font-size: 1rem;
                "></i>
                <h3 style="
                    font-size: 0.875rem;
                    font-weight: 600;
                    color: var(--foreground);
                ">
                    Password Security Tips
                </h3>
            </div>
            <ul style="
                list-style: none;
                padding: 0;
                margin: 0;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            ">
                <li style="
                    display: flex;
                    align-items: flex-start;
                    gap: 0.5rem;
                    font-size: 0.75rem;
                    color: var(--muted-foreground);
                ">
                    <i class="fas fa-check" style="
                        color: var(--success);
                        font-size: 0.5rem;
                        margin-top: 0.25rem;
                    "></i>
                    Use a unique password not used elsewhere
                </li>
                <li style="
                    display: flex;
                    align-items: flex-start;
                    gap: 0.5rem;
                    font-size: 0.75rem;
                    color: var(--muted-foreground);
                ">
                    <i class="fas fa-check" style="
                        color: var(--success);
                        font-size: 0.5rem;
                        margin-top: 0.25rem;
                    "></i>
                    Include numbers, symbols, and mixed case letters
                </li>
                <li style="
                    display: flex;
                    align-items: flex-start;
                    gap: 0.5rem;
                    font-size: 0.75rem;
                    color: var(--muted-foreground);
                ">
                    <i class="fas fa-check" style="
                        color: var(--success);
                        font-size: 0.5rem;
                        margin-top: 0.25rem;
                    "></i>
                    Avoid using personal information like birthdates
                </li>
            </ul>
        </div>
    </div>
@endsection
