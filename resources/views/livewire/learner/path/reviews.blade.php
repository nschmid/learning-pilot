<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('learner.catalog') }}" class="text-gray-500 hover:text-gray-700">{{ __('Katalog') }}</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <a href="{{ route('learner.path.show', $path) }}" class="ml-1 text-gray-500 hover:text-gray-700">{{ $path->title }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-gray-700 font-medium">{{ __('Bewertungen') }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-2xl font-bold text-gray-900">{{ __('Bewertungen für') }} "{{ $path->title }}"</h1>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="ml-3 text-sm font-medium text-green-800">{{ session('message') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Rating Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 sticky top-6">
                    <div class="text-center mb-6">
                        <div class="text-5xl font-bold text-gray-900">{{ $this->averageRating }}</div>
                        <div class="flex items-center justify-center mt-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= round($this->averageRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="mt-1 text-sm text-gray-500">{{ $this->reviews->total() }} {{ __('Bewertungen') }}</p>
                    </div>

                    <!-- Rating Distribution -->
                    <div class="space-y-2">
                        @foreach ($this->ratingDistribution as $stars => $data)
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 w-3">{{ $stars }}</span>
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500 w-8 text-right">{{ $data['count'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Write Review Button -->
                    @if ($this->canReview && !$this->userReview)
                        <button
                            wire:click="$set('showReviewForm', true)"
                            class="mt-6 w-full px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors"
                        >
                            {{ __('Bewertung schreiben') }}
                        </button>
                    @elseif ($this->userReview)
                        <div class="mt-6 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">{{ __('Du hast diesen Kurs bereits bewertet') }}</p>
                            <div class="flex gap-2">
                                <button wire:click="editReview" class="text-sm text-teal-600 hover:text-teal-800">{{ __('Bearbeiten') }}</button>
                                <button wire:click="deleteReview" wire:confirm="{{ __('Möchtest du deine Bewertung wirklich löschen?') }}" class="text-sm text-red-600 hover:text-red-800">{{ __('Löschen') }}</button>
                            </div>
                        </div>
                    @elseif (!$this->canReview)
                        <p class="mt-6 text-sm text-gray-500 text-center">{{ __('Du musst mindestens 20% des Kurses abgeschlossen haben, um eine Bewertung zu schreiben.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Reviews List -->
            <div class="lg:col-span-2">
                <!-- Review Form Modal -->
                @if ($showReviewForm)
                    <div class="bg-white shadow rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $this->userReview ? __('Bewertung bearbeiten') : __('Deine Bewertung') }}</h3>

                        <form wire:submit="submitReview">
                            <!-- Star Rating -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Bewertung') }}</label>
                                <div class="flex gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button
                                            type="button"
                                            wire:click="setRating({{ $i }})"
                                            class="focus:outline-none"
                                        >
                                            <svg class="w-8 h-8 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }} hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                </div>
                                @error('rating') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Review Text -->
                            <div class="mb-4">
                                <label for="reviewText" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Dein Feedback (optional)') }}</label>
                                <textarea
                                    wire:model="reviewText"
                                    id="reviewText"
                                    rows="4"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                    placeholder="{{ __('Was hat dir besonders gut gefallen? Was könnte verbessert werden?') }}"
                                ></textarea>
                                @error('reviewText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    wire:click="$set('showReviewForm', false)"
                                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200"
                                >
                                    {{ __('Abbrechen') }}
                                </button>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700"
                                    {{ $rating === 0 ? 'disabled' : '' }}
                                >
                                    {{ __('Bewertung speichern') }}
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Sort Options -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('Alle Bewertungen') }}</h2>
                    <select wire:model.live="sort" class="rounded-md border-gray-300 text-sm focus:border-teal-500 focus:ring-teal-500">
                        <option value="newest">{{ __('Neueste zuerst') }}</option>
                        <option value="oldest">{{ __('Älteste zuerst') }}</option>
                        <option value="highest">{{ __('Beste zuerst') }}</option>
                        <option value="lowest">{{ __('Schlechteste zuerst') }}</option>
                    </select>
                </div>

                <!-- Reviews -->
                <div class="space-y-4">
                    @forelse ($this->reviews as $review)
                        <div class="bg-white shadow rounded-lg p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center">
                                        <span class="text-teal-600 font-medium">{{ substr($review->user->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $review->user->name }}</h4>
                                        <time class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</time>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    @if ($review->review_text)
                                        <p class="mt-3 text-gray-600">{{ $review->review_text }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white shadow rounded-lg p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Noch keine Bewertungen') }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ __('Sei der Erste, der diesen Kurs bewertet!') }}</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if ($this->reviews->hasPages())
                    <div class="mt-6">
                        {{ $this->reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
