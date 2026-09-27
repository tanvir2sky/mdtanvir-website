@extends('layouts.app')

@section('title', __('Free developer tools').' | MD Tanvir Hossain')
@section('meta_description', __('Free tools for Shopify and Laravel developers: a store health check, a webhook HMAC verifier and a cron expression explainer.'))
@section('meta_url', lroute('tools.index'))

@section('content')
  @include('partials.site-header')

  <main id="main-content" class="relative z-10 px-4 pb-24 pt-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
      <header class="mb-12 max-w-2xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300">{{ __('Tools') }}</p>
        <h1 class="mb-4 text-4xl font-black tracking-tight text-gray-900 dark:text-white md:text-6xl">{{ __('Free developer tools') }}</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">{{ __('Small, focused tools I built for everyday Shopify and Laravel work. Free to use, no sign-up.') }}</p>
      </header>

      <div class="grid gap-5 md:grid-cols-3">
        @foreach ($tools as $tool)
          @php $accent = \App\Models\Project::ACCENTS[$tool['accent']]; @endphp
          <article data-spotlight style="--spotlight: {{ $accent['glow'] }}" class="spotlight-card group relative flex flex-col overflow-hidden rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-7 backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-300 dark:hover:border-white/20 hover:shadow-2xl hover:shadow-primary-900/10">
            <div class="relative flex h-full flex-col">
              <span class="mb-6 grid h-12 w-12 place-items-center rounded-2xl ring-1 {{ $accent['tile'] }}"><i class="{{ $tool['icon'] }} text-xl"></i></span>
              <h2 class="mb-3 text-xl font-bold tracking-tight text-gray-900 dark:text-white">
                <a href="{{ lroute($tool['route']) }}" class="after:absolute after:inset-0">{{ $tool['title'] }}</a>
              </h2>
              <p class="mb-6 text-gray-600 dark:text-gray-400">{{ $tool['description'] }}</p>
              <span class="mt-auto inline-flex items-center gap-1.5 text-sm font-semibold {{ $accent['label'] }}">
                {{ __('Open tool') }} <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
              </span>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </main>
  @include('partials.site-footer')
@endsection
