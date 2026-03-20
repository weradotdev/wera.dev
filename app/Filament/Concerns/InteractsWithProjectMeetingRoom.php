<?php

namespace App\Filament\Concerns;

use App\Events\MeetingSignalSent;
use App\Models\Meeting;
use App\Models\Project;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

trait InteractsWithProjectMeetingRoom
{
    public ?Meeting $meeting = null;

    /**
     * @var array<int, string>
     */
    public array $selectedInvitees = [];

    public function initializeMeetingRoom(?string $meetingId = null): void
    {
        /** @var Project $project */
        $project = $this->record;

        $userId = Auth::id();

        abort_if(null === $userId, 403);

        $selectedMeeting = null;

        if (filled($meetingId)) {
            $selectedMeeting = Meeting::query()
                ->with(['project.projectUsers', 'attendees'])
                ->where('project_id', $project->id)
                ->findOrFail($meetingId);
        }

        if (! $selectedMeeting) {
            $selectedMeeting = Meeting::query()
                ->where('project_id', $project->id)
                ->whereIn('status', ['scheduled', 'live'])
                ->latest('started_at')
                ->latest('created_at')
                ->first();
        }

        if (! $selectedMeeting && $this->isProjectOwner($project, $userId)) {
            $selectedMeeting = Meeting::query()->create([
                'project_id'   => $project->id,
                'host_user_id' => $userId,
                'title'        => $project->name.' meeting',
                'status'       => 'live',
                'started_at'   => now(),
            ]);

            $selectedMeeting->meetingUsers()->updateOrCreate(
                ['user_id' => $userId],
                [
                    'invited_by_user_id' => $userId,
                    'is_host'            => true,
                    'joined_at'          => now(),
                ]
            );
        }

        if (! $selectedMeeting) {
            abort(404, 'No active meeting found for this project.');
        }

        $isParticipant = $selectedMeeting->meetingUsers()->where('user_id', $userId)->exists();
        $isOwner = $this->isProjectOwner($project, $userId);

        abort_unless($isParticipant || $isOwner, 403);

        $this->meeting = $selectedMeeting;

        if ('scheduled' === $selectedMeeting->status) {
            $this->meeting->update([
                'status'     => 'live',
                'started_at' => $selectedMeeting->started_at ?? now(),
            ]);
            $this->meeting->refresh();
        }

        $this->markJoined();
    }

    public function getHeading(): string
    {
        return $this->meeting?->title ?: 'Project meeting';
    }

    public function getSubheading(): ?string
    {
        if (! $this->meeting) {
            return null;
        }

        return sprintf('Meeting #%s', $this->meeting->getKey());
    }

    public function sendSignal(string $type, array $payload = [], ?int $toUserId = null): void
    {
        if (! $this->meeting) {
            return;
        }

        if (! in_array($type, ['offer', 'answer', 'ice-candidate', 'join', 'leave'], true)) {
            return;
        }

        $userId = Auth::id();

        if (null === $userId) {
            return;
        }

        if (null !== $toUserId) {
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
        if (! $this->meeting || ! $this->canManageMeeting()) {
            abort(403);
        }

        $userId = Auth::id();

        if (null === $userId) {
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
                    'is_host'            => false,
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
        if (! $this->meeting) {
            return;
        }

        $userId = Auth::id();

        if (null === $userId) {
            return;
        }

        $this->meeting->meetingUsers()->updateOrCreate(
            ['user_id' => $userId],
            [
                'invited_by_user_id' => $this->meeting->host_user_id,
                'joined_at'          => now(),
                'left_at'            => null,
                'is_host'            => $this->meeting->host_user_id === $userId,
            ]
        );

        $this->meeting->refresh();
    }

    public function markLeft(): void
    {
        if (! $this->meeting) {
            return;
        }

        $userId = Auth::id();

        if (null === $userId) {
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
        if (! $this->meeting) {
            return [];
        }

        return $this->record->users()
            ->where('users.id', '!=', Auth::id())
            ->orderBy('first_name')
            ->get(['users.id', 'first_name', 'last_name', 'email'])
            ->mapWithKeys(fn (User $user): array => [
                $user->id => trim($user->first_name.' '.$user->last_name).' ('.$user->email.')',
            ])
            ->all();
    }

    /**
     * @return Collection<int, User>
     */
    public function attendees(): Collection
    {
        if (! $this->meeting) {
            return new Collection;
        }

        return $this->meeting->attendees()
            ->orderBy('users.first_name')
            ->get(['users.id', 'first_name', 'last_name', 'email']);
    }

    public function canManageMeeting(): bool
    {
        if (! $this->meeting) {
            return false;
        }

        $currentUserId = Auth::id();

        if (null === $currentUserId) {
            return false;
        }

        if ($this->meeting->host_user_id === $currentUserId) {
            return true;
        }

        return $this->isProjectOwner($this->record, $currentUserId);
    }

    protected function isProjectOwner(Project $project, int $userId): bool
    {
        return $project->projectUsers()
            ->where('user_id', $userId)
            ->where('role', 'owner')
            ->exists();
    }
}
