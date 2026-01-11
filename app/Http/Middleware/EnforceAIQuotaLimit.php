<?php

namespace App\Http\Middleware;

use App\Enums\AiServiceType;
use App\Exceptions\AIQuotaExceededException;
use App\Services\AI\AIUsageService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceAIQuotaLimit
{
    public function __construct(
        protected AIUsageService $usageService
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $serviceType  Optional service type to check specific feature quota
     */
    public function handle(Request $request, Closure $next, ?string $serviceType = null): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->handleQuotaExceeded($request, __('Bitte melden Sie sich an, um KI-Funktionen zu nutzen.'));
        }

        try {
            // If a specific service type is provided, check that feature's quota
            if ($serviceType) {
                $aiServiceType = AiServiceType::tryFrom($serviceType);
                if ($aiServiceType) {
                    $this->usageService->checkQuota($user, $aiServiceType);
                }
            } else {
                // Check general quota (use Explanation as default check)
                $this->usageService->checkQuota($user, AiServiceType::Explanation);
            }
        } catch (AIQuotaExceededException $e) {
            return $this->handleQuotaExceeded($request, $e->getMessage());
        }

        return $next($request);
    }

    /**
     * Handle quota exceeded response based on request type.
     */
    protected function handleQuotaExceeded(Request $request, string $message): Response
    {
        if ($request->expectsJson() || $request->is('livewire/*')) {
            return response()->json([
                'error' => true,
                'message' => $message,
                'quota_exceeded' => true,
            ], 429);
        }

        return redirect()->back()->with('error', $message);
    }
}
