<?php

namespace App\Models;

use App\Enums\AiFeedbackType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiFeedbackReport extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'feedbackable_type',
        'feedbackable_id',
        'feedback_type',
        'rating',
        'comment',
        'is_resolved',
        'resolved_at',
        'resolver_notes',
    ];

    protected function casts(): array
    {
        return [
            'feedback_type' => AiFeedbackType::class,
            'is_resolved' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopePositive($query)
    {
        return $query->where('rating', '>=', 4);
    }

    public function scopeNegative($query)
    {
        return $query->where('rating', '<=', 2);
    }

    public function scopeOfType($query, AiFeedbackType $type)
    {
        return $query->where('feedback_type', $type);
    }

    // Helpers

    public function resolve(?string $notes = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolver_notes' => $notes,
        ]);
    }

    public function isPositive(): bool
    {
        return $this->rating >= 4;
    }

    public function isNegative(): bool
    {
        return $this->rating <= 2;
    }

    public function isNeutral(): bool
    {
        return $this->rating === 3;
    }
}
