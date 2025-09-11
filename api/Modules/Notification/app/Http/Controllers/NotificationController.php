<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        // Return user's notifications
        return $request->user()->notifications()->orderByDesc('created_at')->paginate(20);
    }

    public function markAsRead(Request $request, $notificationId)
    {
        $notification = $request->user()->notifications()->findOrFail($notificationId);
        $notification->markAsRead();
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['message' => 'All notifications marked as read']);
    }
}
