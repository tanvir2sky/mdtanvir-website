<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\SiteSetting;
use App\Support\Ics;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the visitor (and a copy to the owner) when a call is confirmed, with an .ics invite. */
class BookingConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public bool $forOwner = false)
    {
        $this->locale($forOwner ? 'en' : $booking->locale);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->forOwner
            ? 'Confirmed: call with '.$this->booking->name
            : __('Your call is confirmed'));
    }

    public function content(): Content
    {
        $settings = SiteSetting::current();

        return new Content(markdown: 'mail.booking.confirmed', with: [
            'when' => $this->booking->startsIn($this->forOwner ? $settings->bookingTimezone() : $this->booking->visitor_timezone),
            'meetingUrl' => $settings->booking_meeting_url,
            'cancelUrl' => $this->booking->cancelUrl(),
        ]);
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => self::ics($this->booking), 'call.ics')
                ->withMime('text/calendar; method=REQUEST; charset=UTF-8'),
        ];
    }

    public static function ics(Booking $booking): string
    {
        $meetingUrl = SiteSetting::current()->booking_meeting_url;

        return Ics::event([
            'uid' => $booking->uuid.'@'.parse_url(config('app.url'), PHP_URL_HOST),
            'start' => $booking->starts_at,
            'end' => $booking->ends_at,
            'summary' => 'Call: '.$booking->name.' & MD Tanvir Hossain',
            'description' => trim($booking->topic.($meetingUrl ? "\n\nJoin: {$meetingUrl}" : '')),
            'location' => $meetingUrl,
            'url' => $meetingUrl,
            'organizer_email' => config('app.admin_email') ?: config('mail.from.address'),
            'organizer_name' => 'MD Tanvir Hossain',
            'attendee_email' => $booking->email,
            'attendee_name' => $booking->name,
        ]);
    }
}
