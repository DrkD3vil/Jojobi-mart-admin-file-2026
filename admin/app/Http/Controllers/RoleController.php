<?php

namespace App\Http\Controllers;

use App\Models\PrivilegeAccessKey;
use App\Services\AccessService;
use HasinHayder\Tyro\Models\Role;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Support\TyroCache;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $slug = trim((string) $request->get('slug'));

        $roles = Role::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('slug', 'like', "%{$q}%");
                });
            })
            ->when($slug, fn ($query) => $query->where('slug', $slug))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('roles.index', compact('roles', 'q', 'slug'));
    }

    public function create()
    {
        $privileges = Privilege::query()->orderBy('name')->get();
        return view('roles.create', compact('privileges'));
    }

public function store(Request $request)
{
    $rolesTable = config('tyro.tables.roles', 'roles');

    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'slug' => ['nullable', 'string', 'max:255', Rule::unique($rolesTable, 'slug')],
        'privileges' => ['nullable', 'array'],
        'privileges.*' => ['integer', 'exists:privileges,id'],
    ]);

    // Auto-generate slug if missing
    $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['name']);

    try {
        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        // sync privileges (if any)
        $sync = $role->privileges()->sync($data['privileges'] ?? []);
        $this->syncRoleAccessKeys($role, $sync);

        // Clear users cache for this role (optional but safe)
        TyroCache::forgetUsersByRoleIds([$role->id]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role created successfully!');
    } catch (\Throwable $e) {
        return back()
            ->withInput()
            ->with('error', 'Failed to create role: ' . $e->getMessage());
    }
}


    public function show(Role $role)
    {
        $role->load(['privileges:id,name,slug,access_key', 'users:id,name,email']);

        // Effective access keys: every key this role's members can actually
        // reach, whether it came from a privilege assignment (auto) or a
        // direct grant made on the Access Keys page.
        $effectiveAccessKeys = PrivilegeAccessKey::where('role_id', $role->id)
            ->orderBy('access_key')
            ->get(['access_key', 'is_auto']);

        return view('roles.show', compact('role', 'effectiveAccessKeys'));
    }

    public function edit(Role $role)
    {
        $role->load('privileges:id');
        $privileges = Privilege::query()->orderBy('name')->get();

        $selectedPrivilegeIds = $role->privileges->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'privileges', 'selectedPrivilegeIds'));
    }

    public function update(Request $request, Role $role)
    {
        $rolesTable = config('tyro.tables.roles', 'roles');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($rolesTable, 'slug')->ignore($role->id)],
            'privileges' => ['nullable', 'array'],
            'privileges.*' => ['integer', 'exists:privileges,id'],
        ]);

        $data['slug'] = ($data['slug'] ?? null) ?: Str::slug($data['name']);

        try {
            $role->fill([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);

            $dirtySlug = $role->isDirty('slug');
            $role->save();

            // Update privileges
            $sync = $role->privileges()->sync($data['privileges'] ?? []);
            $this->syncRoleAccessKeys($role, $sync);

            // Clear cache if slug changed OR privileges changed (safe to always clear)
            if ($dirtySlug) {
                TyroCache::forgetUsersByRoleIds([$role->id]);
            } else {
                TyroCache::forgetUsersByRoleIds([$role->id]);
            }

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role updated successfully!');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        try {
            TyroCache::forgetUsersByRoleIds([$role->id]);

            // privilege_access_keys.role_id is onDelete('set null') -- left
            // alone, deleting the role would turn every mapping that pointed
            // at it (auto or manual) into a user_id=null/role_id=null row,
            // which AccessService reads as "grant to everyone". Delete them
            // outright instead of letting that cascade fire.
            $affectedKeys = PrivilegeAccessKey::where('role_id', $role->id)->pluck('access_key')->unique();
            PrivilegeAccessKey::where('role_id', $role->id)->delete();

            // detach pivots
            $role->privileges()->detach();
            $role->users()->detach();

            $role->delete();

            $accessService = app(AccessService::class);
            foreach ($affectedKeys as $key) {
                $accessService->clearCacheForAccessKey($key);
            }

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate a role and its privileges (and the access keys those
     * privileges grant), landing on the new role's edit page to rename it.
     */
    public function clone(Role $role)
    {
        $role->load('privileges:id');

        $baseSlug = $role->slug . '-copy';
        $slug = $baseSlug;
        $suffix = 1;
        while (Role::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . (++$suffix);
        }

        $clone = Role::create([
            'name' => $role->name . ' (Copy)',
            'slug' => $slug,
        ]);

        $sync = $clone->privileges()->sync($role->privileges->pluck('id')->toArray());
        $this->syncRoleAccessKeys($clone, $sync);

        return redirect()
            ->route('roles.edit', $clone)
            ->with('success', "Cloned \"{$role->name}\" as \"{$clone->name}\". Rename it and adjust privileges as needed.");
    }

    /**
     * Keep privilege_access_keys in step with a role's privilege list: every
     * privilege that carries an access_key gets a role-scoped mapping row
     * when attached, and only the auto-created row (never a manually-added
     * one on the Access Keys page) is removed when detached.
     */
    private function syncRoleAccessKeys(Role $role, array $syncResult): void
    {
        $attachedIds = $syncResult['attached'] ?? [];
        $detachedIds = $syncResult['detached'] ?? [];

        if (empty($attachedIds) && empty($detachedIds)) {
            return;
        }

        $accessService = app(AccessService::class);

        if (!empty($attachedIds)) {
            $privileges = Privilege::whereIn('id', $attachedIds)->whereNotNull('access_key')->get();
            foreach ($privileges as $privilege) {
                PrivilegeAccessKey::updateOrCreate(
                    [
                        'privilege_id' => $privilege->id,
                        'access_key' => $privilege->access_key,
                        'role_id' => $role->id,
                        'user_id' => null,
                    ],
                    ['is_auto' => true]
                );
                $accessService->clearCacheForAccessKey($privilege->access_key);
            }
        }

        if (!empty($detachedIds)) {
            $privileges = Privilege::whereIn('id', $detachedIds)->whereNotNull('access_key')->get();
            foreach ($privileges as $privilege) {
                PrivilegeAccessKey::where([
                    'privilege_id' => $privilege->id,
                    'access_key' => $privilege->access_key,
                    'role_id' => $role->id,
                    'user_id' => null,
                    'is_auto' => true,
                ])->delete();
                $accessService->clearCacheForAccessKey($privilege->access_key);
            }
        }
    }
}
