<?php

namespace App\Jobs;

use App\Models\Meeting;
use App\Notifications\MeetingReminderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMeetingReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array<int> Reminder thresholds in minutes */
    private const THRESHOLDS = [60, 30, 10];

    public function handle(): void
    {
        $meetings = Meeting::query()
            ->whereNotNull('start_at')
            ->where('start_at', '>', now())
            ->where('start_at', '<=', now()->addMinutes(65))
            ->whereNull('started_at')
            ->whereNull('ended_at')
            ->with('attendees')
            ->get();

        foreach ($meetings as $meeting) {
            $this->processReminders($meeting);
        }
    }

    private function processReminders(Meeting $meeting): void
    {
        $minutesUntil = (int) now()->diffInMinutes($meeting->start_at, false);
        $remindersAlreadySent = $meeting->meta['reminders_sent'] ?? [];

        foreach (self::THRESHOLDS as $threshold) {
            if ($minutesUntil > $threshold) {
                continue;
            }

            if (in_array($threshold, $remindersAlreadySent)) {
                continue;
            }

            $this->sendReminder($meeting, $threshold);

            $remindersAlreadySent[] = $threshold;
            $meeting->update([
                'meta' => array_merge($meeting->meta ?? [], [
                    'reminders_sent' => $remindersAlreadySent,
                ]),
            ]);
        }
    }

    private function sendReminder(Meeting $meeting, int $minutesBefore): void
    {
        $notification = new MeetingReminderNotification($meeting, $minutesBefore);

        foreach ($meeting->attendees as $attendee) {
            $attendee->notify($notification);
        }
    }
}
