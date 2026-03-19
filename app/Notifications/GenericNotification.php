<?php

namespace App\Notifications;

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
use NotificationChannels\Telegram\TelegramMessage;

class GenericNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $subject,
        public string $message,
        public ?string $image = null,
        public array $links = [],
        public array $channels = ['database', 'mail', FcmChannel::class]
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return $this->channels;
    }

    public function toFcm($notifiable): FcmMessage
    {
        //     FcmMessage::create()
        // ->name('name')
        // ->token('token')
        // ->topic('topic')
        // ->condition('condition')
        // ->data(['a' => 'b'])
        // ->custom(['notification' => []]);

        return (new FcmMessage(notification: new FcmNotification(
            title: $this->subject,
            body: $this->message,
            image: $this->image ?? asset('logo.png')
        )))
            ->data([
                'notifee' => [
                    'title'   => $this->subject,
                    'body'    => $this->message,
                    'android' => [
                        'channelId' => 'default',
                        'actions'   => [
                            'title'       => 'Mark as read',
                            'pressAction' => [
                                'id' => 'read',
                            ],
                        ],
                    ],
                ],
            ])
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#0A0A0A',
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
                'apns' => [
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
            ]);
    }

    public function toTelegram($notifiable)
    {
        $url = url('/');

        return TelegramMessage::create()
            ->to($notifiable->telegram_user_id)
            // ->content("Hello there!")
            ->keyboard('Button 1')
            ->keyboard('Button 2')
            // ->line("Your invoice has been *PAID*")
            // ->lineIf($notifiable->amount > 0, "Amount paid: {$notifiable->amount}")
            // ->line("Thank you!")

            // (Optional) Blade template for the content.
            // ->view('notification', ['url' => $url])

            // (Optional) Inline Buttons
            ->button('Invoice', $url)
            ->button('Download Invoice', $url)
            // (Optional) Inline Button with callback. You can handle callback in your bot instance
            ->buttonWithCallback('Confirm', 'confirm_invoice ');
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->greeting("Hello {$notifiable->first_name}!")
            ->subject($this->subject)
            ->line($this->message);

        foreach ($this->links as $label => $link) {
            $message->action($label, $link);
        }

        return $message;
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->title($this->subject)
            ->success()
            ->body($this->message)
            ->actions([
                ...collect($this->links)->mapWithKeys(
                    fn ($link, $label) => Action::make($label)
                        ->button()
                        ->url($link)
                ),
                Action::make('markAsRead')
                    ->button()
                    ->markAsUnread(),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Send via SMS
     */
    public function toSms(User $notifiable): string
    {
        return "Hello {$notifiable->first_name}, {$this->message}";
    }

    /**
     * Send via WhatsApp
     */
    public function toWhatsApp(User $notifiable): string
    {
        return "Hello {$notifiable->first_name}, {$this->message}";
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        return [
            'subject' => $this->subject,
            'message' => $this->message,
            'links'   => $this->links,
        ];
    }
}
