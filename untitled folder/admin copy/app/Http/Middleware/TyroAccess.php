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

        // Super admin bypass (optional - you can remove this if you want strict access control)
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
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
