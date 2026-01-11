<?php

namespace App\Livewire\School;

use App\Actions\School\ImportStudentsAction;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Lernende importieren - LearningPilot')]
class StudentImport extends Component
{
    use WithFileUploads;

    public $csvFile;
    public bool $isImporting = false;
    public ?array $importResult = null;

    protected $rules = [
        'csvFile' => 'required|file|mimes:csv,txt|max:2048',
    ];

    public function import(): void
    {
        $this->validate();

        $this->isImporting = true;
        $this->importResult = null;

        try {
            $team = auth()->user()->currentTeam;
            $action = app(ImportStudentsAction::class);

            $result = $action->execute(
                team: $team,
                csvFile: $this->csvFile,
                importedBy: auth()->user(),
            );

            $this->importResult = [
                'success' => $result->success,
                'message' => $result->getMessage(),
                'successCount' => $result->successCount,
                'skippedCount' => $result->skippedCount,
                'errors' => $result->errors,
            ];

            if ($result->success) {
                $this->reset('csvFile');
            }

        } catch (\Exception $e) {
            $this->importResult = [
                'success' => false,
                'message' => __('Import fehlgeschlagen: :message', ['message' => $e->getMessage()]),
                'successCount' => 0,
                'skippedCount' => 0,
                'errors' => [],
            ];
        } finally {
            $this->isImporting = false;
        }
    }

    public function downloadTemplate(): mixed
    {
        $headers = ['Vorname', 'Nachname', 'Email', 'Klasse', 'Rolle'];
        $example = ['Max', 'Muster', 'max.muster@schule.ch', '10A', 'learner'];

        $content = implode(';', $headers) . "\n" . implode(';', $example);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'import_vorlage.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        return view('livewire.school.student-import');
    }
}
