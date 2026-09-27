@extends('layouts.app')

@section('title', __('Guestbook').' | MD Tanvir Hossain')
@section('meta_description', __('Leave a note in my guestbook: say hi, share feedback or tell me what you are building.'))
@section('meta_url', lroute('guestbook.index'))

@php
  $turnstileSiteKey = config('services.turnstile.site_key');
  $guestbookErrors = $errors->getBag('guestbook');
  $input = 'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950/70 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500';
@endphp

@section('content')
  @include('partials.site-header')

  <main id="main-content" class="relative z-10 px-4 pb-24 pt-28 sm:px-6 lg:px-8">
    <div class="mx-auto grid max-w-6xl gap-12 lg:grid-cols-12">
      <div class="lg:col-span-5">
        <div class="lg:sticky lg:top-28">
          <p class="mb-3 text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300">{{ __('Guestbook') }}</p>
          <h1 class="mb-4 text-4xl font-black tracking-tight text-gray-900 dark:text-white md:text-5xl">{{ __('Sign the guestbook') }}</h1>
          <p class="mb-8 text-lg text-gray-600 dark:text-gray-400">{{ __('Say hi, share feedback on the site, or tell me what you are building. Every note is read before it appears.') }}</p>

          <section id="sign" class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm">
            @if (session('guestbook_status'))
              <div role="status" class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                <i class="fas fa-circle-check mt-1"></i><span>{{ session('guestbook_status') }}</span>
              </div>
            @else
              <form method="POST" action="{{ route('guestbook.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="locale" value="{{ app()->getLocale() }}" />
                <div class="hidden" aria-hidden="true"><label for="gb-company">Company</label><input id="gb-company" type="text" name="company" tabindex="-1" autocomplete="off" /></div>

                <div>
                  <label for="gb-name" class="mb-2 block text-sm font-medium">{{ __('Name') }}</label>
                  <input id="gb-name" name="name" type="text" maxlength="60" required value="{{ old('name') }}" class="{{ $input }}" />
                  @if ($guestbookErrors->has('name')) <p class="mt-1 text-sm text-red-600">{{ $guestbookErrors->first('name') }}</p> @endif
                </div>
                <div>
                  <label for="gb-message" class="mb-2 block text-sm font-medium">{{ __('Message') }}</label>
                  <textarea id="gb-message" name="message" rows="4" maxlength="500" required class="{{ $input }}" data-char-count="gb-count">{{ old('message') }}</textarea>
                  <p class="mt-1 flex justify-between text-xs text-gray-500">
                    <span>{{ __('Be kind. Links in messages are not clickable.') }}</span>
                    <span id="gb-count" aria-live="polite">0/500</span>
                  </p>
                  @if ($guestbookErrors->has('message')) <p class="mt-1 text-sm text-red-600">{{ $guestbookErrors->first('message') }}</p> @endif
                </div>
                <div>
                  <label for="gb-website" class="mb-2 block text-sm font-medium">{{ __('Website') }} <span class="font-normal text-gray-500">({{ __('optional') }})</span></label>
                  <input id="gb-website" name="website" type="url" maxlength="255" value="{{ old('website') }}" placeholder="https://" class="{{ $input }}" />
                  @if ($guestbookErrors->has('website')) <p class="mt-1 text-sm text-red-600">{{ $guestbookErrors->first('website') }}</p> @endif
                </div>
                @if (filled($turnstileSiteKey))
                  <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-language="{{ app()->getLocale() }}"></div>
                  @if ($guestbookErrors->has('turnstile')) <p class="text-sm text-red-600">{{ $guestbookErrors->first('turnstile') }}</p> @endif
                @endif
                <button type="submit" class="w-full rounded-xl bg-primary-600 px-5 py-3 font-semibold text-white hover:bg-primary-700 transition">{{ __('Sign the guestbook') }}</button>
              </form>
            @endif
          </section>
        </div>
      </div>

      <div class="lg:col-span-7">
        @if ($entries->isEmpty())
          <div class="rounded-3xl border border-dashed border-gray-300 dark:border-white/15 px-6 py-20 text-center text-gray-500">
            <i class="fas fa-feather-pointed mb-4 text-3xl"></i>
            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('No notes yet') }}</p>
            <p>{{ __('Be the first to sign!') }}</p>
          </div>
        @else
          <ol class="space-y-4">
            @foreach ($entries as $entry)
              <li class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm">
                <div class="mb-3 flex items-center gap-3">
                  <span aria-hidden="true" class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br {{ $entry->avatarGradient() }} text-sm font-bold text-white">{{ $entry->initials() }}</span>
                  <div class="min-w-0">
                    <p class="font-bold text-gray-900 dark:text-white">
                      @if ($entry->website)
                        <a href="{{ $entry->website }}" target="_blank" rel="nofollow ugc noopener noreferrer" class="hover:underline">{{ $entry->name }} <i class="fas fa-arrow-up-right-from-square text-[10px] opacity-60"></i></a>
                      @else
                        {{ $entry->name }}
                      @endif
                    </p>
                    <p class="text-xs text-gray-500"><time datetime="{{ $entry->approved_at->toIso8601String() }}">{{ $entry->created_at->translatedFormat('M d, Y') }}</time></p>
                  </div>
                </div>
                <p class="whitespace-pre-line break-words text-gray-700 dark:text-gray-300">{{ $entry->message }}</p>
              </li>
            @endforeach
          </ol>
          <div class="mt-8">{{ $entries->links() }}</div>
        @endif
      </div>
    </div>
  </main>
  @include('partials.site-footer')

  @if (filled($turnstileSiteKey))
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
  @endif
@endsection
