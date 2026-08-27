<?php

use HasinHayder\Tyro\Models\Privilege;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * The real access_key strings routes/web.php actually gates routes with
     * (kept in sync with AccessKeyMappingController::$accessKeyLabels, minus
     * the two orphaned labels -- 'profile' and 'reports_financial' -- that
     * never matched a real route).
     */
    private function accessKeyLabels(): array
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
        ];
    }

    public function up(): void
    {
        foreach ($this->accessKeyLabels() as $key => $label) {
            $privilege = Privilege::where('access_key', $key)->first();
            if ($privilege) {
                continue;
            }

            $slug = $key;
            $suffix = 1;
            while (Privilege::where('slug', $slug)->exists()) {
                $slug = $key . '-' . (++$suffix);
            }

            // access_key isn't in the vendor model's $fillable, so it can't be
            // mass-assigned via create() -- set it directly instead, which
            // bypasses $fillable the same way any single-attribute assignment does.
            $privilege = Privilege::create([
                'name' => $label,
                'slug' => $slug,
                'description' => 'Grants the "' . $label . '" access key when assigned to a role.',
            ]);
            $privilege->access_key = $key;
            $privilege->save();
        }
    }

    public function down(): void
    {
        Privilege::whereIn('access_key', array_keys($this->accessKeyLabels()))->delete();
    }
};
