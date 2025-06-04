<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        // Eager load the 'data' to ensure all necessary fields are present
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($notifications);
    }

    public function unreadCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'error' => 'Unauthenticated.'], 401);
        }
        return response()->json(['unread_count' => $user->unreadNotifications()->count()]);
    }

    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => 'Notification marked as read.']);
        }
        return response()->json(['error' => 'Notification not found.'], 404);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $user->unreadNotifications->markAsRead();
        return response()->json(['success' => 'All unread notifications marked as read.']);
    }

    public function loadMore(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        $page = $request->input('page', 2); // Default to page 2 if not provided
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->paginate(10, ['*'], 'page', $page);
        
        return response()->json($notifications);
    }
} 