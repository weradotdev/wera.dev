<?php

namespace App\Notifications;

use App\Models\Meeting;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class MeetingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Meeting $meeting,
        public readonly int $minutesBefore,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $label = $this->label();
        $subject = "Reminder: \"{$this->meeting->title}\" starts {$label}";

        $message = (new MailMessage)
            ->greeting("Hello {$notifiable->first_name}!")
            ->subject($subject)
            ->line("Your meeting **{$this->meeting->title}** is starting {$label}.");

        if ($this->meeting->start_at) {
            $message->line('Scheduled for: '.$this->meeting->start_at->format('D, M j Y g:i A'));
        }

        return $message;
    }

    public function toDatabase(User $notifiable): array
    {
        $label = $this->label();

        return FilamentNotification::make()
            ->title("Meeting starting {$label}")
            ->warning()
            ->body("\"{$this->meeting->title}\" starts {$label}.")
            ->actions([
                Action::make('view')
                    ->button()
                    ->url('#'),
                Action::make('markAsRead')
                    ->button()
                    ->markAsUnread(),
            ])
            ->getDatabaseMessage();
    }

    public function toFcm(User $notifiable): FcmMessage
    {
        $label = $this->label();

        return new FcmMessage(notification: new FcmNotification(
            title: "Meeting starting {$label}",
            body: "\"{$this->meeting->title}\" starts {$label}.",
        ));
    }

    private function label(): string
    {
        return match ($this->minutesBefore) {
            60      => 'in 1 hour',
            30      => 'in 30 minutes',
            10      => 'in 10 minutes',
            default => "in {$this->minutesBefore} minutes",
        };
    }
}
