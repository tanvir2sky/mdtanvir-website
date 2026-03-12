@extends('layouts.app')

@section('title', ($post->meta_title ?: $post->title) . ' | MD Tanvir Hossain')
@section('meta_description', $post->meta_description ?: ($post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 155)))

@section('content')
  @include('partials.site-header')

  <main class="relative z-10 py-24 px-4 sm:px-6 lg:px-8">
    <article class="max-w-4xl mx-auto mt-8 bg-white/85 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800 rounded-3xl p-8 sm:p-10 shadow-xl">
      <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400">
        <i class="fas fa-arrow-left mr-2"></i>Back to blog
      </a>

      <p class="mt-5 text-sm text-gray-500">{{ $post->published_at?->format('F d, Y') }}</p>
      <h1 class="text-3xl sm:text-4xl font-black mt-2 mb-4 text-gray-900 dark:text-white">{{ $post->title }}</h1>

      @if ($post->excerpt)
        <p class="text-lg text-gray-600 dark:text-gray-300 mb-8">{{ $post->excerpt }}</p>
      @endif

      @if ($post->featured_image)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full max-h-[460px] object-cover rounded-xl border border-gray-200 dark:border-gray-700 mb-8" />
      @endif

      <div class="prose prose-lg max-w-none dark:prose-invert">
        {!! $post->content !!}
      </div>
    </article>

    @if ($relatedPosts->isNotEmpty())
      <section class="max-w-4xl mx-auto mt-10">
        <h2 class="text-2xl font-bold mb-4">Related Posts</h2>
        <div class="grid md:grid-cols-3 gap-4">
          @foreach ($relatedPosts as $related)
            <a href="{{ route('blog.show', $related->slug) }}" class="block rounded-xl border border-gray-200 dark:border-gray-800 bg-white/85 dark:bg-gray-900/80 p-4 hover:border-primary-400 transition">
              <p class="text-sm font-semibold">{{ $related->title }}</p>
              <p class="text-xs text-gray-500 mt-2">{{ $related->published_at?->format('M d, Y') }}</p>
            </a>
          @endforeach
        </div>
      </section>
    @endif
  </main>
  @include('partials.site-footer')
@endsection
