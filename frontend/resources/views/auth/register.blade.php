<x-layout title="Create account" description="Create a JOJOBI MART account.">
    <div class="container-page py-16 sm:py-24 max-w-md mx-auto">
        <p class="eyebrow text-center">Join us</p>
        <h1 class="font-display text-3xl text-center mt-2 mb-8">Create your account</h1>

        <form method="POST" action="{{ route('register') }}" class="card rounded-2xl p-6 space-y-4">
            @csrf
            <div class="field">
                <label class="label">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus class="input mt-1.5">
                @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label class="label">Phone number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required class="input mt-1.5">
                <span class="text-[11px] text-ink-soft">Shopped with us in-store before? Use that same number to link your past orders and reward points here.</span>
                @error('phone') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label class="label">Email (optional)</label>
                <input type="email" name="email" value="{{ old('email') }}" class="input mt-1.5">
                @error('email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label class="label">Password</label>
                <input type="password" name="password" required class="input mt-1.5">
                @error('password') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label class="label">Confirm password</label>
                <input type="password" name="password_confirmation" required class="input mt-1.5">
            </div>
            <button type="submit" class="btn btn-stamp w-full justify-center">Create account</button>
        </form>

        <p class="text-center text-sm text-ink-soft mt-6">
            Already have an account? <a href="{{ route('login') }}" class="text-accent hover:underline">Sign in</a>
        </p>
    </div>
</x-layout>
