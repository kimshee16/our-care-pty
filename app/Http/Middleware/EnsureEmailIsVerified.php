<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionUser = session('user');
        
        if (!$sessionUser) {
            return redirect('/login');
        }

        $user = User::find($sessionUser['id'] ?? null);

        if (!$user) {
            return redirect('/login');
        }

        // Check if email is verified
        if (!$user->verified && !$user->email_verified_at) {
            return redirect('/email-verification-pending')
                ->with('status', 'Please verify your email address before proceeding.')
                ->with('email', $user->email);
        }

        return $next($request);
    }
}
