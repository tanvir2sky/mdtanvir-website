@php
  $turnstileSiteKey = config('services.turnstile.site_key');
  $input = 'peer w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950/60 px-4 py-3 text-gray-900 dark:text-white placeholder-gray-400 transition focus:border-primary-500 focus:outline-none focus:ring-4 focus:ring-primary-500/15 aria-[invalid=true]:border-rose-500';
  $channels = [
    ['fab fa-linkedin-in', 'LinkedIn', __('Connect with me'), config('portfolio.linkedin'), \App\Models\Project::ACCENTS['sky']['tile']],
    ['fab fa-github', 'GitHub', __('View my code'), config('portfolio.github'), \App\Models\Project::ACCENTS['violet']['tile']],
  ];
@endphp

<section id="contact" class="relative py-20 sm:py-24 px-4 sm:px-6 lg:px-8">
  <div class="relative mx-auto max-w-7xl">
    {{-- Soft glow behind the panel --}}
    <div aria-hidden="true" class="pointer-events-none absolute -inset-x-10 -inset-y-6 rounded-[3rem] bg-gradient-to-br from-cyan-400/15 via-primary-500/10 to-violet-500/15 blur-3xl"></div>

    <div class="relative rounded-[2rem] p-px bg-gradient-to-br from-cyan-400/70 via-primary-500/40 to-violet-500/70 shadow-2xl shadow-primary-900/10">
      <div class="relative overflow-hidden rounded-[calc(2rem-1px)] bg-white/95 dark:bg-gray-950/95 backdrop-blur-xl">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-60 dark:opacity-40 [background-image:radial-gradient(circle_at_1px_1px,rgba(14,165,233,0.22)_1px,transparent_0)] [background-size:22px_22px] [mask-image:linear-gradient(to_bottom_right,black,transparent_55%)]"></div>

        <div class="relative grid gap-10 p-6 sm:p-10 lg:grid-cols-12 lg:gap-14 lg:p-14">
          {{-- Left: pitch and direct channels --}}
          <div class="lg:col-span-5">
            <p class="mb-3 text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300">
              {{ __('Get In Touch') }}
            </p>
            <h2 class="mb-5 text-4xl font-black leading-[1.05] tracking-tight text-gray-900 dark:text-white md:text-5xl [&_span]:bg-gradient-to-r [&_span]:from-cyan-500 [&_span]:to-violet-500 dark:[&_span]:from-cyan-300 dark:[&_span]:to-violet-400 [&_span]:bg-clip-text [&_span]:text-transparent">
              {!! __("Let's build something <span>meaningful</span>") !!}
            </h2>
            <p class="mb-8 text-lg leading-relaxed text-gray-600 dark:text-gray-400">
              {{ __("I'm always open to discussing new projects, creative ideas, or opportunities to be part of your vision. Feel free to reach out!") }}
            </p>

            <p class="mb-8 inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3.5 py-1.5 text-sm font-semibold text-emerald-700 dark:text-emerald-300">
              <span class="relative flex h-2 w-2">
                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
              </span>
              {{ __('Open to impactful engineering work') }}
            </p>

            <ul class="space-y-3" aria-label="{{ __('Other ways to reach me') }}">
              {{-- Email with copy button --}}
              <li class="contact-card group flex items-center gap-4 rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-4 transition hover:border-gray-300 dark:hover:border-white/20">
                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl ring-1 {{ \App\Models\Project::ACCENTS['cyan']['tile'] }}"><i class="fas fa-envelope text-lg"></i></span>
                <a href="mailto:{{ config('portfolio.email') }}" class="min-w-0 flex-1">
                  <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">{{ __('Email') }}</span>
                  <span class="block truncate font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ config('portfolio.email') }}</span>
                </a>
                <button
                  type="button"
                  data-copy-link="{{ config('portfolio.email') }}"
                  class="shrink-0 rounded-lg border border-gray-200 dark:border-white/10 px-3 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition"
                >
                  <i class="far fa-copy mr-1"></i><span data-copy-label>{{ __('Copy') }}</span>
                </button>
              </li>

              @foreach ($channels as [$icon, $name, $description, $url, $tile])
                <li>
                  <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="contact-card group flex items-center gap-4 rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-4 transition hover:-translate-y-0.5 hover:border-gray-300 dark:hover:border-white/20">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl ring-1 {{ $tile }}"><i class="{{ $icon }} text-lg"></i></span>
                    <span class="min-w-0 flex-1">
                      <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">{{ $name }}</span>
                      <span class="block font-semibold text-gray-900 dark:text-white">{{ $description }}</span>
                    </span>
                    <i class="fas fa-arrow-up-right-from-square text-xs text-gray-400 transition group-hover:text-primary-500"></i>
                  </a>
                </li>
              @endforeach

              @if ($bookingAvailable)
                <li>
                  <a href="{{ lroute('book.index') }}" class="contact-card group flex items-center gap-4 rounded-2xl bg-gradient-to-br from-cyan-500 via-primary-600 to-violet-600 p-4 text-white shadow-lg shadow-primary-600/20 transition hover:-translate-y-0.5 hover:shadow-xl">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-white/15"><i class="fas fa-calendar-check text-lg"></i></span>
                    <span class="min-w-0 flex-1">
                      <span class="block font-bold">{{ __('Book a free intro call') }}</span>
                      <span class="block text-sm text-white/85">{{ __('Pick a time that suits you, :minutes minutes, no strings attached.', ['minutes' => config('booking.slot_minutes', 30)]) }}</span>
                    </span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                  </a>
                </li>
              @endif
            </ul>
          </div>

          {{-- Right: brief builder and form --}}
          <div class="space-y-5 lg:col-span-7">
            @if (session('contact_status'))
              <div role="status" class="flex items-start gap-3 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-emerald-800 dark:text-emerald-200">
                <i class="fas fa-circle-check mt-1"></i>
                <span>{{ session('contact_status') }}</span>
              </div>
            @endif

            @if ($errors->any())
              <div role="alert" class="flex items-start gap-3 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-5 py-4 text-rose-800 dark:text-rose-200">
                <i class="fas fa-circle-exclamation mt-1"></i>
                <span>{{ __("Your message wasn't sent. Please fix the highlighted fields and try again.") }}</span>
              </div>
            @endif

            @include('partials.project-brief')

            <form id="contact-form" method="POST" action="{{ route('contact.store') }}" class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/80 dark:bg-white/[0.03] p-6 sm:p-8 shadow-sm">
              @csrf
              <input type="hidden" name="locale" value="{{ app()->getLocale() }}" />

              <div class="mb-6 flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-cyan-400 via-primary-500 to-violet-500 text-white"><i class="fas fa-paper-plane"></i></span>
                <div>
                  <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Send a Message') }}</h3>
                  <p class="text-sm text-gray-500">{{ __('It goes straight to my inbox.') }}</p>
                </div>
              </div>

              <div class="grid gap-5 sm:grid-cols-2">
                @foreach ([
                  ['name', __('Name'), 'text', 'name', __('Jane Doe')],
                  ['email', __('Email'), 'email', 'email', __('you@example.com')],
                ] as [$field, $label, $inputType, $autocomplete, $placeholder])
                  <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="{{ $field }}">{{ $label }}</label>
                    <input id="{{ $field }}" name="{{ $field }}" type="{{ $inputType }}" autocomplete="{{ $autocomplete }}" placeholder="{{ $placeholder }}" value="{{ old($field) }}" required @error($field) aria-invalid="true" @enderror class="{{ $input }}" />
                    @error($field)
                      <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                  </div>
                @endforeach
              </div>

              <div class="mt-5">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="subject">{{ __('Subject') }}</label>
                <input id="subject" name="subject" type="text" placeholder="{{ __('What is it about?') }}" value="{{ old('subject', request()->query('subject')) }}" required @error('subject') aria-invalid="true" @enderror class="{{ $input }}" />
                @error('subject')
                  <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                @enderror
              </div>

              <div class="mt-5">
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300" for="message">{{ __('Message') }}</label>
                <textarea id="message" name="message" rows="6" maxlength="4000" required data-char-count="message-count" placeholder="{{ __('Tell me about your project, timeline and goals…') }}" @error('message') aria-invalid="true" @enderror class="{{ $input }} resize-y">{{ old('message') }}</textarea>
                <div class="mt-1 flex justify-between gap-4 text-xs text-gray-500">
                  <span>@error('message') <span class="text-sm text-rose-600">{{ $message }}</span> @enderror</span>
                  <span id="message-count" aria-live="polite">0/4000</span>
                </div>
              </div>

              @if (filled($turnstileSiteKey))
                <div class="mt-5">
                  <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}" data-language="{{ app()->getLocale() }}"></div>
                  @error('cf-turnstile-response')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                  @enderror
                  @error('turnstile')
                    <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                  @enderror
                </div>
              @endif

              <div class="mt-6 flex flex-col-reverse items-stretch gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="flex items-center gap-2 text-xs text-gray-500">
                  <i class="fas fa-lock"></i>{{ __('Your details are only used to reply to you.') }}
                </p>
                <button type="submit" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary-600 to-violet-600 px-7 py-3.5 font-semibold text-white shadow-lg shadow-primary-600/25 transition hover:shadow-xl hover:shadow-primary-600/30 disabled:cursor-wait disabled:opacity-60">
                  {{ __('Submit Message') }}
                  <i class="fas fa-arrow-right text-sm transition-transform group-hover:translate-x-1"></i>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@if (filled($turnstileSiteKey))
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endif
