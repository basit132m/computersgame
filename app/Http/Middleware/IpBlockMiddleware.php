<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class IpBlockMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        $blocked = Cache::remember("blocked_ip_{$ip}", 300, function () use ($ip) {
            return BlockedIp::where('ip_address', $ip)->exists();
        });

        if ($blocked) {
            abort(403, 'تم حظر عنوان IP الخاص بك');
        }

        return $next($request);
    }
}
