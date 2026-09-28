@php
  $accents = \App\Models\Project::ACCENTS;

  // Principles taken from the story below, so the tiles never claim more than the text does.
  $principles = [
    ['fas fa-code', __('Clean, maintainable code'), __('Readable code that the next developer can change with confidence.'), 'sky'],
    ['fas fa-diagram-project', __('Scalable architecture'), __('Backends and APIs designed to grow with the product.'), 'violet'],
    ['fas fa-shield-halved', __('Performance & security'), __('Fast, secure software with a great user experience.'), 'emerald'],
    ['fas fa-seedling', __('Always learning'), __('Keeping up with best practices, from Laravel to LLMs.'), 'cyan'],
  ];

  $focus = [
    ['fab fa-laravel', 'Laravel & PHP'],
    ['fab fa-shopify', 'Shopify'],
    ['fas fa-brain', __('AI in production')],
  ];
@endphp

<section id="about" class="relative py-20 sm:py-24 px-4 sm:px-6 lg:px-8">
  <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-12 lg:gap-16">
    {{-- Intro column --}}
    <div class="lg:col-span-5">
      <div class="lg:sticky lg:top-28">
        <p class="mb-3 text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300">
          {{ __('About Me') }}
        </p>
        <h2 class="mb-8 text-4xl font-black leading-[1.05] tracking-tight text-gray-900 dark:text-white md:text-5xl">
          {{ __('Engineer focused on quality and scale') }}
        </h2>

        {{-- Portrait with pull quote --}}
        <figure class="relative overflow-hidden rounded-3xl p-px bg-gradient-to-br from-cyan-400/70 via-primary-500/40 to-violet-500/70 shadow-xl shadow-primary-900/10">
          <div class="relative overflow-hidden rounded-[calc(1.5rem-1px)] bg-white dark:bg-gray-950 p-6 sm:p-7">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-50 dark:opacity-30 [background-image:radial-gradient(circle_at_1px_1px,rgba(14,165,233,0.25)_1px,transparent_0)] [background-size:20px_20px] [mask-image:linear-gradient(to_bottom_left,black,transparent_60%)]"></div>
            <i aria-hidden="true" class="fas fa-quote-left absolute right-6 top-5 text-5xl text-primary-500/15"></i>

            <div class="relative flex items-center gap-4 mb-5">
              <img src="{{ asset('img/profile.jpg') }}" alt="MD Tanvir Hossain" loading="lazy" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-white dark:ring-gray-900 shadow-lg" />
              <div>
                <p class="font-bold text-gray-900 dark:text-white">MD Tanvir Hossain</p>
                <p class="text-sm text-gray-500">{{ $currentJob?->t('role') ?? __('Software Engineer') }}@if ($currentJob) · {{ $currentJob->company }}@endif</p>
              </div>
            </div>

            <blockquote class="relative text-lg font-medium leading-relaxed text-gray-800 dark:text-gray-200">
              “{{ __('I believe in building software that not only meets requirements but exceeds expectations in terms of performance, security, and user experience.') }}”
            </blockquote>
          </div>
        </figure>

        <ul class="mt-6 flex flex-wrap gap-2" aria-label="{{ __('Focus areas') }}">
          @foreach ($focus as [$icon, $label])
            <li class="inline-flex items-center gap-2 rounded-full border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] px-3.5 py-1.5 text-sm font-semibold text-gray-700 dark:text-gray-300">
              <i class="{{ $icon }} text-primary-600 dark:text-primary-400"></i>{{ $label }}
            </li>
          @endforeach
        </ul>
      </div>
    </div>

    {{-- Story and principles --}}
    <div class="lg:col-span-7">
      <div class="space-y-6 text-lg leading-relaxed text-gray-700 dark:text-gray-300 [&_strong]:font-semibold [&_strong]:text-gray-900 dark:[&_strong]:text-white">
        <p class="text-xl leading-relaxed text-gray-900 dark:text-white sm:text-2xl">
          {{ __("I'm a passionate Software Engineer with expertise in building scalable web applications using modern technologies. My journey in software development has been driven by a commitment to writing clean, maintainable code and solving complex problems with elegant solutions.") }}
        </p>

        <div class="relative border-l-2 border-gray-200 dark:border-white/10 pl-6">
          <span aria-hidden="true" class="absolute -left-[9px] top-1.5 grid h-4 w-4 place-items-center rounded-full bg-white dark:bg-gray-950 ring-2 ring-sky-500"></span>
          <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-sky-600 dark:text-sky-300">{{ __('Backend & e-commerce') }}</p>
          <p>{!! __("Specializing in <strong>Laravel</strong> and <strong>PHP</strong>, I've developed robust backend systems and RESTful APIs that power high-performance applications. My experience with <strong>Shopify</strong> has enabled me to create seamless e-commerce solutions and custom storefronts for businesses of all sizes.") !!}</p>
        </div>

        <div class="relative border-l-2 border-gray-200 dark:border-white/10 pl-6">
          <span aria-hidden="true" class="absolute -left-[9px] top-1.5 grid h-4 w-4 place-items-center rounded-full bg-white dark:bg-gray-950 ring-2 ring-violet-500"></span>
          <p class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-violet-600 dark:text-violet-300">{{ __('Now: AI in production') }}</p>
          <p>{!! __("More recently, I've been bringing <strong>AI</strong> into production: integrating large language models into Laravel applications to power real product features, and working with AI coding assistants and agentic workflows every day to ship faster without compromising on quality.") !!}</p>
        </div>

        <p>{{ __("Beyond coding, I'm dedicated to continuous learning, staying updated with industry best practices, and contributing to the developer community.") }}</p>
      </div>

      {{-- What I value --}}
      <h3 class="mb-4 mt-12 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('What I care about') }}</h3>
      <div class="grid gap-4 sm:grid-cols-2">
        @foreach ($principles as [$icon, $title, $text, $accentName])
          @php $accent = $accents[$accentName]; @endphp
          <article data-spotlight style="--spotlight: {{ $accent['glow'] }}" class="spotlight-card group relative overflow-hidden rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-5 backdrop-blur-sm transition-all duration-300 hover:-translate-y-0.5 hover:border-gray-300 dark:hover:border-white/20">
            <div class="relative flex gap-4">
              <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl ring-1 {{ $accent['tile'] }}"><i class="{{ $icon }}"></i></span>
              <div>
                <h4 class="font-bold text-gray-900 dark:text-white">{{ $title }}</h4>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $text }}</p>
              </div>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </div>
</section>
