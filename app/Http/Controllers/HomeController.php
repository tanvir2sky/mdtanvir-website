<?php

namespace App\Http\Controllers;

use App\Models\Post;

class HomeController extends Controller
{
    public function __invoke()
    {
        $latestPosts = Post::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $cvPath = config('portfolio.cv_path');
        $cvUrl = $cvPath && file_exists(public_path($cvPath)) ? asset($cvPath) : null;

        return view('home', compact('latestPosts', 'cvUrl'));
    }
}
