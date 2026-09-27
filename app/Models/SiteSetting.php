<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SiteSetting extends Model
{
    protected $fillable = [
        'enable_gtm',
        'gtm_id',
        'enable_ga',
        'ga_measurement_id',
        'enable_clarity',
        'clarity_project_id',
        'booking_enabled',
        'booking_timezone',
        'booking_meeting_url',
    ];

    protected function casts(): array
    {
        return [
            'enable_gtm' => 'boolean',
            'enable_ga' => 'boolean',
            'enable_clarity' => 'boolean',
            'booking_enabled' => 'boolean',
        ];
    }

    /** The single settings row (created on first use). */
    public static function current(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    public function bookingTimezone(): string
    {
        return $this->booking_timezone ?: config('app.timezone', 'UTC');
    }

    /** Booking is shown only when switched on and at least one weekly slot exists. */
    public static function bookingAvailable(): bool
    {
        return Schema::hasTable('availability_rules')
            && static::current()->booking_enabled
            && AvailabilityRule::query()->exists();
    }
}
