<?php

namespace App\Models;

use App\Enums\AiServiceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    use HasFactory;

    /**
     * Disable default timestamps - migration only has created_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'service_type',
        'model',
        'tokens_input',
        'tokens_output',
        'tokens_total',
        'cost_credits',
        'latency_ms',
        'cache_hit',
        'context_type',
        'context_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'service_type' => AiServiceType::class,
            'cache_hit' => 'boolean',
            'cost_credits' => 'decimal:4',
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes

    public function scopeCached($query)
    {
        return $query->where('cache_hit', true);
    }

    public function scopeNotCached($query)
    {
        return $query->where('cache_hit', false);
    }

    public function scopeForService($query, AiServiceType $service)
    {
        return $query->where('service_type', $service);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    public function scopeForContext($query, string $type, string $id)
    {
        return $query->where('context_type', $type)
            ->where('context_id', $id);
    }

    // Static Helpers

    public static function log(
        User $user,
        AiServiceType $serviceType,
        string $model,
        int $tokensInput,
        int $tokensOutput,
        ?int $latencyMs = null,
        bool $cacheHit = false,
        ?float $costCredits = null,
        ?string $contextType = null,
        ?string $contextId = null
    ): self {
        return self::create([
            'user_id' => $user->id,
            'service_type' => $serviceType,
            'model' => $model,
            'tokens_input' => $tokensInput,
            'tokens_output' => $tokensOutput,
            'tokens_total' => $tokensInput + $tokensOutput,
            'latency_ms' => $latencyMs,
            'cache_hit' => $cacheHit,
            'cost_credits' => $costCredits,
            'context_type' => $contextType,
            'context_id' => $contextId,
            'created_at' => now(),
        ]);
    }

    // Helpers

    public function totalTokens(): int
    {
        return $this->tokens_total;
    }
}
