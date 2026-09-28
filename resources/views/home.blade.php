@extends('layouts.app')

@section('meta_url', lroute('home'))

@section('content')
@php
  $currentJob = $experiences->firstWhere('is_current', true);
@endphp
    @include('partials.site-header')

    <main id="main-content" class="relative z-10">
      <section
        id="home"
        class="min-h-screen flex items-center px-4 sm:px-6 lg:px-8 pt-20"
      >
        <div class="max-w-7xl mx-auto w-full">
          <div class="grid lg:grid-cols-12 gap-10 xl:gap-14 items-center">
            <div class="lg:col-span-7 animate-fade-in">
              <p
                class="inline-flex items-center gap-2 px-4 py-1.5 mb-5 rounded-full text-sm font-medium bg-primary-100/80 text-primary-800 dark:bg-primary-900/40 dark:text-primary-200 border border-primary-200 dark:border-primary-800 animate-pulse-slow"
              >
                <span class="w-2 h-2 rounded-full bg-primary-500"></span>
                {{ __('Open to impactful engineering work') }}
              </p>
              <h1
                class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-black tracking-tight mb-5 bg-gradient-to-r from-primary-700 via-primary-500 to-cyan-400 bg-clip-text text-transparent animate-slide-up"
              >
                MD Tanvir Hossain
              </h1>
              <h2
                class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-200 mb-5 animate-slide-up"
                style="animation-delay: 0.1s"
              >
                {{ $currentJob?->t('role') ?? __('Software Engineer') }}
                @if ($currentJob)
                  <span class="text-gray-400 dark:text-gray-500 font-medium">{{ __('at') }}</span>
                  <a
                    href="{{ $currentJob->url }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="bg-gradient-to-r from-cyan-500 to-primary-600 dark:from-cyan-300 dark:to-primary-400 bg-clip-text text-transparent hover:opacity-80 transition-opacity"
                    >{{ $currentJob->company }}</a
                  >
                @endif
              </h2>
              <p
                class="text-lg md:text-xl leading-relaxed text-gray-600 dark:text-gray-300 max-w-2xl mb-8 animate-slide-up"
                style="animation-delay: 0.2s"
              >
                {{ __('Crafting robust web solutions with Laravel, PHP, Shopify, and AI-powered features. Passionate about clean code, scalable architecture, and delivering exceptional digital experiences.') }}
              </p>

              <div
                class="flex flex-wrap gap-3 sm:gap-4 animate-slide-up"
                style="animation-delay: 0.3s"
              >
                <a
                  href="#contact"
                  class="px-6 sm:px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold shadow-soft hover:shadow-xl transition-all transform hover:-translate-y-0.5"
                >
                  {{ __('Get In Touch') }}
                </a>
                @if ($bookingAvailable)
                  <a
                    href="{{ lroute('book.index') }}"
                    class="px-6 sm:px-8 py-3 rounded-xl font-semibold border-2 border-primary-600 text-primary-700 dark:text-primary-300 hover:bg-primary-600 hover:text-white transition-all"
                  >
                    <i class="fas fa-calendar-check mr-2"></i>{{ __('Book a call') }}
                  </a>
                @endif
                <a
                  href="{{ config('portfolio.linkedin') }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="px-6 sm:px-8 py-3 rounded-xl font-semibold border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-900/80 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all"
                >
                  <i class="fab fa-linkedin mr-2 text-primary-600 dark:text-primary-400"></i>LinkedIn
                </a>
                <a
                  href="{{ config('portfolio.github') }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="px-6 sm:px-8 py-3 rounded-xl font-semibold border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-900/80 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all"
                >
                  <i class="fab fa-github mr-2 text-primary-600 dark:text-primary-400"></i>GitHub
                </a>
                @if ($cvUrl)
                  <a
                    href="{{ $cvUrl }}"
                    download
                    class="px-6 sm:px-8 py-3 rounded-xl font-semibold border border-gray-300 dark:border-gray-700 bg-white/80 dark:bg-gray-900/80 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all"
                  >
                    <i class="fas fa-file-arrow-down mr-2 text-primary-600 dark:text-primary-400"></i>{{ __('Download CV') }}
                  </a>
                @endif
              </div>

              <div class="mt-10 grid sm:grid-cols-3 gap-4 max-w-2xl">
                @foreach ([
                  ['8+', __('Years in web development')],
                  ['3', __('Core specialization areas')],
                  [__('Global'), __('Cross-functional collaboration')],
                ] as [$value, $label])
                  <div class="bg-white/80 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
                    <p class="text-2xl font-extrabold text-primary-700 dark:text-primary-300">{{ $value }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $label }}</p>
                  </div>
                @endforeach
              </div>
            </div>

            <div class="lg:col-span-5">
              <div
                class="relative max-w-md mx-auto animate-fade-in"
                style="animation-delay: 0.2s"
              >
                <div
                  class="absolute -inset-2 bg-gradient-to-br from-primary-500/50 to-cyan-400/40 blur-2xl rounded-[2rem]"
                ></div>
                <div
                  class="relative bg-white/85 dark:bg-gray-900/80 rounded-[2rem] p-7 border border-gray-200 dark:border-gray-800 shadow-2xl"
                >
                  <img
                    src="{{ asset('img/profile.jpg') }}"
                    alt="MD Tanvir Hossain"
                    class="w-36 h-36 sm:w-44 sm:h-44 mx-auto rounded-full object-cover border-4 border-primary-200 dark:border-primary-800 shadow-xl animate-float"
                  />
                  <div class="mt-6 text-center">
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                      Laravel | PHP | Shopify | {{ __('AI') }}
                    </p>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                      {{ __('Building secure, scalable, and high-performance products.') }}
                    </p>
                  </div>
                </div>
                @include('partials.hero-terminal')
              </div>
            </div>
          </div>
        </div>
      </section>

      @include('partials.about')

      @include('partials.skills')

      @include('partials.experience')

      @include('partials.projects')

      @if ($latestPosts->isNotEmpty())
        <section id="blog" class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8">
          <div class="max-w-7xl mx-auto">
            <div class="flex flex-wrap items-end justify-between gap-4 mb-10">
              <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
                  {{ __('From the Blog') }}
                </p>
                <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
                  {{ __('Latest writing') }}
                </h2>
              </div>
              <a href="{{ lroute('blog.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                {{ __('View all posts') }} <i class="fas fa-arrow-right ml-1"></i>
              </a>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
              @foreach ($latestPosts as $post)
                @include('blog.partials.card', ['post' => $post])
              @endforeach
            </div>
          </div>
        </section>
      @endif

      @include('partials.contact')
    </main>

    @include('partials.site-footer')

@endsection
