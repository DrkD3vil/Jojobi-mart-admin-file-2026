<?php

namespace App\Services;

use App\Models\PrivilegeAccessKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccessService
{
    public bool $allowIfNoMapping;

    public function __construct()
    {
        // For security, keep false (DENY if admin hasn't mapped anything)
        $this->allowIfNoMapping = false;
    }

    public function canAccessKey($user, string $accessKey): bool
    {
        if (!$user) {
            return false;
        }

        // Super admin bypass
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        // Check if user has direct permission through roles
        if (method_exists($user, 'hasPrivilege')) {
            // Map access key to privilege slug
            $privilegeSlug = $this->mapAccessKeyToPrivilege($accessKey);
            if ($privilegeSlug && $user->hasPrivilege($privilegeSlug)) {
                return true;
            }
        }

        // Cache allowed privilege slugs per access_key
        $slugs = Cache::remember("access_key_slugs:{$accessKey}", 3600, function () use ($accessKey) {
            return PrivilegeAccessKey::query()
                ->join('privileges', 'privileges.id', '=', 'privilege_access_keys.privilege_id')
                ->where('privilege_access_keys.access_key', $accessKey)
                ->pluck('privileges.slug')
                ->unique()
                ->values()
                ->toArray();
        });

        if (empty($slugs)) {
            return $this->allowIfNoMapping;
        }

        // Check if user has any of the required privileges
        foreach ($slugs as $slug) {
            if (method_exists($user, 'can') && $user->can($slug)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Map access key to privilege slug for backward compatibility.
     */
    private function mapAccessKeyToPrivilege(string $accessKey): ?string
    {
        return match ($accessKey) {
            'rbac' => 'rbac.manage',
            'roles' => 'roles.manage',
            'privileges' => 'privileges.manage',
            'user_roles' => 'users.manage',
            'products' => 'products.view',
            'categories' => 'categories.view',
            'brands' => 'brands.view',
            'orders' => 'orders.view',
            'customers' => 'customers.view',
            'expenses' => 'expenses.manage',
            'dashboard' => 'dashboard.view',
            default => $accessKey . '.view',
        };
    }

    public function clearCacheForAccessKey(string $accessKey): void
    {
        Cache::forget("access_key_slugs:{$accessKey}");
    }

    public function clearAllCache(): void
    {
        // Clear all access key cache
        $keys = PrivilegeAccessKey::distinct('access_key')->pluck('access_key');
        foreach ($keys as $key) {
            Cache::forget("access_key_slugs:{$key}");
        }
    }
}
