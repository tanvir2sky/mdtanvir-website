{{-- Shared page frame for tool pages. Sections: tool_title, tool_intro, tool_icon, tool_accent, tool_body, tool_about --}}
@extends('layouts.app')

@section('content')
  @include('partials.site-header')

  <main id="main-content" class="relative z-10 px-4 pb-24 pt-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-5xl">
      <nav aria-label="{{ __('Breadcrumb') }}" class="mb-8 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ lroute('tools.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ __('Tools') }}</a>
        <i class="fas fa-chevron-right text-[10px]"></i>
        <span class="text-gray-700 dark:text-gray-300">@yield('tool_title')</span>
      </nav>

      <header class="mb-10">
        <span class="mb-5 grid h-14 w-14 place-items-center rounded-2xl ring-1 @yield('tool_accent')">
          <i class="@yield('tool_icon') text-2xl"></i>
        </span>
        <h1 class="mb-4 text-4xl font-black tracking-tight text-gray-900 dark:text-white sm:text-5xl">@yield('tool_title')</h1>
        <p class="max-w-2xl text-lg text-gray-600 dark:text-gray-400">@yield('tool_intro')</p>
      </header>

      @yield('tool_body')

      @hasSection('tool_about')
        <section class="mt-14 rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/60 dark:bg-white/[0.02] p-6 sm:p-8">
          <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">{{ __('How it works') }}</h2>
          <div class="article-content text-base">
            @yield('tool_about')
          </div>
        </section>
      @endif

      <section class="mt-8 flex flex-col items-start gap-4 rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/60 dark:bg-white/[0.02] p-6 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-gray-600 dark:text-gray-400">{{ __('Need help with a Shopify app or a Laravel backend?') }}</p>
        <a href="{{ lroute('home') }}#contact" class="shrink-0 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-700 transition">{{ __('Work with me') }}</a>
      </section>
    </div>
  </main>
  @include('partials.site-footer')
@endsection
