@extends('layouts.app')

@section('meta_url', lroute('home'))

@section('content')
@php
  $turnstileSiteKey = config('services.turnstile.site_key');
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

      <section
        id="about"
        class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50/80 dark:bg-gray-900/70"
      >
        <div class="max-w-6xl mx-auto">
          <div class="grid lg:grid-cols-12 gap-10">
            <div class="lg:col-span-4">
              <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
                {{ __('About Me') }}
              </p>
              <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
                {{ __('Engineer focused on quality and scale') }}
              </h2>
            </div>
            <div class="lg:col-span-8 space-y-5 text-lg text-gray-700 dark:text-gray-300 leading-relaxed [&_strong]:text-primary-600 dark:[&_strong]:text-primary-400">
              <p>{{ __("I'm a passionate Software Engineer with expertise in building scalable web applications using modern technologies. My journey in software development has been driven by a commitment to writing clean, maintainable code and solving complex problems with elegant solutions.") }}</p>
              <p>{!! __("Specializing in <strong>Laravel</strong> and <strong>PHP</strong>, I've developed robust backend systems and RESTful APIs that power high-performance applications. My experience with <strong>Shopify</strong> has enabled me to create seamless e-commerce solutions and custom storefronts for businesses of all sizes.") !!}</p>
              <p>{!! __("More recently, I've been bringing <strong>AI</strong> into production: integrating large language models into Laravel applications to power real product features, and working with AI coding assistants and agentic workflows every day to ship faster without compromising on quality.") !!}</p>
              <p>{{ __("Beyond coding, I'm dedicated to continuous learning, staying updated with industry best practices, and contributing to the developer community. I believe in building software that not only meets requirements but exceeds expectations in terms of performance, security, and user experience.") }}</p>
            </div>
          </div>
        </div>
      </section>

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

      <section
        id="contact"
        class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50/80 dark:bg-gray-900/70"
      >
        <div class="max-w-6xl mx-auto">
          <div
            class="rounded-3xl bg-gradient-to-br from-white/90 to-primary-50/70 dark:from-gray-900/90 dark:to-gray-900 border border-gray-200 dark:border-gray-800 p-8 sm:p-10 lg:p-12 shadow-xl"
          >
            <div class="max-w-3xl mb-10">
              <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
                {{ __('Get In Touch') }}
              </p>
              <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-4">
                {{ __("Let's build something meaningful") }}
              </h2>
              <p class="text-lg text-gray-700 dark:text-gray-300 leading-relaxed">
                {{ __("I'm always open to discussing new projects, creative ideas, or opportunities to be part of your vision. Feel free to reach out!") }}
              </p>
            </div>

            @if (session('contact_status'))
              <div
                role="status"
                class="mb-6 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200"
              >
                <i class="fas fa-circle-check mt-1"></i>
                <span>{{ session('contact_status') }}</span>
              </div>
            @endif

            @if ($errors->any())
              <div
                role="alert"
                class="mb-6 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200"
              >
                <i class="fas fa-circle-exclamation mt-1"></i>
                <span>{{ __("Your message wasn't sent. Please fix the highlighted fields and try again.") }}</span>
              </div>
            @endif

            <div class="grid lg:grid-cols-2 gap-6">
              <div class="grid sm:grid-cols-2 gap-5 content-start">
                <a
                  href="mailto:{{ config('portfolio.email') }}"
                  class="contact-card bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 text-center"
                >
                  <div class="text-4xl mb-4 text-primary-600 dark:text-primary-400">
                    <i class="fas fa-envelope"></i>
                  </div>
                  <h3 class="font-bold mb-2 text-gray-900 dark:text-white">{{ __('Email') }}</h3>
                  <p class="text-gray-700 dark:text-gray-300 text-sm">{{ config('portfolio.email') }}</p>
                </a>

                <a
                  href="{{ config('portfolio.linkedin') }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="contact-card bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 text-center"
                >
                  <div class="text-4xl mb-4 text-primary-600 dark:text-primary-400">
                    <i class="fab fa-linkedin"></i>
                  </div>
                  <h3 class="font-bold mb-2 text-gray-900 dark:text-white">LinkedIn</h3>
                  <p class="text-gray-700 dark:text-gray-300 text-sm">{{ __('Connect with me') }}</p>
                </a>

                <a
                  href="{{ config('portfolio.github') }}"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="contact-card bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800 text-center sm:col-span-2"
                >
                  <div class="text-4xl mb-4 text-primary-600 dark:text-primary-400">
                    <i class="fab fa-github"></i>
                  </div>
                  <h3 class="font-bold mb-2 text-gray-900 dark:text-white">GitHub</h3>
                  <p class="text-gray-700 dark:text-gray-300 text-sm">{{ __('View my code') }}</p>
                </a>

                @if ($bookingAvailable)
                  <a
                    href="{{ lroute('book.index') }}"
                    class="contact-card sm:col-span-2 flex items-center gap-5 rounded-2xl p-6 text-left text-white shadow-lg transition-all hover:-translate-y-1 hover:shadow-2xl bg-gradient-to-br from-cyan-500 via-primary-600 to-violet-600"
                  >
                    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-white/15 text-2xl"><i class="fas fa-calendar-check"></i></span>
                    <span>
                      <span class="block text-lg font-bold">{{ __('Book a free intro call') }}</span>
                      <span class="block text-sm text-white/85">{{ __('Pick a time that suits you, :minutes minutes, no strings attached.', ['minutes' => config('booking.slot_minutes', 30)]) }}</span>
                    </span>
                    <i class="fas fa-arrow-right ml-auto"></i>
                  </a>
                @endif
              </div>

              <div class="space-y-5">
                @include('partials.project-brief')

                <form id="contact-form" method="POST" action="{{ route('contact.store') }}" class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800 space-y-4">
                  @csrf
                  <input type="hidden" name="locale" value="{{ app()->getLocale() }}" />
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Send a Message') }}</h3>
                  @foreach ([
                    ['name', __('Name'), 'text'],
                    ['email', __('Email'), 'email'],
                    ['subject', __('Subject'), 'text'],
                  ] as [$field, $label, $inputType])
                    <div>
                      <label class="block text-sm font-medium mb-2" for="{{ $field }}">{{ $label }}</label>
                      <input id="{{ $field }}" name="{{ $field }}" type="{{ $inputType }}" value="{{ old($field, $field === 'subject' ? request()->query('subject') : null) }}" required class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500" />
                      @error($field)
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                      @enderror
                    </div>
                  @endforeach
                  <div>
                    <label class="block text-sm font-medium mb-2" for="message">{{ __('Message') }}</label>
                    <textarea id="message" name="message" rows="5" required class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ old('message') }}</textarea>
                    @error('message')
                      <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                  @if (filled($turnstileSiteKey))
                    <div>
                      <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-language="{{ app()->getLocale() }}"></div>
                      @error('cf-turnstile-response')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                      @enderror
                      @error('turnstile')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                      @enderror
                    </div>
                  @endif
                  <button type="submit" class="w-full px-6 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 disabled:opacity-60 disabled:cursor-wait text-white font-semibold transition">
                    {{ __('Submit Message') }}
                  </button>
                </form>
              </div>
            </div>

            <div class="mt-10 pt-6 border-t border-gray-200 dark:border-gray-800 text-center">
              <p class="text-gray-600 dark:text-gray-400">
                © {{ now()->year }} MD Tanvir Hossain. {{ __('All rights reserved.') }}
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <button
      id="scroll-top"
      aria-label="{{ __('Scroll to top') }}"
      class="fixed bottom-8 right-8 p-4 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all transform hover:scale-110 opacity-0 pointer-events-none z-50"
    >
      <i class="fas fa-arrow-up"></i>
    </button>
    @if (filled($turnstileSiteKey))
      <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
@endsection
