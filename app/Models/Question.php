<?php

namespace App\Models;

use App\Enums\QuestionType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'assessment_id',
        'question_type',
        'question_text',
        'question_image',
        'explanation',
        'points',
        'position',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'question_type' => QuestionType::class,
            'metadata' => 'array',
        ];
    }

    // Relationships

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(AnswerOption::class)->orderBy('position');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(QuestionResponse::class);
    }

    // Scopes

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    // Helpers

    public function isAutoGradable(): bool
    {
        return $this->question_type->isAutoGradable();
    }

    public function hasOptions(): bool
    {
        return $this->question_type->hasOptions();
    }

    public function correctOptions(): \Illuminate\Support\Collection
    {
        return $this->options->where('is_correct', true);
    }
}
