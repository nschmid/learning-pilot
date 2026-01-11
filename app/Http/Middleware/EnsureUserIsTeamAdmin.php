<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTeamAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $team = $user?->currentTeam;

        if (!$team) {
            return redirect()->route('dashboard')
                ->with('error', __('Bitte wählen Sie zuerst ein Team aus.'));
        }

        // Check if user is team owner or has admin role on the team
        $isOwner = $team->user_id === $user->id;
        $isAdmin = $user->hasTeamRole($team, 'admin');

        if (!$isOwner && !$isAdmin) {
            abort(403, __('Sie haben keine Berechtigung für den Schuladmin-Bereich.'));
        }

        return $next($request);
    }
}
