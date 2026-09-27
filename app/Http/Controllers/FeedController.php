<?php

namespace App\Http\Controllers;

use App\Models\Post;

class FeedController extends Controller
{
    public function rss()
    {
        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->take(20)
            ->get();

        return response()
            ->view('feed.rss', compact('posts'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }

    public function sitemap()
    {
        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->get(['slug', 'updated_at']);

        return response()
            ->view('feed.sitemap', compact('posts'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
