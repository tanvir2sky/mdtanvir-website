<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostReaction extends Model
{
    /** Reaction type => emoji. */
    public const TYPES = ['like' => '👍', 'fire' => '🔥', 'idea' => '💡'];

    protected $fillable = ['post_id', 'type', 'visitor_hash'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
