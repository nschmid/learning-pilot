<?php

namespace App\Models;

use App\Enums\Difficulty;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class LearningPath extends Model implements HasMedia
{
    use HasFactory;
    use HasSlug;
    use HasUuids;
    use InteractsWithMedia;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'creator_id',
        'team_id',
        'category_id',
        'title',
        'slug',
        'description',
        'objectives',
        'difficulty',
        'thumbnail',
        'estimated_hours',
        'is_published',
        'is_featured',
        'published_at',
        'version',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'objectives' => 'array',
            'difficulty' => Difficulty::class,
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile();
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'objectives' => $this->objectives,
            'difficulty' => $this->difficulty?->value,
        ];
    }

    // Relationships

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('position');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PathReview::class);
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            LearningPath::class,
            'prerequisites',
            'learning_path_id',
            'required_path_id'
        );
    }

    public function requiredBy(): BelongsToMany
    {
        return $this->belongsToMany(
            LearningPath::class,
            'prerequisites',
            'required_path_id',
            'learning_path_id'
        );
    }

    public function steps(): HasManyThrough
    {
        return $this->hasManyThrough(LearningStep::class, Module::class);
    }

    // Scopes

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByDifficulty($query, Difficulty $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    // Helpers

    public function isPublished(): bool
    {
        return $this->is_published;
    }

    public function totalSteps(): int
    {
        return $this->steps()->count();
    }

    public function totalPoints(): int
    {
        return $this->steps()->sum('points_value');
    }

    public function estimatedMinutes(): int
    {
        return $this->steps()->sum('estimated_minutes') ?? 0;
    }

    public function averageRating(): float
    {
        return $this->reviews()->where('is_approved', true)->avg('rating') ?? 0;
    }

    public function enrollmentCount(): int
    {
        return $this->enrollments()->count();
    }

    public function completionCount(): int
    {
        return $this->enrollments()->where('status', 'completed')->count();
    }
}
