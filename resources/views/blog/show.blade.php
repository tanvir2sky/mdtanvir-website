@extends('layouts.app')

@php
  $rendered = $post->contentWithToc();
  $style = $post->categoryStyle();
  // Articles are English-only: the canonical URL is always the English one.
  $postUrl = lroute('blog.show', $post->slug, 'en');
  $localUrl = lroute('blog.show', $post->slug);
  $foreign = app()->getLocale() !== 'en';
  $description = $post->meta_description ?: $post->summary(155);
@endphp

@section('title', ($post->meta_title ?: $post->title) . ' | MD Tanvir Hossain')
@section('meta_description', $description)
@section('meta_keywords', collect([$post->category])->merge($post->tags ?? [])->filter()->push('MD Tanvir Hossain')->implode(', '))
@section('meta_type', 'article')
@section('meta_url', $postUrl)
@section('meta_image', $post->featured_image ? url($post->imageUrl()) : '')
@section('article_published_time', $post->published_at?->toIso8601String() ?? '')

@push('structured_data')
  @php
    $siteUrl = rtrim(config('app.url'), '/');
    $articleSchema = [
      '@context' => 'https://schema.org',
      '@type' => 'BlogPosting',
      'headline' => $post->meta_title ?: $post->title,
      'description' => $description,
      'url' => $postUrl,
      'datePublished' => $post->published_at?->toIso8601String(),
      'dateModified' => $post->updated_at?->toIso8601String(),
      'wordCount' => str_word_count(strip_tags((string) $post->content)),
      'articleSection' => $post->category,
      'keywords' => $post->tags ? implode(', ', $post->tags) : null,
      'author' => ['@type' => 'Person', 'name' => 'MD Tanvir Hossain', 'url' => $siteUrl],
      'publisher' => ['@type' => 'Person', 'name' => 'MD Tanvir Hossain', 'url' => $siteUrl],
      'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $postUrl],
    ];

    if ($post->featured_image) {
      $articleSchema['image'] = [url($post->imageUrl())];
    }

    $articleSchema = array_filter($articleSchema, fn ($value) => $value !== null);
  @endphp
  <script type="application/ld+json">
  {!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) !!}
  </script>
@endpush

