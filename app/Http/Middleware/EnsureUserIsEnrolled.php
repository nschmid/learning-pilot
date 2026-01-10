<?php

namespace App\Http\Middleware;

use App\Enums\EnrollmentStatus;
use App\Models\LearningPath;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsEnrolled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Get the learning path from the route
        $path = $request->route('path');

        if (! $path) {
            return $next($request);
        }

        // If path is a string (slug), resolve it
        if (is_string($path)) {
            $path = LearningPath::where('slug', $path)->first();
        }

        if (! $path) {
            abort(404);
        }

        // Check if user is enrolled with an active status
        $enrollment = $user->enrollments()
            ->where('learning_path_id', $path->id)
            ->whereIn('status', [EnrollmentStatus::Active, EnrollmentStatus::Completed])
            ->first();

        if (! $enrollment) {
            return redirect()->route('learner.path.show', $path->slug)
                ->with('error', __('Du musst dich für diesen Lernpfad einschreiben.'));
        }

        // Share enrollment with views
        $request->attributes->set('enrollment', $enrollment);

        return $next($request);
    }
}
