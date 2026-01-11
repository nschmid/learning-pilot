<?php

namespace App\Actions\School;

use App\Models\Team;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use App\Services\SubscriptionService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InviteStudentAction
{
    public function __construct(
        protected SubscriptionService $subscriptionService
    ) {}

    /**
     * Invite a student to the team.
     */
    public function execute(Team $team, array $data): InviteResult
    {
        // Check capacity
        if ($this->subscriptionService->hasReachedStudentLimit($team)) {
            return new InviteResult(
                success: false,
                message: __('Das Limit für Lernende ist erreicht. Bitte upgraden Sie Ihren Plan.'),
            );
        }

        // Check if user exists
        $existingUser = User::where('email', $data['email'])->first();

        if ($existingUser) {
            // Check if already in team
            if ($team->hasUser($existingUser)) {
                return new InviteResult(
                    success: false,
                    message: __('Diese Person ist bereits Mitglied des Teams.'),
                );
            }

            // Add to team
            $team->users()->attach($existingUser, [
                'role' => $data['role'] ?? 'learner',
            ]);

            return new InviteResult(
                success: true,
                message: __('Bestehender Benutzer wurde zum Team hinzugefügt.'),
                user: $existingUser,
            );
        }

        // Create new user
        $password = Str::random(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
        ]);

        // Add to team
        $team->users()->attach($user, [
            'role' => $data['role'] ?? 'learner',
        ]);

        // Send invitation email with temporary password
        $user->notify(new TeamInvitationNotification($team, $password));

        return new InviteResult(
            success: true,
            message: __('Einladung wurde gesendet.'),
            user: $user,
            temporaryPassword: $password,
        );
    }
}

class InviteResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?User $user = null,
        public ?string $temporaryPassword = null,
    ) {}
}
