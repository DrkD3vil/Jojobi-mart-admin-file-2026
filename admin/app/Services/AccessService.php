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

        // Super admin & Admin bypass
        if (method_exists($user, 'hasRole') && ($user->hasRole('super-admin') || $user->hasRole('admin'))) {
            return true;
        }
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        // Cache allowed mappings per access_key
        $mappings = Cache::remember("access_key_mappings:{$accessKey}", 3600, function () use ($accessKey) {
            return PrivilegeAccessKey::query()
                ->where('access_key', $accessKey)
                ->select(['user_id', 'role_id'])
                ->get()
                ->toArray();
        });

        if (empty($mappings)) {
            return $this->allowIfNoMapping;
        }

        $userRoleIds = method_exists($user, 'roles')
            ? array_map('intval', $user->roles->pluck('id')->toArray())
            : [];

        // A matching mapping row (by user, by role, or global) is itself the grant.
        // The linked privilege is a label for the mapping, not a second gate that
        // also requires being separately attached to the role via the Roles page.
        foreach ($mappings as $mapping) {
            $userId = $mapping['user_id'];
            $roleId = $mapping['role_id'];

            // User-specific assignment
            if ($userId !== null && (int)$userId === (int)$user->id) {
                return true;
            }
            // Role-specific assignment
            if ($roleId !== null && in_array((int)$roleId, $userRoleIds, true)) {
                return true;
            }
            // Global access key mapping (if both are null)
            if ($userId === null && $roleId === null) {
                return true;
            }
        }

        return false;
    }

    public function clearCacheForAccessKey(string $accessKey): void
    {
        Cache::forget("access_key_slugs:{$accessKey}");
        Cache::forget("access_key_mappings:{$accessKey}");
    }

    public function clearAllCache(): void
    {
        // Clear all access key cache
        $keys = PrivilegeAccessKey::distinct('access_key')->pluck('access_key');
        foreach ($keys as $key) {
            Cache::forget("access_key_slugs:{$key}");
            Cache::forget("access_key_mappings:{$key}");
        }
    }
}
