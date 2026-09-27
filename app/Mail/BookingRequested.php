<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the visitor right after they request a call. */
class BookingRequested extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->locale($booking->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('Call request received'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.booking.requested', with: [
            'when' => $this->booking->startsIn($this->booking->visitor_timezone),
        ]);
    }
}
