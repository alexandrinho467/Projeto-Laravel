<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CrmMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->hasCrmAccess()) {
            abort(403);
        }
        return $next($request);
    }
}
