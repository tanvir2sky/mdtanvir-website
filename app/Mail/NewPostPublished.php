<?php

namespace App\Mail;

use App\Models\Post;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;

class NewPostPublished extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Post $post, public Subscriber $subscriber)
    {
        $this->locale($subscriber->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('New article: :title', ['title' => $this->post->title]));
    }

    public function headers(): Headers
    {
        return new Headers(text: [
            'List-Unsubscribe' => '<'.$this->subscriber->unsubscribeUrl().'>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.newsletter.new-post', with: [
            'postUrl' => lroute('blog.show', $this->post->slug, $this->subscriber->locale),
            'unsubscribeUrl' => $this->subscriber->unsubscribeUrl(),
        ]);
    }
}
