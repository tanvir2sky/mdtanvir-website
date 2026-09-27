@php $style = $post->categoryStyle(); @endphp

<article class="group relative flex flex-col overflow-hidden rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-300 dark:hover:border-white/20 hover:shadow-2xl hover:shadow-primary-900/10">
  <div class="aspect-[16/9] overflow-hidden">
    @include('blog.partials.cover', ['post' => $post])
  </div>

  <div class="flex flex-1 flex-col p-6">
    <div class="mb-4 flex items-center justify-between gap-3 text-xs">
      @if ($post->category)
        <a
          href="{{ route('blog.index', ['category' => $post->category]) }}"
          class="relative z-10 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 font-semibold ring-1 {{ $style['badge'] }}"
        >
          <i class="{{ $style['icon'] }}"></i>{{ $post->category }}
        </a>
      @endif
      <span class="font-mono text-gray-500">{{ $post->readingTime() }} min read</span>
    </div>

    <h3 class="mb-3 text-xl font-bold leading-snug tracking-tight text-gray-900 dark:text-white">
      <a href="{{ route('blog.show', $post->slug) }}" class="after:absolute after:inset-0">
        {{ $post->title }}
      </a>
    </h3>

    <p class="mb-6 line-clamp-3 text-gray-600 dark:text-gray-400">{{ $post->summary(150) }}</p>

    <div class="mt-auto flex items-center justify-between text-sm">
      <time datetime="{{ $post->published_at?->toDateString() }}" class="text-gray-500">
        {{ $post->published_at?->format('M d, Y') }}
      </time>
      <span class="inline-flex items-center gap-1.5 font-semibold text-primary-600 dark:text-primary-400">
        Read
        <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
      </span>
    </div>
  </div>
</article>
