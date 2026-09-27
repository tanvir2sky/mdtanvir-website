@php
  // Accent per card, cycling through the shared palette (full class names live in Project::ACCENTS).
  $accentOrder = ['sky', 'violet', 'emerald', 'cyan', 'rose'];
  $accents = \App\Models\Project::ACCENTS;

  // Bento layout on large screens: two halves, then three thirds, repeating.
  $spans = ['lg:col-span-3', 'lg:col-span-3', 'lg:col-span-2', 'lg:col-span-2', 'lg:col-span-2'];

  $allSkills = $skillGroups->flatMap(fn ($group) => $group->t('items') ?? [])->unique()->values();
@endphp

@if ($skillGroups->isNotEmpty())
<section id="skills" class="relative py-20 sm:py-24 px-4 sm:px-6 lg:px-8 overflow-hidden">
  <div class="max-w-7xl mx-auto">
    <div class="flex flex-wrap items-end justify-between gap-6 mb-10">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
          {{ __('Skills & Technologies') }}
        </p>
        <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
          {{ __('Technical strengths') }}
        </h2>
      </div>
      <p class="max-w-md text-gray-600 dark:text-gray-400">
        {{ __('The stack I reach for every day, from Laravel backends and Shopify apps to LLM-powered features.') }}
      </p>
    </div>

    {{-- Scrolling strip of every skill --}}
    @if ($allSkills->count() > 4)
      <div class="skills-marquee relative -mx-4 mb-10 overflow-hidden py-2 sm:mx-0 [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]" aria-hidden="true">
        <div class="skills-marquee-track flex w-max gap-3">
          @foreach ([$allSkills, $allSkills] as $copyIndex => $copy)
            @foreach ($copy as $skill)
              <span class="{{ $copyIndex ? 'skills-marquee-copy' : '' }} inline-flex items-center gap-2 whitespace-nowrap rounded-full border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] px-4 py-2 font-mono text-sm text-gray-700 dark:text-gray-300">
                <span class="h-1.5 w-1.5 rounded-full bg-gradient-to-br from-cyan-400 to-violet-500"></span>{{ $skill }}
              </span>
            @endforeach
          @endforeach
        </div>
      </div>
    @endif

    {{-- Skill groups --}}
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-6">
      @foreach ($skillGroups as $group)
        @php
          $accent = $accents[$accentOrder[$loop->index % count($accentOrder)]];
          $items = $group->t('items') ?? [];
          $isOddLast = $loop->last && $loop->count % 2 === 1;
        @endphp
        <article
          data-spotlight
          style="--spotlight: {{ $accent['glow'] }}"
          class="skill-card spotlight-card group relative overflow-hidden rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-7 backdrop-blur-sm transition-all duration-300 hover:-translate-y-1 hover:border-gray-300 dark:hover:border-white/20 hover:shadow-2xl hover:shadow-primary-900/10 {{ $spans[$loop->index % count($spans)] }} {{ $isOddLast ? 'md:col-span-2' : '' }}"
        >
          {{-- Oversized faded icon for depth --}}
          <i aria-hidden="true" class="{{ $group->icon }} pointer-events-none absolute -right-4 -bottom-6 text-[8rem] opacity-[0.04] dark:opacity-[0.06] transition-transform duration-700 group-hover:-rotate-6 group-hover:scale-110"></i>

          <div class="relative">
            <div class="mb-6 flex items-center justify-between">
              <span class="grid h-12 w-12 place-items-center rounded-2xl ring-1 {{ $accent['tile'] }}">
                <i class="{{ $group->icon }} text-xl"></i>
              </span>
              <span class="font-mono text-xs text-gray-400 dark:text-gray-500">
                {{ trans_choice(':count skill|:count skills', count($items)) }}
              </span>
            </div>

            <h3 class="mb-5 text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $group->t('name') }}</h3>

            <ul class="flex flex-wrap gap-2" aria-label="{{ $group->t('name') }}">
              @foreach ($items as $item)
                <li class="rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-2.5 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors group-hover:border-gray-300 dark:group-hover:border-white/15">
                  {{ $item }}
                </li>
              @endforeach
            </ul>
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif
