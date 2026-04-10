<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, ['admin', 'editor'])) {
            return redirect()->route('login')->with('error', 'غير مصرح بالدخول');
        }

        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'حسابك معطل. تواصل مع المدير.');
        }

        return $next($request);
    }
}