@section('content')
  @include('partials.site-header')

  {{-- Reading progress --}}
  <div aria-hidden="true" class="fixed inset-x-0 top-16 z-40 h-0.5 bg-transparent">
    <div data-reading-progress class="h-full origin-left scale-x-0 bg-gradient-to-r from-cyan-400 via-primary-500 to-violet-500"></div>
  </div>

  <main id="main-content" class="relative z-10 px-4 pb-24 pt-28 sm:px-6 lg:px-8">
    {{-- Header --}}
    <header class="mx-auto mb-10 max-w-4xl text-center">
      <nav aria-label="{{ __('Breadcrumb') }}" class="mb-8 flex items-center justify-center gap-2 text-sm text-gray-500">
        <a href="{{ lroute('blog.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ __('Blog') }}</a>
        @if ($post->category)
          <i class="fas fa-chevron-right text-[10px]"></i>
          <a href="{{ lroute('blog.index', ['category' => $post->category]) }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ $post->category }}</a>
        @endif
      </nav>

      @if ($post->category)
        <a
          href="{{ lroute('blog.index', ['category' => $post->category]) }}"
          class="mb-6 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $style['badge'] }}"
        >
          <i class="{{ $style['icon'] }}"></i>{{ $post->category }}
        </a>
      @endif

      @if ($foreign)
        <p class="mb-4 flex items-center justify-center gap-2 text-sm text-gray-500">
          <span class="rounded border border-gray-300 dark:border-white/15 px-1.5 font-mono text-[11px] font-semibold">EN</span>
          {{ __('This article is written in English.') }}
        </p>
      @endif

      <h1 @if ($foreign) lang="en" @endif class="mb-6 text-4xl font-black leading-[1.1] tracking-tight text-gray-900 dark:text-white sm:text-5xl lg:text-6xl">
        {{ $post->title }}
      </h1>

      @if ($post->excerpt)
        <p @if ($foreign) lang="en" @endif class="mx-auto mb-8 max-w-2xl text-lg leading-relaxed text-gray-600 dark:text-gray-400 sm:text-xl">{{ $post->excerpt }}</p>
      @endif

      <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3 text-sm text-gray-500">
        <span class="flex items-center gap-3">
          <img src="{{ asset('img/profile.jpg') }}" alt="" class="h-9 w-9 rounded-full object-cover" />
          <span class="font-semibold text-gray-900 dark:text-white">MD Tanvir Hossain</span>
        </span>
        <span class="flex items-center gap-2"><i class="far fa-calendar"></i>
          <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('F d, Y') }}</time>
        </span>
        <span class="flex items-center gap-2"><i class="far fa-clock"></i>{{ __(':minutes min read', ['minutes' => $post->readingTime()]) }}</span>
      </div>
    </header>

    {{-- Cover --}}
    <div class="group mx-auto mb-14 aspect-[21/9] max-w-6xl overflow-hidden rounded-[2rem] border border-gray-200/80 dark:border-white/10 shadow-2xl shadow-primary-900/10">
      @include('blog.partials.cover', ['post' => $post, 'size' => 'lg'])
    </div>

    <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-12">
      {{-- Sidebar --}}
      <aside class="order-last lg:col-span-4">
        <div class="space-y-6 lg:sticky lg:top-28">
          @if (count($rendered['toc']) > 1)
            <nav aria-label="{{ __('Table of contents') }}" class="hidden lg:block rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm">
              <p class="mb-4 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('On this page') }}</p>
              <ol class="space-y-1 border-l border-gray-200 dark:border-white/10 text-sm" data-toc>
                @foreach ($rendered['toc'] as $item)
                  <li>
                    <a
                      href="#{{ $item['id'] }}"
                      data-toc-link="{{ $item['id'] }}"
                      class="-ml-px block border-l-2 border-transparent py-1.5 text-gray-600 dark:text-gray-400 transition-colors hover:text-gray-900 dark:hover:text-white {{ $item['level'] === 3 ? 'pl-7' : 'pl-4' }}"
                    >{{ $item['text'] }}</a>
                  </li>
                @endforeach
              </ol>
            </nav>
          @endif

          <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm">
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('Share') }}</p>
            <div class="flex gap-2">
              <a
                href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($postUrl) }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="{{ __('Share on LinkedIn') }}"
                class="grid h-11 w-11 place-items-center rounded-xl border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition"
              ><i class="fab fa-linkedin-in"></i></a>
              <a
                href="https://twitter.com/intent/tweet?url={{ urlencode($postUrl) }}&text={{ urlencode($post->title) }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="{{ __('Share on X') }}"
                class="grid h-11 w-11 place-items-center rounded-xl border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition"
              ><i class="fab fa-x-twitter"></i></a>
              <button
                type="button"
                data-copy-link="{{ $localUrl }}"
                class="flex h-11 flex-1 items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition"
              >
                <i class="fas fa-link"></i><span data-copy-label>{{ __('Copy link') }}</span>
              </button>
            </div>
          </div>
        </div>
      </aside>

      {{-- Article --}}
      <div class="min-w-0 lg:col-span-8">
        <article class="article-content" @if ($foreign) lang="en" @endif>
          {!! $rendered['html'] !!}
        </article>

        @if (! empty($post->tags))
          <ul class="mt-12 flex flex-wrap gap-2" aria-label="{{ __('Tags') }}">
            @foreach ($post->tags as $postTag)
              <li>
                <a href="{{ lroute('blog.index', ['tag' => $postTag]) }}" class="inline-block rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-3 py-1.5 font-mono text-xs text-gray-700 dark:text-gray-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition">
                  #{{ $postTag }}
                </a>
              </li>
            @endforeach
          </ul>
        @endif

        {{-- Author --}}
        <section class="relative mt-12 overflow-hidden rounded-3xl p-px bg-gradient-to-br from-cyan-400 via-primary-500 to-violet-500">
          <div class="flex flex-col gap-6 rounded-[calc(1.5rem-1px)] bg-white dark:bg-gray-950 p-6 sm:flex-row sm:items-center sm:p-8">
            <img src="{{ asset('img/profile.jpg') }}" alt="MD Tanvir Hossain" class="h-20 w-20 shrink-0 rounded-2xl object-cover" />
            <div class="flex-1">
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('Written by') }}</p>
              <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">MD Tanvir Hossain</p>
              <p class="mt-1 text-gray-600 dark:text-gray-400">
                {{ __('Software Engineer at Altruan GmbH, building with Laravel, Shopify and AI.') }}
              </p>
            </div>
            <a href="{{ lroute('home') }}#contact" class="shrink-0 rounded-xl bg-primary-600 px-5 py-3 text-center text-sm font-semibold text-white hover:bg-primary-700 transition">
              {{ __('Work with me') }}
            </a>
          </div>
        </section>

        @include('partials.newsletter-form', ['source' => 'blog-post', 'compact' => true])

        {{-- Previous / next --}}
        @if ($previous || $next)
          <nav aria-label="{{ __('More articles') }}" class="mt-8 grid gap-4 sm:grid-cols-2">
            @if ($previous)
              <a href="{{ lroute('blog.show', $previous->slug) }}" class="group rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-5 transition hover:border-gray-300 dark:hover:border-white/20">
                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500"><i class="fas fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i>{{ __('Previous') }}</span>
                <span class="mt-2 block font-bold text-gray-900 dark:text-white">{{ $previous->title }}</span>
              </a>
            @else
              <span></span>
            @endif
            @if ($next)
              <a href="{{ lroute('blog.show', $next->slug) }}" class="group rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-5 text-right transition hover:border-gray-300 dark:hover:border-white/20">
                <span class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('Next') }}<i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i></span>
                <span class="mt-2 block font-bold text-gray-900 dark:text-white">{{ $next->title }}</span>
              </a>
            @endif
          </nav>
        @endif
      </div>
    </div>

    {{-- Related --}}
    @if ($relatedPosts->isNotEmpty())
      <section class="mx-auto mt-24 max-w-6xl">
        <div class="mb-8 flex items-end justify-between gap-4">
          <h2 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">{{ __('Keep reading') }}</h2>
          <a href="{{ lroute('blog.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline">
            {{ __('All articles') }} <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </div>
        <div class="grid gap-6 md:grid-cols-3">
          @foreach ($relatedPosts as $related)
            @include('blog.partials.card', ['post' => $related])
          @endforeach
        </div>
      </section>
    @endif
  </main>
  @include('partials.site-footer')
@endsection
