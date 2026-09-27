@extends('layouts.app')

@php
  $accent = $project->accentStyle();
  $rendered = $project->bodyWithToc();
  $title = $project->t('title');
  $summary = $project->t('summary');
  $projectUrl = lroute('projects.show', $project->slug);

  $facts = array_filter([
    __('Role') => $project->t('role'),
    __('Year') => $project->year,
    __('Duration') => $project->duration,
  ]);

  $story = array_filter([
    ['fas fa-mountain', __('The challenge'), $project->t('challenge')],
    ['fas fa-compass-drafting', __('My approach'), $project->t('approach')],
    ['fas fa-flag-checkered', __('The outcome'), $project->t('outcome')],
  ], fn ($block) => filled($block[2]));
@endphp

@section('title', $title.' · '.__('Case study').' | MD Tanvir Hossain')
@section('meta_description', \Illuminate\Support\Str::limit($summary, 155))
@section('meta_url', $projectUrl)
@section('meta_image', $project->coverUrl() ? url($project->coverUrl()) : '')

@push('structured_data')
  <script type="application/ld+json">
  {!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'CreativeWork',
    'name' => $title,
    'description' => $summary,
    'url' => $projectUrl,
    'inLanguage' => app()->getLocale(),
    'keywords' => $project->tags ? implode(', ', $project->tags) : null,
    'image' => $project->coverUrl() ? url($project->coverUrl()) : null,
    'creator' => ['@type' => 'Person', 'name' => 'MD Tanvir Hossain', 'url' => rtrim(config('app.url'), '/')],
  ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) !!}
  </script>
@endpush

