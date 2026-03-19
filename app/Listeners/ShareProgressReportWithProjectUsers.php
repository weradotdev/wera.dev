<?php

namespace App\Listeners;

use App\Events\ProgressReportGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ShareProgressReportWithProjectUsers implements ShouldQueue
{
    public function handle(ProgressReportGenerated $event): void
    {
        $project = $event->project;
        $report = $event->report;

        $project->load('users');
        $subject = "Progress report: {$project->name}";

        foreach ($project->users as $user) {
            if (blank($user->email)) {
                continue;
            }
            try {
                Mail::raw($report, function ($message) use ($user, $subject): void {
                    $message->to($user->email)->subject($subject);
                });
            } catch (\Throwable) {
                // Continue to other users
            }
        }
    }
}
