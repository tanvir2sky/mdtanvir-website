<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use App\Support\Locale;
use Illuminate\Support\Facades\Cache;

/** Search index for the ⌘K command palette. */
class SearchController extends Controller
{
    public function __invoke()
    {
        $locale = Locale::current();

        $items = Cache::remember("search.index.{$locale}", now()->addMinutes(10), function () {
            $posts = Post::query()
                ->published()
                ->latest('published_at')
                ->get(['title', 'slug', 'category', 'excerpt', 'tags'])
                ->map(fn (Post $post) => [
                    'type' => 'post',
                    'title' => $post->title,
                    'category' => $post->category,
                    'keywords' => implode(' ', $post->tags ?? []),
                    'url' => lroute('blog.show', $post->slug),
                ]);

            $projects = Project::query()
                ->withPublishedCaseStudy()
                ->ordered()
                ->get()
                ->map(fn (Project $project) => [
                    'type' => 'project',
                    'title' => $project->t('title'),
                    'category' => $project->t('category'),
                    'keywords' => implode(' ', $project->tags ?? []),
                    'url' => lroute('projects.show', $project->slug),
                ]);

            return $projects->concat($posts)->values()->all();
        });

        return response()->json(['items' => $items]);
    }
}
