<div class="flex h-[calc(100vh-8rem)]">
    <!-- Sidebar with conversations -->
    <div class="hidden w-64 flex-shrink-0 border-r border-gray-200 bg-gray-50 lg:block">
        <div class="flex h-full flex-col">
            <div class="p-4">
                <button
                    wire:click="startNewConversation"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-medium text-white hover:bg-teal-500"
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('Neue Konversation') }}
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-4">
                <h3 class="mb-2 text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Verlauf') }}</h3>
                <ul class="space-y-1">
                    @foreach($this->conversations as $conv)
                        <li>
                            <button
                                wire:click="loadConversation('{{ $conv->id }}')"
                                class="w-full rounded-lg px-3 py-2 text-left text-sm {{ $conversationId === $conv->id ? 'bg-teal-100 text-teal-700' : 'text-gray-700 hover:bg-gray-100' }}"
                            >
                                <p class="truncate font-medium">{{ $conv->title }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $conv->updated_at->diffForHumans() }}</p>
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Main chat area -->
    <div class="flex flex-1 flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ __('KI-Tutor') }}</h2>
                @if($this->step)
                    <p class="text-sm text-gray-500">{{ __('Kontext: :title', ['title' => $this->step->title]) }}</p>
                @endif
            </div>
            <livewire:learner.ai.usage-stats />
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-6">
            @if($this->messages->isEmpty())
                <div class="flex h-full items-center justify-center">
                    <div class="text-center">
                        <svg class="mx-auto size-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('Starten Sie eine Konversation') }}</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ __('Stellen Sie dem KI-Tutor eine Frage zu Ihrem Lerninhalt.') }}</p>
                    </div>
                </div>
            @else
                <div class="space-y-6">
                    @foreach($this->messages as $msg)
                        <div class="flex {{ $msg->role === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-2xl {{ $msg->role === 'user' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-2xl px-4 py-3">
                                <div class="prose prose-sm {{ $msg->role === 'user' ? 'prose-invert' : '' }} max-w-none">
                                    {!! \Illuminate\Support\Str::markdown($msg->content) !!}
                                </div>
                                <p class="mt-1 text-xs {{ $msg->role === 'user' ? 'text-teal-200' : 'text-gray-500' }}">
                                    {{ $msg->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @endforeach

                    @if($isTyping)
                        <div class="flex justify-start">
                            <div class="rounded-2xl bg-gray-100 px-4 py-3">
                                <div class="flex items-center gap-1">
                                    <div class="size-2 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.3s]"></div>
                                    <div class="size-2 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.15s]"></div>
                                    <div class="size-2 animate-bounce rounded-full bg-gray-400"></div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Input -->
        <div class="border-t border-gray-200 px-6 py-4">
            <form wire:submit="sendMessage" class="flex gap-4">
                <input
                    type="text"
                    wire:model="message"
                    placeholder="{{ __('Stellen Sie eine Frage...') }}"
                    class="flex-1 rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                    @disabled($isTyping)
                >
                <button
                    type="submit"
                    class="rounded-lg bg-teal-600 px-4 py-2 text-white hover:bg-teal-500 disabled:cursor-not-allowed disabled:opacity-50"
                    @disabled($isTyping || !$message)
                >
                    <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </button>
            </form>
            @error('message')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
