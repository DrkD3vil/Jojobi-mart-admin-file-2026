<?php

namespace App\Http\Middleware;

use App\Services\AccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TyroAccess
{
    // public function handle(Request $request, Closure $next): Response
    // {
    //     $route = $request->route();
    //     $accessKey = $route?->defaults['access_key'] ?? null;

    //     // No access_key means: not protected by this system
    //     if (!$accessKey) {
    //         return $next($request);
    //     }

    //     // Always allow logged-in user to hit logout etc if you need:
    //     $ignoreRouteNames = [
    //         'me',
    //         'profile.edit',
    //         'profile.update',
    //         'password.change.form',
    //         'password.change',
    //     ];

    //     $routeName = $route?->getName();
    //     if ($routeName && in_array($routeName, $ignoreRouteNames, true)) {
    //         return $next($request);
    //     }

    //     $user = $request->user();
    //     $service = app(AccessService::class);

    //     if (!$service->canAccessKey($user, $accessKey)) {
    //         abort(403, "Access denied for module: {$accessKey}");
    //     }

    //     return $next($request);
    // }


    // public function handle($request, Closure $next)
    // {
    //     $user = auth()->user();
    //     if (!$user) return redirect()->route('tyro-login.login');

    //     // $accessKey = $request->route()?->defaults['access_key'] ?? null;
    //     $accessKey = $request->route()?->parameter('access_key')
    //         ?? $request->route()?->getAction('access_key');

    //         dd($request->route()->getAction());
    //     // If route doesn't define access_key, allow (safe)
    //     if (!$accessKey) return $next($request);

    //     // Map access_key to permission slug
    //     $requiredPrivilege = match ($accessKey) {
    //         'rbac'        => 'rbac.manage',
    //         'roles'       => 'roles.manage',
    //         'privileges'  => 'privileges.manage',
    //         'user_roles'  => 'users.manage',
    //         'products'    => 'products.view',
    //         'categories'  => 'categories.view',
    //         'brands'      => 'brands.view',
    //         'orders'      => 'orders.view',
    //         'customers'   => 'customers.view',
    //         'expenses'    => 'expenses.manage',
    //         'dashboard'   => 'dashboard.view',
    //         default       => $accessKey . '.view',
    //     };

    //     if (!$user->hasPrivilege($requiredPrivilege)) {
    //         abort(403, "You don't have access.");
    //     }

    //     return $next($request);
    // }

    public function handle($request, Closure $next)
{
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('tyro-login.login');
    }

    $accessKey = $request->route()?->getAction('defaults')['access_key'] ?? null;
        // dd($accessKey);
    if (!$accessKey) {
        return $next($request);
    }

    $requiredPrivilege = match ($accessKey) {
        'rbac'        => 'rbac.manage',
        'roles'       => 'roles.manage',
        'privileges'  => 'privileges.manage',
        'user_roles'  => 'users.manage',
        'products'    => 'products.view',
        'categories'  => 'categories.view',
        'brands'      => 'brands.view',
        'orders'      => 'orders.view',
        'customers'   => 'customers.view',
        'expenses'    => 'expenses.manage',
        'dashboard'   => 'dashboard.view',
        default       => $accessKey . '.view',
    };

//     dd(
//     $requiredPrivilege,
//     $user->roles->pluck('slug'),
//     $user->roles->flatMap->privileges->pluck('slug')
// );

    if (!$user->hasPrivilege($requiredPrivilege)) {
        abort(403, "You don't have access.");
    }

    return $next($request);
}
}
