<?php

namespace App\Http\Controllers;

use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Support\TyroCache;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class PrivilegeController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $slug = trim((string) $request->get('slug'));

        $privileges = Privilege::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('slug', 'like', "%{$q}%")
                       ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($slug, fn ($query) => $query->where('slug', $slug))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('privileges.index', compact('privileges', 'q', 'slug'));
    }

    public function create()
    {
        return view('privileges.create');
    }

    public function store(Request $request)
    {
        $table = config('tyro.tables.privileges', 'privileges');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($table, 'slug')],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Auto-generate slug if missing
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        try {
            Privilege::create($data);

            return redirect()
                ->route('privileges.index')
                ->with('success', 'Privilege created successfully!');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create privilege: ' . $e->getMessage());
        }
    }

    public function show(Privilege $privilege)
    {
        // optional details page
        $privilege->load('roles:id,name,slug');
        return view('privileges.show', compact('privilege'));
    }

    public function edit(Privilege $privilege)
    {
        return view('privileges.edit', compact('privilege'));
    }

    public function update(Request $request, Privilege $privilege)
    {
        $table = config('tyro.tables.privileges', 'privileges');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($table, 'slug')->ignore($privilege->id)],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        try {
            $privilege->fill($data);

            $dirtySlug = $privilege->isDirty('slug');

            $privilege->save();

            // If slug changed, clear cache for users affected
            if ($dirtySlug) {
                TyroCache::forgetUsersByPrivilege($privilege);
            }

            return redirect()
                ->route('privileges.index')
                ->with('success', 'Privilege updated successfully!');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update privilege: ' . $e->getMessage());
        }
    }

    public function destroy(Privilege $privilege)
    {
        try {
            // clear users cache, detach role relations, delete
            TyroCache::forgetUsersByPrivilege($privilege);
            $privilege->roles()->detach();
            $privilege->delete();

            return redirect()
                ->route('privileges.index')
                ->with('success', 'Privilege deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete privilege: ' . $e->getMessage());
        }
    }
}
