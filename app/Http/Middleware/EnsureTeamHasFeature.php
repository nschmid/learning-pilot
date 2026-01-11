<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamHasFeature
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $team = $request->user()?->currentTeam;

        if (!$team) {
            return redirect()->route('dashboard')
                ->with('error', __('Bitte wählen Sie zuerst ein Team aus.'));
        }

        if (!$this->subscriptionService->canUseFeature($feature, $team)) {
            return redirect()->route('billing.plans')
                ->with('error', __('Diese Funktion ist in Ihrem aktuellen Plan nicht verfügbar. Bitte upgraden Sie Ihren Plan.'));
        }

        return $next($request);
    }
}
