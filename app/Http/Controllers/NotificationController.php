<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Notifications
 */
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        /**
         * @var User $user
         */
        $user = User::findOrFail(Auth::id());

        if ($request->has('read')) {
            DatabaseNotification::find($request->query('read'))->markAsRead();
        }

        $notifications = $user->notifications()
            ->when(
                value: $request->has('unread'),
                callback: fn (Builder $nq) => $nq->whereNull('read_at')
            )
            ->latest()
            ->paginate($request->query('per', 20));

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        if ($request->has('all')) {
            Auth::user()->unreadNotifications->markAsRead();
        } else {
            $user = User::findOrFail($request->input('notifiableId'));
            $user->notify(
                instance: new GenericNotification(
                    subject: $request->input('data.subject'),
                    message: $request->input('data.message'),
                    links: $request->input('data.links', []),
                    channels: $request->input('data.channels', ['database', 'fcm']),
                )
            );
        }
    }

    public function show(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->json($notification);
    }

    public function update(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->json($notification);
    }

    public function destroy(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->json($notification);
    }
}