<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $fillable = ['email', 'locale', 'token', 'confirmed_at', 'unsubscribed_at', 'consent_ip', 'source'];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Subscriber $subscriber) {
            $subscriber->token ??= Str::random(64);
        });
    }

    /** Confirmed and not unsubscribed. */
    public function scopeActive(Builder $query): void
    {
        $query->whereNotNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    public function scopePending(Builder $query): void
    {
        $query->whereNull('confirmed_at')->whereNull('unsubscribed_at');
    }

    public function scopeUnsubscribed(Builder $query): void
    {
        $query->whereNotNull('unsubscribed_at');
    }

    public function isActive(): bool
    {
        return $this->confirmed_at !== null && $this->unsubscribed_at === null;
    }

    public function status(): string
    {
        return match (true) {
            $this->unsubscribed_at !== null => 'unsubscribed',
            $this->confirmed_at !== null => 'confirmed',
            default => 'pending',
        };
    }

    public function confirmUrl(): string
    {
        return URL::temporarySignedRoute('newsletter.confirm', now()->addDays(7), ['subscriber' => $this->id]);
    }

    public function unsubscribeUrl(): string
    {
        return route('newsletter.unsubscribe', $this->token);
    }
}
