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
        'content_metadata',
        'context_snapshot',
        'rating',
        'was_helpful',
        'user_feedback',
        'cache_key',
        'expires_at',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'content_type' => AiContentType::class,
            'content_metadata' => 'array',
            'context_snapshot' => 'array',
            'was_helpful' => 'boolean',
            'expires_at' => 'datetime',
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
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function scopeOfType($query, AiContentType $type)
    {
        return $query->where('content_type', $type);
    }

    // Helpers

    public function isCacheValid(): bool
    {
        if (! $this->expires_at) {
            return true;
        }

        return $this->expires_at->isFuture();
    }

    public function markAsExpired(): void
    {
        $this->update(['expires_at' => now()]);
    }
}
