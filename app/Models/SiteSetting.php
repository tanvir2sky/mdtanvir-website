<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'enable_gtm',
        'gtm_id',
        'enable_ga',
        'ga_measurement_id',
        'enable_clarity',
        'clarity_project_id',
    ];

    protected function casts(): array
    {
        return [
            'enable_gtm' => 'boolean',
            'enable_ga' => 'boolean',
            'enable_clarity' => 'boolean',
        ];
    }
}
