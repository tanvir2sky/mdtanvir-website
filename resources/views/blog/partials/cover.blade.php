{{-- Featured image, or a generated cover from the category colours. --}}
@php
  $style = $post->categoryStyle();
  $size = $size ?? 'md';
@endphp

@if ($post->imageUrl())
  <img
    src="{{ $post->imageUrl() }}"
    alt="{{ $post->title }}"
    loading="lazy"
    class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"
  />
@else
  <div aria-hidden="true" class="relative h-full w-full overflow-hidden bg-gradient-to-br {{ $style['cover'] }}">
    <div class="absolute inset-0 opacity-30 [background-image:radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.6)_1px,transparent_0)] [background-size:18px_18px]"></div>
    <div class="absolute -right-10 -bottom-10 h-48 w-48 rounded-full bg-white/20 blur-3xl"></div>
    <i class="{{ $style['icon'] }} absolute right-6 bottom-4 text-white/25 transition-transform duration-700 group-hover:scale-110 group-hover:-rotate-6 {{ $size === 'lg' ? 'text-[9rem]' : 'text-7xl' }}"></i>
    @if ($size === 'lg')
      <span class="absolute left-6 top-6 font-mono text-xs uppercase tracking-[0.3em] text-white/80">{{ $post->category ?: 'Article' }}</span>
    @endif
  </div>
@endif
