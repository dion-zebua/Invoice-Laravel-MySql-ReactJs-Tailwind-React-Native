<?php

namespace App\Http\Middleware;

use App\Traits\BaseResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use BaseResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!in_array(Auth::user()->role, $roles)) {
            return $this->unauthorizedResponse('Akses tidak sah.');
        }
        return $next($request);
    }
}
