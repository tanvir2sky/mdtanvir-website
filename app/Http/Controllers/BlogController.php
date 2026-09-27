<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $tag = $request->query('tag');
        $filtering = $search !== '' || filled($category) || filled($tag);

        // The featured hero only shows on the unfiltered first page.
        $featured = ! $filtering && $request->integer('page', 1) === 1
            ? Post::query()->published()->where('is_featured', true)->latest('published_at')->first()
            : null;

        $posts = Post::query()
            ->published()
            ->search($search)
            ->when($category, fn ($query) => $query->where('category', $category))
            ->when($tag, fn ($query) => $query->whereJsonContains('tags', $tag))
            ->when($featured, fn ($query) => $query->whereKeyNot($featured->id))
            ->latest('published_at')
            ->paginate(config('blog.per_page', 9))
            ->withQueryString();

        $categories = Post::query()
            ->published()
            ->whereNotNull('category')
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->pluck('total', 'category');

        return view('blog.index', compact('posts', 'featured', 'categories', 'search', 'category', 'tag', 'filtering'));
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

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

        return view('blog.show', compact('post', 'relatedPosts', 'previous', 'next'));
    }
}
