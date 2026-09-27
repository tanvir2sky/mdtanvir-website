<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendPostToSubscribers;
use App\Models\Post;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::query()->withCount('reactions')->latest()->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = Slug::unique(Post::class, $data['title']);
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;
        $data['featured_image'] = $this->storeFeaturedImage($request);

        $post = Post::create($data);
        $this->queueNewsletter($post);

        return redirect()->route('admin.posts.index')->with('status', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return view('admin.posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $data = $this->validatedData($request);
        $data['slug'] = Slug::unique(Post::class, $data['title'], $post->id);
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? ($post->published_at ?? now())) : null;

        $newImage = $this->storeFeaturedImage($request);
        if ($newImage) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $newImage;
        }

        $post->update($data);
        $this->queueNewsletter($post->fresh());

        return redirect()->route('admin.posts.index')->with('status', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:60'],
            'tags' => ['nullable', 'string', 'max:500'],
            'excerpt' => ['nullable', 'string', 'max:320'],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'notify_subscribers' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        $tags = collect(explode(',', (string) ($data['tags'] ?? '')))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->unique(fn ($tag) => mb_strtolower($tag))
            ->values()
            ->all();

        return [
            'category' => filled($data['category'] ?? null) ? trim($data['category']) : null,
            'tags' => $tags ?: null,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'notify_subscribers' => $request->boolean('notify_subscribers'),
        ] + $data;
    }

    private function storeFeaturedImage(Request $request): ?string
    {
        if (! $request->hasFile('featured_image')) {
            return null;
        }

        return $request->file('featured_image')->store('posts', 'public');
    }

    /** Email subscribers about the post once it is published and due; later posts go out via the scheduler. */
    private function queueNewsletter(Post $post): void
    {
        if (SendPostToSubscribers::shouldSend($post)) {
            SendPostToSubscribers::dispatch($post);
        }
    }
}
