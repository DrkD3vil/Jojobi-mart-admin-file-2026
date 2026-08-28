<x-layout title="Sign in" description="Sign in to your JOJOBI MART account.">
    <div class="container-page py-16 sm:py-24 max-w-md mx-auto">
        <p class="eyebrow text-center">Welcome back</p>
        <h1 class="font-display text-3xl text-center mt-2 mb-8">Sign in</h1>

        <form method="POST" action="{{ route('login') }}" class="card rounded-2xl p-6 space-y-4">
            @csrf
            <div class="field">
                <label class="label">Email or phone</label>
                <input type="text" name="login" value="{{ old('login') }}" required autofocus class="input mt-1.5">
                @error('login') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label class="label">Password</label>
                <input type="password" name="password" required class="input mt-1.5">
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-ink-soft">
                    <input type="checkbox" name="remember" class="accent-current"> Remember me
                </label>
                <a href="{{ route('password.request') }}" class="text-accent hover:underline">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-stamp w-full justify-center">Sign in</button>
        </form>

        <p class="text-center text-sm text-ink-soft mt-6">
            New here? <a href="{{ route('register') }}" class="text-accent hover:underline">Create an account</a>
        </p>
    </div>
</x-layout>
