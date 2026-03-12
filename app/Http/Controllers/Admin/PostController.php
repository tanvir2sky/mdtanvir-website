<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::latest()->paginate(15);

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
        $data['slug'] = $this->generateSlug($data['title']);
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;
        $data['featured_image'] = $this->storeFeaturedImage($request);

        Post::create($data);

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
        $data['slug'] = $this->generateSlug($data['title'], $post->id);
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? ($post->published_at ?? now())) : null;

        $newImage = $this->storeFeaturedImage($request);
        if ($newImage) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $newImage;
        }

        $post->update($data);

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
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:320'],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]) + ['is_published' => $request->boolean('is_published')];
    }

    private function storeFeaturedImage(Request $request): ?string
    {
        if (! $request->hasFile('featured_image')) {
            return null;
        }

        return $request->file('featured_image')->store('posts', 'public');
    }

    private function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $baseSlug = $base !== '' ? $base : 'post';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Post::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
