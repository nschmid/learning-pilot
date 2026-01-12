<?php

namespace App\Models;

use App\Enums\AiFeedbackType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFeedbackReport extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'ai_generated_content_id',
        'feedback_type',
        'description',
        'expected_response',
        'status',
        'admin_notes',
        'resolved_at',
        'resolved_by',
    ];

    protected function casts(): array
    {
        return [
            'feedback_type' => AiFeedbackType::class,
            'resolved_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function aiGeneratedContent(): BelongsTo
    {
        return $this->belongsTo(AiGeneratedContent::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['pending', 'reviewed']);
    }

    public function scopeOfType($query, AiFeedbackType $type)
    {
        return $query->where('feedback_type', $type);
    }

    // Helpers

    public function markAsReviewed(?string $notes = null): void
    {
        $this->update([
            'status' => 'reviewed',
            'admin_notes' => $notes,
        ]);
    }

    public function resolve(User $resolver, ?string $notes = null): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $resolver->id,
            'admin_notes' => $notes ?? $this->admin_notes,
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReviewed(): bool
    {
        return $this->status === 'reviewed';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }
}
