<?php

namespace App\Http\Middleware;

use App\Traits\BaseResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class UnverifiedMiddleware
{
    use BaseResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check() && !Auth::user()->is_verified) {
            return $this->unauthorizedResponse('Anda belum verifikasi.');
        }
        return $next($request);
    }
}
