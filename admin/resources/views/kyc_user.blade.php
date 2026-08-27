@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')

    <style>
        .profile-page { max-width: 1180px; margin: 0 auto; }

        .profile-page-head { margin-bottom: 1.75rem; }
        .profile-page-head .eyebrow {
            font-family: var(--font-mono); font-size: .72rem; text-transform: uppercase;
            letter-spacing: .08em; color: var(--primary); font-weight: 600; margin-bottom: .35rem;
        }
        .profile-page-head h1 { font-size: clamp(1.5rem, 2.4vw, 2rem); margin-bottom: .35rem; }
        .profile-page-head .sub { color: var(--muted-foreground); font-size: .9rem; }

        .profile-grid { display: grid; grid-template-columns: 300px 1fr; gap: 1.5rem; align-items: start; margin-top: 1.5rem; }
        @media (max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } }

        .profile-aside { position: sticky; top: 88px; display: flex; flex-direction: column; gap: 1rem; }
        @media (max-width: 900px) { .profile-aside { position: static; } }

        .profile-card { text-align: center; padding: 1.75rem 1.25rem; }

        .profile-avatar-wrap { position: relative; width: 104px; height: 104px; margin: 0 auto 1rem; }
        .profile-avatar {
            width: 100%; height: 100%; border-radius: 50%; overflow: hidden;
            box-shadow: 0 0 0 3px var(--border); background: linear-gradient(135deg, var(--primary), var(--info));
            display: flex; align-items: center; justify-content: center; transition: box-shadow .2s var(--ease);
        }
        .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-avatar .initials { color: var(--primary-foreground); font-family: var(--font-display); font-size: 1.9rem; font-weight: 600; }
        .profile-avatar-wrap.dragover .profile-avatar { box-shadow: 0 0 0 3px var(--primary); }

        .profile-avatar-edit {
            position: absolute; right: -2px; bottom: -2px; width: 32px; height: 32px; border-radius: 50%;
            background: var(--primary); color: var(--primary-foreground); display: flex; align-items: center;
            justify-content: center; border: 3px solid var(--card); cursor: pointer; transition: transform .2s var(--ease);
        }
        .profile-avatar-edit:hover { transform: scale(1.08); }
        .profile-avatar-edit svg { width: 14px; height: 14px; }

        .profile-name { font-family: var(--font-display); font-size: 1.2rem; font-weight: 600; margin-bottom: .15rem; }
        .profile-email { color: var(--muted-foreground); font-size: .82rem; word-break: break-all; margin-bottom: .85rem; }
        .profile-roles { display: flex; flex-wrap: wrap; gap: .35rem; justify-content: center; margin-bottom: 1rem; }
        .profile-meta { font-size: .76rem; color: var(--muted-foreground); border-top: 1px solid var(--border); padding-top: .8rem; margin-top: .2rem; display: flex; align-items: center; justify-content: center; gap: .4rem; }

        .profile-jumpnav { display: flex; flex-direction: column; gap: .15rem; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); padding: .5rem; box-shadow: var(--card-shadow); }
        .profile-jumpnav a {
            display: flex; align-items: center; gap: .55rem; padding: .55rem .7rem; border-radius: calc(var(--radius) - 2px);
            color: var(--muted-foreground); font-size: .85rem; font-weight: 500; text-decoration: none;
            transition: background-color .15s ease, color .15s ease;
        }
        .profile-jumpnav a:hover, .profile-jumpnav a.is-active { background: var(--accent); color: var(--foreground); }
        .profile-jumpnav a svg { width: 16px; height: 16px; flex-shrink: 0; }

        .profile-security-link { display: flex; align-items: center; justify-content: center; gap: .5rem; text-decoration: none; }

        .profile-main { display: flex; flex-direction: column; gap: 1.25rem; min-width: 0; }

        .profile-section-head { display: flex; align-items: flex-start; gap: .75rem; margin-bottom: 1.25rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border); }
        .profile-section-icon {
            width: 38px; height: 38px; border-radius: calc(var(--radius) - 2px);
            background: color-mix(in oklch, var(--primary) 14%, var(--card)); color: var(--primary);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .profile-section-icon svg { width: 19px; height: 19px; }
        .profile-section-head h3 { font-size: 1.02rem; margin-bottom: .15rem; }
        .profile-section-head p { font-size: .82rem; color: var(--muted-foreground); }

        .field-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.1rem 1.25rem; }
        @media (max-width: 600px) { .field-grid { grid-template-columns: 1fr; } }
        .field-grid .span-2 { grid-column: 1 / -1; }

        .field label { display: block; font-size: .82rem; font-weight: 600; margin-bottom: .4rem; color: var(--foreground); }
        .field .hint { font-size: .76rem; color: var(--muted-foreground); margin-top: .35rem; }
        .field .row-inline { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
        .field-error { display: flex; align-items: center; gap: .35rem; font-size: .78rem; color: var(--danger); margin-top: .4rem; }
        .field-error svg { width: 14px; height: 14px; flex-shrink: 0; }
        .char-counter { font-size: .74rem; color: var(--muted-foreground); text-align: right; margin-top: .3rem; }

        .profile-submit-bar { display: flex; justify-content: flex-end; gap: .75rem; }

        .kv-table td:first-child { font-weight: 600; width: 35%; }
        .kv-empty { padding: 1.25rem; text-align: center; color: var(--muted-foreground); font-size: .85rem; }

        .metadata-add-grid { display: grid; grid-template-columns: 1fr 1fr auto; gap: .75rem; align-items: end; margin-top: 1rem; }
        @media (max-width: 640px) { .metadata-add-grid { grid-template-columns: 1fr; } }

        .submit-btn.loading { pointer-events: none; opacity: .75; }
        @keyframes profileSpin { to { transform: rotate(360deg); } }
        .spin-icon { animation: profileSpin .8s linear infinite; }
    </style>

    <div class="profile-page" data-reveal-group>

        <div class="profile-page-head" data-reveal>
            <p class="eyebrow">Account</p>
            <h1>Profile Settings</h1>
            <p class="sub">Manage your account details, KYC information, and access.</p>
        </div>

        @if (session('success'))
            <div class="ui-alert ui-alert-success" style="margin-bottom: 1.25rem;" data-reveal>
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (! $user->hasVerifiedEmail())
            <div class="ui-alert ui-alert-warning" style="margin-bottom: 1.25rem;" data-reveal>
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                <div><strong>Your email address is unverified.</strong> If you changed your email, please check your inbox for the verification link.</div>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
            @csrf

            <div class="profile-grid">

                {{-- ============ LEFT: summary + navigation ============ --}}
                <aside class="profile-aside" data-reveal>

                    <div class="ui-card profile-card">
                        <div class="profile-avatar-wrap" id="avatar-wrap">
                            <div class="profile-avatar">
                                @if($user->kycDetail?->profile_image)
                                    <img src="{{ asset('storage/' . $user->kycDetail->profile_image) }}"
                                         alt="Profile picture" id="avatar-preview">
                                @else
                                    <span class="initials" id="avatar-initials">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                    <img src="" alt="Profile picture" id="avatar-preview" style="display:none;">
                                @endif
                            </div>
                            <label for="profile_image" class="profile-avatar-edit" title="Change photo">
                                <i data-lucide="camera"></i>
                            </label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display:none;">
                        </div>

                        <div class="profile-name">{{ $user->name }}</div>
                        <div class="profile-email">{{ $user->email }}</div>

                        <div class="profile-roles">
                            @forelse($user->roles as $role)
                                <span class="badge badge-neutral">{{ $role->name }}</span>
                            @empty
                                <span class="badge badge-outline">No role assigned</span>
                            @endforelse
                            @if($user->hasVerifiedEmail())
                                <span class="badge badge-success"><span class="badge-dot"></span> Verified</span>
                            @else
                                <span class="badge badge-warning"><span class="badge-dot"></span> Unverified</span>
                            @endif
                        </div>

                        <div class="profile-meta">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            Member since {{ $user->created_at?->format('M Y') ?? '—' }}
                        </div>

                        @error('profile_image')
                            <div class="field-error" style="justify-content:center;">
                                <i data-lucide="alert-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <nav class="profile-jumpnav" id="profile-jumpnav">
                        <a href="#sec-account" data-jump>
                            <i data-lucide="user"></i> Account Details
                        </a>
                        <a href="#sec-personal" data-jump>
                            <i data-lucide="shield-check"></i> Personal &amp; KYC
                        </a>
                        <a href="#sec-access" data-jump>
                            <i data-lucide="key"></i> Access Permissions
                        </a>
                        <a href="#sec-metadata" data-jump>
                            <i data-lucide="database"></i> Custom Metadata
                        </a>
                    </nav>

                    <a href="{{ route('password.change.form') }}" class="btn-outline profile-security-link">
                        <i data-lucide="lock" class="w-4 h-4"></i> Change Password
                    </a>
                </aside>

                {{-- ============ RIGHT: sectioned content ============ --}}
                <div class="profile-main">

                    {{-- Account details --}}
                    <section id="sec-account" class="ui-card profile-section" data-reveal>
                        <div class="profile-section-head">
                            <div class="profile-section-icon"><i data-lucide="user"></i></div>
                            <div>
                                <h3>Account Details</h3>
                                <p>Your login name and email address.</p>
                            </div>
                        </div>

                        <div class="field-grid">
                            <div class="field span-2">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="ui-input"
                                       value="{{ old('name', $user->name) }}" required
                                       placeholder="Enter your full name">
                                @error('name')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="field span-2">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="ui-input"
                                       value="{{ old('email', $user->email) }}" readonly
                                       style="cursor: not-allowed; background: var(--muted); color: var(--muted-foreground);"
                                       title="Email cannot be changed for security reasons">
                                <p class="hint">Email cannot be changed for security reasons.</p>
                                @error('email')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    {{-- Personal / KYC --}}
                    <section id="sec-personal" class="ui-card profile-section" data-reveal>
                        <div class="profile-section-head">
                            <div class="profile-section-icon"><i data-lucide="shield-check"></i></div>
                            <div>
                                <h3>Personal &amp; KYC Information</h3>
                                <p>Contact details used for verification and delivery.</p>
                            </div>
                        </div>

                        <div class="field-grid">
                            <div class="field">
                                <label for="phone">Phone Number</label>
                                <div class="row-inline">
                                    <input type="text" id="phone" name="phone" class="ui-input"
                                           value="{{ old('phone', $user->kycDetail?->phone) }}"
                                           placeholder="+1 (555) 123-4567" style="flex:1;">
                                    @if ($user->kycDetail?->phone_verified_at)
                                        <span class="badge badge-success" style="flex-shrink:0;"><span class="badge-dot"></span> Verified</span>
                                    @endif
                                </div>
                                @error('phone')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="gender">Gender</label>
                                @php $selectedGender = old('gender', $user->kycDetail?->gender); @endphp
                                <select id="gender" name="gender" class="ui-select">
                                    <option value="">Select gender</option>
                                    <option value="male" @selected($selectedGender == 'male')>Male</option>
                                    <option value="female" @selected($selectedGender == 'female')>Female</option>
                                    <option value="other" @selected($selectedGender == 'other')>Other</option>
                                </select>
                                @error('gender')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" class="ui-input"
                                       value="{{ old('date_of_birth', $user->kycDetail?->date_of_birth?->format('Y-m-d')) }}">
                                @error('date_of_birth')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="field">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" class="ui-input"
                                       value="{{ old('city', $user->kycDetail?->city) }}"
                                       placeholder="Enter your city">
                                @error('city')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="field span-2">
                                <label for="address_1">Address Line 1</label>
                                <textarea id="address_1" name="address_1" class="ui-textarea" data-count
                                          placeholder="Enter your primary address">{{ old('address_1', $user->kycDetail?->address_1) }}</textarea>
                                <div class="char-counter" data-counter-for="address_1"></div>
                                @error('address_1')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>

                            <div class="field span-2">
                                <label for="address_2">Address Line 2 <span style="font-weight:400; color:var(--muted-foreground);">(optional)</span></label>
                                <textarea id="address_2" name="address_2" class="ui-textarea" data-count
                                          placeholder="Apartment, suite, unit, building, floor, etc.">{{ old('address_2', $user->kycDetail?->address_2) }}</textarea>
                                <div class="char-counter" data-counter-for="address_2"></div>
                                @error('address_2')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    {{-- Access permissions --}}
                    <section id="sec-access" class="ui-card profile-section" data-reveal>
                        <div class="profile-section-head">
                            <div class="profile-section-icon"><i data-lucide="key"></i></div>
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
                                        <tr><td colspan="2" class="kv-empty">No access keys assigned.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>

                    {{-- Metadata --}}
                    <section id="sec-metadata" class="ui-card profile-section" data-reveal>
                        <div class="profile-section-head">
                            <div class="profile-section-icon"><i data-lucide="database"></i></div>
                            <div>
                                <h3>Custom Metadata</h3>
                                <p>Flexible key/value data attached to your profile.</p>
                            </div>
                        </div>

                        @php $metadata = $user->kycDetail?->metadata ?? []; @endphp
                        <div class="ui-table-wrap">
                            <table class="kv-table">
                                <thead>
                                    <tr><th>Key</th><th>Value</th></tr>
                                </thead>
                                <tbody>
                                    @forelse($metadata as $mk => $mv)
                                        <tr>
                                            <td class="mono">{{ $mk }}</td>
                                            <td>{{ is_array($mv) ? json_encode($mv) : $mv }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="kv-empty">No custom metadata yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="metadata-add-grid">
                            <div class="field" style="margin:0;">
                                <label for="custom_json_key">Add key</label>
                                <input type="text" id="custom_json_key" name="custom_json_key" class="ui-input"
                                       value="{{ old('custom_json_key') }}"
                                       placeholder="e.g. favorite_color">
                                @error('custom_json_key')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                            <div class="field" style="margin:0;">
                                <label for="custom_json_value">Value</label>
                                <input type="text" id="custom_json_value" name="custom_json_value" class="ui-input"
                                       value="{{ old('custom_json_value') }}"
                                       placeholder="e.g. blue">
                                @error('custom_json_value')
                                    <div class="field-error"><i data-lucide="alert-circle"></i> {{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </section>

                    <div class="profile-submit-bar">
                        <button type="submit" class="btn-primary" id="submit-btn">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Save Changes
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
        (function () {
            // Avatar preview + drag/drop onto the avatar
            const fileInput = document.getElementById('profile_image');
            const avatarPreview = document.getElementById('avatar-preview');
            const avatarInitials = document.getElementById('avatar-initials');
            const avatarWrap = document.getElementById('avatar-wrap');
            const MAX_MB = 5;

            function applyFile(file) {
                if (!file) return;
                if (file.size > MAX_MB * 1024 * 1024) {
                    alert('File size must be less than ' + MAX_MB + 'MB');
                    fileInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    avatarPreview.src = e.target.result;
                    avatarPreview.style.display = 'block';
                    if (avatarInitials) avatarInitials.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            if (fileInput) {
                fileInput.addEventListener('change', (e) => applyFile(e.target.files[0]));
            }

            if (avatarWrap) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
                    avatarWrap.addEventListener(evt, (e) => { e.preventDefault(); e.stopPropagation(); });
                });
                ['dragenter', 'dragover'].forEach(evt => avatarWrap.addEventListener(evt, () => avatarWrap.classList.add('dragover')));
                ['dragleave', 'drop'].forEach(evt => avatarWrap.addEventListener(evt, () => avatarWrap.classList.remove('dragover')));
                avatarWrap.addEventListener('drop', (e) => {
                    const file = e.dataTransfer.files[0];
                    if (file) {
                        fileInput.files = e.dataTransfer.files;
                        applyFile(file);
                    }
                });
            }

            // Character counters
            document.querySelectorAll('textarea[data-count]').forEach((el) => {
                const counter = document.querySelector(`[data-counter-for="${el.id}"]`);
                if (!counter) return;
                const update = () => { counter.textContent = `${el.value.length} characters`; };
                el.addEventListener('input', update);
                update();
            });

            // Submit loading state
            const form = document.getElementById('profile-form');
            const submitBtn = document.getElementById('submit-btn');
            if (form && submitBtn) {
                form.addEventListener('submit', () => {
                    submitBtn.classList.add('loading');
                    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 spin-icon"></i> Saving…';
                    if (window.lucide) lucide.createIcons();
                });
            }

            // Keyboard shortcut: Ctrl/Cmd+S to save
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    submitBtn?.click();
                }
            });

            // Jump-nav active section highlighting
            const jumpLinks = Array.from(document.querySelectorAll('#profile-jumpnav [data-jump]'));
            const sections = jumpLinks
                .map(a => document.querySelector(a.getAttribute('href')))
                .filter(Boolean);

            if ('IntersectionObserver' in window && sections.length) {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;
                        const id = '#' + entry.target.id;
                        jumpLinks.forEach(a => a.classList.toggle('is-active', a.getAttribute('href') === id));
                    });
                }, { rootMargin: '-40% 0px -50% 0px', threshold: 0 });
                sections.forEach(s => io.observe(s));
            }

            if (window.lucide) lucide.createIcons();
        })();
    </script>

@endsection
