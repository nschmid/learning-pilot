<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use Billable;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use HasUuids;
    use LogsActivity;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar',
        'is_active',
        'bio',
        'preferences',
        'last_login_at',
        'profile_photo_path',
        // OAuth fields
        'oauth_google_id',
        'oauth_google_token',
        'oauth_google_refresh_token',
        'oauth_microsoft_id',
        'oauth_microsoft_token',
        'oauth_microsoft_refresh_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'oauth_google_token',
        'oauth_google_refresh_token',
        'oauth_microsoft_token',
        'oauth_microsoft_refresh_token',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'preferences' => 'array',
            'last_login_at' => 'datetime',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'role', 'is_active'])
            ->logOnlyDirty();
    }

    // Relationships

    public function createdLearningPaths(): HasMany
    {
        return $this->hasMany(LearningPath::class, 'creator_id');
    }

    public function createdPaths(): HasMany
    {
        return $this->createdLearningPaths();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(UserNote::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PathReview::class);
    }

    public function aiQuota(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AiUserQuota::class);
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiTutorConversation::class);
    }

    public function tutorConversations(): HasMany
    {
        return $this->aiConversations();
    }

    // Scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, UserRole $role)
    {
        return $query->where('role', $role);
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', UserRole::Admin);
    }

    public function scopeInstructors($query)
    {
        return $query->where('role', UserRole::Instructor);
    }

    public function scopeLearners($query)
    {
        return $query->where('role', UserRole::Learner);
    }

    // Helpers

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isInstructor(): bool
    {
        return $this->role === UserRole::Instructor;
    }

    public function isLearner(): bool
    {
        return $this->role === UserRole::Learner;
    }

    public function canAccessAdmin(): bool
    {
        return $this->role->canAccessAdmin();
    }

    public function canAccessInstructor(): bool
    {
        return $this->role->canAccessInstructor();
    }

    public function isEnrolledIn(LearningPath $path): bool
    {
        return $this->enrollments()->where('learning_path_id', $path->id)->exists();
    }
}
