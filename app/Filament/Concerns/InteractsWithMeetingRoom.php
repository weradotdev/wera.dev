<?php

namespace App\Filament\Concerns;

use App\Events\MeetingSignalSent;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

trait InteractsWithMeetingRoom
{
    public Meeting $meeting;

    /**
     * @var array<int, string>
     */
    public array $selectedInvitees = [];

    public function initializeMeetingRoomFromRecord(Meeting $meeting): void
    {
        $meeting->loadMissing(['project.projectUsers', 'attendees']);

        $userId = Auth::id();

        abort_if($userId === null, 403);

        $isParticipant = $meeting->meetingUsers()->where('user_id', $userId)->exists();
        $isProjectOwner = $meeting->project->projectUsers()
            ->where('user_id', $userId)
            ->where('role', 'owner')
            ->exists();

        abort_unless($isParticipant || $isProjectOwner, 403);

        $this->meeting = $meeting;

        if ($this->meeting->started_at === null) {
            $this->meeting->update(['started_at' => now()]);
            $this->meeting->refresh();
        }

        $this->markJoined();
    }

    public function getHeading(): string
    {
        return $this->meeting->title ?: 'Project meeting';
    }

    public function getSubheading(): ?string
    {
        return sprintf('Meeting #%s', $this->meeting->getKey());
    }

    public function sendSignal(string $type, array $payload = [], ?int $toUserId = null): void
    {
        if (! in_array($type, ['offer', 'answer', 'ice-candidate', 'join', 'leave'], true)) {
            return;
        }

        $userId = Auth::id();

        if ($userId === null) {
            return;
        }

        if ($toUserId !== null) {
            $isKnownAttendee = $this->meeting->meetingUsers()->where('user_id', $toUserId)->exists();

            if (! $isKnownAttendee) {
                return;
            }
        }

        event(new MeetingSignalSent(
            meetingId: $this->meeting->getKey(),
            fromUserId: $userId,
            type: $type,
            payload: $payload,
            toUserId: $toUserId,
        ));
    }

    public function inviteSelectedUsers(): void
    {
        if (! $this->canManageMeeting()) {
            abort(403);
        }

        $userId = Auth::id();

        if ($userId === null) {
            abort(403);
        }

        foreach ($this->selectedInvitees as $invitee) {
            $inviteeId = (int) $invitee;

            if ($inviteeId <= 0) {
                continue;
            }

            $this->meeting->meetingUsers()->firstOrCreate(
                ['user_id' => $inviteeId],
                [
                    'invited_by_user_id' => $userId,
                    'is_host' => false,
                ]
            );
        }

        $this->selectedInvitees = [];
        $this->meeting->refresh();

        Notification::make()
            ->title('Attendees invited')
            ->success()
            ->send();
    }

    public function markJoined(): void
    {
        $userId = Auth::id();

        if ($userId === null) {
            return;
        }

        $this->meeting->meetingUsers()->updateOrCreate(
            ['user_id' => $userId],
            [
                'invited_by_user_id' => $this->meeting->user_id,
                'joined_at' => now(),
                'left_at' => null,
                'is_host' => $this->meeting->user_id === $userId,
            ]
        );

        $this->meeting->refresh();
    }

    public function markLeft(): void
    {
        $userId = Auth::id();

        if ($userId === null) {
            return;
        }

        $this->meeting->meetingUsers()
            ->where('user_id', $userId)
            ->update(['left_at' => now()]);
    }

    /**
     * @return array<int, string>
     */
    public function inviteOptions(): array
    {
        $project = $this->meeting->project;

        return $project->users()
            ->where('users.id', '!=', Auth::id())
            ->orderBy('first_name')
            ->get(['users.id', 'first_name', 'last_name', 'email'])
            ->mapWithKeys(fn (User $user): array => [
                $user->id => trim($user->first_name . ' ' . $user->last_name) . ' (' . $user->email . ')',
            ])
            ->all();
    }

    /**
     * @return Collection<int, User>
     */
    public function attendees(): Collection
    {
        return $this->meeting->attendees()
            ->orderBy('users.first_name')
            ->get(['users.id', 'first_name', 'last_name', 'email']);
    }

    public function canManageMeeting(): bool
    {
        $currentUserId = Auth::id();

        if ($currentUserId === null) {
            return false;
        }

        if ($this->meeting->user_id === $currentUserId) {
            return true;
        }

        /** @var Project $project */
        $project = $this->meeting->project;

        return $project->projectUsers()
            ->where('user_id', $currentUserId)
            ->where('role', 'owner')
            ->exists();
    }
}
