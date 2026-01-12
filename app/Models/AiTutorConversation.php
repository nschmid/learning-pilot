<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiTutorConversation extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'learning_path_id',
        'module_id',
        'step_id',
        'title',
        'status',
        'system_context',
        'total_messages',
        'total_tokens_used',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'system_context' => 'array',
            'last_message_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function learningPath(): BelongsTo
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(LearningStep::class, 'step_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiTutorMessage::class, 'conversation_id');
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeForPath($query, LearningPath $path)
    {
        return $query->where('learning_path_id', $path->id);
    }

    public function scopeForModule($query, Module $module)
    {
        return $query->where('module_id', $module->id);
    }

    public function scopeForStep($query, LearningStep $step)
    {
        return $query->where('step_id', $step->id);
    }

    // Helpers

    public function addMessage(string $role, string $content, ?int $tokensInput = null, ?int $tokensOutput = null): AiTutorMessage
    {
        $message = $this->messages()->create([
            'role' => $role,
            'content' => $content,
            'tokens_input' => $tokensInput,
            'tokens_output' => $tokensOutput,
            'created_at' => now(),
        ]);

        $this->increment('total_messages');
        $totalTokens = ($tokensInput ?? 0) + ($tokensOutput ?? 0);
        if ($totalTokens > 0) {
            $this->increment('total_tokens_used', $totalTokens);
        }
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

    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    public function resolve(): void
    {
        $this->update(['status' => 'resolved']);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
