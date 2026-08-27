<?php

namespace App\Http\Controllers;

use App\Models\AiAccessRequest;
use App\Models\PrivilegeAccessKey;
use App\Models\Timeline;
use App\Models\User;
use App\Services\AccessService;
use App\Support\AccessKeys;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;

class AccessKeyMappingController extends Controller
{
    // 'profile' and 'reports_financial' never matched any route's
    // access_key, so granting them did nothing -- they're excluded from
    // App\Support\AccessKeys (the grantable/validated list) but kept here
    // so any pre-existing row using one still renders a friendly name
    // instead of a raw key.
    private array $legacyAccessKeyLabels = [
        'profile' => 'User Profile',
        'reports_financial' => 'Financial Reports',
    ];

    private function accessKeys(): array
    {
        return AccessKeys::keys();
    }

    private function accessKeyLabels(): array
    {
        return array_merge($this->legacyAccessKeyLabels, AccessKeys::labels());
    }

    /**
     * Display a listing of access key mappings.
     */
    public function index(Request $request)
    {
        $privileges = Privilege::orderBy('name')->get();
        $users = User::with('roles.privileges')->get();
        $roles = Role::with('privileges')->withCount('users')->get();

        $query = PrivilegeAccessKey::with(['privilege', 'user', 'role']);

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('access_key', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('role', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('privilege', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $mappings = $query->latest()->paginate(15);

        // Recent grant/revoke history -- recorded on every assignment and
        // deletion already (see recordTimeline() below), but nothing has
        // ever surfaced it in the UI until now.
        // `timelineable_type` is stored using the morph map alias
        // (AppServiceProvider), not the FQCN, so it must be looked up via
        // getMorphClass() rather than PrivilegeAccessKey::class directly.
        // Paginated on its own 'activity_page' query param so paging through
        // activity never collides with paging through the mappings table
        // below (both would otherwise fight over ?page=).
        $recentActivity = Timeline::where('timelineable_type', (new PrivilegeAccessKey())->getMorphClass())
            ->with('causer')
            ->latest()
            ->paginate(8, ['*'], 'activity_page')
            ->withQueryString();

        $aiAccessRequests = AiAccessRequest::with('user:id,name,email')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('access_keys.index', [
            'privileges' => $privileges,
            'accessKeys' => $this->accessKeys(),
            'accessKeyLabels' => $this->accessKeyLabels(),
            'users' => $users,
            'roles' => $roles,
            'mappings' => $mappings,
            'stats' => $this->getStats(),
            'recentActivity' => $recentActivity,
            'aiAccessRequests' => $aiAccessRequests,
        ]);
    }

    /**
     * Approve a pending "please grant me AI Assistant access" request from
     * the AI Assistant's own request-access flow. Grants the access key the
     * exact same way the manual assign form does (a privilege_access_keys
     * row keyed to the one-time-backfilled "AI Assistant" privilege).
     */
    public function approveAiAccessRequest(int $id)
    {
        $accessRequest = AiAccessRequest::findOrFail($id);
        $aiPrivilege = Privilege::where('access_key', 'ai_assistant')->first();

        if ($aiPrivilege) {
            $mapping = PrivilegeAccessKey::updateOrCreate(
                [
                    'privilege_id' => $aiPrivilege->id,
                    'access_key' => 'ai_assistant',
                    'user_id' => $accessRequest->user_id,
                    'role_id' => null,
                ],
                ['is_auto' => false]
            );
            $mapping->recordTimeline(
                'granted',
                'Access Key Granted',
                "\"ai_assistant\" granted to user #{$accessRequest->user_id} (approved access request).",
                null,
                'ai_assistant',
                'key'
            );
            app(AccessService::class)->clearCacheForAccessKey('ai_assistant');
        }

        $accessRequest->update(['status' => 'approved', 'resolved_by' => auth()->id(), 'resolved_at' => now()]);

        return back()->with('success', 'AI Assistant access granted.');
    }

    public function denyAiAccessRequest(int $id)
    {
        $accessRequest = AiAccessRequest::findOrFail($id);
        $accessRequest->update(['status' => 'denied', 'resolved_by' => auth()->id(), 'resolved_at' => now()]);

        return back()->with('success', 'Access request denied.');
    }

    /**
     * Full detail + history for a single mapping (used by the "View Details"
     * action on the mappings table).
     */
    public function show($id)
    {
        try {
            $mapping = PrivilegeAccessKey::with(['privilege', 'user', 'role', 'timeline.causer'])->findOrFail($id);

            $target = $mapping->user
                ? ['type' => 'user', 'name' => $mapping->user->name, 'email' => $mapping->user->email]
                : ($mapping->role
                    ? ['type' => 'role', 'name' => $mapping->role->name, 'email' => null]
                    : ['type' => 'unknown', 'name' => 'Unknown', 'email' => null]);

            return response()->json([
                'success' => true,
                'mapping' => [
                    'id' => $mapping->id,
                    'access_key' => $mapping->access_key,
                    'access_key_label' => $this->accessKeyLabels()[$mapping->access_key] ?? ucfirst($mapping->access_key),
                    'privilege' => $mapping->privilege->name ?? 'N/A',
                    'target_type' => $target['type'],
                    'target_name' => $target['name'],
                    'target_email' => $target['email'],
                    'created_at' => $mapping->created_at->format('M d, Y \a\t g:i A'),
                    'created_at_human' => $mapping->created_at->diffForHumans(),
                ],
                'timeline' => $mapping->timeline->map(fn ($t) => [
                    'event' => $t->event,
                    'title' => $t->title,
                    'description' => $t->description,
                    'icon' => $t->icon,
                    'causer' => $t->causer->name ?? 'System',
                    'created_at' => $t->created_at->diffForHumans(),
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('Failed to load mapping details:', ['id' => $id, 'error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Mapping not found.'
            ], 404);
        }
    }

    /**
     * Store a newly created access key mapping.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'privilege_id' => 'required|exists:privileges,id',
                'access_keys' => 'required|array|min:1',
                'access_keys.*' => 'string|in:' . implode(',', $this->accessKeys()),
                'assignment_type' => 'required|in:user,role,all_users,all_roles',
                'user_id' => 'required_if:assignment_type,user|nullable|exists:users,id',
                'role_id' => 'required_if:assignment_type,role|nullable|exists:roles,id',
            ]);

            DB::beginTransaction();

            $mappingsCreated = 0;

            switch ($validated['assignment_type']) {
                case 'user':
                    $mappingsCreated = $this->assignToUser(
                        $validated['user_id'],
                        $validated['privilege_id'],
                        $validated['access_keys']
                    );
                    break;

                case 'role':
                    $mappingsCreated = $this->assignToRole(
                        $validated['role_id'],
                        $validated['privilege_id'],
                        $validated['access_keys']
                    );
                    break;

                case 'all_users':
                    $mappingsCreated = $this->assignToAllUsers(
                        $validated['privilege_id'],
                        $validated['access_keys']
                    );
                    break;

                case 'all_roles':
                    $mappingsCreated = $this->assignToAllRoles(
                        $validated['privilege_id'],
                        $validated['access_keys']
                    );
                    break;
            }

            DB::commit();

            // Clear cache for affected access keys
            foreach ($validated['access_keys'] as $key) {
                Cache::forget("access_key_slugs:{$key}");
                Cache::forget("access_key_mappings:{$key}");
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully assigned {$mappingsCreated} access key(s).",
                'mappings_count' => $mappingsCreated
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Access key assignment failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign access keys: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Human-friendly label for an access key slug, e.g. "product_images" -> "Product Images".
     */
    private function friendlyKey(string $key): string
    {
        return $this->accessKeyLabels()[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Assign access keys to a specific user.
     */
    private function assignToUser(int $userId, int $privilegeId, array $accessKeys): int
    {
        $userName = User::find($userId)?->name ?? "user #{$userId}";
        $count = 0;
        foreach ($accessKeys as $key) {
            $mapping = PrivilegeAccessKey::updateOrCreate(
                [
                    'privilege_id' => $privilegeId,
                    'access_key' => $key,
                    'user_id' => $userId,
                ],
                ['role_id' => null]
            );
            $mapping->recordTimeline('granted', 'Access Key Granted', "\"{$this->friendlyKey($key)}\" access granted to {$userName}.", null, $key, 'key');
            $count++;
        }
        return $count;
    }

    /**
     * Assign access keys to a specific role.
     */
    private function assignToRole(int $roleId, int $privilegeId, array $accessKeys): int
    {
        $roleName = Role::find($roleId)?->name ?? "role #{$roleId}";
        $count = 0;
        foreach ($accessKeys as $key) {
            $mapping = PrivilegeAccessKey::updateOrCreate(
                [
                    'privilege_id' => $privilegeId,
                    'access_key' => $key,
                    'role_id' => $roleId,
                ],
                ['user_id' => null]
            );
            $mapping->recordTimeline('granted', 'Access Key Granted', "\"{$this->friendlyKey($key)}\" access granted to everyone with the {$roleName} role.", null, $key, 'key');
            $count++;
        }
        return $count;
    }

    /**
     * Assign access keys to all users.
     */
    private function assignToAllUsers(int $privilegeId, array $accessKeys): int
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            foreach ($accessKeys as $key) {
                $mapping = PrivilegeAccessKey::updateOrCreate(
                    [
                        'privilege_id' => $privilegeId,
                        'access_key' => $key,
                        'user_id' => $user->id,
                    ],
                    ['role_id' => null]
                );
                $mapping->recordTimeline('granted', 'Access Key Granted', "\"{$this->friendlyKey($key)}\" access granted to {$user->name} (bulk: all users).", null, $key, 'key');
                $count++;
            }
        }

        return $count;
    }

    /**
     * Assign access keys to all roles.
     */
    private function assignToAllRoles(int $privilegeId, array $accessKeys): int
    {
        $roles = Role::all();
        $count = 0;

        foreach ($roles as $role) {
            foreach ($accessKeys as $key) {
                $mapping = PrivilegeAccessKey::updateOrCreate(
                    [
                        'privilege_id' => $privilegeId,
                        'access_key' => $key,
                        'role_id' => $role->id,
                    ],
                    ['user_id' => null]
                );
                $mapping->recordTimeline('granted', 'Access Key Granted', "\"{$this->friendlyKey($key)}\" access granted to everyone with the {$role->name} role (bulk: all roles).", null, $key, 'key');
                $count++;
            }
        }

        return $count;
    }

    /**
     * Search for users or mappings.
     *
     * Mapping search also accepts optional filters/sorting so the
     * "Existing Mappings" table can be narrowed down without paging
     * through everything:
     *   - access_key: exact access key slug to filter to
     *   - assignment_type: 'user' | 'role'
     *   - sort: 'latest' (default) | 'oldest' | 'access_key_asc' |
     *           'access_key_desc' | 'name_asc' | 'name_desc'
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        $type = $request->get('type', 'mappings');

        if ($type === 'users') {
            $users = User::where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->limit(10)
                ->get(['id', 'name', 'email']);

            return response()->json($users);
        }

        $accessKeyFilter = $request->get('access_key');
        $assignmentTypeFilter = $request->get('assignment_type');
        $sort = $request->get('sort', 'latest');

        $builder = PrivilegeAccessKey::with(['user', 'privilege', 'role'])
            ->where(function($q) use ($query) {
                $q->where('access_key', 'like', "%{$query}%")
                  ->orWhereHas('user', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                  })
                  ->orWhereHas('role', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  })
                  ->orWhereHas('privilege', function($q) use ($query) {
                      $q->where('name', 'like', "%{$query}%");
                  });
            })
            ->when($accessKeyFilter, fn($q) => $q->where('access_key', $accessKeyFilter))
            ->when($assignmentTypeFilter === 'user', fn($q) => $q->whereNotNull('user_id'))
            ->when($assignmentTypeFilter === 'role', fn($q) => $q->whereNotNull('role_id'));

        switch ($sort) {
            case 'oldest':
                $builder->oldest();
                break;
            case 'access_key_asc':
                $builder->orderBy('access_key');
                break;
            case 'access_key_desc':
                $builder->orderBy('access_key', 'desc');
                break;
            case 'name_asc':
            case 'name_desc':
                $direction = $sort === 'name_desc' ? 'desc' : 'asc';
                $builder->leftJoin('users', 'users.id', '=', 'privilege_access_keys.user_id')
                    ->leftJoin('roles', 'roles.id', '=', 'privilege_access_keys.role_id')
                    ->select('privilege_access_keys.*')
                    ->orderByRaw("COALESCE(users.name, roles.name) {$direction}");
                break;
            default:
                $builder->latest();
        }

        $mappings = $builder->paginate(15);

        return response()->json($mappings);
    }

    /**
     * Get a full access breakdown for a single user: keys assigned to them
     * directly, plus keys they inherit through each of their roles.
     */
    public function userAccess($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roleIds = $user->roles->pluck('id');

        $labelFor = fn ($key) => $this->accessKeyLabels()[$key] ?? $key;

        $direct = PrivilegeAccessKey::with('privilege')
            ->where('user_id', $user->id)
            ->get()
            ->map(fn ($mapping) => [
                'access_key' => $mapping->access_key,
                'label' => $labelFor($mapping->access_key),
                'privilege' => $mapping->privilege->name ?? null,
            ])
            ->values();

        $viaRole = PrivilegeAccessKey::with(['privilege', 'role'])
            ->whereIn('role_id', $roleIds)
            ->get()
            ->groupBy('role_id')
            ->map(function ($mappings) use ($labelFor) {
                return [
                    'role' => $mappings->first()->role->name ?? 'Unknown Role',
                    'access' => $mappings->map(fn ($mapping) => [
                        'access_key' => $mapping->access_key,
                        'label' => $labelFor($mapping->access_key),
                        'privilege' => $mapping->privilege->name ?? null,
                    ])->values(),
                ];
            })
            ->values();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'direct' => $direct,
            'via_role' => $viaRole,
        ]);
    }

    /**
     * Remove the specified access key mapping.
     */
    public function destroy($id)
    {
        try {
            $mapping = PrivilegeAccessKey::with(['user', 'role'])->findOrFail($id);
            $accessKey = $mapping->access_key;

            // Recorded before delete() -- the timeline row survives its
            // subject being deleted (that's the point of an audit trail),
            // so the audit log still shows this revocation afterward.
            $target = $mapping->user->name ?? ($mapping->role ? "everyone with the {$mapping->role->name} role" : 'everyone');
            $mapping->recordTimeline('revoked', 'Access Key Revoked', "\"{$this->friendlyKey($accessKey)}\" access revoked from {$target}.", $accessKey, null, 'key-off');

            $mapping->delete();

            // Clear cache for this access key
            Cache::forget("access_key_slugs:{$accessKey}");
            Cache::forget("access_key_mappings:{$accessKey}");

            return response()->json([
                'success' => true,
                'message' => 'Mapping removed successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Failed to delete mapping:', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove mapping: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete access key mappings.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:privilege_access_keys,id'
        ]);

        try {
            $mappings = PrivilegeAccessKey::with(['user', 'role'])->whereIn('id', $request->ids)->get();

            // Get unique access keys for cache clearing
            $accessKeys = $mappings->pluck('access_key')->unique();

            foreach ($mappings as $mapping) {
                $target = $mapping->user->name ?? ($mapping->role ? "everyone with the {$mapping->role->name} role" : 'everyone');
                $mapping->recordTimeline('revoked', 'Access Key Revoked', "\"{$this->friendlyKey($mapping->access_key)}\" access revoked from {$target} (bulk action).", $mapping->access_key, null, 'key-off');
            }

            // Delete mappings
            PrivilegeAccessKey::whereIn('id', $request->ids)->delete();

            // Clear cache for affected access keys
            foreach ($accessKeys as $key) {
                Cache::forget("access_key_slugs:{$key}");
                Cache::forget("access_key_mappings:{$key}");
            }

            return response()->json([
                'success' => true,
                'message' => count($request->ids) . ' mappings removed successfully.'
            ]);
        } catch (Exception $e) {
            Log::error('Bulk delete failed:', [
                'ids' => $request->ids,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to remove mappings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics for the dashboard.
     */
    private function getStats(): array
    {
        return [
            'total_mappings' => PrivilegeAccessKey::count(),
            'total_users_with_access' => PrivilegeAccessKey::whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'total_roles_with_access' => PrivilegeAccessKey::whereNotNull('role_id')->distinct('role_id')->count('role_id'),
            'most_assigned_key' => PrivilegeAccessKey::select('access_key', DB::raw('count(*) as total'))
                ->groupBy('access_key')
                ->orderBy('total', 'desc')
                ->first()
        ];
    }
}
