<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $required = null): Response
    {
        $user = session('user');
        if (!$user) {
            return redirect('/login');
        }

        if ($required && $user['accounttype'] !== $required) {
            switch ($user['accounttype']) {
                case 'admin':
                    return redirect('/admin-registrations');
                case 'client':
                    return redirect('/client-dashboard');
                case 'healthcare_worker':
                    return redirect('/healthcare-jobs');
            }
        }

        return $next($request);
    }
}
