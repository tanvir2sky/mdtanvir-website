<?php

namespace App\Notifications;

use App\Models\GuestbookEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGuestbookEntry extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public GuestbookEntry $entry) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New guestbook note from '.$this->entry->name)
            ->line($this->entry->message)
            ->line('Website: '.($this->entry->website ?? '—'))
            ->action('Review in admin', route('admin.guestbook.index', ['status' => 'pending']));
    }
}
