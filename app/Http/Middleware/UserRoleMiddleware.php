<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if(array_intersect($roles, Auth::user()->roles->pluck('role')->toArray())) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.'); //forbidden
        
    }
}
