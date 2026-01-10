<?php

namespace App\Livewire\Shared;

use Livewire\Attributes\On;
use Livewire\Component;

class Timer extends Component
{
    public int $seconds = 0;

    public int $initialSeconds = 0;

    public bool $countdown = false;

    public bool $autoStart = false;

    public bool $running = false;

    public string $size = 'md'; // sm, md, lg

    public bool $showHours = false;

    public ?int $warningThreshold = null; // Seconds remaining to show warning

    public function mount(
        int $seconds = 0,
        bool $countdown = false,
        bool $autoStart = false,
        string $size = 'md',
        bool $showHours = false,
        ?int $warningThreshold = null
    ): void {
        $this->seconds = $seconds;
        $this->initialSeconds = $seconds;
        $this->countdown = $countdown;
        $this->autoStart = $autoStart;
        $this->running = $autoStart;
        $this->size = $size;
        $this->showHours = $showHours;
        $this->warningThreshold = $warningThreshold;
    }

    public function start(): void
    {
        $this->running = true;
        $this->dispatch('timer-started');
    }

    public function pause(): void
    {
        $this->running = false;
        $this->dispatch('timer-paused', seconds: $this->seconds);
    }

    public function reset(): void
    {
        $this->running = false;
        $this->seconds = $this->initialSeconds;
        $this->dispatch('timer-reset');
    }

    #[On('tick')]
    public function tick(): void
    {
        if (! $this->running) {
            return;
        }

        if ($this->countdown) {
            $this->seconds--;
            if ($this->seconds <= 0) {
                $this->seconds = 0;
                $this->running = false;
                $this->dispatch('timer-finished');
            }
        } else {
            $this->seconds++;
        }

        $this->dispatch('timer-tick', seconds: $this->seconds);
    }

    public function getFormattedTime(): string
    {
        $hours = floor($this->seconds / 3600);
        $minutes = floor(($this->seconds % 3600) / 60);
        $secs = $this->seconds % 60;

        if ($this->showHours || $hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%02d:%02d', $minutes, $secs);
    }

    public function isWarning(): bool
    {
        return $this->countdown
            && $this->warningThreshold !== null
            && $this->seconds <= $this->warningThreshold
            && $this->seconds > 0;
    }

    public function getSizeClasses(): array
    {
        return match ($this->size) {
            'sm' => ['text-lg', 'px-3 py-1'],
            'lg' => ['text-4xl', 'px-6 py-4'],
            default => ['text-2xl', 'px-4 py-2'],
        };
    }

    public function render()
    {
        return view('livewire.shared.timer');
    }
}
