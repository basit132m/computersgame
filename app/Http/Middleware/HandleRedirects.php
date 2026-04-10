<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/' . ltrim($request->getPathInfo(), '/');

        $redirects = Cache::remember('all_redirects', 3600, function () {
            return Redirect::where('active', true)->get(['from_url', 'to_url', 'type']);
        });

        foreach ($redirects as $redirect) {
            if ($redirect->from_url === $path) {
                return redirect($redirect->to_url, $redirect->type);
            }
        }

        return $next($request);
    }
}
