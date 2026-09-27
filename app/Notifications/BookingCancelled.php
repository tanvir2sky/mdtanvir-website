<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
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
            ->subject('Cancelled: call with '.$this->booking->name)
            ->line($this->booking->name.' cancelled the call on '.$when->format('l, F j, Y H:i').' ('.$when->tzName.').')
            ->line('The slot is free again.')
            ->action('View bookings', route('admin.bookings.index'));
    }
}
