<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreCheck extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['host', 'is_shopify', 'score', 'visitor_hash', 'locale'];

    protected function casts(): array
    {
        return ['is_shopify' => 'boolean', 'created_at' => 'datetime'];
    }
}
