<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\GuestbookEntry;
use App\Models\Post;
use App\Models\StoreCheck;
use App\Models\Subscriber;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'stats' => [
                'posts_total' => Post::count(),
                'posts_published' => Post::where('is_published', true)->count(),
                'messages_total' => ContactMessage::count(),
                'messages_unread' => ContactMessage::where('is_read', false)->count(),
                'subscribers_active' => Subscriber::active()->count(),
                'subscribers_pending' => Subscriber::pending()->count(),
                'guestbook_pending' => GuestbookEntry::pending()->count(),
                'bookings_pending' => Booking::where('status', Booking::PENDING)->where('starts_at', '>', now())->count(),
                'store_checks_week' => StoreCheck::where('created_at', '>=', now()->subDays(7))->count(),
            ],
            'recentMessages' => ContactMessage::latest()->take(5)->get(),
            'recentPosts' => Post::latest()->take(5)->get(),
            'topPosts' => Post::query()->published()->withCount('reactions')->orderByDesc('views_count')->take(5)->get(),
        ]);
    }
}
