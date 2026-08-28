<x-layout title="Forgot password" description="Reset your JOJOBI MART password.">
    <div class="container-page py-16 sm:py-24 max-w-md mx-auto">
        <p class="eyebrow text-center">Account recovery</p>
        <h1 class="font-display text-3xl text-center mt-2 mb-3">Forgot your password?</h1>
        <p class="text-sm text-ink-soft text-center mb-8">Enter your email and we'll send you a reset link.</p>

        <form method="POST" action="{{ route('password.email') }}" class="card rounded-2xl p-6 space-y-4">
            @csrf
            <div class="field">
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input mt-1.5">
                @error('email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn btn-stamp w-full justify-center">Send reset link</button>
        </form>

        <p class="text-center text-sm text-ink-soft mt-6">
            <a href="{{ route('login') }}" class="text-accent hover:underline">← Back to sign in</a>
        </p>
    </div>
</x-layout>
