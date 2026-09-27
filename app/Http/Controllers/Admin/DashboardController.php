<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Post;
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
            ],
            'recentMessages' => ContactMessage::latest()->take(5)->get(),
            'recentPosts' => Post::latest()->take(5)->get(),
        ]);
    }
}
