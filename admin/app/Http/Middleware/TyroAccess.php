<?php

namespace App\Http\Middleware;

use App\Services\AccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TyroAccess
{
    protected AccessService $accessService;

    public function __construct(AccessService $accessService)
    {
        $this->accessService = $accessService;
    }

    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('tyro-login.login');
        }

        // Admin & super admin always bypass access-key checks
        if (method_exists($user, 'hasRole') && ($user->hasRole('admin') || $user->hasRole('super-admin'))) {
            return $next($request);
        }

        $accessKey = $request->route()?->getAction('defaults')['access_key'] ?? null;

        if (!$accessKey) {
            // If no access key specified, allow access (or deny based on your preference)
            return $next($request);
        }

        // Check if user has access to this module
        if ($this->accessService->canAccessKey($user, $accessKey)) {
            return $next($request);
        }

        // Log unauthorized access attempt
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'email' => $user->email,
            'access_key' => $accessKey,
            'route' => $request->route()->getName() ?? 'unknown',
            'url' => $request->fullUrl()
        ]);

        abort(403, "You don't have permission to access this resource.");
    }
}
