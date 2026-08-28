<x-layout title="Profile settings" description="Manage your JOJOBI MART account.">
    <div class="container-page py-10">
        <p class="eyebrow">My account</p>
        <h1 class="font-display text-3xl sm:text-4xl mt-1 mb-8">Profile settings</h1>

        <div class="grid lg:grid-cols-[240px_1fr] gap-8">
            @include('partials.account-nav')

            <div class="space-y-8 max-w-xl">
                <div class="card rounded-2xl p-6">
                    <h2 class="font-display text-lg mb-5">Personal details</h2>
                    <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-4">
                        @csrf @method('PUT')
                        <div class="field">
                            <label class="label">Full name</label>
                            <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="input mt-1.5">
                            @error('name') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label class="label">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required class="input mt-1.5">
                            @error('phone') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label class="label">Email</label>
                            <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="input mt-1.5">
                            @error('email') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label class="label">Default delivery address</label>
                            <textarea name="address" rows="3" class="input mt-1.5">{{ old('address', $customer->address) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-stamp">Save changes</button>
                    </form>
                </div>

                <div class="card rounded-2xl p-6">
                    <h2 class="font-display text-lg mb-5">Change password</h2>
                    <form method="POST" action="{{ route('account.profile.password') }}" class="space-y-4">
                        @csrf @method('PUT')
                        <div class="field">
                            <label class="label">Current password</label>
                            <input type="password" name="current_password" required class="input mt-1.5">
                            @error('current_password') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
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
                        <button type="submit" class="btn btn-cut">Update password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
