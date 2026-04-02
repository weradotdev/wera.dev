<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Workspace;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitedWithWorkspaceNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Workspace $workspace
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $workspaceName = $this->workspace->name;

        return (new MailMessage)
            ->subject('You\'re invited to Wera')
            ->greeting("Hello {$notifiable->name}!")
            ->line("You've been invited to **The {$workspaceName}**.")
            ->line('A personal workspace has been created for you. You can add unlimited projects to track them.')
            ->action('Go to your workspace', url('/'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User $notifiable): array
    {
        $workspaceName = $this->workspace->name;

        return FilamentNotification::make()
            ->title('You\'re invited to Wera')
            ->success()
            ->body("You've been invited to **The {$workspaceName}**. A personal workspace has been created for you—you can add unlimited projects to track them.")
            ->getDatabaseMessage();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        return [
            'workspace_id'   => $this->workspace->getKey(),
            'workspace_name' => $this->workspace->name,
        ];
    }
}
