@props([
    'path',
    'showProgress' => false,
    'enrollment' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200']) }}>
    {{-- Thumbnail --}}
    <div class="relative aspect-video bg-gray-100">
        @if ($path->thumbnail)
            <img src="{{ Storage::url($path->thumbnail) }}" alt="{{ $path->title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-600">
                <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        @endif

        {{-- Difficulty Badge --}}
        <div class="absolute top-3 left-3">
            <x-ui.badge :type="match($path->difficulty->value) {
                'beginner' => 'success',
                'intermediate' => 'warning',
                'advanced' => 'error',
                'expert' => 'purple',
                default => 'default'
            }">
                {{ $path->difficulty->label() }}
            </x-ui.badge>
        </div>

        {{-- Progress Overlay --}}
        @if ($showProgress && $enrollment)
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-1.5 bg-white/30 rounded-full overflow-hidden">
                        <div class="h-full bg-white rounded-full transition-all duration-300" style="width: {{ $enrollment->progress_percent }}%"></div>
                    </div>
                    <span class="text-xs font-medium text-white">{{ number_format($enrollment->progress_percent) }}%</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <div class="p-4">
        {{-- Category --}}
        @if ($path->category)
            <p class="text-xs font-medium text-indigo-600 uppercase tracking-wide mb-1">
                {{ $path->category->name }}
            </p>
        @endif

        {{-- Title --}}
        <h3 class="font-semibold text-gray-900 line-clamp-2 mb-2">
            <a href="{{ route('learner.paths.show', $path) }}" class="hover:text-indigo-600">
                {{ $path->title }}
            </a>
        </h3>

        {{-- Description --}}
        @if ($path->description)
            <p class="text-sm text-gray-500 line-clamp-2 mb-3">
                {{ Str::limit($path->description, 100) }}
            </p>
        @endif

        {{-- Meta --}}
        <div class="flex items-center gap-4 text-sm text-gray-500">
            @if ($path->modules_count ?? $path->modules->count())
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    {{ $path->modules_count ?? $path->modules->count() }} {{ __('Module') }}
                </span>
            @endif

            @if ($path->estimated_hours)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $path->estimated_hours }}h
                </span>
            @endif

            @if ($path->enrollments_count ?? false)
                <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{ $path->enrollments_count }}
                </span>
            @endif
        </div>

        {{-- Tags --}}
        @if ($path->tags && $path->tags->count())
            <div class="flex flex-wrap gap-1 mt-3">
                @foreach ($path->tags->take(3) as $tag)
                    <x-ui.badge type="default" size="sm">{{ $tag->name }}</x-ui.badge>
                @endforeach
                @if ($path->tags->count() > 3)
                    <x-ui.badge type="default" size="sm">+{{ $path->tags->count() - 3 }}</x-ui.badge>
                @endif
            </div>
        @endif
    </div>

    {{-- Footer --}}
    @if ($showProgress && $enrollment)
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ route('learner.paths.learn', $path) }}" class="block w-full text-center py-2 px-4 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                {{ __('Weiterlernen') }}
            </a>
        </div>
    @endif
</div>
