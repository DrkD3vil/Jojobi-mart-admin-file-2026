@extends('layouts.app')

@section('title', 'Home Page')

@section('content')



    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Using existing CSS variables from the previous dashboard */
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
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
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

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -468px 0;
            }

            100% {
                background-position: 468px 0;
            }
        }

        /* Card Styles */
        .card {
            background-color: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: all var(--transition-normal) cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-shadow-hover);
            border-color: var(--primary);
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--foreground);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--info));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Profile Avatar */
        .profile-avatar {
            text-align: center;
            margin-bottom: 2rem;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 1rem;
            background: linear-gradient(135deg, var(--primary), var(--info));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 600;
            color: white;
            border: 4px solid var(--primary);
            box-shadow: 0 4px 20px var(--primary) / 0.3;
            animation: pulse 2s infinite;
        }

        .profile-name {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary), var(--info));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .profile-role {
            display: inline-block;
            padding: 0.25rem 1rem;
            background-color: var(--primary) / 0.1;
            color: var(--primary);
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            border: 1px solid var(--primary) / 0.3;
        }

        /* Info Items */
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.25rem;
            padding: 0.75rem;
            border-radius: var(--radius);
            transition: all var(--transition-fast) ease;
        }

        .info-item:hover {
            background-color: var(--accent);
            transform: translateX(5px);
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background-color: var(--primary) / 0.1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
        }

        .info-content {
            flex: 1;
        }

        .info-content h4 {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--muted-foreground);
            margin-bottom: 0.25rem;
        }

        .info-content p {
            font-size: 1rem;
            font-weight: 500;
            color: var(--foreground);
        }

        /* Role & Permissions */
        .role-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--primary), var(--info));
            color: white;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .role-description {
            color: var(--muted-foreground);
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .permissions-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .permissions-list span {
            padding: 0.375rem 0.75rem;
            background-color: var(--success) / 0.1;
            color: var(--success);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid var(--success) / 0.3;
        }

        /* Stats */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 768px) {
            .stats-container {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            border-radius: var(--radius);
            background-color: var(--accent);
            transition: all var(--transition-normal) ease;
        }

        .stat-item:hover {
            transform: translateY(-3px);
            background-color: var(--primary) / 0.1;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--muted-foreground);
        }

        .progress-bar {
            height: 10px;
            background-color: var(--border);
            border-radius: 5px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--info));
            border-radius: 5px;
            transition: width 1s ease;
        }

        /* Grid Layout */
        .grid-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (min-width: 768px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .grid-container {
                grid-template-columns: repeat(4, 1fr);
            }

            .profile-card {
                grid-column: span 1;
            }

            .info-card {
                grid-column: span 1;
            }

            .role-card {
                grid-column: span 2;
            }

            .stats-card {
                grid-column: span 2;
            }
        }

        @media (min-width: 1280px) {
            .grid-container {
                grid-template-columns: repeat(4, 1fr);
            }

            .profile-card {
                grid-column: span 1;
            }

            .info-card {
                grid-column: span 1;
            }

            .role-card {
                grid-column: span 1;
            }

            .stats-card {
                grid-column: span 1;
            }
        }

        /* Action Buttons */
        .actions-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }

        @media (min-width: 640px) {
            .actions-container {
                flex-direction: row;
                justify-content: center;
            }
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all var(--transition-normal) ease;
            border: none;
            font-size: 0.875rem;
            text-decoration: none;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Enhanced Button Styles */
        .btn-primary {
            background-color: var(--sidebar-primary);
            color: var(--sidebar-primary-foreground);
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-weight: 500;
            transition: all var(--transition-normal) ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid transparent;
        }

        .btn-primary:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--accent-glow);
            border-color: var(--sidebar-primary);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: var(--secondary-foreground);
        }

        .btn-secondary:hover {
            background-color: var(--secondary) / 0.9;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: var(--danger) / 0.9;
        }

        /* Animation Classes */
        .fade-in-up {
            opacity: 0;
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .fade-in {
            opacity: 0;
            animation: fadeIn 0.5s ease-out forwards;
        }

        .slide-in-left {
            opacity: 0;
            animation: slideInLeft 0.5s ease-out forwards;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary) / 0.8;
        }

        /* Loading Shimmer */
        .shimmer {
            background: linear-gradient(90deg,
                    var(--card) 0%,
                    var(--accent) 50%,
                    var(--card) 100%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
        }



        /* Custom Avater Css */
        @keyframes avatar-pulse {

            0%,
            100% {
                box-shadow: 0 8px 25px var(--primary) / 0.3;
            }

            50% {
                box-shadow: 0 8px 35px var(--primary) / 0.5,
                    0 0 0 8px var(--primary) / 0.1;
            }
        }

        #user-avatar:hover {
            transform: scale(1.05) rotate(5deg);
            border-color: var(--info);
            animation: none;
        }

        #user-avatar:hover::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg,
                    var(--primary) 0%,
                    var(--info) 50%,
                    var(--primary) 100%);
            border-radius: 50%;
            z-index: -1;
            animation: rotate-gradient 2s linear infinite;
        }

        @keyframes rotate-gradient {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- Main Grid Layout -->
    <div class="grid-container">
        <!-- Profile Card -->
        <div class="card profile-card fade-in-up" style="animation-delay: 0.1s">
            <div class="card-header">
                <h2 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    Profile Overview
                </h2>
            </div>

            <div class="profile-avatar">
                <div class="avatar" id="user-avatar"
                    style="
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid var(--primary);
        background: linear-gradient(135deg, var(--primary) 0%, var(--info) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px var(--primary) / 0.3;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        animation: avatar-pulse 3s infinite ease-in-out;
     ">

                    @if ($user->kycDetail?->profile_image)
                        <img src="{{ asset('storage/' . $user->kycDetail->profile_image) }}" alt="Profile Picture"
                            style="
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
             "
                            onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    @else
                        <div
                            style="
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--info) 100%);
            color: white;
            font-size: 2.5rem;
            font-weight: 600;
            text-transform: uppercase;
        ">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                    @endif

                    <style>

                    </style>
                </div>
                <h1 class="profile-name" id="user-name">{{ $user->name }}</h1>
                <div class="profile-role" id="user-role-display">
                    @forelse ($user->roles as $role)
                        <span class="badge bg-primary">{{ $role->name }}</span>
                    @empty
                        <span class="text-muted">No role assigned</span>
                    @endforelse
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="info-content">
                    <h4>Email Address</h4>
                    <p id="user-email">{{ $user->email }}</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="info-content">
                    <h4>Member Since</h4>
                    <p id="member-since">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Personal Information Card -->
        <div class="card info-card fade-in-up" style="animation-delay: 0.2s">
            <div class="card-header">
                <h2 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    Personal Information
                </h2>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="info-content">
                    <h4>Full Name</h4>
                    <p id="full-name">{{ $user->name }}</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-phone"></i>
                </div>
                <div class="info-content">
                    <h4>Phone Number</h4>
                    <p id="user-phone">{{ $user->kycDetail?->phone }}</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="info-content">
                    <h4>Location</h4>
                    <p id="user-location">{{ $user->kycDetail?->city }}</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="info-content">
                    <h4>Date of Birth</h4>
                    <p id="user-department">{{ $user->kycDetail?->date_of_birth }}</p>
                </div>
            </div>
        </div>

        <!-- Role Card -->
        <div class="card role-card fade-in-up" style="animation-delay: 0.3s">
            <div class="card-header">
                <h2 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    Role & Permissions
                </h2>
            </div>

            <div>
                <div class="role-badge" id="role-badge">

                    @forelse ($user->roles as $role)
                        <span class="badge bg-primary">{{ $role->name }}</span>
                    @empty
                        <span class="text-muted">No role assigned</span>
                    @endforelse
                </div>
                <p class="role-description" id="role-description">
                    As an administrator, you have full access to all system features including user management,
                    configuration settings, and data analytics.
                </p>
            </div>

            <div style="margin-top: 20px;">
                <h3 style="font-size: 1rem; margin-bottom: 10px; color: var(--foreground);">Permissions:</h3>
