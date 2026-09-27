<?php

namespace App\Support;

use App\Models\Experience;
use App\Models\Project;
use App\Models\SkillGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/** Home page content from the database, plus the profile basics in config/portfolio.php. */
class Portfolio
{
    public static function experiences(): Collection
    {
        return self::tableExists('experiences') ? Experience::query()->ordered()->get() : collect();
    }

    public static function skillGroups(): Collection
    {
        return self::tableExists('skill_groups') ? SkillGroup::query()->ordered()->get() : collect();
    }

    public static function projects(): Collection
    {
        return self::tableExists('projects') ? Project::query()->visible()->ordered()->get() : collect();
    }

    public static function cvUrl(): ?string
    {
        $path = config('portfolio.cv_path');

        return $path && file_exists(public_path($path)) ? asset($path) : null;
    }

    /** Data for the hero terminal, in the current locale. */
    public static function terminalData(): array
    {
        $locale = Locale::current();

        return Cache::rememberForever("portfolio.terminal.{$locale}", function () {
            return [
                'name' => config('portfolio.name'),
                'role' => __('Software Engineer'),
                'email' => config('portfolio.email'),
                'linkedin' => config('portfolio.linkedin'),
                'github' => config('portfolio.github'),
                'summary' => __('Software Engineer with 8+ years building scalable web apps with Laravel, PHP and Shopify, now shipping LLM-powered features in production.'),
                'skills' => self::skillGroups()
                    ->mapWithKeys(fn (SkillGroup $group) => [$group->t('name') => $group->t('items') ?? []])
                    ->all(),
                'experience' => self::experiences()
                    ->map(fn (Experience $job) => [
                        'role' => $job->t('role'),
                        'company' => $job->company,
                        'period' => $job->is_current ? __('Present') : $job->period,
                    ])
                    ->all(),
                'projects' => self::projects()
                    ->map(fn (Project $project) => [
                        'name' => $project->t('title'),
                        'stack' => implode(', ', $project->tags ?? []),
                    ])
                    ->all(),
            ];
        }) + ['cv_url' => self::cvUrl()];
    }

    public static function flush(): void
    {
        foreach (Locale::supported() as $locale) {
            Cache::forget("portfolio.terminal.{$locale}");
        }
    }

    private static function tableExists(string $table): bool
    {
        static $checked = [];

        return $checked[$table] ??= Schema::hasTable($table);
    }
}
