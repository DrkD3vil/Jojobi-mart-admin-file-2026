<?php

namespace App\Support;

/**
 * The single canonical list of access_key strings routes/web.php actually
 * gates routes with. AccessKeyMappingController and PrivilegeController both
 * validate against this, so there's one place that says what a valid
 * access_key even is -- instead of two arrays that can quietly drift apart.
 *
 * (The one-time privilege-backfill migration keeps its own frozen copy of
 * this list, by design: migrations shouldn't depend on application code that
 * can change after they've already run.)
 */
class AccessKeys
{
    public static function labels(): array
    {
        return [
            'rbac' => 'Role & Permissions',
            'roles' => 'Roles Management',
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
            'dashboard' => 'Financial Dashboard',
            'expenses' => 'Expenses',
            'settings' => 'Store Settings',
            'ai_assistant' => 'AI Assistant',
        ];
    }

    public static function keys(): array
    {
        return array_keys(self::labels());
    }

    public static function label(string $key): string
    {
        return self::labels()[$key] ?? $key;
    }
}
