<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\SiteSetting;
use App\Support\Portfolio;

class HomeController extends Controller
{
    public function __invoke()
    {
        return view('home', [
            'latestPosts' => Post::query()->published()->latest('published_at')->take(3)->get(),
            'experiences' => Portfolio::experiences(),
            'skillGroups' => Portfolio::skillGroups(),
            'projects' => Portfolio::projects(),
            'cvUrl' => Portfolio::cvUrl(),
            'bookingAvailable' => SiteSetting::bookingAvailable(),
        ]);
    }
}
