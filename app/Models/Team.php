<?php

namespace App\Models;

use App\Enums\SchoolType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Team extends JetstreamTeam
{
    use Billable;
    use HasFactory;
    use HasSlug;
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'personal_team',
        'school_type',
        'description',
        'logo_path',
        'website',
        'address',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
        'currency',
        'locale',
        'timezone',
        'max_students',
        'max_instructors',
        'storage_limit_gb',
        'settings',
        'trial_ends_at',
    ];

    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'school_type' => SchoolType::class,
            'settings' => 'array',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    // Relationships

    public function learningPaths(): HasMany
    {
        return $this->hasMany(LearningPath::class);
    }

    // Helpers

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function trialDaysRemaining(): int
    {
        if (! $this->trial_ends_at) {
            return 0;
        }

        return max(0, now()->diffInDays($this->trial_ends_at, false));
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscribed('default') || $this->isOnTrial();
    }

    public function studentCount(): int
    {
        return $this->users()->where('role', 'learner')->count();
    }

    public function instructorCount(): int
    {
        return $this->users()->where('role', 'instructor')->count();
    }

    public function canAddMoreStudents(): bool
    {
        if (! $this->max_students) {
            return true;
        }

        return $this->studentCount() < $this->max_students;
    }

    public function canAddMoreInstructors(): bool
    {
        if (! $this->max_instructors) {
            return true;
        }

        return $this->instructorCount() < $this->max_instructors;
    }
}
