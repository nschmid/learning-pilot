<?php

namespace App\Livewire\Learner;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\LearningPath;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function getActiveEnrollmentsProperty()
    {
        return Enrollment::with(['learningPath.creator', 'learningPath.category'])
            ->where('user_id', Auth::id())
            ->where('status', EnrollmentStatus::Active)
            ->orderBy('last_activity_at', 'desc')
            ->take(6)
            ->get();
    }

    public function getCompletedEnrollmentsProperty()
    {
        return Enrollment::with(['learningPath'])
            ->where('user_id', Auth::id())
            ->where('status', EnrollmentStatus::Completed)
            ->orderBy('completed_at', 'desc')
            ->take(3)
            ->get();
    }

    public function getRecommendedPathsProperty()
    {
        $enrolledPathIds = Enrollment::where('user_id', Auth::id())
            ->pluck('learning_path_id');

        return LearningPath::with(['creator', 'category'])
            ->where('is_published', true)
            ->whereNotIn('id', $enrolledPathIds)
            ->withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(4)
            ->get();
    }

    public function getStatsProperty()
    {
        $user = Auth::user();

        $totalEnrollments = Enrollment::where('user_id', $user->id)->count();
        $completedPaths = Enrollment::where('user_id', $user->id)
            ->where('status', EnrollmentStatus::Completed)
            ->count();
        $totalTimeSpent = Enrollment::where('user_id', $user->id)
            ->sum('total_time_spent_seconds');
        $totalPoints = Enrollment::where('user_id', $user->id)
            ->sum('points_earned');

        return [
            'total_enrollments' => $totalEnrollments,
            'completed_paths' => $completedPaths,
            'total_time_hours' => round($totalTimeSpent / 3600, 1),
            'total_points' => $totalPoints,
        ];
    }

    public function continueLearning(string $enrollmentId)
    {
        $enrollment = Enrollment::with('learningPath')->findOrFail($enrollmentId);

        return $this->redirect(
            route('learner.learn.index', $enrollment->learningPath->slug),
            navigate: true
        );
    }

    public function render()
    {
        return view('livewire.learner.dashboard')
            ->layout('layouts.learner', ['title' => __('Mein Lernen')]);
    }
}
