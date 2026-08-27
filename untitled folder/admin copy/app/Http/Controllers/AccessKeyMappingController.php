<?php

namespace App\Http\Controllers;

use App\Models\PrivilegeAccessKey;
use App\Models\User;
use HasinHayder\Tyro\Models\Privilege;
use HasinHayder\Tyro\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Exception;

class AccessKeyMappingController extends Controller
{
    private array $accessKeys = [
        'profile', 'rbac', 'categories', 'brands', 'products',
        'product_images', 'product_batches', 'product_statuses', 'pos',
        'gift_audit', 'customers', 'orders', 'locations', 'returns',
        'stock', 'reports_financial', 'expenses', 'user_roles', 'privileges'
    ];

    private array $accessKeyLabels = [
        'profile' => 'User Profile',
        'rbac' => 'Role & Permissions',
        'user_roles' => 'User Roles',
        'privileges' => 'Privileges',
        'categories' => 'Categories',
        'brands' => 'Brands',
        'products' => 'Products',
        'product_images' => 'Product Images',
        'product_batches' => 'Product Batches',
        'product_statuses' => 'Product Statuses',
        'pos' => 'Point of Sale',
        'gift_audit' => 'Gift Audit',
        'customers' => 'Customers',
        'orders' => 'Orders',
        'locations' => 'Locations',
        'returns' => 'Returns',
        'stock' => 'Stock Management',
        'reports_financial' => 'Financial Reports',
        'expenses' => 'Expenses'
    ];

    /**
     * Display a listing of access key mappings.
     */
    public function index(Request $request)
    {
        $privileges = Privilege::orderBy('name')->get();
        $users = User::with('roles')->get();
        $roles = Role::withCount('users')->get();

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

        return view('access_keys.index', [
            'privileges' => $privileges,
            'accessKeys' => $this->accessKeys,
            'accessKeyLabels' => $this->accessKeyLabels,
            'users' => $users,
            'roles' => $roles,
            'mappings' => $mappings,
            'stats' => $this->getStats()
        ]);
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
                'access_keys.*' => 'string|in:' . implode(',', $this->accessKeys),
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
     * Assign access keys to a specific user.
     */
    private function assignToUser(int $userId, int $privilegeId, array $accessKeys): int
    {
        $count = 0;
        foreach ($accessKeys as $key) {
            PrivilegeAccessKey::updateOrCreate(
                [
                    'privilege_id' => $privilegeId,
                    'access_key' => $key,
                    'user_id' => $userId,
                ],
                ['role_id' => null]
            );
            $count++;
        }
        return $count;
    }

    /**
     * Assign access keys to a specific role.
     */
    private function assignToRole(int $roleId, int $privilegeId, array $accessKeys): int
    {
        $count = 0;
        foreach ($accessKeys as $key) {
            PrivilegeAccessKey::updateOrCreate(
                [
                    'privilege_id' => $privilegeId,
                    'access_key' => $key,
                    'role_id' => $roleId,
                ],
                ['user_id' => null]
            );
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
                PrivilegeAccessKey::updateOrCreate(
                    [
                        'privilege_id' => $privilegeId,
                        'access_key' => $key,
                        'user_id' => $user->id,
                    ],
                    ['role_id' => null]
                );
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
                PrivilegeAccessKey::updateOrCreate(
                    [
                        'privilege_id' => $privilegeId,
                        'access_key' => $key,
                        'role_id' => $role->id,
                    ],
                    ['user_id' => null]
                );
                $count++;
            }
        }

        return $count;
    }

    /**
     * Search for users or mappings.
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

        $mappings = PrivilegeAccessKey::with(['user', 'privilege', 'role'])
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
            ->paginate(15);

        return response()->json($mappings);
    }

    /**
     * Remove the specified access key mapping.
     */
    public function destroy($id)
    {
        try {
            $mapping = PrivilegeAccessKey::findOrFail($id);
            $accessKey = $mapping->access_key;
            $mapping->delete();

            // Clear cache for this access key
            Cache::forget("access_key_slugs:{$accessKey}");

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
            $mappings = PrivilegeAccessKey::whereIn('id', $request->ids)->get();

            // Get unique access keys for cache clearing
            $accessKeys = $mappings->pluck('access_key')->unique();

            // Delete mappings
            PrivilegeAccessKey::whereIn('id', $request->ids)->delete();

            // Clear cache for affected access keys
            foreach ($accessKeys as $key) {
                Cache::forget("access_key_slugs:{$key}");
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
