<?php

namespace App\Jobs;

use App\Mail\NewPostPublished;
use App\Models\Post;
use App\Models\Subscriber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

/** Emails a newly published post to every active subscriber, exactly once. */
class SendPostToSubscribers implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        // Claim the post atomically so it can never be sent twice, even if dispatched twice.
        $claimed = Post::query()
            ->whereKey($this->post->id)
            ->whereNull('newsletter_sent_at')
            ->update(['newsletter_sent_at' => now()]);

        if ($claimed === 0) {
            return;
        }

        Subscriber::query()->active()->chunkById(200, function ($subscribers) {
            foreach ($subscribers as $subscriber) {
                Mail::to($subscriber->email)->queue(new NewPostPublished($this->post, $subscriber));
            }
        });
    }

    /** Whether the post should be emailed now: published, due, opted in and not already sent. */
    public static function shouldSend(Post $post): bool
    {
        return $post->is_published
            && $post->notify_subscribers
            && $post->newsletter_sent_at === null
            && $post->published_at !== null
            && $post->published_at->lte(now());
    }
}
