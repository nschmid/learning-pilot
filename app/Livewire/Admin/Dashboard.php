<?php

namespace App\Livewire\Admin;

use App\Enums\EnrollmentStatus;
use App\Enums\UserRole;
use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\TaskSubmission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function totalUsers(): int
    {
        return User::count();
    }

    #[Computed]
    public function newUsersThisMonth(): int
    {
        return User::where('created_at', '>=', now()->startOfMonth())->count();
    }

    #[Computed]
    public function totalTeams(): int
    {
        return Team::count();
    }

    #[Computed]
    public function totalPaths(): int
    {
        return LearningPath::count();
    }

    #[Computed]
    public function publishedPaths(): int
    {
        return LearningPath::where('is_published', true)->count();
    }

    #[Computed]
    public function totalEnrollments(): int
    {
        return Enrollment::count();
    }

    #[Computed]
    public function activeEnrollments(): int
    {
        return Enrollment::where('status', EnrollmentStatus::Active)->count();
    }

    #[Computed]
    public function completedEnrollments(): int
    {
        return Enrollment::where('status', EnrollmentStatus::Completed)->count();
    }

    #[Computed]
    public function pendingSubmissions(): int
    {
        return TaskSubmission::where('status', 'pending')->count();
    }

    #[Computed]
    public function usersByRole(): array
    {
        return [
            'admins' => User::where('role', UserRole::Admin)->count(),
            'instructors' => User::where('role', UserRole::Instructor)->count(),
            'learners' => User::where('role', UserRole::Learner)->count(),
        ];
    }

    #[Computed]
    public function recentUsers()
    {
        return User::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function recentEnrollments()
    {
        return Enrollment::with(['user', 'learningPath'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function topPaths()
    {
        return LearningPath::withCount('enrollments')
            ->where('is_published', true)
            ->orderBy('enrollments_count', 'desc')
            ->take(5)
            ->get();
    }

    #[Computed]
    public function monthlyEnrollments(): array
    {
        $data = Enrollment::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return $data;
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin', ['title' => __('Dashboard')]);
    }
}
