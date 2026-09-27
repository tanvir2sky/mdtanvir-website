@extends('layouts.app')

@section('title', ($category ? __(':category articles', ['category' => $category]) : ($tag ? '#'.$tag : __('Blog'))).' | MD Tanvir Hossain')
@section('meta_description', __('Practical writing on Laravel, PHP, Shopify, AI/LLM integration, architecture and performance by MD Tanvir Hossain.'))
@section('meta_url', lroute('blog.index'))

@section('content')
  @include('partials.site-header')

  <main id="main-content" class="relative z-10 px-4 pb-24 pt-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
      {{-- Header --}}
      <header class="relative mb-12 overflow-hidden rounded-[2rem] border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] px-6 py-12 backdrop-blur-sm sm:px-12 sm:py-16">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-60 dark:opacity-40 [background-image:radial-gradient(circle_at_1px_1px,rgba(14,165,233,0.25)_1px,transparent_0)] [background-size:22px_22px] [mask-image:linear-gradient(to_left,black,transparent_75%)]"></div>
        <div aria-hidden="true" class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-gradient-to-br from-cyan-400/30 to-violet-500/30 blur-3xl"></div>

        <div class="relative grid items-end gap-10 lg:grid-cols-12">
          <div class="lg:col-span-7">
            <p class="mb-3 text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300">{{ __('The Blog') }}</p>
            <h1 class="mb-4 text-4xl font-black tracking-tight text-gray-900 dark:text-white md:text-6xl [&_span]:bg-gradient-to-r [&_span]:from-cyan-500 [&_span]:to-primary-600 dark:[&_span]:from-cyan-300 dark:[&_span]:to-primary-400 [&_span]:bg-clip-text [&_span]:text-transparent">
              {!! __('Notes from the <span>engine room</span>') !!}
            </h1>
            <p class="max-w-xl text-lg text-gray-600 dark:text-gray-400">
              {{ __('Practical writing on Laravel, Shopify, AI in production, architecture and performance: the things I learn shipping real products.') }}
            </p>
          </div>

          <div class="lg:col-span-5">
            <form method="GET" action="{{ lroute('blog.index') }}" role="search" class="relative">
              @if ($category)
                <input type="hidden" name="category" value="{{ $category }}" />
              @endif
              <label for="blog-search" class="sr-only">{{ __('Search articles') }}</label>
              <i class="fas fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input
                id="blog-search"
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="{{ __('Search articles…') }}"
                class="w-full rounded-2xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950/70 py-4 pl-11 pr-28 text-gray-900 dark:text-white placeholder-gray-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
              />
              <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition">
                {{ __('Search') }}
              </button>
            </form>
          </div>
        </div>
      </header>

      {{-- Category filter --}}
      @if ($categories->isNotEmpty())
        <nav aria-label="{{ __('Categories') }}" class="mb-10 -mx-4 overflow-x-auto px-4">
          <ul class="flex w-max gap-2">
            <li>
              <a
                href="{{ lroute('blog.index', array_filter(['q' => $search])) }}"
                @class([
                  'inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition',
                  'border-gray-900 bg-gray-900 text-white dark:border-white dark:bg-white dark:text-gray-900' => ! $category,
                  'border-gray-200 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-white/30' => $category,
                ])
              >
                {{ __('All') }} <span class="font-mono text-xs opacity-60">{{ $categories->sum() }}</span>
              </a>
            </li>
            @foreach ($categories as $name => $total)
              @php $style = config("blog.categories.{$name}") ?? config('blog.default'); @endphp
              <li>
                <a
                  href="{{ lroute('blog.index', array_filter(['category' => $name, 'q' => $search])) }}"
                  @class([
                    'inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition',
                    'border-gray-900 bg-gray-900 text-white dark:border-white dark:bg-white dark:text-gray-900' => $category === $name,
                    'border-gray-200 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] text-gray-700 dark:text-gray-300 hover:border-gray-400 dark:hover:border-white/30' => $category !== $name,
                  ])
                >
                  <i class="{{ $style['icon'] }} text-xs"></i>{{ $name }}
                  <span class="font-mono text-xs opacity-60">{{ $total }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </nav>
      @endif

      {{-- Sort --}}
      <div class="mb-8 flex items-center justify-end gap-1 text-sm" role="group" aria-label="{{ __('Sort articles') }}">
        @foreach (['latest' => [__('Latest'), 'fa-clock'], 'popular' => [__('Popular'), 'fa-fire']] as $value => [$label, $icon])
          <a
            href="{{ lroute('blog.index', array_filter(['q' => $search, 'category' => $category, 'tag' => $tag, 'sort' => $value === 'latest' ? null : $value])) }}"
            @if ($sort === $value) aria-current="true" @endif
            class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 font-semibold transition {{ $sort === $value ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}"
          ><i class="fas {{ $icon }} text-xs"></i>{{ $label }}</a>
        @endforeach
      </div>

      {{-- Active filters --}}
      @if ($filtering)
        <div class="mb-8 flex flex-wrap items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
          <span>
            {{ trans_choice(':count article|:count articles', $posts->total()) }}
            @if ($search) {{ __('matching') }} <strong class="text-gray-900 dark:text-white">“{{ $search }}”</strong> @endif
            @if ($category) {{ __('in') }} <strong class="text-gray-900 dark:text-white">{{ $category }}</strong> @endif
            @if ($tag) {{ __('tagged') }} <strong class="text-gray-900 dark:text-white">#{{ $tag }}</strong> @endif
          </span>
          <a href="{{ lroute('blog.index') }}" class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 dark:border-white/10 px-3 py-1 font-semibold hover:border-gray-400 dark:hover:border-white/30">
            <i class="fas fa-xmark text-xs"></i>{{ __('Clear filters') }}
          </a>
        </div>
      @endif

      {{-- Featured post --}}
      @if ($featured)
        @php $style = $featured->categoryStyle(); @endphp
        <article class="group relative mb-12 grid overflow-hidden rounded-[2rem] border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] backdrop-blur-sm transition-all duration-300 hover:shadow-2xl hover:shadow-primary-900/10 lg:grid-cols-2">
          <div class="aspect-[16/10] overflow-hidden lg:aspect-auto lg:min-h-[420px]">
            @include('blog.partials.cover', ['post' => $featured, 'size' => 'lg'])
          </div>
          <div class="flex flex-col justify-center p-8 sm:p-12">
            <div class="mb-5 flex flex-wrap items-center gap-3 text-xs">
              <span class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-cyan-500 to-primary-600 px-3 py-1 font-semibold uppercase tracking-wider text-white">
                <i class="fas fa-star"></i>{{ __('Featured') }}
              </span>
              @if ($featured->category)
                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold ring-1 {{ $style['badge'] }}">
                  <i class="{{ $style['icon'] }}"></i>{{ $featured->category }}
                </span>
              @endif
            </div>
            <h2 class="mb-4 text-3xl font-black leading-tight tracking-tight text-gray-900 dark:text-white sm:text-4xl">
              <a href="{{ lroute('blog.show', $featured->slug) }}" class="after:absolute after:inset-0" @if (app()->getLocale() !== 'en') lang="en" @endif>{{ $featured->title }}</a>
            </h2>
            <p class="mb-8 text-lg text-gray-600 dark:text-gray-400" @if (app()->getLocale() !== 'en') lang="en" @endif>{{ $featured->summary(220) }}</p>
            <div class="flex items-center gap-4 text-sm text-gray-500">
              <img src="{{ asset('img/profile.jpg') }}" alt="" class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-900" />
              <div>
                <p class="font-semibold text-gray-900 dark:text-white">MD Tanvir Hossain</p>
                <p>{{ $featured->published_at?->translatedFormat('M d, Y') }} · {{ __(':minutes min read', ['minutes' => $featured->readingTime()]) }}</p>
              </div>
              <span class="ml-auto hidden h-11 w-11 place-items-center rounded-full bg-gray-900 text-white transition-transform group-hover:translate-x-1 dark:bg-white dark:text-gray-900 sm:grid">
                <i class="fas fa-arrow-right"></i>
              </span>
            </div>
          </div>
        </article>
      @endif

      {{-- Grid --}}
      @if ($posts->isNotEmpty())
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
          @foreach ($posts as $post)
            @include('blog.partials.card', ['post' => $post])
          @endforeach
        </div>

        @if ($posts->hasPages())
          <nav aria-label="{{ __('Pagination') }}" class="mt-12 flex items-center justify-between gap-4">
            @if ($posts->onFirstPage())
              <span class="rounded-full border border-gray-200 dark:border-white/10 px-5 py-2.5 text-sm font-semibold text-gray-400">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Newer') }}
              </span>
            @else
              <a href="{{ $posts->previousPageUrl() }}" class="rounded-full border border-gray-200 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] px-5 py-2.5 text-sm font-semibold hover:border-gray-400 dark:hover:border-white/30">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Newer') }}
              </a>
            @endif
            <span class="font-mono text-sm text-gray-500">{{ __('Page :current of :last', ['current' => $posts->currentPage(), 'last' => $posts->lastPage()]) }}</span>
            @if ($posts->hasMorePages())
              <a href="{{ $posts->nextPageUrl() }}" class="rounded-full border border-gray-200 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] px-5 py-2.5 text-sm font-semibold hover:border-gray-400 dark:hover:border-white/30">
                {{ __('Older') }}<i class="fas fa-arrow-right ml-2"></i>
              </a>
            @else
              <span class="rounded-full border border-gray-200 dark:border-white/10 px-5 py-2.5 text-sm font-semibold text-gray-400">
                {{ __('Older') }}<i class="fas fa-arrow-right ml-2"></i>
              </span>
            @endif
          </nav>
        @endif
      @elseif (! $featured)
        <div class="rounded-3xl border border-dashed border-gray-300 dark:border-white/10 px-6 py-20 text-center">
          <i class="fas fa-magnifying-glass mb-4 text-3xl text-gray-400"></i>
          <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('No articles found') }}</p>
          <p class="mt-1 text-gray-500">
            @if ($filtering)
              {{ __('Try a different search or') }} <a href="{{ lroute('blog.index') }}" class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">{{ __('browse everything') }}</a>.
            @else
              {{ __('New writing is on the way. Check back soon.') }}
            @endif
          </p>
        </div>
      @endif

      @include('partials.newsletter-form', ['source' => 'blog-index'])
    </div>
  </main>
  @include('partials.site-footer')
@endsection
