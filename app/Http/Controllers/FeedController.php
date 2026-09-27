<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;

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

        $projects = Project::query()->withPublishedCaseStudy()->ordered()->get(['id', 'slug', 'updated_at']);

        return response()
            ->view('feed.sitemap', compact('posts', 'projects'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
