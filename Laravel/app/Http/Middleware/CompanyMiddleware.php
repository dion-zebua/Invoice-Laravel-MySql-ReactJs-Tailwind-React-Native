<?php

namespace App\Http\Middleware;

use App\Traits\BaseResponse;
use Closure;
use Illuminate\Support\Facades\Auth;

class CompanyMiddleware
{
    use BaseResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle($request, Closure $next, ...$guards)
    {

        if (Auth::check() && Auth::user()->role == "user" && !$this->isProfileIncomplete(Auth::user())) {

            return $this->unauthorizedResponse('Lengkapi profil anda.');
        }

        return $next($request);
    }

    protected function isProfileIncomplete($user)
    {
        // Memeriksa jika semua field profil lengkap
        return $user->name &&
            $user->email &&
            $user->sales &&
            $user->logo &&
            $user->address &&
            $user->telephone &&
            $user->payment_methode &&
            $user->payment_name &&
            $user->payment_number;
    }
}
