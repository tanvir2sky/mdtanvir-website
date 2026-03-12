@extends('layouts.app')

@section('title', 'Blog | MD Tanvir Hossain')
@section('meta_description', 'Insights on Laravel, PHP, Shopify, architecture, and modern web engineering by MD Tanvir Hossain.')

@section('content')
  @include('partials.site-header')

  <main class="relative z-10 py-24 px-4 sm:px-6 lg:px-8">
    <section class="max-w-6xl mx-auto pt-8">
      <div class="text-center mb-12">
        <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
          Blog
        </p>
        <h1 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">Latest Articles</h1>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Practical notes on backend engineering, e-commerce, and clean software architecture.</p>
      </div>

      <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse ($posts as $post)
          <article class="bg-white/85 dark:bg-gray-900/80 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden shadow-lg">
            @if ($post->featured_image)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-48 object-cover" />
            @endif
            <div class="p-6">
            <p class="text-xs text-gray-500 mb-2">{{ $post->published_at?->format('M d, Y') }}</p>
            <h2 class="text-xl font-bold mb-3">
              <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-primary-600 dark:hover:text-primary-400 transition">
                {{ $post->title }}
              </a>
            </h2>
            <p class="text-gray-700 dark:text-gray-300 mb-5">{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}</p>
            <a href="{{ route('blog.show', $post->slug) }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400">
              Read article <i class="fas fa-arrow-right ml-1"></i>
            </a>
            </div>
          </article>
        @empty
          <div class="col-span-full text-center py-16 text-gray-500">
            No published posts yet.
          </div>
        @endforelse
      </div>

      <div class="mt-8">
        {{ $posts->links() }}
      </div>
    </section>
  </main>
  @include('partials.site-footer')
@endsection
