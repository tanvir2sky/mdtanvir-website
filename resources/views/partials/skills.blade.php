<section id="skills" class="py-20 sm:py-24 px-4 sm:px-6 lg:px-8">
  <div class="max-w-7xl mx-auto">
    <div class="text-center mb-12">
      <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
        {{ __('Skills & Technologies') }}
      </p>
      <h2 class="text-4xl md:text-5xl font-black text-gray-900 dark:text-white">
        {{ __('Technical strengths') }}
      </h2>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
      @foreach ($skillGroups as $group)
        <article class="skill-card bg-white/80 dark:bg-gray-900/80 p-6 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1 border border-gray-200 dark:border-gray-800">
          <div class="text-3xl mb-4 text-primary-600 dark:text-primary-400">
            <i class="{{ $group->icon }}"></i>
          </div>
          <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">{{ $group->t('name') }}</h3>
          <ul class="space-y-2 text-gray-700 dark:text-gray-300">
            @foreach ($group->t('items') ?? [] as $item)
              <li class="flex items-center"><i class="fas fa-check-circle text-primary-600 dark:text-primary-400 mr-2"></i>{{ $item }}</li>
            @endforeach
          </ul>
        </article>
      @endforeach
    </div>
  </div>
</section>
