<?php

namespace App\Livewire\School;

use App\Services\SchoolUsageService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Schulverwaltung - LearningPilot')]
class Dashboard extends Component
{
    public function render()
    {
        $team = auth()->user()->currentTeam;
        $usageService = app(SchoolUsageService::class);

        return view('livewire.school.dashboard', [
            'team' => $team,
            'stats' => $usageService->getUsageStats($team),
        ]);
    }
}
