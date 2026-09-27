{{-- Newsletter sign-up. Params: $source (where it was shown), $compact (smaller variant). --}}
@php
  $compact = $compact ?? false;
  $newsletterErrors = $errors->getBag('newsletter');
@endphp

<section
  id="newsletter"
  class="relative overflow-hidden rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] backdrop-blur-sm {{ $compact ? 'mt-8 p-6 sm:p-8' : 'mt-16 p-8 sm:p-12' }}"
>
  <div aria-hidden="true" class="pointer-events-none absolute -left-20 -top-20 h-60 w-60 rounded-full bg-gradient-to-br from-cyan-400/25 to-violet-500/25 blur-3xl"></div>

  <div class="relative grid items-center gap-8 {{ $compact ? '' : 'lg:grid-cols-2' }}">
    <div>
      <p class="mb-2 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">
        <i class="fas fa-envelope-open-text"></i>{{ __('Newsletter') }}
      </p>
      <h2 class="{{ $compact ? 'text-xl' : 'text-2xl sm:text-3xl' }} font-black tracking-tight text-gray-900 dark:text-white">
        {{ __('Get new articles in your inbox') }}
      </h2>
      <p class="mt-2 text-gray-600 dark:text-gray-400">
        {{ __('One email when I publish something new on Laravel, Shopify or AI engineering. No spam, unsubscribe anytime.') }}
      </p>
    </div>

    <div>
      @if (session('newsletter_status'))
        <div role="status" class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
          <i class="fas fa-circle-check mt-1"></i>
          <span>{{ session('newsletter_status') }}</span>
        </div>
      @else
        <form method="POST" action="{{ route('newsletter.store') }}" class="space-y-3">
          @csrf
          <input type="hidden" name="locale" value="{{ app()->getLocale() }}" />
          <input type="hidden" name="source" value="{{ $source ?? 'website' }}" />
          {{-- Honeypot: hidden from people, often filled in by bots. --}}
          <div class="hidden" aria-hidden="true">
            <label for="newsletter-website-{{ $source ?? 'x' }}">Website</label>
            <input id="newsletter-website-{{ $source ?? 'x' }}" type="text" name="website" tabindex="-1" autocomplete="off" />
          </div>

          <div class="flex flex-col gap-2 sm:flex-row">
            <label for="newsletter-email-{{ $source ?? 'x' }}" class="sr-only">{{ __('Email') }}</label>
            <input
              id="newsletter-email-{{ $source ?? 'x' }}"
              type="email"
              name="email"
              required
              autocomplete="email"
              value="{{ old('email') }}"
              placeholder="{{ __('you@example.com') }}"
              class="w-full flex-1 rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950/70 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500"
            />
            <button type="submit" class="shrink-0 rounded-xl bg-primary-600 px-5 py-3 font-semibold text-white hover:bg-primary-700 transition">
              {{ __('Subscribe') }}
            </button>
          </div>
          @if ($newsletterErrors->has('email'))
            <p class="text-sm text-red-600">{{ $newsletterErrors->first('email') }}</p>
          @endif
          <p class="text-xs text-gray-500">
            {{ __('I will send you an email to confirm. Your address is only used for this newsletter and never shared.') }}
          </p>
        </form>
      @endif
    </div>
  </div>
</section>
