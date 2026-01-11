<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Statistiken') }}
            </h2>
            <select wire:model.live="period" class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($periods as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <!-- Quick Stats -->
            <div class="grid gap-6 sm:grid-cols-3">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="size-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ __('Durchschn. Fortschritt') }}</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $avgCompletionRate }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="size-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ __('Durchschn. Lernzeit') }}</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $avgTimeSpent }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="size-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="truncate text-sm font-medium text-gray-500">{{ __('Heute aktiv') }}</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">{{ $activeToday }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="mt-8 grid gap-8 lg:grid-cols-2">
                <!-- Enrollments Chart -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-base font-medium text-gray-900">{{ __('Einschreibungen') }}</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        @if(count($enrollmentsOverTime) > 0)
                            <div class="h-64">
                                <div class="flex h-full items-end gap-1">
                                    @php
                                        $maxEnrollments = max($enrollmentsOverTime) ?: 1;
                                    @endphp
                                    @foreach($enrollmentsOverTime as $date => $count)
                                        <div
                                            class="flex-1 rounded-t bg-indigo-500 transition-all hover:bg-indigo-600"
                                            style="height: {{ ($count / $maxEnrollments) * 100 }}%"
                                            title="{{ $date }}: {{ $count }}"
                                        ></div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="py-8 text-center text-sm text-gray-500">{{ __('Keine Daten für den gewählten Zeitraum') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Completions Chart -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-base font-medium text-gray-900">{{ __('Abschlüsse') }}</h3>
                    </div>
                    <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                        @if(count($completionsOverTime) > 0)
                            <div class="h-64">
                                <div class="flex h-full items-end gap-1">
                                    @php
                                        $maxCompletions = max($completionsOverTime) ?: 1;
                                    @endphp
                                    @foreach($completionsOverTime as $date => $count)
                                        <div
                                            class="flex-1 rounded-t bg-green-500 transition-all hover:bg-green-600"
                                            style="height: {{ ($count / $maxCompletions) * 100 }}%"
                                            title="{{ $date }}: {{ $count }}"
                                        ></div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="py-8 text-center text-sm text-gray-500">{{ __('Keine Daten für den gewählten Zeitraum') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Paths -->
            <div class="mt-8">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-base font-medium text-gray-900">{{ __('Beliebteste Lernpfade') }}</h3>
                    </div>
                    <div class="border-t border-gray-200">
                        @if($topPaths->isNotEmpty())
                            <ul class="divide-y divide-gray-200">
                                @foreach($topPaths as $path)
                                    <li class="flex items-center justify-between px-4 py-4 sm:px-6">
                                        <div class="flex items-center">
                                            @if($path->thumbnail)
                                                <img src="{{ Storage::url($path->thumbnail) }}" alt="" class="size-10 rounded-lg object-cover">
                                            @else
                                                <div class="flex size-10 items-center justify-center rounded-lg bg-indigo-100">
                                                    <svg class="size-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">{{ $path->title }}</p>
                                                <p class="text-sm text-gray-500">{{ $path->modules_count ?? 0 }} {{ __('Module') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $path->enrollments_count }} {{ __('Einschreibungen') }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="px-4 py-8 text-center text-sm text-gray-500">{{ __('Keine Lernpfade vorhanden') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
