<?php

namespace App\Http\Controllers;


use App\Models\User;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    /**
     * Display a list of users and their roles.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Optionally filter by roles
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($query) use ($request) {
                $query->where('slug', $request->role);
            });
        }

        $users = $query->with('roles')->paginate(10);  // Get users with roles
        $roles = Role::all();  // Get all available roles

        return view('user_roles.index', compact('users', 'roles'));
    }

    /**
     * Show the form for assigning roles to multiple users.
     */
    public function assignMultipleRolesForm()
    {
        $users = User::all();
        $roles = Role::all();

        return view('user_roles.assign_multiple_roles', compact('users', 'roles'));
    }

    /**
     * Assign roles to multiple users.
     */
    public function assignMultipleRoles(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $users = User::findOrFail($request->users);
        $roles = Role::findOrFail($request->roles);

        foreach ($users as $user) {
            $user->roles()->syncWithoutDetaching($roles);  // Attach roles without removing existing ones
        }

        return redirect()->route('user.roles.index')->with('success', 'Roles assigned to selected users.');
    }

    /**
     * Show the form to assign roles to an individual user.
     */
    public function show(int $userId)
    {
        $user = User::findOrFail($userId);

        // Roles assigned to the user
        $assignedRoles = $user->roles()
            ->withCount(['privileges', 'users'])
            ->orderBy('name')
            ->get();

        $assignedRoleIds = $assignedRoles->pluck('id')->map(fn($v) => (int)$v)->all();

        // All roles available
        $allRoles = Role::query()
            ->withCount(['privileges', 'users'])
            ->orderBy('name')
            ->get();

        return view('user_roles.show', compact(
            'user',
            'assignedRoles',
            'assignedRoleIds',
            'allRoles'
        ));
    }

    public function store(Request $request, int $userId)
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'roles' => ['nullable', 'array'],              // allow empty if you want remove all
            'roles.*' => ['integer', 'exists:roles,id'],
            'operation_mode' => ['required', 'in:add,replace'],
        ]);

        $roleIds = collect($validated['roles'] ?? [])
            ->map(fn($id) => (int)$id)
            ->unique()
            ->values()
            ->all();

        DB::transaction(function () use ($user, $roleIds, $validated) {
            if ($validated['operation_mode'] === 'replace') {
                // ✅ THIS removes old roles and keeps ONLY selected roles
                $user->roles()->sync($roleIds);
            } else {
                // ✅ THIS adds selected roles and keeps old ones
                $user->roles()->syncWithoutDetaching($roleIds);
            }
        });

        return redirect()
            ->route('user.roles.show', $user->id)
            ->with('success', 'Roles updated successfully.');
    }

    /**
     * Remove a role from a user.
     */
    public function destroy($userId, $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);

        $user->roles()->detach($role);

        return redirect()->route('user.roles.index')->with('success', 'Role removed from user.');
    }

    /**
     * Dynamically assign privileges to roles.
     */
    public function assignPrivilegesToRole(Request $request, $roleId)
    {
        $request->validate([
            'privileges' => 'required|array',
            'privileges.*' => 'exists:privileges,id',
        ]);

        $role = Role::findOrFail($roleId);
        $privileges = Privilege::findOrFail($request->privileges);

        $role->privileges()->syncWithoutDetaching($privileges);

        return redirect()->route('roles.show', $roleId)->with('success', 'Privileges assigned to role.');
    }

    /**
     * Remove a privilege from a role.
     */
    public function removePrivilegeFromRole($roleId, $privilegeId)
    {
        $role = Role::findOrFail($roleId);
        $privilege = Privilege::findOrFail($privilegeId);

        $role->privileges()->detach($privilege);

        return redirect()->route('roles.show', $roleId)->with('success', 'Privilege removed from role.');
    }
}
