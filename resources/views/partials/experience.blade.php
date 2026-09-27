@php
  $jobs = collect(config('portfolio.experience'));
  $currentJob = $jobs->first(fn ($job) => $job['current'] ?? false);
  $pastJobs = $jobs->reject(fn ($job) => $job['current'] ?? false)->values();

  // Monogram accents for previous roles (full class names so Tailwind can detect them).
  $accents = [
    'bg-sky-500/10 text-sky-600 ring-sky-500/30 dark:text-sky-300',
    'bg-violet-500/10 text-violet-600 ring-violet-500/30 dark:text-violet-300',
    'bg-cyan-500/10 text-cyan-600 ring-cyan-500/30 dark:text-cyan-300',
  ];

  $monogram = fn (string $company) => collect(preg_split('/\s+/', preg_replace('/\b(GmbH|IT|Ltd|Inc)\b/i', '', $company)))
    ->filter()
    ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
    ->take(2)
    ->implode('');

  $duration = function (string $period) {
    if (! preg_match('/^(\d{4})\s*-\s*(\d{4})$/', $period, $m)) {
      return null;
    }
    $years = (int) $m[2] - (int) $m[1];

    return $years.' '.\Illuminate\Support\Str::plural('yr', $years);
  };
@endphp

<section
  id="experience"
  class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8 bg-gray-50/80 dark:bg-gray-900/70"
