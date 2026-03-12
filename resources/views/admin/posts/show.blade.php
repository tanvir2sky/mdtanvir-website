@extends('admin.layouts.app')

@section('title', 'View Post | Admin')

@section('content')
  <div class="mb-6 flex items-center justify-between gap-3">
    <a href="{{ route('admin.posts.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400">
      <i class="fas fa-arrow-left mr-2"></i>Back to posts
    </a>
    <a href="{{ route('admin.posts.edit', $post) }}" class="px-4 py-2 rounded-lg bg-primary-600 text-white font-semibold">Edit</a>
  </div>

  <article class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 sm:p-8">
    <h1 class="text-3xl font-black mb-2">{{ $post->title }}</h1>
    <p class="text-sm text-gray-500 mb-6">
      Slug: {{ $post->slug }} |
      {{ $post->is_published ? 'Published' : 'Draft' }}
      @if ($post->published_at)
        | {{ $post->published_at->format('M d, Y H:i') }}
      @endif
    </p>

    @if ($post->excerpt)
      <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">{{ $post->excerpt }}</p>
    @endif

    @if ($post->featured_image)
      <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full max-h-[420px] object-cover rounded-xl border border-gray-200 dark:border-gray-700 mb-8" />
    @endif

    <div class="prose max-w-none dark:prose-invert">
      {!! $post->content !!}
    </div>
  </article>
@endsection
