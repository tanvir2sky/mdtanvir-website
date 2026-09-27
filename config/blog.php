<?php

/*
|--------------------------------------------------------------------------
| Blog categories
|--------------------------------------------------------------------------
|
| Categories are free text on each post; entries here give a category its
| colour and icon on the public blog and are offered as suggestions in the
| admin editor. Unknown categories fall back to "default".
|
| Class strings must be written out in full so Tailwind can detect them.
|
*/

return [

    'per_page' => 9,

    'categories' => [
        'Laravel' => [
            'icon' => 'fab fa-laravel',
            'badge' => 'bg-rose-500/10 text-rose-600 ring-rose-500/20 dark:text-rose-300',
            'cover' => 'from-rose-500 via-orange-500 to-amber-400',
        ],
        'AI' => [
            'icon' => 'fas fa-brain',
            'badge' => 'bg-cyan-500/10 text-cyan-600 ring-cyan-500/20 dark:text-cyan-300',
            'cover' => 'from-cyan-400 via-sky-500 to-violet-500',
        ],
        'Shopify' => [
            'icon' => 'fab fa-shopify',
            'badge' => 'bg-emerald-500/10 text-emerald-600 ring-emerald-500/20 dark:text-emerald-300',
            'cover' => 'from-emerald-400 via-teal-500 to-cyan-600',
        ],
        'Architecture' => [
            'icon' => 'fas fa-diagram-project',
            'badge' => 'bg-violet-500/10 text-violet-600 ring-violet-500/20 dark:text-violet-300',
            'cover' => 'from-violet-500 via-purple-500 to-fuchsia-500',
        ],
        'Performance' => [
            'icon' => 'fas fa-gauge-high',
            'badge' => 'bg-amber-500/10 text-amber-600 ring-amber-500/20 dark:text-amber-300',
            'cover' => 'from-amber-400 via-orange-500 to-rose-500',
        ],
        'Workflow' => [
            'icon' => 'fas fa-wand-magic-sparkles',
            'badge' => 'bg-sky-500/10 text-sky-600 ring-sky-500/20 dark:text-sky-300',
            'cover' => 'from-sky-400 via-primary-500 to-indigo-600',
        ],
    ],

    'default' => [
        'icon' => 'fas fa-pen-nib',
        'badge' => 'bg-gray-500/10 text-gray-600 ring-gray-500/20 dark:text-gray-300',
        'cover' => 'from-primary-400 via-primary-600 to-gray-900',
    ],

];
