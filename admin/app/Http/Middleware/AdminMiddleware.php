<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log that the middleware is being executed
        Log::info('AdminMiddleware executed', ['user_id' => auth()->id(), 'request_url' => $request->url()]);

        // Ensure the user is authenticated
        if (!auth()->check()) {
            Log::warning('Unauthenticated user attempted to access admin route', ['request_url' => $request->url()]);
            return redirect()->route('tyro-login.login');
        }

        // Check if the user has the admin role (make sure 'admin' exists in the role table)
        if (auth()->user()->hasRole('admin')) {
            Log::info('User is an admin', ['user_id' => auth()->id()]);
            return $next($request);
        }

        // If not admin, deny access and log the attempt
        Log::warning('User without admin role attempted to access admin route', ['user_id' => auth()->id(), 'request_url' => $request->url()]);

        return response()->json(['message' => 'You do not have admin access.'], 403);
    }
}
