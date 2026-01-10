<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUserQuota extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'monthly_token_limit',
        'daily_request_limit',
        'tokens_used_this_month',
        'requests_today',
        'last_request_at',
        'quota_resets_at',
    ];

    protected function casts(): array
    {
        return [
            'last_request_at' => 'datetime',
            'quota_resets_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helpers

    public function hasTokensRemaining(): bool
    {
        return $this->tokens_used_this_month < $this->monthly_token_limit;
    }

    public function hasRequestsRemaining(): bool
    {
        return $this->requests_today < $this->daily_request_limit;
    }

    public function canMakeRequest(): bool
    {
        return $this->hasTokensRemaining() && $this->hasRequestsRemaining();
    }

    public function remainingTokens(): int
    {
        return max(0, $this->monthly_token_limit - $this->tokens_used_this_month);
    }

    public function remainingRequests(): int
    {
        return max(0, $this->daily_request_limit - $this->requests_today);
    }

    public function tokenUsagePercent(): float
    {
        if ($this->monthly_token_limit === 0) {
            return 100;
        }

        return round(($this->tokens_used_this_month / $this->monthly_token_limit) * 100, 2);
    }

    public function incrementUsage(int $tokensUsed): void
    {
        $this->increment('tokens_used_this_month', $tokensUsed);
        $this->increment('requests_today');
        $this->update(['last_request_at' => now()]);
    }

    public function resetDailyRequests(): void
    {
        $this->update(['requests_today' => 0]);
    }

    public function resetMonthlyTokens(): void
    {
        $this->update([
            'tokens_used_this_month' => 0,
            'quota_resets_at' => now()->addMonth()->startOfMonth(),
        ]);
    }
}
