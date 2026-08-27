{{-- Shared footer for both the desktop and mobile sidebars. --}}
<div class="sidebar-footer p-4 border-t" style="border-color: var(--sidebar-border);">
    <div class="flex items-center space-x-3 p-3 rounded-xl" style="background-color: var(--sidebar-accent);">
        <img class="h-10 w-10 rounded-full object-cover border-2 border-[var(--sidebar-primary)]/50"
            src="{{ asset('storage/' . (Auth::user()->kycDetail ? Auth::user()->kycDetail->profile_image : 'default-profile.jpg')) }}"
            alt="{{ Auth::user()->name }} Profile Image">

        <div class="user-info flex-1 min-w-0">
            <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
            <p class="text-xs truncate" style="color: var(--muted-foreground);">
                @foreach (Auth::user()->roles as $role)
                    <p>{{ $role->name }}</p>
                @endforeach
            </p>
        </div>

        <button onclick="event.preventDefault(); document.getElementById('tyro-logout-form-{{ $footerId ?? 'desktop' }}').submit();"
            class="text-[var(--muted-foreground)] hover:text-[var(--sidebar-primary)] transition-colors duration-200"
            title="Logout">
            <i data-lucide="log-out" class="w-4 h-4"></i>
        </button>

        <!-- Logout form -->
        <form id="tyro-logout-form-{{ $footerId ?? 'desktop' }}" method="POST" action="/logout" class="hidden">
            @csrf
        </form>
    </div>
</div>