@section('content')
  @include('partials.site-header')

  <main id="main-content" class="relative z-10 px-4 pb-24 pt-28 sm:px-6 lg:px-8">
    {{-- Hero --}}
    <header class="mx-auto mb-12 max-w-6xl">
      <nav aria-label="{{ __('Breadcrumb') }}" class="mb-8 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ lroute('home') }}#projects" class="hover:text-primary-600 dark:hover:text-primary-400">{{ __('Projects') }}</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-700 dark:text-gray-300">{{ $title }}</span>
      </nav>

      <div class="grid gap-10 lg:grid-cols-12 lg:items-end">
        <div class="lg:col-span-8">
          <div class="mb-6 flex flex-wrap items-center gap-3">
            <span class="grid h-12 w-12 place-items-center rounded-2xl ring-1 {{ $accent['tile'] }}">
              <i class="{{ $project->icon }} text-xl"></i>
            </span>
            <span class="text-xs font-semibold uppercase tracking-[0.18em] {{ $accent['label'] }}">
              {{ __('Case study') }}@if ($project->t('category')) · {{ $project->t('category') }}@endif
            </span>
          </div>
          <h1 class="mb-5 text-4xl font-black leading-[1.1] tracking-tight text-gray-900 dark:text-white sm:text-5xl lg:text-6xl">
            {{ $title }}
          </h1>
          <p class="max-w-2xl text-lg leading-relaxed text-gray-600 dark:text-gray-400 sm:text-xl">{{ $summary }}</p>
        </div>

        <div class="lg:col-span-4">
          <dl class="grid grid-cols-2 gap-3 lg:grid-cols-1">
            @foreach ($facts as $label => $value)
              <div class="rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-4">
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $label }}</dt>
                <dd class="mt-1 font-bold text-gray-900 dark:text-white">{{ $value }}</dd>
              </div>
            @endforeach
          </dl>
          @if ($project->live_url || $project->repo_url)
            <div class="mt-3 flex flex-wrap gap-2">
              @if ($project->live_url)
                <a href="{{ $project->live_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition">
                  <i class="fas fa-arrow-up-right-from-square text-xs"></i>{{ __('Visit live site') }}
                </a>
              @endif
              @if ($project->repo_url)
                <a href="{{ $project->repo_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 dark:border-white/15 px-4 py-2.5 text-sm font-semibold hover:border-gray-400 dark:hover:border-white/30 transition">
                  <i class="fab fa-github"></i>{{ __('View code') }}
                </a>
              @endif
            </div>
          @endif
        </div>
      </div>

      @if (! empty($project->tags))
        <ul class="mt-8 flex flex-wrap gap-2" aria-label="{{ __('Tech stack') }}">
          @foreach ($project->tags as $tag)
            <li class="rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-2.5 py-1 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $tag }}</li>
          @endforeach
        </ul>
      @endif
    </header>

    {{-- Cover --}}
    <div class="group mx-auto mb-14 aspect-[21/9] max-w-6xl overflow-hidden rounded-[2rem] border border-gray-200/80 dark:border-white/10 shadow-2xl shadow-primary-900/10">
      @if ($project->coverUrl())
        <img src="{{ $project->coverUrl() }}" alt="{{ $title }}" class="h-full w-full object-cover" />
      @else
        <div aria-hidden="true" class="relative h-full w-full overflow-hidden bg-gradient-to-br {{ $accent['cover'] }}">
          <div class="absolute inset-0 opacity-30 [background-image:radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.6)_1px,transparent_0)] [background-size:18px_18px]"></div>
          <div class="absolute -right-10 -bottom-10 h-64 w-64 rounded-full bg-white/20 blur-3xl"></div>
          <i class="{{ $project->icon }} absolute right-8 bottom-4 text-[10rem] text-white/25 transition-transform duration-700 group-hover:scale-110 group-hover:-rotate-6"></i>
          <span class="absolute left-8 top-8 font-mono text-xs uppercase tracking-[0.3em] text-white/80">{{ __('Case study') }}</span>
        </div>
      @endif
    </div>

    {{-- Challenge / approach / outcome --}}
    @if ($story)
      <section class="mx-auto mb-16 grid max-w-6xl gap-5 {{ [1 => 'md:grid-cols-1', 2 => 'md:grid-cols-2', 3 => 'md:grid-cols-3'][count($story)] }}">
        @foreach ($story as [$icon, $heading, $text])
          <article data-spotlight style="--spotlight: {{ $accent['glow'] }}" class="spotlight-card relative overflow-hidden rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-7 backdrop-blur-sm">
            <div class="relative">
              <span class="mb-5 grid h-11 w-11 place-items-center rounded-xl ring-1 {{ $accent['tile'] }}"><i class="{{ $icon }}"></i></span>
              <h2 class="mb-3 text-lg font-bold text-gray-900 dark:text-white">{{ $heading }}</h2>
              <p class="leading-relaxed text-gray-600 dark:text-gray-400">{{ $text }}</p>
            </div>
          </article>
        @endforeach
      </section>
    @endif

    {{-- Full write-up --}}
    @if (filled(strip_tags($rendered['html'])))
      <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-12">
        @if (count($rendered['toc']) > 1)
          <aside class="hidden lg:order-last lg:col-span-4 lg:block">
            <nav aria-label="{{ __('Table of contents') }}" class="sticky top-28 rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm">
              <p class="mb-4 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('On this page') }}</p>
              <ol class="space-y-1 border-l border-gray-200 dark:border-white/10 text-sm">
                @foreach ($rendered['toc'] as $item)
                  <li>
                    <a href="#{{ $item['id'] }}" data-toc-link="{{ $item['id'] }}" class="-ml-px block border-l-2 border-transparent py-1.5 text-gray-600 dark:text-gray-400 transition-colors hover:text-gray-900 dark:hover:text-white {{ $item['level'] === 3 ? 'pl-7' : 'pl-4' }}">{{ $item['text'] }}</a>
                  </li>
                @endforeach
              </ol>
            </nav>
          </aside>
        @endif
        <article class="article-content min-w-0 {{ count($rendered['toc']) > 1 ? 'lg:col-span-8' : 'lg:col-span-8 lg:col-start-3' }}">
          {!! $rendered['html'] !!}
        </article>
      </div>
    @endif

    {{-- Call to action --}}
    <section class="relative mx-auto mt-20 max-w-6xl overflow-hidden rounded-3xl p-px bg-gradient-to-br from-cyan-400 via-primary-500 to-violet-500">
      <div class="flex flex-col items-start gap-6 rounded-[calc(1.5rem-1px)] bg-white dark:bg-gray-950 p-8 sm:flex-row sm:items-center sm:justify-between sm:p-10">
        <div>
          <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white">{{ __('Have a similar project in mind?') }}</h2>
          <p class="mt-1 text-gray-600 dark:text-gray-400">{{ __("Tell me about it and I'll get back to you with ideas and next steps.") }}</p>
        </div>
        <a href="{{ lroute('home') }}#contact" class="shrink-0 rounded-xl bg-primary-600 px-6 py-3 font-semibold text-white hover:bg-primary-700 transition">
          {{ __('Work with me') }}
        </a>
      </div>
    </section>

    {{-- Previous / next --}}
    @if ($previous || $next)
      <nav aria-label="{{ __('More case studies') }}" class="mx-auto mt-8 grid max-w-6xl gap-4 sm:grid-cols-2">
        @if ($previous)
          <a href="{{ lroute('projects.show', $previous->slug) }}" class="group rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-5 transition hover:border-gray-300 dark:hover:border-white/20">
            <span class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500"><i class="fas fa-arrow-left mr-2 transition-transform group-hover:-translate-x-1"></i>{{ __('Previous') }}</span>
            <span class="mt-2 block font-bold text-gray-900 dark:text-white">{{ $previous->t('title') }}</span>
          </a>
        @else
          <span></span>
        @endif
        @if ($next)
          <a href="{{ lroute('projects.show', $next->slug) }}" class="group rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-5 text-right transition hover:border-gray-300 dark:hover:border-white/20">
            <span class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('Next') }}<i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i></span>
            <span class="mt-2 block font-bold text-gray-900 dark:text-white">{{ $next->t('title') }}</span>
          </a>
        @endif
      </nav>
    @endif
  </main>
  @include('partials.site-footer')
@endsection
