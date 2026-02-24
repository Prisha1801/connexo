<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckOtpVerification
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_otp_verified == 0) {
            return redirect()->route('verify-otp');
        }

        return $next($request);
    }
}
