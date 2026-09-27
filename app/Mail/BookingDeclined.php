<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingDeclined extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->locale($booking->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('About your call request'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.booking.declined', with: [
            'when' => $this->booking->startsIn($this->booking->visitor_timezone),
            'bookUrl' => SiteSetting::bookingAvailable() ? lroute('book.index', [], $this->booking->locale) : lroute('home', [], $this->booking->locale).'#contact',
        ]);
    }
}
