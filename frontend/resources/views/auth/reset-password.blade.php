<x-layout title="Reset password" description="Choose a new JOJOBI MART password.">
    <div class="container-page py-16 sm:py-24 max-w-md mx-auto">
        <p class="eyebrow text-center">Account recovery</p>
        <h1 class="font-display text-3xl text-center mt-2 mb-8">Choose a new password</h1>

        <form method="POST" action="{{ route('password.store') }}" class="card rounded-2xl p-6 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="field">
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email', $email) }}" required class="input mt-1.5">
                @error('email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label class="label">New password</label>
                <input type="password" name="password" required class="input mt-1.5">
                @error('password') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="field">
                <label class="label">Confirm new password</label>
                <input type="password" name="password_confirmation" required class="input mt-1.5">
            </div>
            <button type="submit" class="btn btn-stamp w-full justify-center">Reset password</button>
        </form>
    </div>
</x-layout>
