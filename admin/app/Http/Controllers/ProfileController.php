<?php

namespace App\Http\Controllers;

use App\Models\KycUser;
use App\Models\PrivilegeAccessKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    //
    // Me
    public function me(Request $request)
    {
        $user = $request->user()->load('roles'); // Eager load roles

        // Load permissions for blade
        $permissions = $user->roles->flatMap(function ($role) {
            return $role->privileges;
        })->unique('id');

        // Get access keys
        $accessKeys = PrivilegeAccessKey::with('privilege')
            ->whereIn('privilege_id', $permissions->pluck('id'))
            ->get();

        return view('me', [
            'user' => $user,
            'permissions' => $permissions,
            'accessKeys' => $accessKeys
        ]);
    }


    /**
     * Show the user profile form.
     */
    // public function edit()
    // {
    //     // Eager load the relationship.
    //     $user = Auth::user()->load('kycDetail');
    //     return view('kyc_user', compact('user'));
    // }



    public function edit()
{
    // Eager load the user's KYC details and roles
    $user = Auth::user()->load(['kycDetail', 'roles']);

    // Load permissions based on the user's roles and privileges
    $permissions = $user->roles->flatMap(function ($role) {
        return $role->privileges;
    })->unique('id');

    // Get access keys assigned to the user based on privileges
    $accessKeys = PrivilegeAccessKey::with('privilege')
        ->whereIn('privilege_id', $permissions->pluck('id'))
        ->get();

    return view('kyc_user', [
        'user' => $user,
        'permissions' => $permissions,
        'accessKeys' => $accessKeys
    ]);
}

    /**
     * Store or Update the user's profile information (Upsert Logic).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // **KEY CHANGE: How new/existing KYC record is handled**
        // If kycDetail exists, use it. If not, create a new KycUser instance linked to the current user ID.
        // The subsequent $kyc->save() will perform the actual INSERT if it's new.
        $kyc = $user->kycDetail ?? new KycUser(['user_id' => $user->id]);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'profile_image' => 'nullable|image',
            'phone' => 'nullable|string',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'city' => 'nullable|string',
            'address_1' => 'nullable|string',
            'address_2' => 'nullable|string',
            'custom_json_key' => 'nullable|string|max:50',
            'custom_json_value' => 'nullable|string',
        ]);

        // 1. Handle Email Re-verification (Users Table)
        if ($request->email !== $user->email) {
            $user->email = $request->email;
            $user->email_verified_at = null;
        }
        $user->name = $request->name;
        $user->save();

        // --- KYC_user Table Logic ---

        // 2. Handle Image Upload (KYC_user Table)
        if ($request->hasFile('profile_image')) {
            // Delete old image if it exists
            if ($kyc->profile_image) {
                Storage::delete('public/' . $kyc->profile_image);
            }
            // Store new image and save path
            $kyc->profile_image = $request->file('profile_image')->store('profiles', 'public');
        }

        // 3. Handle JSON Data Update (KYC_user Table)
        $metadata = $kyc->metadata ?? []; // Initialize or fetch existing
        if ($request->filled('custom_json_key')) {
            $metadata[$request->custom_json_key] = $request->custom_json_value;
        }
        $kyc->metadata = $metadata;

        // 4. Fill and Save KYC Data
        // Mass-assign the rest of the KYC fields
        $kyc->fill($request->only([
            'phone',
            'gender',
            'date_of_birth',
            'city',
            'address_1',
            'address_2'
        ]));

        // The save() method performs an UPDATE if $kyc is an existing model
        // OR an INSERT if $kyc is a new instance (as defined by the logic above).
        $kyc->save();

        return back()->with('success', 'Profile updated successfully!');
    }


    /**
     * Show the form for changing the password.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Handle the password update.
     */
    public function changePassword(Request $request)
    {
        // Validate the input
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        // If validation fails, return back with error messages
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if the current password matches
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        // Update the password
        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Redirect to a success page with a success message
        return redirect()->route('profile.edit')->with('success', 'Password changed successfully!');
    }
}
