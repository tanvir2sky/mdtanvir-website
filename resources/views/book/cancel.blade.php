@extends('layouts.app')

@section('title', __('Cancel your call').' | MD Tanvir Hossain')
@section('robots', 'noindex, nofollow')

@section('content')
  @include('partials.site-header')

  <main id="main-content" class="relative z-10 flex min-h-[80vh] items-center justify-center px-4 pb-24 pt-28">
    <div class="w-full max-w-lg rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/80 dark:bg-white/[0.03] p-10 text-center backdrop-blur-sm shadow-xl">
      <span class="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-cyan-400 via-primary-500 to-violet-500 text-2xl text-white shadow-lg shadow-primary-500/30">
        <i class="fas fa-calendar-xmark"></i>
      </span>

      @if ($booking->isCancellable())
        <h1 class="mb-3 text-3xl font-black tracking-tight text-gray-900 dark:text-white">{{ __('Cancel your call?') }}</h1>
        <p class="mb-2 text-gray-600 dark:text-gray-400">{{ __('Your call with MD Tanvir Hossain on:') }}</p>
        <p class="mb-8 font-bold text-gray-900 dark:text-white">
          <span data-local-time="{{ $booking->starts_at->toIso8601ZuluString() }}">{{ $booking->startsIn($booking->visitor_timezone)->translatedFormat('l, j F Y · H:i') }}</span>
          <span class="block text-sm font-normal text-gray-500">{{ $booking->visitor_timezone }}</span>
        </p>
        <form method="POST" action="{{ route('book.cancel.confirm', $booking->cancel_token) }}" class="flex flex-col gap-3 sm:flex-row sm:justify-center">
          @csrf
          <button type="submit" class="rounded-xl bg-rose-600 px-5 py-3 font-semibold text-white hover:bg-rose-700 transition">{{ __('Yes, cancel the call') }}</button>
          <a href="{{ lroute('home') }}" class="rounded-xl border border-gray-300 dark:border-white/15 px-5 py-3 font-semibold">{{ __('Keep it') }}</a>
        </form>
      @else
        <h1 class="mb-3 text-3xl font-black tracking-tight text-gray-900 dark:text-white">{{ __('This call can no longer be cancelled') }}</h1>
        <p class="mb-8 text-gray-600 dark:text-gray-400">{{ __('It has already been cancelled, declined or taken place.') }}</p>
        <a href="{{ lroute('home') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-3 font-semibold text-white hover:bg-primary-700 transition">{{ __('Back to home') }}</a>
      @endif
    </div>
  </main>
  @include('partials.site-footer')
@endsection
