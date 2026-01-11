<?php

namespace App\Livewire\Admin\Settings;

use App\Models\AiUsageLog;
use App\Models\AiUserQuota;
use App\Services\AI\AIClientService;
use App\Settings\AISettings;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AI extends Component
{
    public bool $enabled = true;

    #[Validate('required|string|in:anthropic,openai,mistral,groq,gemini,deepseek,ollama,openrouter')]
    public string $provider = 'anthropic';

    public ?string $model_default = null;

    public ?string $model_tutor = null;

    #[Validate('required|integer|min:1000')]
    public int $default_monthly_tokens = 100000;

    #[Validate('required|integer|min:10')]
    public int $default_daily_requests = 100;

    public bool $cache_enabled = true;

    #[Validate('required|integer|min:1|max:168')]
    public int $cache_ttl_hours = 24;

    public bool $feature_explanations = true;

    public bool $feature_tutor = true;

    public bool $feature_practice = true;

    public bool $feature_hints = true;

    public bool $feature_summaries = true;

    public bool $feature_flashcards = true;

    public function mount(AISettings $settings): void
    {
        $this->enabled = $settings->enabled;
        $this->provider = $settings->provider;
        $this->model_default = $settings->model_default;
        $this->model_tutor = $settings->model_tutor;
        $this->default_monthly_tokens = $settings->default_monthly_tokens;
        $this->default_daily_requests = $settings->default_daily_requests;
        $this->cache_enabled = $settings->cache_enabled;
        $this->cache_ttl_hours = $settings->cache_ttl_hours;
        $this->feature_explanations = $settings->feature_explanations;
        $this->feature_tutor = $settings->feature_tutor;
        $this->feature_practice = $settings->feature_practice;
        $this->feature_hints = $settings->feature_hints;
        $this->feature_summaries = $settings->feature_summaries;
        $this->feature_flashcards = $settings->feature_flashcards;
    }

    public function save(AISettings $settings): void
    {
        $this->validate();

        $settings->enabled = $this->enabled;
        $settings->provider = $this->provider;
        $settings->model_default = $this->model_default ?: null;
        $settings->model_tutor = $this->model_tutor ?: null;
        $settings->default_monthly_tokens = $this->default_monthly_tokens;
        $settings->default_daily_requests = $this->default_daily_requests;
        $settings->cache_enabled = $this->cache_enabled;
        $settings->cache_ttl_hours = $this->cache_ttl_hours;
        $settings->feature_explanations = $this->feature_explanations;
        $settings->feature_tutor = $this->feature_tutor;
        $settings->feature_practice = $this->feature_practice;
        $settings->feature_hints = $this->feature_hints;
        $settings->feature_summaries = $this->feature_summaries;
        $settings->feature_flashcards = $this->feature_flashcards;
        $settings->save();

        session()->flash('success', __('KI-Einstellungen wurden gespeichert.'));
    }

    #[Computed]
    public function providers(): array
    {
        return AIClientService::getSupportedProviders();
    }

    #[Computed]
    public function aiConfigured(): bool
    {
        return match ($this->provider) {
            'anthropic' => ! empty(config('prism.providers.anthropic.api_key')),
            'openai' => ! empty(config('prism.providers.openai.api_key')),
            'mistral' => ! empty(config('prism.providers.mistral.api_key')),
            'groq' => ! empty(config('prism.providers.groq.api_key')),
            'gemini' => ! empty(config('prism.providers.gemini.api_key')),
            'deepseek' => ! empty(config('prism.providers.deepseek.api_key')),
            'openrouter' => ! empty(config('prism.providers.openrouter.api_key')),
            'ollama' => true, // Ollama doesn't need API key
            default => false,
        };
    }

    #[Computed]
    public function usageStats(): array
    {
        $thisMonth = AiUsageLog::where('created_at', '>=', now()->startOfMonth());

        return [
            'total_requests' => $thisMonth->clone()->count(),
            'total_tokens' => $thisMonth->clone()->sum('tokens_used'),
            'unique_users' => $thisMonth->clone()->distinct('user_id')->count('user_id'),
            'avg_tokens_per_request' => $thisMonth->clone()->count() > 0
                ? round($thisMonth->clone()->sum('tokens_used') / $thisMonth->clone()->count())
                : 0,
        ];
    }

    #[Computed]
    public function quotaStats(): array
    {
        return [
            'users_with_quota' => AiUserQuota::count(),
            'users_at_limit' => AiUserQuota::whereRaw('tokens_used_this_month >= monthly_token_limit')->count(),
        ];
    }

    #[Computed]
    public function topFeatures(): array
    {
        return AiUsageLog::select('feature', DB::raw('COUNT(*) as count'), DB::raw('SUM(tokens_used) as tokens'))
            ->where('created_at', '>=', now()->startOfMonth())
            ->groupBy('feature')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'feature' => $row->feature,
                'count' => $row->count,
                'tokens' => $row->tokens,
            ])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.settings.ai')
            ->layout('layouts.admin', ['title' => __('KI-Einstellungen')]);
    }
}
