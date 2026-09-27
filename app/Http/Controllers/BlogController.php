<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Support\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $tag = $request->query('tag');
        $sort = $request->query('sort') === 'popular' ? 'popular' : 'latest';
        $filtering = $search !== '' || filled($category) || filled($tag);

        // The featured hero only shows on the unfiltered first page.
        $featured = ! $filtering && $sort === 'latest' && $request->integer('page', 1) === 1
            ? Post::query()->published()->where('is_featured', true)->latest('published_at')->first()
            : null;

        $posts = Post::query()
            ->published()
            ->search($search)
            ->when($category, fn ($query) => $query->where('category', $category))
            ->when($tag, fn ($query) => $query->whereJsonContains('tags', $tag))
            ->when($featured, fn ($query) => $query->whereKeyNot($featured->id))
            ->withCount('reactions')
            ->when(
                $sort === 'popular',
                fn ($query) => $query->orderByDesc('views_count')->orderByDesc('reactions_count')->latest('published_at'),
                fn ($query) => $query->latest('published_at'),
            )
            ->paginate(config('blog.per_page', 9))
            ->withQueryString();

        $categories = Post::query()
            ->published()
            ->whereNotNull('category')
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->pluck('total', 'category');

        return view('blog.index', compact('posts', 'featured', 'categories', 'search', 'category', 'tag', 'filtering', 'sort'));
    }

    public function show(Request $request, string $slug)
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $visitor = Visitor::hash($request);
        $this->countView($request, $post, $visitor);

        $relatedPosts = Post::query()
            ->published()
            ->whereKeyNot($post->id)
            ->orderByRaw('case when category = ? then 0 else 1 end', [$post->category])
            ->latest('published_at')
            ->take(3)
            ->get();

        $previous = Post::query()
            ->published()
            ->where('published_at', '<', $post->published_at)
            ->latest('published_at')
            ->first(['id', 'title', 'slug']);

        $next = Post::query()
            ->published()
            ->where('published_at', '>', $post->published_at)
            ->oldest('published_at')
            ->first(['id', 'title', 'slug']);

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'previous' => $previous,
            'next' => $next,
            'reactionCounts' => $post->reactionCounts(),
            'myReactions' => ReactionController::mine($post, $visitor),
        ]);
    }

    /** One view per visitor per post per day; bots and the logged-in admin aren't counted. */
    private function countView(Request $request, Post $post, string $visitor): void
    {
        if (Visitor::isBot($request) || $request->user()) {
            return;
        }

        if (Cache::add("post-view:{$post->id}:{$visitor}", true, now()->addDay())) {
            // Plain query so a view doesn't touch updated_at.
            DB::table('posts')->where('id', $post->id)->increment('views_count');
            $post->views_count++;
        }
    }
}
