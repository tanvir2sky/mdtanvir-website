<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmed;
use App\Mail\BookingDeclined;
use App\Models\Booking;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index()
    {
        return view('admin.bookings.index', [
            'upcoming' => Booking::query()->active()->where('ends_at', '>', now())->orderBy('starts_at')->get(),
            'past' => Booking::query()
                ->where(fn ($query) => $query->where('ends_at', '<=', now())->orWhereNotIn('status', Booking::ACTIVE))
                ->latest('starts_at')
                ->paginate(20),
            'timezone' => SiteSetting::current()->bookingTimezone(),
        ]);
    }

    public function approve(Booking $booking)
    {
        abort_unless($booking->status === Booking::PENDING, 409, 'Only pending bookings can be approved.');

        $booking->update(['status' => Booking::CONFIRMED]);

        Mail::to($booking->email)->queue(new BookingConfirmed($booking));
        if (filled($adminEmail = config('app.admin_email'))) {
            Mail::to($adminEmail)->queue(new BookingConfirmed($booking, forOwner: true));
        }

        return back()->with('status', 'Booking confirmed. A calendar invite was sent to '.$booking->email.'.');
    }

    public function decline(Request $request, Booking $booking)
    {
        abort_unless($booking->isActive(), 409, 'This booking is no longer active.');

        $data = $request->validate(['reason' => ['nullable', 'string', 'max:500']]);

        $booking->update(['status' => Booking::DECLINED, 'decline_reason' => $data['reason'] ?? null]);
        Mail::to($booking->email)->queue(new BookingDeclined($booking));

        return back()->with('status', 'Booking declined and '.$booking->name.' was notified.');
    }
}
