<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Chỉ lấy thông báo chưa đọc thay vì tất cả thông báo
        
        $notifications = $user->unreadNotifications()->paginate(20);

        // Định dạng lại notifications để dễ sử dụng trong React
        /** @var \Illuminate\Pagination\LengthAwarePaginator $notifications */
        $formattedNotifications = $notifications->through(function ($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        });

        return Inertia::render('Content/Notifications', [
            'notifications' => $formattedNotifications
        ]);
    }
    public function getAllNotifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);

        $formattedNotifications = $notifications->through(function ($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        });

        return Inertia::render('Content/AllNotifications', [
            'notifications' => $formattedNotifications
        ]);
    }
    public function markAsRead(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($request->has('id')) {
            // Đánh dấu một notification cụ thể là đã đọc
            $notification = $user->notifications()->where('id', $request->id)->first();
            if ($notification) {
                $notification->markAsRead();
                Log::info('Marked single notification as read', ['notification_id' => $request->id]);
            }
        } else {
            // Đánh dấu tất cả notification là đã đọc
            $count = $user->unreadNotifications->count();
            $user->unreadNotifications->markAsRead();
            Log::info('Marked all notifications as read', ['count' => $count]);
        }

        return response()->json([
            'success' => true,
            'unread_count' => $user->unreadNotifications->count()
        ]);
    }
    // Thêm phương thức API

    public function getNotifications()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);

        $formattedNotifications = $notifications->through(function ($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at->diffForHumans()
            ];
        });

        return response()->json([
            'notifications' => $formattedNotifications,
            'unread_count' => $user->unreadNotifications->count()
        ]);
    }
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user ? $user->unreadNotifications->count() : 0;

        return response()->json(['count' => $count]);
    }
}
