<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    //
    public function index()
    {
        $user = auth()->user();
        $notifs = Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();
        return response()->json($notifs);
    }

    public function markAllRead(Request $request)
    {

        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
