<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user?->notifications()->latest()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function unreadCount(Request $request)
    {
        $user = $request->user();
        return response()->json(['count' => $user?->unreadNotifications()->count() ?? 0]);
    }

    public function list(Request $request)
    {
        $user = $request->user();
        $items = $user?->notifications()->latest()->limit(10)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'read_at' => $n->read_at,
                'created_at' => $n->created_at?->toDateTimeString(),
            ];
        }) ?? collect();
        return response()->json(['items' => $items]);
    }

    public function markAllRead(Request $request)
    {
        $user = $request->user();
        $user?->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }
}

