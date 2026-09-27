<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GuestbookEntry extends Model
{
    protected $fillable = ['name', 'message', 'website', 'locale', 'visitor_hash', 'approved_at'];

    protected function casts(): array
    {
        return ['approved_at' => 'datetime'];
    }

    public function scopeApproved(Builder $query): void
    {
        $query->whereNotNull('approved_at');
    }

    public function scopePending(Builder $query): void
    {
        $query->whereNull('approved_at');
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('') ?: '?';
    }

    /** A stable avatar gradient derived from the name (full class names so Tailwind can detect them). */
    public function avatarGradient(): string
    {
        $gradients = [
            'from-cyan-400 to-sky-600',
            'from-violet-400 to-fuchsia-600',
            'from-emerald-400 to-teal-600',
            'from-amber-400 to-orange-600',
            'from-rose-400 to-pink-600',
            'from-sky-400 to-indigo-600',
        ];

        return $gradients[crc32(mb_strtolower($this->name)) % count($gradients)];
    }
}