>
  <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-12 lg:gap-16">
    {{-- Intro column --}}
    <div class="lg:col-span-4">
      <div class="lg:sticky lg:top-28">
        <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
          Professional Experience
        </p>
        <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white mb-5">
          Career timeline
        </h2>
        <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-8 max-w-sm">
          From my first PHP projects to shipping LLM-powered features in
          production: the teams I've worked with and what I built there.
        </p>

        <dl class="grid grid-cols-2 gap-3 max-w-sm">
          <div class="rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-4">
            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Experience</dt>
            <dd class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">8+ yrs</dd>
          </div>
          <div class="rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-4">
            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Companies</dt>
            <dd class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ count($jobs) }}</dd>
          </div>
        </dl>
      </div>
    </div>

    {{-- Timeline --}}
    <div class="lg:col-span-8 space-y-10">
      @if ($currentJob)
        {{-- Current role: the main focus --}}
        <article class="relative">
          <div aria-hidden="true" class="absolute -inset-4 rounded-[2rem] bg-gradient-to-br from-cyan-400/25 via-primary-500/20 to-violet-500/25 blur-2xl"></div>

          <div class="relative rounded-3xl p-px bg-gradient-to-br from-cyan-400 via-primary-500 to-violet-500 shadow-2xl shadow-primary-900/20">
            <div
              data-spotlight
              style="--spotlight: rgba(6, 182, 212, 0.14)"
              class="spotlight-card relative overflow-hidden rounded-[calc(1.5rem-1px)] bg-white dark:bg-gray-950 p-6 sm:p-10"
            >
              <div
                aria-hidden="true"
                class="pointer-events-none absolute inset-0 opacity-60 dark:opacity-40 [background-image:radial-gradient(circle_at_1px_1px,rgba(14,165,233,0.25)_1px,transparent_0)] [background-size:22px_22px] [mask-image:linear-gradient(to_bottom_left,black,transparent_60%)]"
              ></div>

              <div class="relative">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-8">
                  <span class="inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700 dark:text-emerald-300">
                    <span class="relative flex w-2 h-2">
                      <span class="absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-75 animate-ping"></span>
                      <span class="relative inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                    Now
                  </span>
                  <span class="rounded-full border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-3 py-1 font-mono text-xs text-gray-600 dark:text-gray-400">
                    {{ $currentJob['period'] }}
                  </span>
                </div>

                <header class="flex items-center gap-5 mb-8">
                  <span aria-hidden="true" class="grid place-items-center w-16 h-16 sm:w-20 sm:h-20 shrink-0 rounded-3xl bg-gradient-to-br from-cyan-400 via-primary-500 to-violet-500 text-2xl sm:text-3xl font-black text-white shadow-lg shadow-primary-500/30">
                    {{ $monogram($currentJob['company']) }}
                  </span>
                  <div>
                    <h3 class="text-2xl sm:text-4xl font-black tracking-tight text-gray-900 dark:text-white">
                      {{ $currentJob['role'] }}
                    </h3>
                    <a
                      href="{{ $currentJob['url'] }}"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="mt-1 inline-flex items-center gap-2 text-lg sm:text-xl font-bold bg-gradient-to-r from-cyan-500 to-primary-600 dark:from-cyan-300 dark:to-primary-400 bg-clip-text text-transparent hover:opacity-80"
                    >
                      {{ $currentJob['company'] }}
                      <i class="fas fa-arrow-up-right-from-square text-xs text-primary-500"></i>
                    </a>
                  </div>
                </header>

                @if (! empty($currentJob['focus']))
                  <ul class="grid sm:grid-cols-3 gap-3 mb-8" aria-label="Focus areas">
                    @foreach ($currentJob['focus'] as $area)
                      <li class="rounded-2xl border border-gray-200 dark:border-white/10 bg-white/80 dark:bg-white/[0.04] p-4">
                        <i class="{{ $area['icon'] }} text-lg text-cyan-500 dark:text-cyan-300"></i>
                        <p class="mt-3 font-bold text-gray-900 dark:text-white">{{ $area['title'] }}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $area['text'] }}</p>
                      </li>
                    @endforeach
                  </ul>
                @endif

                <ul class="space-y-3 text-gray-700 dark:text-gray-300 leading-relaxed mb-8">
                  @foreach ($currentJob['highlights'] as $highlight)
                    <li class="flex gap-3">
                      <i aria-hidden="true" class="fas fa-check mt-1.5 text-xs text-cyan-500"></i>
                      <span>{{ $highlight }}</span>
                    </li>
                  @endforeach
                </ul>

                <ul class="flex flex-wrap gap-2" aria-label="Tech used">
                  @foreach ($currentJob['tags'] as $tag)
                    <li class="rounded-lg border border-primary-200 dark:border-primary-400/20 bg-primary-50 dark:bg-primary-400/10 px-2.5 py-1 font-mono text-xs text-primary-700 dark:text-primary-300">
                      {{ $tag }}
                    </li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </article>
      @endif

      @if ($pastJobs->isNotEmpty())
        <div>
          <p class="flex items-center gap-4 mb-6 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
            Previously
            <span aria-hidden="true" class="h-px flex-1 bg-gray-200 dark:bg-white/10"></span>
          </p>

          <ol data-timeline class="relative space-y-5">
            {{-- Rail + scroll progress --}}
            <span aria-hidden="true" class="absolute left-5 top-2 bottom-2 w-px bg-gray-200 dark:bg-white/10"></span>
            <span
              aria-hidden="true"
              class="absolute left-5 top-2 w-px origin-top bg-gradient-to-b from-sky-400 to-violet-500"
              style="height: calc(100% - 1rem); transform: scaleY(var(--timeline-progress, 1));"
            ></span>

            @foreach ($pastJobs as $job)
              <li class="relative pl-16">
                <span aria-hidden="true" class="absolute left-0 top-5 w-10 h-10 rounded-xl bg-white dark:bg-gray-950 font-bold text-xs">
                  <span class="grid place-items-center w-full h-full rounded-xl ring-1 {{ $accents[$loop->index % count($accents)] }}">
                    {{ $monogram($job['company']) }}
                  </span>
                </span>

                <article
                  data-spotlight
                  style="--spotlight: rgba(14, 165, 233, 0.10)"
                  class="spotlight-card relative overflow-hidden rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/60 dark:bg-white/[0.02] p-5 sm:p-6 transition-all duration-300 hover:border-gray-300 dark:hover:border-white/20"
                >
                  <div class="relative">
                    <header class="flex flex-wrap items-start justify-between gap-x-6 gap-y-2 mb-4">
                      <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $job['role'] }}</h3>
                        <a
                          href="{{ $job['url'] }}"
                          target="_blank"
                          rel="noopener noreferrer"
                          class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400"
                        >
                          {{ $job['company'] }}
                          <i class="fas fa-arrow-up-right-from-square text-[10px] opacity-60"></i>
                        </a>
                      </div>
                      <span class="rounded-full border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-3 py-1 font-mono text-xs text-gray-600 dark:text-gray-400">
                        {{ $job['period'] }}
                        @if ($length = $duration($job['period']))
                          <span class="text-gray-400 dark:text-gray-600">· {{ $length }}</span>
                        @endif
                      </span>
                    </header>

                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                      @foreach ($job['highlights'] as $highlight)
                        <li class="flex gap-3">
                          <span aria-hidden="true" class="mt-2 w-1 h-1 shrink-0 rounded-full bg-gray-400"></span>
                          <span>{{ $highlight }}</span>
                        </li>
                      @endforeach
                    </ul>

                    <ul class="flex flex-wrap gap-1.5" aria-label="Tech used">
                      @foreach ($job['tags'] as $tag)
                        <li class="rounded-md border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-2 py-0.5 font-mono text-[11px] text-gray-600 dark:text-gray-400">
                          {{ $tag }}
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </article>
              </li>
            @endforeach
          </ol>
        </div>
      @endif
    </div>
  </div>
</section>
