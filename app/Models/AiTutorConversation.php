<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiTutorConversation extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'contextable_type',
        'contextable_id',
        'title',
        'is_active',
        'total_tokens_used',
        'message_count',
        'last_message_at',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_message_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contextable(): MorphTo
    {
        return $this->morphTo();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiTutorMessage::class, 'conversation_id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForContext($query, Model $context)
    {
        return $query->where('contextable_type', get_class($context))
            ->where('contextable_id', $context->getKey());
    }

    // Helpers

    public function addMessage(string $role, string $content, int $tokensUsed = 0): AiTutorMessage
    {
        $message = $this->messages()->create([
            'role' => $role,
            'content' => $content,
            'tokens_used' => $tokensUsed,
        ]);

        $this->increment('message_count');
        $this->increment('total_tokens_used', $tokensUsed);
        $this->update(['last_message_at' => now()]);

        return $message;
    }

    public function getContextMessages(int $limit = 10): array
    {
        return $this->messages()
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->reverse()
            ->map(fn ($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->values()
            ->toArray();
    }

    public function close(): void
    {
        $this->update(['is_active' => false]);
    }
}
