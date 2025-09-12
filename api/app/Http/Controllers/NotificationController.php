<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::forUser($request->user()->id)
            ->with('campaign:id,title')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $unreadCount = Notification::forUser($request->user()->id)
            ->unread()
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = Notification::forUser($request->user()->id)
            ->findOrFail($id);
        
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        Notification::forUser($request->user()->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
