<?php

namespace App\Livewire\Admin\Reports;

use App\Models\AiUsageLog;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AIUsage extends Component
{
    use WithPagination;

    #[Url]
    public string $period = 'month';

    #[Url]
    public string $feature = '';

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    public function updatedFeature(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function dateRange(): array
    {
        return match ($this->period) {
            'week' => [now()->subWeek(), now()],
            'month' => [now()->subMonth(), now()],
            'quarter' => [now()->subQuarter(), now()],
            'year' => [now()->subYear(), now()],
            default => [now()->subMonth(), now()],
        };
    }

    #[Computed]
    public function stats(): array
    {
        [$start, $end] = $this->dateRange;

        $query = AiUsageLog::whereBetween('created_at', [$start, $end]);

        return [
            'total_requests' => $query->clone()->count(),
            'total_tokens' => $query->clone()->sum('tokens_total'),
            'unique_users' => $query->clone()->distinct('user_id')->count('user_id'),
            'avg_response_time' => round($query->clone()->avg('latency_ms') ?? 0),
        ];
    }

    #[Computed]
    public function featureBreakdown(): array
    {
        [$start, $end] = $this->dateRange;

        return AiUsageLog::select('service_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(tokens_total) as tokens'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('service_type')
            ->orderByDesc('count')
            ->get()
            ->map(fn ($row) => [
                'feature' => $row->service_type,
                'count' => $row->count,
                'tokens' => $row->tokens,
            ])
            ->toArray();
    }

    #[Computed]
    public function dailyUsage(): array
    {
        [$start, $end] = $this->dateRange;

        return AiUsageLog::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as requests'),
            DB::raw('SUM(tokens_total) as tokens')
        )
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'requests' => $row->requests,
                'tokens' => $row->tokens,
            ])
            ->toArray();
    }

    #[Computed]
    public function logs()
    {
        [$start, $end] = $this->dateRange;

        return AiUsageLog::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->when($this->feature, fn ($q) => $q->where('service_type', $this->feature))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    #[Computed]
    public function availableFeatures(): array
    {
        return AiUsageLog::distinct('service_type')
            ->pluck('service_type')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.reports.ai-usage')
            ->layout('layouts.admin', ['title' => __('KI-Nutzungsbericht')]);
    }
}
