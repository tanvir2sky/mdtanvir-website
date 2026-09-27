<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingRequest extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $when = $this->booking->startsIn(SiteSetting::current()->bookingTimezone());

        return (new MailMessage)
            ->subject('Call request: '.$this->booking->name.' · '.$when->format('D M j, H:i'))
            ->greeting('New call request')
            ->line('From: '.$this->booking->name.' <'.$this->booking->email.'>')
            ->line('When: '.$when->format('l, F j, Y H:i').' ('.$when->tzName.')')
            ->line('Their time zone: '.$this->booking->visitor_timezone)
            ->line('Topic: '.$this->booking->topic)
            ->action('Approve or decline', route('admin.bookings.index'));
    }
}
