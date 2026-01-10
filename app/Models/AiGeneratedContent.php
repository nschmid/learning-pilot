<?php

namespace App\Models;

use App\Enums\AiContentType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiGeneratedContent extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'ai_generated_contents';

    protected $fillable = [
        'contentable_type',
        'contentable_id',
        'user_id',
        'content_type',
        'content',
        'model_used',
        'tokens_used',
        'generation_time_ms',
        'is_cached',
        'cache_expires_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'content_type' => AiContentType::class,
            'content' => 'array',
            'is_cached' => 'boolean',
            'cache_expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    // Relationships

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes

    public function scopeCached($query)
    {
        return $query->where('is_cached', true)
            ->where(function ($q) {
                $q->whereNull('cache_expires_at')
                    ->orWhere('cache_expires_at', '>', now());
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('cache_expires_at', '<=', now());
    }

    public function scopeOfType($query, AiContentType $type)
    {
        return $query->where('content_type', $type);
    }

    // Helpers

    public function isCacheValid(): bool
    {
        if (! $this->is_cached) {
            return false;
        }

        if (! $this->cache_expires_at) {
            return true;
        }

        return $this->cache_expires_at->isFuture();
    }

    public function markAsExpired(): void
    {
        $this->update(['cache_expires_at' => now()]);
    }
}
