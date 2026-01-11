<?php

namespace App\Actions\School;

use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use App\Services\SubscriptionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImportStudentsAction
{
    protected array $errors = [];
    protected int $successCount = 0;
    protected int $skippedCount = 0;

    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Execute the import action.
     */
    public function execute(Team $team, UploadedFile $csvFile, User $importedBy): ImportResult
    {
        $this->errors = [];
        $this->successCount = 0;
        $this->skippedCount = 0;

        // Check if team has capacity
        if ($this->subscriptionService->hasReachedStudentLimit($team)) {
            return new ImportResult(
                success: false,
                successCount: 0,
                skippedCount: 0,
                errors: [__('Das Limit für Lernende ist erreicht. Bitte upgraden Sie Ihren Plan.')],
            );
        }

        // Parse CSV
        $rows = $this->parseCsv($csvFile);

        if (empty($rows)) {
            return new ImportResult(
                success: false,
                successCount: 0,
                skippedCount: 0,
                errors: [__('Die CSV-Datei ist leer oder hat ein ungültiges Format.')],
            );
        }

        // Process rows
        DB::beginTransaction();

        try {
            foreach ($rows as $index => $row) {
                $this->processRow($team, $row, $index + 2); // +2 for header and 0-index
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return new ImportResult(
                success: false,
                successCount: 0,
                skippedCount: 0,
                errors: [__('Fehler beim Import: :message', ['message' => $e->getMessage()])],
            );
        }

        return new ImportResult(
            success: $this->successCount > 0,
            successCount: $this->successCount,
            skippedCount: $this->skippedCount,
            errors: $this->errors,
        );
    }

    protected function parseCsv(UploadedFile $file): array
    {
        $rows = [];
        $handle = fopen($file->getPathname(), 'r');

        // Read header
        $header = fgetcsv($handle, 0, ';');

        if (!$header) {
            fclose($handle);
            return [];
        }

        // Normalize header
        $header = array_map(fn ($h) => strtolower(trim($h)), $header);

        // Map common header variations
        $mapping = [
            'vorname' => 'first_name',
            'firstname' => 'first_name',
            'first_name' => 'first_name',
            'nachname' => 'last_name',
            'lastname' => 'last_name',
            'last_name' => 'last_name',
            'email' => 'email',
            'e-mail' => 'email',
            'klasse' => 'class',
            'class' => 'class',
            'rolle' => 'role',
            'role' => 'role',
        ];

        $normalizedHeader = array_map(fn ($h) => $mapping[$h] ?? $h, $header);

        // Read data rows
        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            if (count($data) === count($normalizedHeader)) {
                $rows[] = array_combine($normalizedHeader, $data);
            }
        }

        fclose($handle);

        return $rows;
    }

    protected function processRow(Team $team, array $row, int $lineNumber): void
    {
        // Validate row
        $validator = Validator::make($row, [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'class' => 'nullable|string|max:50',
            'role' => 'nullable|string|in:learner,instructor',
        ]);

        if ($validator->fails()) {
            $this->errors[] = __('Zeile :line: :errors', [
                'line' => $lineNumber,
                'errors' => implode(', ', $validator->errors()->all()),
            ]);
            $this->skippedCount++;
            return;
        }

        $data = $validator->validated();

        // Check if user already exists
        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            // Check if already in team
            if ($team->hasUser($existingUser)) {
                $this->skippedCount++;
                return;
            }

            // Add to team
            $team->users()->attach($existingUser, [
                'role' => $data['role'] ?? 'learner',
            ]);

            $this->successCount++;
            return;
        }

        // Check limit again for each new user
        if ($this->subscriptionService->hasReachedStudentLimit($team)) {
            $this->errors[] = __('Zeile :line: Limit für Lernende erreicht.', ['line' => $lineNumber]);
            $this->skippedCount++;
            return;
        }

        // Create new user
        $password = Str::random(12);

        $user = User::create([
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $data['email'],
            'password' => Hash::make($password),
        ]);

        // Add to team
        $team->users()->attach($user, [
            'role' => $data['role'] ?? 'learner',
        ]);

        // Send welcome email with temporary password
        $user->notify(new TeamInvitationNotification($team, $password));

        $this->successCount++;
    }
}

class ImportResult
{
    public function __construct(
        public bool $success,
        public int $successCount,
        public int $skippedCount,
        public array $errors = [],
    ) {}

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getMessage(): string
    {
        if (!$this->success && $this->successCount === 0) {
            return __('Import fehlgeschlagen.');
        }

        return __(':count Lernende erfolgreich importiert. :skipped übersprungen.', [
            'count' => $this->successCount,
            'skipped' => $this->skippedCount,
        ]);
    }
}
