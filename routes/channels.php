<?php

use App\Models\Meeting;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('meetings.{meetingId}', function ($user, string $meetingId) {
    $meeting = Meeting::query()
        ->with('project.projectUsers')
        ->find($meetingId);

    if (! $meeting) {
        return false;
    }

    $isParticipant = $meeting->meetingUsers()->where('user_id', $user->id)->exists();
    $isProjectOwner = $meeting->project->projectUsers()
        ->where('user_id', $user->id)
        ->where('role', 'owner')
        ->exists();

    return $isParticipant || $isProjectOwner;
});
