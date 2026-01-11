<?php

namespace App\Livewire\Learner\AI;

use App\Models\AIGeneratedContent;
use App\Models\Module;
use App\Services\AI\AISummaryService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.learner')]
#[Title('Modul-Zusammenfassung - LearningPilot')]
class SummaryPanel extends Component
{
    public ?string $moduleId = null;
    public ?array $summary = null;
    public bool $isLoading = false;
    public bool $isGenerating = false;

    public function mount(string $module): void
    {
        $this->moduleId = $module;
        $this->loadSummary();
    }

    public function loadSummary(): void
    {
        $this->isLoading = true;

        try {
            // Try to load existing summary
            $content = AIGeneratedContent::where('contentable_type', Module::class)
                ->where('contentable_id', $this->moduleId)
                ->where('content_type', 'summary')
                ->where('user_id', auth()->id())
                ->latest()
                ->first();

            if ($content) {
                $this->summary = $content->content;
            }
        } finally {
            $this->isLoading = false;
        }
    }

    public function generateSummary(): void
    {
        $this->isGenerating = true;

        try {
            $module = Module::findOrFail($this->moduleId);
            $summaryService = app(AISummaryService::class);

            $this->summary = $summaryService->generateModuleSummary(
                user: auth()->user(),
                module: $module,
            );

        } catch (\Exception $e) {
            session()->flash('error', __('Fehler beim Generieren der Zusammenfassung: :message', ['message' => $e->getMessage()]));
        } finally {
            $this->isGenerating = false;
        }
    }

    public function regenerateSummary(): void
    {
        // Delete existing summary
        AIGeneratedContent::where('contentable_type', Module::class)
            ->where('contentable_id', $this->moduleId)
            ->where('content_type', 'summary')
            ->where('user_id', auth()->id())
            ->delete();

        $this->summary = null;
        $this->generateSummary();
    }

    public function getModuleProperty(): ?Module
    {
        return $this->moduleId ? Module::with('steps')->find($this->moduleId) : null;
    }

    public function render()
    {
        return view('livewire.learner.ai.summary-panel');
    }
}
