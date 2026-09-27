<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Booking extends Model
{
    public const PENDING = 'pending';

    public const CONFIRMED = 'confirmed';

    public const DECLINED = 'declined';

    public const CANCELLED = 'cancelled';

    /** Statuses that hold a slot. */
    public const ACTIVE = [self::PENDING, self::CONFIRMED];

    protected $fillable = [
        'name', 'email', 'topic', 'starts_at', 'ends_at', 'visitor_timezone',
        'locale', 'status', 'decline_reason', 'visitor_hash',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            $booking->uuid ??= (string) Str::uuid();
            $booking->cancel_token ??= Str::random(48);
        });
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereIn('status', self::ACTIVE);
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE, true);
    }

    public function isCancellable(): bool
    {
        return $this->isActive() && $this->starts_at->isFuture();
    }

    /** Start time in the given zone, e.g. for emails. */
    public function startsIn(string $timezone): Carbon
    {
        return $this->starts_at->copy()->setTimezone($timezone);
    }

    public function cancelUrl(): string
    {
        return lroute('book.cancel', $this->cancel_token, $this->locale);
    }
}
