<div
    x-data="richTextEditor(@js($content), @js($editorId), @js($placeholder), @js($disabled))"
    x-init="init()"
    wire:ignore
    class="rich-text-editor"
>
    @if($showToolbar && !$disabled)
        <div class="flex flex-wrap items-center gap-1 p-2 bg-gray-50 dark:bg-gray-800 border border-b-0 border-gray-300 dark:border-gray-600 rounded-t-lg">
            @if(in_array('heading', $toolbar))
                <select
                    x-on:change="toggleHeading($event.target.value)"
                    class="px-2 py-1 text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded"
                >
                    <option value="p">{{ __('Normal') }}</option>
                    <option value="h2">{{ __('Überschrift 2') }}</option>
                    <option value="h3">{{ __('Überschrift 3') }}</option>
                    <option value="h4">{{ __('Überschrift 4') }}</option>
                </select>
                <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
            @endif

            @if(in_array('bold', $toolbar))
                <button
                    type="button"
                    x-on:click="toggleBold()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('bold') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Fett') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h8a4 4 0 100-8H6v8zm0 0h9a4 4 0 110 8H6v-8z"/>
                    </svg>
                </button>
            @endif

            @if(in_array('italic', $toolbar))
                <button
                    type="button"
                    x-on:click="toggleItalic()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('italic') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Kursiv') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l-4 4m0 0l4 4M6 16l4-4m0 0L6 8"/>
                    </svg>
                </button>
            @endif

            @if(in_array('underline', $toolbar))
                <button
                    type="button"
                    x-on:click="toggleUnderline()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('underline') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Unterstrichen') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8v4a5 5 0 0010 0V8M5 20h14"/>
                    </svg>
                </button>
            @endif

            @if(in_array('strike', $toolbar))
                <button
                    type="button"
                    x-on:click="toggleStrike()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('strike') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Durchgestrichen') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 10H3m14 4H3m9-8l-2 16"/>
                    </svg>
                </button>
            @endif

            @if(in_array('bulletList', $toolbar) || in_array('orderedList', $toolbar))
                <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
            @endif

            @if(in_array('bulletList', $toolbar))
                <button
                    type="button"
                    x-on:click="toggleBulletList()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('bulletList') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Aufzählung') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </button>
            @endif

            @if(in_array('orderedList', $toolbar))
                <button
                    type="button"
                    x-on:click="toggleOrderedList()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('orderedList') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Nummerierung') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                </button>
            @endif

            @if(in_array('blockquote', $toolbar))
                <button
                    type="button"
                    x-on:click="toggleBlockquote()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('blockquote') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Zitat') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </button>
            @endif

            @if(in_array('code', $toolbar))
                <button
                    type="button"
                    x-on:click="toggleCode()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('code') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Code') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </button>
            @endif

            @if(in_array('link', $toolbar))
                <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-1"></div>
                <button
                    type="button"
                    x-on:click="toggleLink()"
                    :class="{ 'bg-gray-200 dark:bg-gray-600': isActive('link') }"
                    class="p-1.5 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                    title="{{ __('Link') }}"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </button>
            @endif
        </div>
    @endif

    <div
        x-ref="editor"
        class="prose prose-sm dark:prose-invert max-w-none p-4 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 {{ $showToolbar && !$disabled ? 'rounded-b-lg' : 'rounded-lg' }} {{ $disabled ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : '' }}"
        style="min-height: {{ $minHeight }}px"
    ></div>

    <input type="hidden" x-ref="input" x-bind:value="content" wire:model.live.debounce.500ms="content">
</div>

@pushOnce('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('richTextEditor', (initialContent, editorId, placeholder, disabled) => ({
            editor: null,
            content: initialContent,

            init() {
                // Simple contenteditable implementation
                // For production, integrate TipTap: npm install @tiptap/core @tiptap/starter-kit
                const editorEl = this.$refs.editor;
                editorEl.contentEditable = !disabled;
                editorEl.innerHTML = this.content || '';

                if (placeholder && !this.content) {
                    editorEl.dataset.placeholder = placeholder;
                    editorEl.classList.add('empty');
                }

                editorEl.addEventListener('input', () => {
                    this.content = editorEl.innerHTML;
                    this.$refs.input.value = this.content;
                    this.$refs.input.dispatchEvent(new Event('input', { bubbles: true }));

                    if (editorEl.textContent.trim() === '') {
                        editorEl.classList.add('empty');
                    } else {
                        editorEl.classList.remove('empty');
                    }
                });

                editorEl.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const text = e.clipboardData.getData('text/plain');
                    document.execCommand('insertText', false, text);
                });
            },

            isActive(type) {
                return document.queryCommandState(type);
            },

            toggleBold() {
                document.execCommand('bold', false, null);
                this.$refs.editor.focus();
            },

            toggleItalic() {
                document.execCommand('italic', false, null);
                this.$refs.editor.focus();
            },

            toggleUnderline() {
                document.execCommand('underline', false, null);
                this.$refs.editor.focus();
            },

            toggleStrike() {
                document.execCommand('strikeThrough', false, null);
                this.$refs.editor.focus();
            },

            toggleBulletList() {
                document.execCommand('insertUnorderedList', false, null);
                this.$refs.editor.focus();
            },

            toggleOrderedList() {
                document.execCommand('insertOrderedList', false, null);
                this.$refs.editor.focus();
            },

            toggleBlockquote() {
                document.execCommand('formatBlock', false, 'blockquote');
                this.$refs.editor.focus();
            },

            toggleCode() {
                document.execCommand('formatBlock', false, 'pre');
                this.$refs.editor.focus();
            },

            toggleHeading(level) {
                if (level === 'p') {
                    document.execCommand('formatBlock', false, 'p');
                } else {
                    document.execCommand('formatBlock', false, level);
                }
                this.$refs.editor.focus();
            },

            toggleLink() {
                const url = prompt('{{ __("URL eingeben:") }}');
                if (url) {
                    document.execCommand('createLink', false, url);
                }
                this.$refs.editor.focus();
            }
        }));
    });
</script>

<style>
    .rich-text-editor [contenteditable].empty:before {
        content: attr(data-placeholder);
        color: #9ca3af;
        pointer-events: none;
        position: absolute;
    }

    .rich-text-editor [contenteditable]:focus {
        outline: none;
    }

    .rich-text-editor [contenteditable] blockquote {
        border-left: 3px solid #d1d5db;
        padding-left: 1rem;
        margin-left: 0;
        color: #6b7280;
    }

    .rich-text-editor [contenteditable] pre {
        background: #f3f4f6;
        padding: 0.5rem;
        border-radius: 0.25rem;
        font-family: monospace;
    }

    .dark .rich-text-editor [contenteditable] pre {
        background: #374151;
    }
</style>
@endpushOnce
