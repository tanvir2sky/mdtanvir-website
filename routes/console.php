<?php

use App\Jobs\SendPostToSubscribers;
use App\Models\Post;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('newsletter:send-due', function () {
    $posts = Post::query()
        ->published()
        ->where('notify_subscribers', true)
        ->whereNull('newsletter_sent_at')
        ->get();

    foreach ($posts as $post) {
        SendPostToSubscribers::dispatch($post);
    }

    $this->info("Queued newsletter for {$posts->count()} post(s).");
})->purpose('Email subscribers about published posts that have not been sent yet');

Schedule::command('newsletter:send-due')->everyTenMinutes()->withoutOverlapping();
