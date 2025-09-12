<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = Notification::forUser($user->id)
            ->with('campaign:id,title')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $unreadCount = Notification::forUser($user->id)
            ->unread()
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        /** @var Notification $notification */
        $notification = Notification::forUser($user->id)
            ->findOrFail($id);
        
        $notification->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllAsRead(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Notification::forUser($user->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
