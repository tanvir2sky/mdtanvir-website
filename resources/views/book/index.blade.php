@extends('layouts.app')

@section('title', __('Book a call').' | MD Tanvir Hossain')
@section('meta_description', __('Book a free :minutes-minute intro call to talk about your Laravel, Shopify or AI project.', ['minutes' => $slotMinutes]))
@section('meta_url', lroute('book.index'))

@php
  $input = 'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950/70 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500';
@endphp

@section('content')
  @include('partials.site-header')

  <main id="main-content" class="relative z-10 px-4 pb-24 pt-28 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-6xl">
      <header class="mb-10 max-w-2xl">
        <p class="mb-3 text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300">{{ __('Book a call') }}</p>
        <h1 class="mb-4 text-4xl font-black tracking-tight text-gray-900 dark:text-white md:text-5xl">{{ __("Let's talk about your project") }}</h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">{{ __('Pick a time for a free :minutes-minute intro call. I will confirm by email with a calendar invite and a meeting link.', ['minutes' => $slotMinutes]) }}</p>
      </header>

      @if ($errors->any())
        <div role="alert" class="mb-6 flex items-start gap-3 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-5 py-4 text-rose-800 dark:text-rose-200">
          <i class="fas fa-circle-exclamation mt-1"></i>
          <span>{{ $errors->first('start') ?: __('Please fix the highlighted fields and try again.') }}</span>
        </div>
      @endif

      <noscript>
        <p class="mb-6 rounded-2xl border border-amber-500/40 bg-amber-500/10 px-5 py-4">{{ __('The booking calendar needs JavaScript.') }} <a href="{{ lroute('home') }}#contact" class="font-semibold underline">{{ __('Send me a message instead.') }}</a></p>
      </noscript>

      <form id="booking" method="POST" action="{{ route('book.store') }}" data-slots-url="{{ lroute('book.slots') }}" class="grid gap-6 lg:grid-cols-5">
        @csrf
        <input type="hidden" name="locale" value="{{ app()->getLocale() }}" />
        <input type="hidden" name="start" value="{{ old('start') }}" data-booking-start />
        <input type="hidden" name="timezone" value="{{ old('timezone') }}" data-booking-timezone-input />
        <div class="hidden" aria-hidden="true"><label for="bk-company">Company</label><input id="bk-company" type="text" name="company" tabindex="-1" autocomplete="off" /></div>

        {{-- Step 1: date & time --}}
        <section class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm lg:col-span-3">
          <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <h2 class="flex items-center gap-2 font-bold text-gray-900 dark:text-white">
              <span class="grid h-7 w-7 place-items-center rounded-full bg-primary-600 text-xs text-white">1</span>{{ __('Choose a time') }}
            </h2>
            <label class="flex items-center gap-2 text-sm text-gray-500">
              <i class="fas fa-globe"></i>
              <span class="sr-only">{{ __('Your time zone') }}</span>
              <select data-booking-timezone class="max-w-[14rem] rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950 px-2 py-1.5 text-sm"></select>
            </label>
          </div>

          <div data-booking-loading class="py-16 text-center text-gray-500"><i class="fas fa-spinner fa-spin mr-2"></i>{{ __('Loading available times…') }}</div>
          <div data-booking-empty class="hidden py-16 text-center text-gray-500">
            <i class="far fa-calendar-xmark mb-3 text-3xl"></i>
            <p>{{ __('No open times right now.') }} <a href="{{ lroute('home') }}#contact" class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">{{ __('Send me a message instead.') }}</a></p>
          </div>

          <div data-booking-picker class="hidden">
            <div role="tablist" aria-label="{{ __('Days') }}" data-booking-days class="-mx-1 mb-5 flex gap-2 overflow-x-auto px-1 pb-2"></div>
            <div data-booking-times class="grid grid-cols-3 gap-2 sm:grid-cols-4"></div>
          </div>
        </section>

        {{-- Step 2: details --}}
        <section class="space-y-4 rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm lg:col-span-2">
          <h2 class="flex items-center gap-2 font-bold text-gray-900 dark:text-white">
            <span class="grid h-7 w-7 place-items-center rounded-full bg-primary-600 text-xs text-white">2</span>{{ __('Your details') }}
          </h2>

          <p data-booking-summary class="rounded-xl bg-primary-50 dark:bg-primary-500/10 px-4 py-3 text-sm font-semibold text-primary-800 dark:text-primary-200">{{ __('Pick a time first.') }}</p>

          <div>
            <label for="bk-name" class="mb-2 block text-sm font-medium">{{ __('Name') }}</label>
            <input id="bk-name" name="name" type="text" required maxlength="120" value="{{ old('name') }}" class="{{ $input }}" />
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="bk-email" class="mb-2 block text-sm font-medium">{{ __('Email') }}</label>
            <input id="bk-email" name="email" type="email" required maxlength="190" value="{{ old('email') }}" class="{{ $input }}" />
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="bk-topic" class="mb-2 block text-sm font-medium">{{ __('What would you like to talk about?') }}</label>
            <textarea id="bk-topic" name="topic" rows="4" required maxlength="1000" class="{{ $input }}">{{ old('topic') }}</textarea>
            @error('topic') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
          <button type="submit" data-booking-submit disabled class="w-full rounded-xl bg-primary-600 px-5 py-3 font-semibold text-white hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-50 transition">
            {{ __('Request this time') }}
          </button>
          <p class="text-xs text-gray-500">{{ __('Your request is confirmed by email. You can cancel anytime with the link in the email.') }}</p>
        </section>
      </form>
    </div>
  </main>
  @include('partials.site-footer')
@endsection
