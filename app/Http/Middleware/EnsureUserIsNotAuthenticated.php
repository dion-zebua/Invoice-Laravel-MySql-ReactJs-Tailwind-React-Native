<?php

namespace App\Http\Middleware;

use App\Traits\BaseResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureUserIsNotAuthenticated
{
    use BaseResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('sanctum')->user()) {
            return $this->unauthorizedResponse('Anda terautentikasi.');
        }

        return $next($request);
    }
}
