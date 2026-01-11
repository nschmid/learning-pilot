<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Enrollment;
use App\Models\LearningPath;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

class Paths extends Component
{
    #[Url]
    public string $sortBy = 'enrollments';

    #[Computed]
    public function stats(): array
    {
        return [
            'total_paths' => LearningPath::count(),
            'published' => LearningPath::where('is_published', true)->count(),
            'draft' => LearningPath::where('is_published', false)->count(),
            'total_modules' => Module::count(),
        ];
    }

    #[Computed]
    public function pathsByCategory(): array
    {
        return LearningPath::select('category_id', DB::raw('COUNT(*) as count'))
            ->with('category')
            ->groupBy('category_id')
            ->get()
            ->map(fn ($row) => [
                'category' => $row->category?->name ?? __('Ohne Kategorie'),
                'count' => $row->count,
            ])
            ->toArray();
    }

    #[Computed]
    public function pathsByDifficulty(): array
    {
        return LearningPath::select('difficulty', DB::raw('COUNT(*) as count'))
            ->groupBy('difficulty')
            ->get()
            ->map(fn ($row) => [
                'difficulty' => $row->difficulty?->value ?? 'unknown',
                'count' => $row->count,
            ])
            ->toArray();
    }

    #[Computed]
    public function topPaths(): array
    {
        $query = LearningPath::withCount(['enrollments', 'modules', 'steps'])
            ->with('creator');

        $query = match ($this->sortBy) {
            'enrollments' => $query->orderByDesc('enrollments_count'),
            'completion' => $query->orderByDesc(
                Enrollment::selectRaw('COUNT(*)')
                    ->whereColumn('learning_path_id', 'learning_paths.id')
                    ->where('status', 'completed')
            ),
            'newest' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('enrollments_count'),
        };

        return $query->limit(20)
            ->get()
            ->map(function ($path) {
                $completedCount = Enrollment::where('learning_path_id', $path->id)
                    ->where('status', 'completed')
                    ->count();

                return [
                    'id' => $path->id,
                    'slug' => $path->slug,
                    'title' => $path->title,
                    'creator' => $path->creator?->name ?? '-',
                    'is_published' => $path->is_published,
                    'enrollments' => $path->enrollments_count,
                    'completed' => $completedCount,
                    'completion_rate' => $path->enrollments_count > 0
                        ? round(($completedCount / $path->enrollments_count) * 100, 1)
                        : 0,
                    'modules' => $path->modules_count,
                    'steps' => $path->steps_count,
                    'created_at' => $path->created_at->format('d.m.Y'),
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.reports.paths')
            ->layout('layouts.admin', ['title' => __('Lernpfad-Bericht')]);
    }
}
