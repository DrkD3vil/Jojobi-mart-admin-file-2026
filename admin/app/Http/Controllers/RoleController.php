<?php

namespace App\Http\Controllers;

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
        'privileges.*' => ['integer'],
    ]);

    // Auto-generate slug if missing
    $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

    try {
        $role = Role::create([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);

        // sync privileges (if any)
        $role->privileges()->sync($data['privileges'] ?? []);

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
        $role->load('privileges:id,name,slug');
        return view('roles.show', compact('role'));
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
            'privileges.*' => ['integer'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        try {
            $role->fill([
                'name' => $data['name'],
                'slug' => $data['slug'],
            ]);

            $dirtySlug = $role->isDirty('slug');
            $role->save();

            // Update privileges
            $role->privileges()->sync($data['privileges'] ?? []);

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

            // detach pivots
            $role->privileges()->detach();
            $role->users()->detach();

            $role->delete();

            return redirect()
                ->route('roles.index')
                ->with('success', 'Role deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }
}
