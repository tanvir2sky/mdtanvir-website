@extends('admin.layouts.app')

@section('title', 'Blog Posts | Admin')

@section('content')
  <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
      <h1 class="text-3xl font-black">Blog Posts</h1>
      <p class="text-gray-600 dark:text-gray-400">Create, edit, and publish SEO-friendly blog content.</p>
    </div>
    <a href="{{ route('admin.posts.create') }}" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition">
      <i class="fas fa-plus mr-2"></i>New Post
    </a>
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 dark:bg-gray-800/50 text-left">
        <tr>
          <th class="px-5 py-3 font-semibold">Image</th>
          <th class="px-5 py-3 font-semibold">Title</th>
          <th class="px-5 py-3 font-semibold">Slug</th>
          <th class="px-5 py-3 font-semibold">Status</th>
          <th class="px-5 py-3 font-semibold">Published At</th>
          <th class="px-5 py-3 font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($posts as $post)
          <tr class="border-t border-gray-200 dark:border-gray-800">
            <td class="px-5 py-4">
              @if ($post->featured_image)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="h-14 w-20 object-cover rounded-lg border border-gray-200 dark:border-gray-700" />
              @else
                <span class="text-xs text-gray-500">No image</span>
              @endif
            </td>
            <td class="px-5 py-4 font-semibold">{{ $post->title }}</td>
            <td class="px-5 py-4 text-gray-600 dark:text-gray-400">{{ $post->slug }}</td>
            <td class="px-5 py-4">
              <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $post->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                {{ $post->is_published ? 'Published' : 'Draft' }}
              </span>
            </td>
            <td class="px-5 py-4">{{ $post->published_at?->format('M d, Y H:i') ?? '-' }}</td>
            <td class="px-5 py-4">
              <div class="flex items-center gap-2">
                <a href="{{ route('admin.posts.show', $post) }}" class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700">View</a>
                <a href="{{ route('admin.posts.edit', $post) }}" class="px-3 py-1.5 rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">Edit</a>
                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Delete this post?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-5 py-10 text-center text-gray-500">No posts found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $posts->links() }}
  </div>
@endsection
