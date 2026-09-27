@php
  $style = $post->categoryStyle();
  // Articles are written in English; mark them as such on other language versions.
  $foreign = app()->getLocale() !== 'en';
@endphp

<article class="group relative flex flex-col overflow-hidden rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-300 dark:hover:border-white/20 hover:shadow-2xl hover:shadow-primary-900/10">
  <div class="aspect-[16/9] overflow-hidden">
    @include('blog.partials.cover', ['post' => $post])
  </div>

  <div class="flex flex-1 flex-col p-6">
    <div class="mb-4 flex items-center justify-between gap-3 text-xs">
      @if ($post->category)
        <a
          href="{{ lroute('blog.index', ['category' => $post->category]) }}"
          class="relative z-10 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold ring-1 {{ $style['badge'] }}"
        >
          <i class="{{ $style['icon'] }}"></i>{{ $post->category }}
        </a>
      @endif
      <span class="flex items-center gap-2 font-mono text-gray-500">
        @if ($foreign)
          <span class="rounded border border-gray-300 dark:border-white/15 px-1 text-[10px] font-semibold" title="{{ __('Article in English') }}">EN</span>
        @endif
        {{ __(':minutes min read', ['minutes' => $post->readingTime()]) }}
      </span>
    </div>

    <h3 @if ($foreign) lang="en" @endif class="mb-3 text-xl font-bold leading-snug tracking-tight text-gray-900 dark:text-white">
      <a href="{{ lroute('blog.show', $post->slug) }}" class="after:absolute after:inset-0">
        {{ $post->title }}
      </a>
    </h3>

    <p @if ($foreign) lang="en" @endif class="mb-6 line-clamp-3 text-gray-600 dark:text-gray-400">{{ $post->summary(150) }}</p>

    <div class="mt-auto flex items-center justify-between text-sm">
      <time datetime="{{ $post->published_at?->toDateString() }}" class="text-gray-500">
        {{ $post->published_at?->translatedFormat('M d, Y') }}
      </time>
      <span class="inline-flex items-center gap-1.5 font-semibold text-primary-600 dark:text-primary-400">
        {{ __('Read') }}
        <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
      </span>
    </div>
  </div>
</article>