<div class="permissions-list" id="permissions-list">
    @forelse ($permissions as $permission)
        <span>{{ $permission->slug ?? $permission->name }}</span>
    @empty
        <span class="text-muted">No privileges assigned</span>
    @endforelse
</div>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card stats-card fade-in-up" style="animation-delay: 0.4s">
            <div class="card-header">
                <h2 class="card-title">
                    <div class="card-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    Activity Stats
                </h2>
            </div>

            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-value" id="projects-count">24</div>
                    <div class="stat-label">Projects</div>
                </div>

                <div class="stat-item">
                    <div class="stat-value" id="tasks-count">127</div>
                    <div class="stat-label">Tasks Completed</div>
                </div>

                <div class="stat-item">
                    <div class="stat-value" id="hours-count">342</div>
                    <div class="stat-label">Hours Logged</div>
                </div>

                <div class="stat-item">
                    <div class="stat-value" id="rating">4.8</div>
                    <div class="stat-label">Performance Rating</div>
                </div>
            </div>

            <div style="margin-top: 25px;">
                <h3 style="font-size: 1rem; margin-bottom: 10px; color: var(--foreground);">Activity Level:</h3>
                <div class="progress-bar">
                    <div class="progress-fill" id="activity-bar" style="width: 75%"></div>
                </div>
                <div
                    style="display: flex; justify-content: space-between; margin-top: 5px; font-size: 0.8rem; color: var(--muted-foreground);">
                    <span>Low</span>
                    <span>High</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="actions-container">
        <button class="btn btn-secondary" id="edit-profile-btn"
            onclick="window.location.href = '{{ route('profile.edit') }}'">
            <i class="fas fa-edit"></i> Edit Profile
        </button>

        <button class="btn btn-secondary" id="change-password-btn">
            <i class="fas fa-key"></i> Change Password
        </button>

        <button class="btn btn-secondary" id="email-verification-btn">
            <i class="fas fa-bell"></i> Email Verification
        </button>

        <button class="btn btn-danger" id="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>

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

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Smooth animations on load
        document.addEventListener('DOMContentLoaded', () => {
            // Add staggered animation to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${(index + 1) * 0.1}s`;
                card.classList.add('fade-in-up');
            });

            // Add hover effects to interactive elements
            const interactiveElements = document.querySelectorAll('.info-item, .stat-item, .btn');
            interactiveElements.forEach(element => {
                element.addEventListener('mouseenter', () => {
                    element.style.transform = 'translateY(-2px)';
                });
                element.addEventListener('mouseleave', () => {
                    element.style.transform = 'translateY(0)';
                });
            });

            // Animate stats on scroll into view
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const card = entry.target;
                        card.classList.add('fade-in-up');

                        // Animate stats if it's the stats card
                        if (card.classList.contains('stats-card')) {
                            animateStats();
                        }
                    }
                });
            }, observerOptions);

            cards.forEach(card => observer.observe(card));


            // Button click animations
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.7);
                        transform: scale(0);
                        animation: ripple-animation 0.6s linear;
                        width: ${size}px;
                        height: ${size}px;
                        top: ${y}px;
                        left: ${x}px;
                        pointer-events: none;
                    `;

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple-animation {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        });

        // Animate stats with counting effect
        function animateStats() {
            const stats = [{
                    id: 'projects-count',
                    target: 24,
                    duration: 1000
                },
                {
                    id: 'tasks-count',
                    target: 127,
                    duration: 1500
                },
                {
                    id: 'hours-count',
                    target: 342,
                    duration: 2000
                },
                {
                    id: 'rating',
                    target: 4.8,
                    duration: 1000
                }
            ];

            stats.forEach(stat => {
                const element = document.getElementById(stat.id);
                if (!element) return;

                const startValue = 0;
                const endValue = stat.target;
                const duration = stat.duration;
                const startTime = performance.now();

                function updateValue(currentTime) {
                    const elapsedTime = currentTime - startTime;
                    const progress = Math.min(elapsedTime / duration, 1);

                    // Easing function for smooth animation
                    const easeOutQuart = 1 - Math.pow(1 - progress, 4);

                    let currentValue;
                    if (stat.id === 'rating') {
                        currentValue = (startValue + (endValue - startValue) * easeOutQuart).toFixed(1);
                    } else {
                        currentValue = Math.floor(startValue + (endValue - startValue) * easeOutQuart);
                    }

                    element.textContent = currentValue + (stat.id === 'rating' ? '' : '');

                    if (progress < 1) {
                        requestAnimationFrame(updateValue);
                    }
                }

                requestAnimationFrame(updateValue);
            });

            // Animate progress bar
            const progressBar = document.getElementById('activity-bar');
            if (progressBar) {
                progressBar.style.width = '0%';
                setTimeout(() => {
                    progressBar.style.transition = 'width 1.5s ease-in-out';
                    progressBar.style.width = '75%';
                }, 500);
            }
        }

        // Handle responsive menu if needed
        function handleResponsiveMenu() {
            const menuBtn = document.getElementById('menu-toggle');
            const sidebar = document.getElementById('sidebar');

            if (menuBtn && sidebar) {
                menuBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                });
            }
        }

        // Initialize on load
        window.addEventListener('load', () => {
            // Trigger stats animation on page load
            setTimeout(animateStats, 500);

            // Handle responsive menu
            handleResponsiveMenu();

            // Add loading animation for data
            const loadingElements = document.querySelectorAll('.shimmer');
            loadingElements.forEach(element => {
                setTimeout(() => {
                    element.classList.remove('shimmer');
                }, 1000);
            });
        });
    </script>



@endsection
