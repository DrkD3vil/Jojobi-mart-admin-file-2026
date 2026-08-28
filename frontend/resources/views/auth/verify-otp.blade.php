<x-layout title="Verify your email" description="Enter the verification code we emailed you.">
    <div class="container-page py-16 sm:py-24 max-w-md mx-auto">
        <p class="eyebrow text-center">{{ $purpose === 'register' ? 'Almost there' : 'One more step' }}</p>
        <h1 class="font-display text-3xl text-center mt-2 mb-2">Enter your code</h1>
        <p class="text-center text-sm text-ink-soft mb-8">
            We emailed a 6-digit code to <strong>{{ $email }}</strong>. It expires in 5 minutes.
        </p>

        <form method="POST" action="{{ $verifyRoute }}" class="card rounded-2xl p-6 space-y-4">
            @csrf
            <div class="field">
                <label class="label">Verification code</label>
                <input
                    type="text"
                    name="code"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    autocomplete="one-time-code"
                    maxlength="6"
                    value="{{ old('code') }}"
                    required
                    autofocus
                    class="input mt-1.5 text-center text-lg tracking-[0.4em]"
                >
                @error('code') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn btn-stamp w-full justify-center">Verify &amp; continue</button>
        </form>

        <form method="POST" action="{{ $resendRoute }}" class="text-center mt-6">
            @csrf
            <button type="submit" class="text-accent hover:underline text-sm">Didn't get a code? Resend it</button>
        </form>

        <p class="text-center text-sm text-ink-soft mt-6">
            <a href="{{ $backRoute }}" class="text-accent hover:underline">Start over</a>
        </p>
    </div>
</x-layout>
