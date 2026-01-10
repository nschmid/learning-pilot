<?php

namespace App\Models;

use App\Enums\AiServiceType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiUsageLog extends Model
{
    use HasFactory;
    use HasUuids;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'team_id',
        'service_type',
        'model_used',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'response_time_ms',
        'was_cached',
        'was_successful',
        'error_message',
        'loggable_type',
        'loggable_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'service_type' => AiServiceType::class,
            'was_cached' => 'boolean',
            'was_successful' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes

    public function scopeSuccessful($query)
    {
        return $query->where('was_successful', true);
    }

    public function scopeFailed($query)
    {
        return $query->where('was_successful', false);
    }

    public function scopeCached($query)
    {
        return $query->where('was_cached', true);
    }

    public function scopeForService($query, AiServiceType $service)
    {
        return $query->where('service_type', $service);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeForTeam($query, Team $team)
    {
        return $query->where('team_id', $team->id);
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

    // Static Helpers

    public static function log(
        User $user,
        AiServiceType $serviceType,
        string $model,
        int $promptTokens,
        int $completionTokens,
        int $responseTimeMs,
        bool $wasSuccessful = true,
        ?string $errorMessage = null,
        ?Model $loggable = null,
        bool $wasCached = false
    ): self {
        return self::create([
            'user_id' => $user->id,
            'team_id' => $user->current_team_id,
            'service_type' => $serviceType,
            'model_used' => $model,
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $promptTokens + $completionTokens,
            'response_time_ms' => $responseTimeMs,
            'was_cached' => $wasCached,
            'was_successful' => $wasSuccessful,
            'error_message' => $errorMessage,
            'loggable_type' => $loggable ? get_class($loggable) : null,
            'loggable_id' => $loggable?->getKey(),
            'created_at' => now(),
        ]);
    }
}
