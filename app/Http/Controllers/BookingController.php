<?php

namespace App\Http\Controllers;

use App\Mail\BookingRequested;
use App\Models\Booking;
use App\Models\SiteSetting;
use App\Notifications\BookingCancelled;
use App\Notifications\NewBookingRequest;
use App\Services\BookingSlots;
use App\Support\Locale;
use App\Support\Visitor;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index()
    {
        abort_unless(SiteSetting::bookingAvailable(), 404);

        return view('book.index', [
            'slotMinutes' => (int) config('booking.slot_minutes', 30),
        ]);
    }

    public function slots(BookingSlots $slots)
    {
        abort_unless(SiteSetting::bookingAvailable(), 404);

        return response()->json([
            'timezone' => $slots->timezone(),
            'slot_minutes' => $slots->slotMinutes(),
            'slots' => $slots->available()->map(fn (CarbonImmutable $slot) => $slot->toIso8601ZuluString())->all(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(SiteSetting::bookingAvailable(), 404);

        $locale = Locale::isSupported($request->input('locale')) ? $request->input('locale') : Locale::default();
        App::setLocale($locale);
        $back = lroute('book.index', [], $locale);

        if (filled($request->input('company'))) {
            return redirect()->to(lroute('book.requested', [], $locale));
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'topic' => ['required', 'string', 'max:1000'],
            'start' => ['required', 'date'],
            'timezone' => ['required', 'timezone:all'],
        ]);

        if ($validator->fails()) {
            return redirect()->to($back)->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $start = CarbonImmutable::parse($data['start'])->utc();

        // Serialise booking creation so two visitors can't grab the same slot at once.
        $booking = Cache::lock('booking:create', 10)->block(5, function () use ($data, $start, $locale, $request) {
            if (! app(BookingSlots::class)->isAvailable($start)) {
                return null;
            }

            return Booking::create([
                'name' => trim($data['name']),
                'email' => mb_strtolower(trim($data['email'])),
                'topic' => trim($data['topic']),
                'starts_at' => $start,
                'ends_at' => $start->addMinutes((int) config('booking.slot_minutes', 30)),
                'visitor_timezone' => $data['timezone'],
                'locale' => $locale,
                'status' => Booking::PENDING,
                'visitor_hash' => Visitor::hash($request),
            ]);
        });

        if (! $booking) {
            return redirect()->to($back)
                ->withErrors(['start' => __('Sorry, that time was just taken. Please pick another slot.')])
                ->withInput();
        }

        Mail::to($booking->email)->queue(new BookingRequested($booking));
        $this->notifyAdmin(new NewBookingRequest($booking));

        return redirect()->to(lroute('book.requested', [], $locale));
    }

    public function requested()
    {
        return view('newsletter.status', [
            'icon' => 'fas fa-calendar-check',
            'title' => __('Request sent!'),
            'message' => __('Thanks! I will confirm your call by email shortly, with a calendar invite and the meeting link.'),
            'actionUrl' => lroute('home'),
            'actionLabel' => __('Back to home'),
        ]);
    }

    public function cancelForm(string $token)
    {
        $booking = Booking::query()->where('cancel_token', $token)->firstOrFail();

        return view('book.cancel', ['booking' => $booking]);
    }

    public function cancel(string $token)
    {
        $booking = Booking::query()->where('cancel_token', $token)->firstOrFail();
        App::setLocale($booking->locale);

        if ($booking->isCancellable()) {
            $booking->update(['status' => Booking::CANCELLED]);
            $this->notifyAdmin(new BookingCancelled($booking));
        }

        return view('newsletter.status', [
            'icon' => 'fas fa-calendar-xmark',
            'title' => __('Call cancelled'),
            'message' => __('Your call has been cancelled. You are welcome to book another time whenever it suits you.'),
            'actionUrl' => SiteSetting::bookingAvailable() ? lroute('book.index') : lroute('home'),
            'actionLabel' => SiteSetting::bookingAvailable() ? __('Book another time') : __('Back to home'),
        ]);
    }

    private function notifyAdmin($notification): void
    {
        if (blank($adminEmail = config('app.admin_email'))) {
            return;
        }

        try {
            Notification::route('mail', $adminEmail)->notify($notification);
        } catch (\Throwable $e) {
            Log::warning('Booking notification failed: '.$e->getMessage());
        }
    }
}
