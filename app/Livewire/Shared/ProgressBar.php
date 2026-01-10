<?php

namespace App\Livewire\Shared;

use Livewire\Component;

class ProgressBar extends Component
{
    public int|float $percent = 0;

    public string $size = 'md'; // sm, md, lg

    public string $color = 'indigo'; // indigo, green, orange, blue, red

    public bool $showLabel = true;

    public bool $animate = true;

    public ?string $label = null;

    public function mount(
        int|float $percent = 0,
        string $size = 'md',
        string $color = 'indigo',
        bool $showLabel = true,
        bool $animate = true,
        ?string $label = null
    ): void {
        $this->percent = max(0, min(100, $percent));
        $this->size = $size;
        $this->color = $color;
        $this->showLabel = $showLabel;
        $this->animate = $animate;
        $this->label = $label;
    }

    public function getHeightClass(): string
    {
        return match ($this->size) {
            'sm' => 'h-1.5',
            'lg' => 'h-4',
            default => 'h-2.5',
        };
    }

    public function getColorClass(): string
    {
        return match ($this->color) {
            'green' => 'bg-green-500',
            'orange' => 'bg-orange-500',
            'blue' => 'bg-blue-500',
            'red' => 'bg-red-500',
            default => 'bg-indigo-600',
        };
    }

    public function render()
    {
        return view('livewire.shared.progress-bar');
    }
}
