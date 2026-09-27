<?php

namespace App\Services;

use App\Models\AvailabilityRule;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\SiteSetting;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Bookable call slots: weekly availability in the owner's time zone, minus blocked dates,
 * the minimum-notice period and existing pending/confirmed bookings. Slots are returned in UTC.
 */
class BookingSlots
{
    private string $timezone;

    private int $slotMinutes;

    private int $bufferMinutes;

    public function __construct(?string $timezone = null)
    {
        $this->timezone = $timezone ?? SiteSetting::current()->bookingTimezone();
        $this->slotMinutes = max(5, (int) config('booking.slot_minutes', 30));
        $this->bufferMinutes = max(0, (int) config('booking.buffer_minutes', 0));
    }

    public function timezone(): string
    {
        return $this->timezone;
    }

    public function slotMinutes(): int
    {
        return $this->slotMinutes;
    }

    /** @return Collection<int, CarbonImmutable> slot start times in UTC, ascending */
    public function available(?int $days = null): Collection
    {
        $now = CarbonImmutable::now('UTC');
        $earliest = $now->addHours((int) config('booking.min_notice_hours', 24));
        $days ??= (int) config('booking.window_days', 30);
        $latest = $now->addDays($days);

        $rules = AvailabilityRule::query()->get()->groupBy('weekday');
        $blocked = BlockedDate::query()->pluck('date')->map(fn ($date) => $date->format('Y-m-d'))->flip();
        $busy = Booking::query()->active()->where('ends_at', '>', $now)->get(['starts_at', 'ends_at']);

        $slots = collect();
        $day = CarbonImmutable::now($this->timezone)->startOfDay();
        $lastDay = $latest->setTimezone($this->timezone)->startOfDay();

        for (; $day->lte($lastDay); $day = $day->addDay()) {
            if ($blocked->has($day->format('Y-m-d'))) {
                continue;
            }

            foreach ($rules->get($day->dayOfWeek, []) as $rule) {
                // Parsing wall-clock times in the owner's zone keeps slots correct across DST changes.
                $start = CarbonImmutable::parse($day->format('Y-m-d').' '.$rule->start_time, $this->timezone);
                $end = CarbonImmutable::parse($day->format('Y-m-d').' '.$rule->end_time, $this->timezone);

                for ($slot = $start; $slot->addMinutes($this->slotMinutes)->lte($end); $slot = $slot->addMinutes($this->slotMinutes + $this->bufferMinutes)) {
                    $utc = $slot->utc();

                    if ($utc->lt($earliest) || $utc->gt($latest) || $this->overlaps($busy, $utc)) {
                        continue;
                    }

                    $slots->push($utc);
                }
            }
        }

        return $slots->unique(fn (CarbonImmutable $slot) => $slot->timestamp)->sort()->values();
    }

    public function isAvailable(CarbonInterface $start): bool
    {
        $timestamp = $start->getTimestamp();

        return $this->available()->contains(fn (CarbonImmutable $slot) => $slot->timestamp === $timestamp);
    }

    private function overlaps(Collection $busy, CarbonImmutable $start): bool
    {
        $end = $start->addMinutes($this->slotMinutes + $this->bufferMinutes);

        return $busy->contains(fn (Booking $booking) => $booking->starts_at->lt($end) && $booking->ends_at->gt($start));
    }
}
