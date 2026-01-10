<?php

namespace App\Livewire\Shared;

use Livewire\Component;

class StarRating extends Component
{
    public int|float $rating = 0;

    public int $maxStars = 5;

    public string $size = 'md'; // sm, md, lg

    public bool $interactive = false;

    public bool $showValue = false;

    public ?int $reviewCount = null;

    public function mount(
        int|float $rating = 0,
        int $maxStars = 5,
        string $size = 'md',
        bool $interactive = false,
        bool $showValue = false,
        ?int $reviewCount = null
    ): void {
        $this->rating = max(0, min($maxStars, $rating));
        $this->maxStars = $maxStars;
        $this->size = $size;
        $this->interactive = $interactive;
        $this->showValue = $showValue;
        $this->reviewCount = $reviewCount;
    }

    public function setRating(int $star): void
    {
        if ($this->interactive) {
            $this->rating = $star;
            $this->dispatch('rating-changed', rating: $star);
        }
    }

    public function getSizeClass(): string
    {
        return match ($this->size) {
            'sm' => 'h-4 w-4',
            'lg' => 'h-8 w-8',
            default => 'h-5 w-5',
        };
    }

    public function render()
    {
        return view('livewire.shared.star-rating');
    }
}
