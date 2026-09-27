<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityRule;
use App\Models\BlockedDate;
use App\Models\SiteSetting;
use App\Services\BookingSlots;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::current();
        $slots = new BookingSlots($settings->bookingTimezone());

        return view('admin.availability.edit', [
            'settings' => $settings,
            'rules' => AvailabilityRule::query()->orderBy('weekday')->orderBy('start_time')->get()->groupBy('weekday'),
            'blockedDates' => BlockedDate::query()->where('date', '>=', today())->orderBy('date')->get(),
            'preview' => $slots->available(7)->groupBy(fn ($slot) => $slot->setTimezone($settings->bookingTimezone())->format('Y-m-d')),
            'timezones' => \DateTimeZone::listIdentifiers(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'booking_enabled' => ['nullable', 'boolean'],
            'booking_timezone' => ['required', 'timezone:all'],
            'booking_meeting_url' => ['nullable', 'url', 'max:255'],
        ]);

        SiteSetting::current()->update([
            'booking_enabled' => $request->boolean('booking_enabled'),
            'booking_timezone' => $data['booking_timezone'],
            'booking_meeting_url' => $data['booking_meeting_url'] ?? null,
        ]);

        return back()->with('status', 'Booking settings saved.');
    }

    public function storeRule(Request $request)
    {
        $data = $request->validate([
            'weekdays' => ['required', 'array', 'min:1'],
            'weekdays.*' => ['integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        foreach (array_unique($data['weekdays']) as $weekday) {
            AvailabilityRule::create([
                'weekday' => (int) $weekday,
                'start_time' => $data['start_time'].':00',
                'end_time' => $data['end_time'].':00',
            ]);
        }

        return back()->with('status', 'Availability added.');
    }

    public function destroyRule(AvailabilityRule $rule)
    {
        $rule->delete();

        return back()->with('status', 'Availability removed.');
    }

    public function storeBlocked(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        BlockedDate::query()->updateOrCreate(['date' => $data['date']], ['reason' => $data['reason'] ?? null]);

        return back()->with('status', 'Date blocked.');
    }

    public function destroyBlocked(BlockedDate $blockedDate)
    {
        $blockedDate->delete();

        return back()->with('status', 'Date unblocked.');
    }
}
