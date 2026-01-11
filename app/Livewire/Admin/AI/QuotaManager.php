<?php

namespace App\Livewire\Admin\AI;

use App\Models\AiUserQuota;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class QuotaManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filter = 'all';

    public bool $showEditModal = false;

    public ?string $editingQuotaId = null;

    #[Validate('required|integer|min:0')]
    public int $monthlyTokenLimit = 100000;

    #[Validate('required|integer|min:0')]
    public int $dailyRequestLimit = 100;

    public bool $featureExplanations = true;

    public bool $featureTutor = true;

    public bool $featurePractice = true;

    public bool $featureSummaries = true;

    #[Computed]
    public function quotas()
    {
        $query = AiUserQuota::with('user:id,name,email');

        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        $query = match ($this->filter) {
            'near_limit' => $query->where('monthly_token_limit', '>', 0)
                ->whereRaw('tokens_used_this_month >= monthly_token_limit * 0.9'),
            'over_limit' => $query->where('monthly_token_limit', '>', 0)
                ->whereRaw('tokens_used_this_month >= monthly_token_limit'),
            'inactive' => $query->where(function ($q) {
                $q->whereNull('last_request_at')
                    ->orWhere('last_request_at', '<', now()->subDays(30));
            }),
            'active' => $query->where('last_request_at', '>=', now()->subDays(7)),
            default => $query,
        };

        return $query->orderByDesc('tokens_used_this_month')->paginate(20);
    }

    #[Computed]
    public function statistics(): array
    {
        return [
            'total_quotas' => AiUserQuota::count(),
            'active_users' => AiUserQuota::where('last_request_at', '>=', now()->subDays(7))->count(),
            'near_limit' => AiUserQuota::where('monthly_token_limit', '>', 0)
                ->whereRaw('tokens_used_this_month >= monthly_token_limit * 0.9')->count(),
            'over_limit' => AiUserQuota::where('monthly_token_limit', '>', 0)
                ->whereRaw('tokens_used_this_month >= monthly_token_limit')->count(),
        ];
    }

    public function editQuota(string $quotaId): void
    {
        $quota = AiUserQuota::findOrFail($quotaId);

        $this->editingQuotaId = $quotaId;
        $this->monthlyTokenLimit = $quota->monthly_token_limit;
        $this->dailyRequestLimit = $quota->daily_request_limit;
        $this->featureExplanations = $quota->feature_explanations_enabled ?? true;
        $this->featureTutor = $quota->feature_tutor_enabled ?? true;
        $this->featurePractice = $quota->feature_practice_enabled ?? true;
        $this->featureSummaries = $quota->feature_summaries_enabled ?? true;
        $this->showEditModal = true;
    }

    public function updateQuota(): void
    {
        $this->validate();

        $quota = AiUserQuota::findOrFail($this->editingQuotaId);

        $quota->update([
            'monthly_token_limit' => $this->monthlyTokenLimit,
            'daily_request_limit' => $this->dailyRequestLimit,
            'feature_explanations_enabled' => $this->featureExplanations,
            'feature_tutor_enabled' => $this->featureTutor,
            'feature_practice_enabled' => $this->featurePractice,
            'feature_summaries_enabled' => $this->featureSummaries,
        ]);

        $this->showEditModal = false;
        $this->editingQuotaId = null;

        session()->flash('success', __('Quota erfolgreich aktualisiert.'));
    }

    public function resetMonthlyUsage(string $quotaId): void
    {
        $quota = AiUserQuota::findOrFail($quotaId);
        $quota->resetMonthlyTokens();

        session()->flash('success', __('Monatliche Nutzung wurde zurückgesetzt.'));
    }

    public function resetDailyUsage(string $quotaId): void
    {
        $quota = AiUserQuota::findOrFail($quotaId);
        $quota->resetDailyRequests();

        session()->flash('success', __('Tägliche Anfragen wurden zurückgesetzt.'));
    }

    public function createQuotaForUser(string $userId): void
    {
        $user = User::findOrFail($userId);

        AiUserQuota::firstOrCreate(
            ['user_id' => $user->id],
            [
                'monthly_token_limit' => config('lernpfad.ai.default_monthly_tokens', 100000),
                'daily_request_limit' => config('lernpfad.ai.default_daily_requests', 100),
                'tokens_used_this_month' => 0,
                'requests_today' => 0,
            ]
        );

        session()->flash('success', __('Quota für Benutzer erstellt.'));
    }

    public function closeModal(): void
    {
        $this->showEditModal = false;
        $this->editingQuotaId = null;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.ai.quota-manager')
            ->layout('layouts.admin', ['title' => __('KI-Quota-Verwaltung')]);
    }
}
