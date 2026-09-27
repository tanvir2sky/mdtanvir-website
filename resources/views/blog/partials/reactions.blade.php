{{-- Anonymous reactions. Params: $post, $reactionCounts, $myReactions --}}
@php
  $labels = ['like' => __('Helpful'), 'fire' => __('Loved it'), 'idea' => __('Learned something')];
@endphp

<section class="mt-12 rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6" data-reactions="{{ route('blog.react', $post->slug) }}">
  <p class="mb-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Was this article useful?') }}</p>
  <div class="flex flex-wrap gap-3">
    @foreach (\App\Models\PostReaction::TYPES as $type => $emoji)
      @php $active = in_array($type, $myReactions, true); @endphp
      <button
        type="button"
        data-reaction="{{ $type }}"
        aria-pressed="{{ $active ? 'true' : 'false' }}"
        aria-label="{{ $labels[$type] }}"
        title="{{ $labels[$type] }}"
        class="group inline-flex items-center gap-2 rounded-2xl border px-4 py-2.5 text-sm font-semibold transition hover:-translate-y-0.5 aria-pressed:border-primary-500 aria-pressed:bg-primary-50 aria-pressed:text-primary-700 dark:aria-pressed:bg-primary-500/15 dark:aria-pressed:text-primary-200 border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300"
      >
        <span class="text-lg transition-transform group-active:scale-125" aria-hidden="true">{{ $emoji }}</span>
        <span>{{ $labels[$type] }}</span>
        <span data-reaction-count class="font-mono text-xs opacity-70">{{ $reactionCounts[$type] }}</span>
      </button>
    @endforeach
  </div>
</section>
