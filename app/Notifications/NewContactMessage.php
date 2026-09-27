<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ContactMessage $contact)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New contact message: '.$this->contact->subject)
            ->replyTo($this->contact->email, $this->contact->name)
            ->greeting('New message from '.$this->contact->name)
            ->line('Email: '.$this->contact->email)
            ->line('Subject: '.$this->contact->subject)
            ->line($this->contact->message)
            ->action('Open in admin', route('admin.contacts.show', $this->contact));
    }
}
