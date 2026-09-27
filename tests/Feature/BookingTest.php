<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmed;
use App\Mail\BookingDeclined;
use App\Mail\BookingRequested;
use App\Models\AvailabilityRule;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\BookingCancelled;
use App\Notifications\NewBookingRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** Monday 5 Oct 2026, 08:00 in Berlin (CEST, UTC+2). */
    private const NOW = '2026-10-05 06:00:00';

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse(self::NOW, 'UTC'));
        config(['booking.slot_minutes' => 30, 'booking.min_notice_hours' => 24, 'booking.window_days' => 30, 'booking.buffer_minutes' => 0]);
        Mail::fake();
        Notification::fake();

        SiteSetting::current()->update(['booking_enabled' => true, 'booking_timezone' => 'Europe/Berlin', 'booking_meeting_url' => 'https://meet.example.com/tanvir']);

        foreach ([1, 2, 3, 4, 5] as $weekday) {
            AvailabilityRule::create(['weekday' => $weekday, 'start_time' => '09:00:00', 'end_time' => '12:00:00']);
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function slots(): array
    {
        return $this->getJson(route('book.slots'))->assertOk()->json('slots');
    }

    private function book(string $start, array $overrides = [])
    {
        return $this->post(route('book.store'), $overrides + [
            'name' => 'Grace Hopper',
            'email' => 'grace@example.com',
            'topic' => 'A Shopify app for our stores.',
            'start' => $start,
            'timezone' => 'America/New_York',
        ]);
    }

    public function test_booking_is_hidden_when_disabled_or_without_hours(): void
    {
        $this->get(route('home'))->assertSee(route('book.index'), false);

        SiteSetting::current()->update(['booking_enabled' => false]);
        $this->get(route('book.index'))->assertNotFound();
        $this->getJson(route('book.slots'))->assertNotFound();
        $this->get(route('home'))->assertDontSee(route('book.index'), false);

        SiteSetting::current()->update(['booking_enabled' => true]);
        AvailabilityRule::query()->delete();
        $this->get(route('book.index'))->assertNotFound();
    }

    public function test_slots_respect_notice_timezone_and_weekdays(): void
    {
        $slots = $this->slots();

        // Earliest is 24h from now: Tuesday 09:00 Berlin = 07:00 UTC. Monday is inside the notice period.
        $this->assertSame('2026-10-06T07:00:00Z', $slots[0]);
        $this->assertNotContains('2026-10-05T07:00:00Z', $slots);

        // Six 30-minute slots between 09:00 and 12:00.
        $this->assertCount(6, array_filter($slots, fn ($slot) => str_starts_with($slot, '2026-10-06')));
        $this->assertContains('2026-10-06T09:30:00Z', $slots);
        $this->assertNotContains('2026-10-06T10:00:00Z', $slots);

        // No weekend slots (10/11 Oct).
        $this->assertEmpty(array_filter($slots, fn ($slot) => str_starts_with($slot, '2026-10-10') || str_starts_with($slot, '2026-10-11')));
    }

    public function test_slots_follow_daylight_saving_changes(): void
    {
        $slots = $this->slots();

        // Berlin leaves summer time on 25 Oct 2026: 09:00 is 07:00 UTC before and 08:00 UTC after.
        $this->assertContains('2026-10-23T07:00:00Z', $slots);
        $this->assertContains('2026-10-26T08:00:00Z', $slots);
        $this->assertNotContains('2026-10-26T07:00:00Z', $slots);
    }

    public function test_blocked_dates_and_existing_bookings_remove_slots(): void
    {
        BlockedDate::create(['date' => '2026-10-07']);
        $this->book('2026-10-06T07:30:00Z')->assertRedirect(route('book.requested'));

        $slots = $this->slots();

        $this->assertEmpty(array_filter($slots, fn ($slot) => str_starts_with($slot, '2026-10-07')));
        $this->assertNotContains('2026-10-06T07:30:00Z', $slots);
        $this->assertContains('2026-10-06T07:00:00Z', $slots);
    }

    public function test_visitor_can_request_a_call(): void
    {
        $this->book('2026-10-06T07:00:00Z')->assertRedirect(route('book.requested'));

        $booking = Booking::firstOrFail();
        $this->assertSame(Booking::PENDING, $booking->status);
        $this->assertSame('2026-10-06 07:30:00', $booking->ends_at->utc()->format('Y-m-d H:i:s'));
        $this->assertSame('America/New_York', $booking->visitor_timezone);

        Mail::assertQueued(BookingRequested::class, fn ($mail) => $mail->hasTo('grace@example.com'));
        Notification::assertSentOnDemand(NewBookingRequest::class);

        $this->get(route('book.requested'))->assertOk()->assertSee('Request sent!');
    }

    public function test_double_booking_and_unavailable_times_are_rejected(): void
    {
        // The endpoint allows 3 requests per 10 minutes; this test makes more on purpose.
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $this->book('2026-10-06T07:00:00Z');

        $this->book('2026-10-06T07:00:00Z', ['email' => 'other@example.com'])
            ->assertRedirect(route('book.index'))
            ->assertSessionHasErrors('start');

        $this->book('2026-10-06T11:00:00Z')->assertSessionHasErrors('start'); // 13:00 Berlin, outside hours
        $this->book('2026-10-05T08:00:00Z')->assertSessionHasErrors('start'); // inside notice period
        $this->book('2026-10-06T07:00:00Z', ['timezone' => 'Mars/Olympus'])->assertSessionHasErrors('timezone');

        $this->assertSame(1, Booking::count());
    }

    public function test_admin_approval_sends_calendar_invites(): void
    {
        $this->book('2026-10-06T07:00:00Z');
        $booking = Booking::firstOrFail();

        $this->actingAs(User::factory()->create(['email' => config('app.admin_email')]))
            ->patch(route('admin.bookings.approve', $booking))
            ->assertRedirect();

        $this->assertSame(Booking::CONFIRMED, $booking->fresh()->status);
        Mail::assertQueued(BookingConfirmed::class, fn ($mail) => $mail->hasTo('grace@example.com') && ! $mail->forOwner);
        Mail::assertQueued(BookingConfirmed::class, fn ($mail) => $mail->hasTo(config('app.admin_email')) && $mail->forOwner);

        $ics = BookingConfirmed::ics($booking->fresh());
        $unfolded = str_replace("\r\n ", '', $ics);
        $this->assertStringContainsString("BEGIN:VEVENT\r\n", $ics);
        $this->assertStringContainsString('DTSTART:20261006T070000Z', $ics);
        $this->assertStringContainsString('DTEND:20261006T073000Z', $ics);
        $this->assertStringContainsString('UID:'.$booking->uuid, $ics);
        $this->assertStringContainsString('mailto:grace@example.com', $unfolded);
        $this->assertStringContainsString('LOCATION:https://meet.example.com/tanvir', $unfolded);

        $mail = new BookingConfirmed($booking->fresh());
        $mail->assertSeeInHtml('https://meet.example.com/tanvir');
        $mail->assertSeeInHtml($booking->cancelUrl());
        $mail->assertHasAttachedData($ics, 'call.ics', ['mime' => 'text/calendar; method=REQUEST; charset=UTF-8']);
    }

    public function test_admin_can_decline_with_a_reason_and_the_slot_frees_up(): void
    {
        $this->book('2026-10-06T07:00:00Z');
        $booking = Booking::firstOrFail();

        $this->actingAs(User::factory()->create(['email' => config('app.admin_email')]))
            ->patch(route('admin.bookings.decline', $booking), ['reason' => 'Travelling that week.']);

        $this->assertSame(Booking::DECLINED, $booking->fresh()->status);
        Mail::assertQueued(BookingDeclined::class);
        (new BookingDeclined($booking->fresh()))->assertSeeInHtml('Travelling that week.');

        $this->assertContains('2026-10-06T07:00:00Z', $this->slots());
    }

    public function test_visitor_can_cancel_with_their_link(): void
    {
        $this->book('2026-10-06T07:00:00Z');
        $booking = Booking::firstOrFail();

        $this->get($booking->cancelUrl())->assertOk()->assertSee('Cancel your call?');
        $this->post(route('book.cancel.confirm', $booking->cancel_token))->assertOk()->assertSee('Call cancelled');

        $this->assertSame(Booking::CANCELLED, $booking->fresh()->status);
        Notification::assertSentOnDemand(BookingCancelled::class);
        $this->assertContains('2026-10-06T07:00:00Z', $this->slots());

        $this->get($booking->cancelUrl())->assertSee('This call can no longer be cancelled');
        $this->get(route('book.cancel', 'bad-token'))->assertNotFound();
    }

    public function test_german_booking_flow(): void
    {
        $this->get(route('de.book.index'))->assertOk()->assertSee('Lass uns über dein Projekt sprechen');

        $this->book('2026-10-06T07:00:00Z', ['locale' => 'de'])->assertRedirect(route('de.book.requested'));
        $this->assertSame('de', Booking::first()->locale);

        Mail::assertQueued(BookingRequested::class, fn ($mail) => $mail->locale === 'de');
    }

    public function test_admin_manages_availability(): void
    {
        $admin = User::factory()->create(['email' => config('app.admin_email')]);

        $this->actingAs($admin)->get(route('admin.availability.edit'))->assertOk()->assertSee('Next 7 days');

        $this->actingAs($admin)->post(route('admin.availability.rules.store'), ['weekdays' => [6], 'start_time' => '10:00', 'end_time' => '11:00'])->assertRedirect();
        $this->assertContains('2026-10-10T08:00:00Z', $this->slots()); // Saturday 10:00 Berlin

        $this->actingAs($admin)->post(route('admin.availability.rules.store'), ['weekdays' => [0], 'start_time' => '12:00', 'end_time' => '11:00'])
            ->assertSessionHasErrors('end_time');

        $this->actingAs($admin)->post(route('admin.availability.blocked.store'), ['date' => '2026-10-10'])->assertRedirect();
        $this->assertNotContains('2026-10-10T08:00:00Z', $this->slots());

        $this->actingAs($admin)->put(route('admin.availability.settings'), ['booking_timezone' => 'Asia/Dhaka', 'booking_enabled' => '1'])->assertRedirect();
        $this->assertSame('Asia/Dhaka', SiteSetting::current()->booking_timezone);

        $this->actingAs($admin)->get(route('admin.bookings.index'))->assertOk()->assertSee('Asia/Dhaka');
    }
}
