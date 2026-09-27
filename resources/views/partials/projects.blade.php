@php
  // Bento layout: repeats every four cards (wide + narrow, then two halves).
  $spans = ['lg:col-span-4', 'lg:col-span-2', 'lg:col-span-3', 'lg:col-span-3'];

  // How the AI features flow through the app (shown on the featured AI card).
  $aiFlow = [
    ['fas fa-user', __('User request')],
    ['fas fa-layer-group', __('Laravel queue job')],
    ['fas fa-brain', __('LLM + prompt')],
    ['fas fa-shield-halved', __('Guardrails & checks')],
    ['fas fa-circle-check', __('Response to user')],
  ];
@endphp

@if ($projects->isNotEmpty())
<section id="projects" class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8">
  <div class="max-w-7xl mx-auto">
    <div class="flex flex-wrap items-end justify-between gap-6 mb-12">
      <div>
        <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
          {{ __('Featured Projects') }}
        </p>
        <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
          {{ __('Work highlights') }}
        </h2>
      </div>
      <p class="max-w-md text-gray-600 dark:text-gray-400">
        {{ __("A selection of products I've designed and shipped, from AI features to e-commerce and finance.") }}
      </p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-6 gap-5">
      @foreach ($projects as $project)
        @php
          $accent = $project->accentStyle();
          $featured = $project->is_featured;
          $caseStudyUrl = $project->hasCaseStudy() ? lroute('projects.show', $project->slug) : null;
          $isOddLast = $loop->last && $loop->count % 2 === 1;
        @endphp
        <article
          data-spotlight
          style="--spotlight: {{ $accent['glow'] }}"
          class="project-card spotlight-card group relative overflow-hidden rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] backdrop-blur-sm p-7 sm:p-8 transition-all duration-300 hover:-translate-y-1 hover:border-gray-300 dark:hover:border-white/20 hover:shadow-2xl hover:shadow-primary-900/10 {{ $spans[$loop->index % 4] }} {{ $featured || $isOddLast ? 'md:col-span-2' : '' }}"
        >
          @if ($featured)
            {{-- Decorative dot grid --}}
            <div
              aria-hidden="true"
              class="pointer-events-none absolute inset-0 opacity-60 dark:opacity-40 [background-image:radial-gradient(circle_at_1px_1px,rgba(14,165,233,0.25)_1px,transparent_0)] [background-size:22px_22px] [mask-image:linear-gradient(to_left,black,transparent_70%)]"
            ></div>
          @endif

          <div class="relative flex h-full flex-col {{ $featured ? 'lg:flex-row lg:items-center lg:gap-10' : '' }}">
            <div class="flex flex-1 flex-col">
              <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                  <span class="grid place-items-center w-12 h-12 rounded-2xl ring-1 {{ $accent['tile'] }}">
                    <i class="{{ $project->icon }} text-xl"></i>
                  </span>
                  @if ($project->t('category'))
                    <span class="text-xs font-semibold uppercase tracking-[0.18em] {{ $accent['label'] }}">
                      {{ $project->t('category') }}
                    </span>
                  @endif
                </div>
                <span class="font-mono text-sm text-gray-400 dark:text-gray-600">
                  {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                </span>
              </div>

              <h3 class="{{ $featured ? 'text-2xl sm:text-3xl' : 'text-xl sm:text-2xl' }} font-bold tracking-tight text-gray-900 dark:text-white mb-3">
                @if ($caseStudyUrl)
                  <a href="{{ $caseStudyUrl }}" class="after:absolute after:inset-0">{{ $project->t('title') }}</a>
                @else
                  {{ $project->t('title') }}
                @endif
              </h3>
              <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-8 {{ $featured ? 'max-w-xl' : '' }}">
                {{ $project->t('summary') }}
              </p>

              <div class="mt-auto flex flex-wrap items-end justify-between gap-4">
                <ul class="flex flex-wrap gap-2" aria-label="{{ __('Tech stack') }}">
                  @foreach ($project->tags ?? [] as $tag)
                    <li class="rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-2.5 py-1 font-mono text-xs text-gray-700 dark:text-gray-300">
                      {{ $tag }}
                    </li>
                  @endforeach
                </ul>
                @if ($caseStudyUrl)
                  <span class="inline-flex items-center gap-1.5 text-sm font-semibold {{ $accent['label'] }}">
                    {{ __('Case study') }}
                    <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
                  </span>
                @endif
              </div>
            </div>

            @if ($featured && $project->slug === 'ai-powered-features')
              <ol
                aria-label="{{ __('Request flow') }}"
                class="hidden lg:flex flex-col gap-2 w-60 shrink-0 font-mono text-xs"
              >
                @foreach ($aiFlow as [$stepIcon, $stepLabel])
                  <li class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-white/10 bg-white/80 dark:bg-gray-950/60 px-3 py-2.5 text-gray-700 dark:text-gray-300 transition-transform duration-300 group-hover:translate-x-1" style="transition-delay: {{ $loop->index * 40 }}ms">
                    <i class="{{ $stepIcon }} w-4 text-center text-cyan-500"></i>
                    {{ $stepLabel }}
                  </li>
                @endforeach
              </ol>
            @endif
          </div>
        </article>
      @endforeach
    </div>
  </div>
</section>
@endif
