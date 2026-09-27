<details
  id="project-brief"
  class="group bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-800"
>
  <summary class="flex items-center justify-between gap-3 cursor-pointer list-none p-6">
    <span>
      <span class="block text-xl font-bold text-gray-900 dark:text-white">
        <i class="fas fa-wand-magic-sparkles mr-2 text-primary-600 dark:text-primary-400"></i>Build a project brief
      </span>
      <span class="block mt-1 text-sm text-gray-600 dark:text-gray-400">
        Not sure what to write? Pick a few options and I'll draft the message for you.
      </span>
    </span>
    <i class="fas fa-chevron-down text-gray-500 transition-transform group-open:rotate-180"></i>
  </summary>

  <div class="px-6 pb-6 space-y-5">
    <fieldset>
      <legend class="block text-sm font-medium mb-2">What are you building?</legend>
      <div class="grid grid-cols-2 gap-2" data-brief-types>
        @foreach ([
          'laravel' => ['Laravel web app', 'fab fa-laravel'],
          'shopify' => ['Shopify store / app', 'fab fa-shopify'],
          'ai' => ['AI feature', 'fas fa-robot'],
          'other' => ['Something else', 'fas fa-lightbulb'],
        ] as $value => [$label, $icon])
          <label class="flex items-center gap-2 rounded-xl border border-gray-300 dark:border-gray-700 px-3 py-2.5 cursor-pointer text-sm has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-900/30">
            <input type="radio" name="brief_type" value="{{ $value }}" class="sr-only" @checked($loop->first) />
            <i class="{{ $icon }} text-primary-600 dark:text-primary-400"></i>{{ $label }}
          </label>
        @endforeach
      </div>
    </fieldset>

    <fieldset>
      <legend class="block text-sm font-medium mb-2">What should it include?</legend>
      <div class="flex flex-wrap gap-2" data-brief-features></div>
    </fieldset>

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label for="brief-budget" class="block text-sm font-medium mb-2">Budget</label>
        <select id="brief-budget" data-brief-budget class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500">
          <option>Not sure yet</option>
          <option>Under €2,000</option>
          <option>€2,000 – €5,000</option>
          <option>€5,000 – €15,000</option>
          <option>€15,000+</option>
        </select>
      </div>
      <div>
        <label for="brief-timeline" class="block text-sm font-medium mb-2">Timeline</label>
        <select id="brief-timeline" data-brief-timeline class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500">
          <option>Flexible</option>
          <option>ASAP (within 2 weeks)</option>
          <option>Within 1 month</option>
          <option>1 – 3 months</option>
          <option>Ongoing / long-term</option>
        </select>
      </div>
    </div>

    <div>
      <label for="brief-idea" class="block text-sm font-medium mb-2">Your idea in a sentence or two</label>
      <textarea id="brief-idea" data-brief-idea rows="3" maxlength="1000" placeholder="e.g. A customer portal where clients can track their orders…" class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
    </div>

    <button type="button" data-brief-generate class="w-full px-6 py-3 rounded-xl border-2 border-primary-600 text-primary-700 dark:text-primary-300 hover:bg-primary-600 hover:text-white font-semibold transition">
      Fill in the message below
    </button>
  </div>
</details>
