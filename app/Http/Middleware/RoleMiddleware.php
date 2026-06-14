<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard): Response
    {
        if (!Auth::guard($guard)->check()) {
            return redirect()->route('login');
        }
        $user = Auth::guard($guard)->user();
        $expectedRole = ($guard === 'admin') ? 'admin' : 'users';
        if ($user->role !== $expectedRole) {
            Auth::guard($guard)->logout();
            return redirect()->route('login')->withErrors(['email' => 'Akses tidak diizinkan.']);
        }
        return $next($request);
    }
}
