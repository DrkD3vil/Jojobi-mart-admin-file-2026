

        @extends('layouts.app')

@section('title', 'Home Page')

@section('content')


        <!-- Header with theme toggle -->


        <!-- Main Grid Layout -->
        <div class="grid-container">
            <!-- Profile Card -->
            <div class="card profile-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <div class="card-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        Profile Overview
                    </h2>
                </div>

                <div class="profile-avatar">
                    <div class="avatar" id="user-avatar">
                        <!-- Initials will be filled by JavaScript -->
                    </div>
                    <h1 class="profile-name" id="user-name">{{ $user->name }}</h1>
                    <div class="profile-role" id="user-role-display">{{ $user->role }}</div>
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
            <div class="card info-card">
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
                        <p id="user-phone">+1 (555) 123-4567</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>Location</h4>
                        <p id="user-location">New York, USA</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="info-content">
                        <h4>Department</h4>
                        <p id="user-department">Engineering</p>
                    </div>
                </div>
            </div>

            <!-- Role Card -->
            <div class="card role-card">
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
                        @forelse($user->roles as $role)
                            <span class="role-badge">{{ $role->name }}</span>
                        @empty
                            <span>No role assigned</span>
                        @endforelse
                    </div>
                    <p class="role-description" id="role-description">
                        As an administrator, you have full access to all system features including user management,
                        configuration settings, and data analytics.
                    </p>
                </div>

                <div style="margin-top: 20px;">
                    <h3 style="font-size: 1rem; margin-bottom: 10px; color: var(--text-color);">Permissions:</h3>
                    <div class="permissions-list" id="permissions-list">
                        @forelse($permissions as $permission)
                            <span class="badge bg-success">{{ $permission->name }}</span>
                        @empty
                            <span>No permissions assigned</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Stats Card -->
            <div class="card stats-card">
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
                    <h3 style="font-size: 1rem; margin-bottom: 10px; color: var(--text-color);">Activity Level:</h3>
                    <div
                        style="background-color: var(--border-color); height: 10px; border-radius: 5px; overflow: hidden;">
                        <div id="activity-bar"
                            style="height: 100%; width: 75%; background: linear-gradient(90deg, var(--primary-color), var(--accent-color)); border-radius: 5px;">
                        </div>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; margin-top: 5px; font-size: 0.8rem; color: var(--text-secondary);">
                        <span>Low</span>
                        <span>High</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="actions-container">
<!-- Button -->
<button class="btn btn-primary" id="edit-profile-btn" >
    <i class="fas fa-edit"><a href="{{ route('profile.edit') }}">Edit Profile</a></i>
</button>



            <button class="btn btn-secondary" id="change-password-btn">
                <i class="fas fa-key"></i> Change Password
            </button>


{{-- <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#verifyModal">
    <i class="fas fa-bell"></i> Email Verification
</button> --}}


            <form id="logout-form" action="{{ route('tyro-login.logout') }}" method="post"
                style="display: contents;">
                @csrf
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>



@endsection
{{-- </body>

</html> --}}
