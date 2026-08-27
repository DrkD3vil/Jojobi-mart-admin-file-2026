<?php

namespace App\Http\Controllers;

use App\Models\PrivilegeAccessKey;
use App\Services\AccessService;
use App\Support\AccessKeys;
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
        $accessKeyOptions = $this->availableAccessKeyOptions();
        return view('privileges.create', compact('accessKeyOptions'));
    }

    public function store(Request $request)
    {
        $table = config('tyro.tables.privileges', 'privileges');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($table, 'slug')],
            'description' => ['nullable', 'string', 'max:1000'],
            'access_key' => ['nullable', 'string', Rule::in(AccessKeys::keys()), Rule::unique($table, 'access_key')],
        ]);

        // Auto-generate slug if missing
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $accessKey = $data['access_key'] ?: null;
        unset($data['access_key']);

        try {
            // access_key isn't in the vendor Privilege model's $fillable, so
            // create()/fill() silently drop it -- it has to be set as a
            // direct property assignment instead (same pattern the
            // privilege-backfill migration uses).
            $privilege = Privilege::create($data);
            $privilege->access_key = $accessKey;
            $privilege->save();

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
        $mappingCount = PrivilegeAccessKey::where('privilege_id', $privilege->id)->count();
        return view('privileges.show', compact('privilege', 'mappingCount'));
    }

    public function edit(Privilege $privilege)
    {
        $accessKeyOptions = $this->availableAccessKeyOptions($privilege->access_key);
        return view('privileges.edit', compact('privilege', 'accessKeyOptions'));
    }

    public function update(Request $request, Privilege $privilege)
    {
        $table = config('tyro.tables.privileges', 'privileges');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique($table, 'slug')->ignore($privilege->id)],
            'description' => ['nullable', 'string', 'max:1000'],
            'access_key' => ['nullable', 'string', Rule::in(AccessKeys::keys()), Rule::unique($table, 'access_key')->ignore($privilege->id)],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $newAccessKey = $data['access_key'] ?: null;
        unset($data['access_key']);

        try {
            $privilege->fill($data);

            $dirtySlug = $privilege->isDirty('slug');

            $oldAccessKey = $privilege->access_key;
            $accessKeyChanged = $oldAccessKey !== $newAccessKey;
            $privilege->access_key = $newAccessKey;

            $privilege->save();

            // If slug changed, clear cache for users affected
            if ($dirtySlug) {
                TyroCache::forgetUsersByPrivilege($privilege);
            }

            // Every role that has this privilege attached carries an
            // auto-mapped privilege_access_keys row mirroring the OLD
            // access_key. Leaving those rows pointed at the old key after
            // it changes here would silently keep granting the old access
            // and never grant the new one -- retarget (or drop) them so
            // the role's real access matches what this privilege now says.
            if ($accessKeyChanged) {
                $this->retargetAutoMappings($privilege, $oldAccessKey);
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

            // privilege_access_keys.privilege_id cascades on delete, so
            // every mapping using this privilege -- auto-linked via a role
            // or a manual direct grant -- is about to disappear along with
            // it. Record the revocation and clear the access cache for
            // each affected key first, the same way an explicit revoke does.
            $mappings = PrivilegeAccessKey::where('privilege_id', $privilege->id)->get();
            $accessService = app(AccessService::class);
            foreach ($mappings as $mapping) {
                $target = $mapping->user_id
                    ? "user #{$mapping->user_id}"
                    : ($mapping->role_id ? "role #{$mapping->role_id}" : 'everyone');
                $mapping->recordTimeline(
                    'revoked',
                    'Access Key Revoked',
                    "\"{$mapping->access_key}\" revoked from {$target} (privilege \"{$privilege->name}\" deleted).",
                    $mapping->access_key,
                    null,
                    'key-off'
                );
                $accessService->clearCacheForAccessKey($mapping->access_key);
            }

            $privilege->roles()->detach();
            $privilege->delete();

            return redirect()
                ->route('privileges.index')
                ->with('success', 'Privilege deleted successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete privilege: ' . $e->getMessage());
        }
    }

    /**
     * access_key is unique per privilege, so the dropdown only offers keys
     * not already claimed by a different privilege (plus whichever key this
     * privilege itself already holds, when editing).
     */
    private function availableAccessKeyOptions(?string $keepCurrent = null): array
    {
        $taken = Privilege::whereNotNull('access_key')
            ->when($keepCurrent, fn ($q) => $q->where('access_key', '!=', $keepCurrent))
            ->pluck('access_key')
            ->all();

        $options = [];
        foreach (AccessKeys::labels() as $key => $label) {
            if (!in_array($key, $taken, true)) {
                $options[$key] = $label;
            }
        }
        return $options;
    }

    private function retargetAutoMappings(Privilege $privilege, ?string $oldAccessKey): void
    {
        $accessService = app(AccessService::class);
        $autoMappings = PrivilegeAccessKey::where('privilege_id', $privilege->id)
            ->where('is_auto', true)
            ->get();

        foreach ($autoMappings as $mapping) {
            $from = $mapping->access_key;

            if ($privilege->access_key === null) {
                $mapping->recordTimeline(
                    'revoked',
                    'Access Key Revoked',
                    "Auto-granted \"{$from}\" removed (privilege \"{$privilege->name}\" no longer links to an access key).",
                    $from,
                    null,
                    'key-off'
                );
                $mapping->delete();
                continue;
            }

            // Guard against the unique index (privilege_id, access_key,
            // user_id, role_id): if a row already grants the new key to
            // this exact target, retargeting would collide with it -- drop
            // the now-redundant stale row instead of erroring.
            $conflict = PrivilegeAccessKey::where('privilege_id', $mapping->privilege_id)
                ->where('access_key', $privilege->access_key)
                ->where('user_id', $mapping->user_id)
                ->where('role_id', $mapping->role_id)
                ->where('id', '!=', $mapping->id)
                ->exists();

            if ($conflict) {
                $mapping->delete();
                continue;
            }

            $mapping->access_key = $privilege->access_key;
            $mapping->save();
            $mapping->recordTimeline(
                'updated',
                'Access Key Retargeted',
                "Auto-granted access key changed from \"{$from}\" to \"{$privilege->access_key}\" (privilege \"{$privilege->name}\"'s linked key changed).",
                $from,
                $privilege->access_key,
                'key'
            );
        }

        if ($oldAccessKey) {
            $accessService->clearCacheForAccessKey($oldAccessKey);
        }
        if ($privilege->access_key) {
            $accessService->clearCacheForAccessKey($privilege->access_key);
        }
    }
}
