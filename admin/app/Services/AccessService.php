<?php

namespace App\Services;

use App\Models\PrivilegeAccessKey;
use Illuminate\Support\Facades\Cache;

class AccessService
{
    public bool $allowIfNoMapping;

    public function __construct()
    {
        // For security, keep false (DENY if admin hasn't mapped anything).
        $this->allowIfNoMapping = false;
    }

    public function canAccessKey($user, string $accessKey): bool
    {
        if (!$user) return false;

        // Admin bypass (optional)
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }

        // Cache allowed privilege slugs per access_key
        $slugs = Cache::remember("access_key_slugs:{$accessKey}", 60, function () use ($accessKey) {
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

        // User has ANY of those privileges
        foreach ($slugs as $slug) {
            if ($user->can($slug)) {
                return true;
            }
        }

        return false;
    }

    public function clearCacheForAccessKey(string $accessKey): void
    {
        Cache::forget("access_key_slugs:{$accessKey}");
    }
}
