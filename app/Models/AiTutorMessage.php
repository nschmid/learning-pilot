<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiTutorMessage extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * Disable default timestamps - migration only has created_at.
     */
    public $timestamps = false;

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'model',
        'tokens_input',
        'tokens_output',
        'latency_ms',
        'references',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'references' => 'array',
            'created_at' => 'datetime',
        ];
    }

    // Relationships

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AiTutorConversation::class, 'conversation_id');
    }

    // Scopes

    public function scopeFromUser($query)
    {
        return $query->where('role', 'user');
    }

    public function scopeFromAssistant($query)
    {
        return $query->where('role', 'assistant');
    }

    public function scopeFromSystem($query)
    {
        return $query->where('role', 'system');
    }

    // Helpers

    public function isFromUser(): bool
    {
        return $this->role === 'user';
    }

    public function isFromAssistant(): bool
    {
        return $this->role === 'assistant';
    }

    public function isFromSystem(): bool
    {
        return $this->role === 'system';
    }
}
