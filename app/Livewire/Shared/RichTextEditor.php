<?php

namespace App\Livewire\Shared;

use Livewire\Component;

class RichTextEditor extends Component
{
    public string $content = '';
    public string $editorId;
    public string $placeholder = '';
    public bool $disabled = false;
    public int $minHeight = 200;
    public bool $showToolbar = true;
    public array $toolbar = ['bold', 'italic', 'underline', 'strike', 'link', 'bulletList', 'orderedList', 'blockquote', 'code', 'heading'];

    public function mount(
        string $content = '',
        ?string $editorId = null,
        string $placeholder = '',
        bool $disabled = false,
        int $minHeight = 200,
        bool $showToolbar = true,
        array $toolbar = []
    ): void {
        $this->content = $content;
        $this->editorId = $editorId ?? 'editor-' . uniqid();
        $this->placeholder = $placeholder;
        $this->disabled = $disabled;
        $this->minHeight = $minHeight;
        $this->showToolbar = $showToolbar;

        if (!empty($toolbar)) {
            $this->toolbar = $toolbar;
        }
    }

    public function updatedContent(string $value): void
    {
        $this->dispatch('content-updated', content: $value, editorId: $this->editorId);
    }

    public function render()
    {
        return view('livewire.shared.rich-text-editor');
    }
}
