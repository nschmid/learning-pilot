<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Convert string roles to UserRole enum
        $allowedRoles = array_map(
            fn ($role) => UserRole::tryFrom($role),
            $roles
        );

        // Check if user has one of the allowed roles
        if (in_array($user->role, $allowedRoles, true)) {
            return $next($request);
        }

        // Redirect based on user's actual role
        return match ($user->role) {
            UserRole::Admin => redirect()->route('admin.dashboard'),
            UserRole::Instructor => redirect()->route('instructor.dashboard'),
            default => redirect()->route('learner.dashboard'),
        };
    }
}
