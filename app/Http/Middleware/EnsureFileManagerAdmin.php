<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureFileManagerAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (! session('user')) {
            return redirect('/login');
        }
        abort_unless(session('user.accounttype') === 'admin', 403);

        return $next($request);
    }
}
