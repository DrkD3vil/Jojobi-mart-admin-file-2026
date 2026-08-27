@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

    <style>
        .me-page { max-width: 1180px; margin: 0 auto; }

        /* Hero */
        .me-hero {
            position: relative; overflow: hidden; padding: 2rem;
            display: flex; align-items: center; gap: 1.75rem; flex-wrap: wrap;
        }
        .me-hero::before {
            content: ''; position: absolute; inset: 0; z-index: 0; pointer-events: none;
            background:
                radial-gradient(480px 220px at 8% -10%, color-mix(in oklch, var(--primary) 16%, transparent), transparent 70%),
                radial-gradient(420px 220px at 100% 120%, color-mix(in oklch, var(--info) 14%, transparent), transparent 70%);
        }
        .me-hero > * { position: relative; z-index: 1; }

        .me-avatar {
            width: 96px; height: 96px; border-radius: 50%; flex-shrink: 0; overflow: hidden;
            background: linear-gradient(135deg, var(--primary), var(--info));
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 0 4px var(--card), 0 0 0 5px var(--border);
        }
        .me-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .me-avatar .initials { color: var(--primary-foreground); font-family: var(--font-display); font-size: 2.1rem; font-weight: 600; }

        .me-hero-body { flex: 1; min-width: 220px; }
        .me-hero-name { font-family: var(--font-display); font-size: 1.7rem; font-weight: 600; margin-bottom: .35rem; }
        .me-hero-email { color: var(--muted-foreground); font-size: .9rem; margin-bottom: .75rem; display: flex; align-items: center; gap: .4rem; }
        .me-hero-badges { display: flex; flex-wrap: wrap; gap: .4rem; }

        .me-hero-actions { display: flex; flex-direction: column; gap: .6rem; flex-shrink: 0; }
        @media (max-width: 720px) {
            .me-hero { text-align: center; justify-content: center; }
            .me-hero-email { justify-content: center; }
            .me-hero-badges { justify-content: center; }
            .me-hero-actions { flex-direction: row; flex-wrap: wrap; justify-content: center; width: 100%; }
        }

        /* Stats */
        .me-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin: 1.5rem 0; }
        @media (max-width: 720px) { .me-stats { grid-template-columns: repeat(2, 1fr); } }

        /* Content grid */
        .me-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start; }
        @media (max-width: 860px) { .me-grid { grid-template-columns: 1fr; } }

        .me-section-head { display: flex; align-items: flex-start; gap: .75rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
        .me-section-icon {
            width: 38px; height: 38px; border-radius: calc(var(--radius) - 2px);
            background: color-mix(in oklch, var(--primary) 14%, var(--card)); color: var(--primary);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .me-section-icon svg { width: 19px; height: 19px; }
        .me-section-head h3 { font-size: 1.02rem; margin-bottom: .15rem; }
        .me-section-head p { font-size: .82rem; color: var(--muted-foreground); }

        .me-info-item { display: flex; align-items: flex-start; gap: .85rem; padding: .6rem .25rem; border-radius: calc(var(--radius) - 2px); transition: background-color .15s ease; }
        .me-info-item:hover { background: var(--accent); }
        .me-info-icon { width: 34px; height: 34px; border-radius: calc(var(--radius) - 2px); background: var(--secondary); color: var(--muted-foreground); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .me-info-icon svg { width: 16px; height: 16px; }
        .me-info-content h4 { font-size: .74rem; text-transform: uppercase; letter-spacing: .04em; font-family: var(--font-mono); color: var(--muted-foreground); margin-bottom: .2rem; }
        .me-info-content p { font-size: .92rem; font-weight: 500; color: var(--foreground); }

        .me-role-note { font-size: .85rem; color: var(--muted-foreground); line-height: 1.6; margin-bottom: 1.1rem; }
        .me-permissions { display: flex; flex-wrap: wrap; gap: .4rem; }

        .me-footer-actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.5rem; }
        .me-logout-form { display: inline-flex; }
    </style>

    <div class="me-page" data-reveal-group>

        {{-- Hero --}}
        <div class="ui-card me-hero" data-reveal>
            <div class="me-avatar">
                @if ($user->kycDetail?->profile_image)
                    <img src="{{ asset('storage/' . $user->kycDetail->profile_image) }}" alt="{{ $user->name }}">
                @else
                    <span class="initials">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                @endif
            </div>

            <div class="me-hero-body">
                <div class="me-hero-name">{{ $user->name }}</div>
                <div class="me-hero-email">
                    <i data-lucide="mail" class="w-4 h-4"></i> {{ $user->email }}
                </div>
                <div class="me-hero-badges">
                    @forelse ($user->roles as $role)
                        <span class="badge badge-neutral">{{ $role->name }}</span>
                    @empty
                        <span class="badge badge-outline">No role assigned</span>
                    @endforelse

                    @if ($user->hasVerifiedEmail())
                        <span class="badge badge-success"><span class="badge-dot"></span> Verified</span>
                    @else
                        <span class="badge badge-warning"><span class="badge-dot"></span> Unverified</span>
                    @endif

                    <span class="badge badge-outline">
                        <i data-lucide="calendar" class="w-3 h-3"></i> Since {{ $user->created_at->format('M Y') }}
                    </span>
                </div>
            </div>

            <div class="me-hero-actions">
                <a href="{{ route('profile.edit') }}" class="btn-primary">
                    <i data-lucide="pencil" class="w-4 h-4"></i> Edit Profile
                </a>
                <a href="{{ route('password.change.form') }}" class="btn-outline">
                    <i data-lucide="lock" class="w-4 h-4"></i> Change Password
                </a>
            </div>
        </div>

        {{-- Real, data-backed stats (not placeholders) --}}
        <div class="me-stats" data-reveal>
            <x-stat title="Roles" :value="$user->roles->count()" />
            <x-stat title="Permissions" :value="$permissions->count()" />
            <x-stat title="Access Keys" :value="$accessKeys->count()" />
            <x-stat title="Email Status" :value="$user->hasVerifiedEmail() ? 'Verified' : 'Pending'"
                :positive="$user->hasVerifiedEmail()" :negative="! $user->hasVerifiedEmail()" />
        </div>

        <div class="me-grid">

            {{-- Contact & personal --}}
            <section class="ui-card" data-reveal>
                <div class="me-section-head">
                    <div class="me-section-icon"><i data-lucide="id-card"></i></div>
                    <div>
                        <h3>Contact &amp; Personal</h3>
                        <p>Details on file from your KYC profile.</p>
                    </div>
                </div>

                <div class="me-info-item">
                    <div class="me-info-icon"><i data-lucide="phone"></i></div>
                    <div class="me-info-content">
                        <h4>Phone Number</h4>
                        <p>
                            {{ $user->kycDetail?->phone ?? '—' }}
                            @if ($user->kycDetail?->phone_verified_at)
                                <span class="badge badge-success" style="margin-left:.35rem;"><span class="badge-dot"></span> Verified</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="me-info-item">
                    <div class="me-info-icon"><i data-lucide="map-pin"></i></div>
                    <div class="me-info-content">
                        <h4>City</h4>
                        <p>{{ $user->kycDetail?->city ?? '—' }}</p>
                    </div>
                </div>

                <div class="me-info-item">
                    <div class="me-info-icon"><i data-lucide="cake"></i></div>
                    <div class="me-info-content">
                        <h4>Date of Birth</h4>
                        <p>{{ $user->kycDetail?->date_of_birth?->format('M d, Y') ?? '—' }}</p>
                    </div>
                </div>

                <div class="me-info-item">
                    <div class="me-info-icon"><i data-lucide="home"></i></div>
                    <div class="me-info-content">
                        <h4>Address</h4>
                        <p>{{ $user->kycDetail?->address_1 ?? '—' }}{{ $user->kycDetail?->address_2 ? ', ' . $user->kycDetail->address_2 : '' }}</p>
                    </div>
                </div>
            </section>

            {{-- Role & permissions --}}
            <section class="ui-card" data-reveal>
                <div class="me-section-head">
                    <div class="me-section-icon"><i data-lucide="user-cog"></i></div>
                    <div>
                        <h3>Role &amp; Permissions</h3>
                        <p>What your assigned roles grant you access to.</p>
                    </div>
                </div>

                <p class="me-role-note">
                    You are assigned <strong>{{ $user->roles->count() }}</strong> {{ Str::plural('role', $user->roles->count()) }},
                    granting <strong>{{ $permissions->count() }}</strong> {{ Str::plural('privilege', $permissions->count()) }}.
                </p>

                <div class="me-permissions">
                    @forelse ($permissions as $permission)
                        <span class="badge badge-outline">{{ $permission->slug ?? $permission->name }}</span>
                    @empty
                        <span class="badge badge-outline">No privileges assigned</span>
                    @endforelse
                </div>
            </section>
        </div>

        {{-- Access keys --}}
        <section class="ui-card" style="margin-top: 1.5rem;" data-reveal>
            <div class="me-section-head">
                <div class="me-section-icon"><i data-lucide="key"></i></div>
                <div>
                    <h3>Access Permissions</h3>
                    <p>Access keys granted to you through your assigned roles.</p>
                </div>
            </div>

            <div class="ui-table-wrap">
                <table>
                    <thead>
                        <tr><th>Access Key</th><th>Privilege</th></tr>
                    </thead>
                    <tbody>
                        @forelse($accessKeys as $mapping)
                            <tr>
                                <td class="mono">{{ $mapping->access_key }}</td>
                                <td>{{ $mapping->privilege->name }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="text-align:center; color: var(--muted-foreground); padding: 1.25rem;">No access keys assigned.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Footer actions --}}
        <div class="me-footer-actions" data-reveal>
            <form action="/logout" method="POST" class="me-logout-form">
                @csrf
                <button type="submit" class="btn-destructive">
                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <script>
        if (window.lucide) lucide.createIcons();
    </script>

@endsection
