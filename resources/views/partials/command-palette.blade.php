@php
  $homeUrl = lroute('home');
  $otherLocale = app()->getLocale() === 'de' ? 'en' : 'de';
  $cvUrl = \App\Support\Portfolio::cvUrl();

  $paletteCommands = collect([
    ['navigation', 'home', __('Home'), 'fas fa-house', $homeUrl.'#home', 'start top'],
    ['navigation', 'about', __('About'), 'fas fa-user', $homeUrl.'#about', 'bio me'],
    ['navigation', 'skills', __('Skills'), 'fas fa-layer-group', $homeUrl.'#skills', 'stack tech'],
    ['navigation', 'experience', __('Experience'), 'fas fa-briefcase', $homeUrl.'#experience', 'career jobs cv'],
    ['navigation', 'projects', __('Projects'), 'fas fa-diagram-project', $homeUrl.'#projects', 'work portfolio'],
    ['navigation', 'blog', __('Blog'), 'fas fa-newspaper', lroute('blog.index'), 'articles posts writing'],
    ['navigation', 'contact', __('Contact'), 'fas fa-paper-plane', $homeUrl.'#contact', 'hire email message'],
    ['navigation', 'tools', __('Tools'), 'fas fa-toolbox', lroute('tools.index'), 'developer utilities free'],
    ['navigation', 'tool-shopify-check', __('Shopify store health check'), 'fab fa-shopify', lroute('tools.shopify-check'), 'audit seo speed store'],
    ['navigation', 'tool-hmac', __('Shopify webhook HMAC verifier'), 'fas fa-shield-halved', lroute('tools.hmac'), 'signature webhook security'],
    ['navigation', 'tool-cron', __('Cron expression explainer'), 'fas fa-clock', lroute('tools.cron'), 'schedule crontab laravel scheduler'],
    ['navigation', 'guestbook', __('Guestbook'), 'fas fa-book-open', lroute('guestbook.index'), 'notes messages sign'],
  ])->map(fn ($c) => ['group' => $c[0], 'id' => "nav-{$c[1]}", 'title' => $c[2], 'icon' => $c[3], 'url' => $c[4], 'keywords' => $c[5]])
    ->concat(array_values(array_filter([
      ['group' => 'action', 'id' => 'copy-email', 'title' => __('Copy email address'), 'subtitle' => config('portfolio.email'), 'icon' => 'fas fa-copy', 'action' => 'copy', 'value' => config('portfolio.email'), 'keywords' => 'mail contact'],
      ['group' => 'action', 'id' => 'toggle-theme', 'title' => __('Toggle dark mode'), 'icon' => 'fas fa-circle-half-stroke', 'action' => 'theme', 'keywords' => 'light dark theme'],
      ['group' => 'action', 'id' => 'switch-language', 'title' => $otherLocale === 'de' ? 'Auf Deutsch lesen' : 'Read in English', 'icon' => 'fas fa-language', 'url' => locale_switch_url($otherLocale), 'keywords' => 'language deutsch english sprache'],
      \App\Models\SiteSetting::bookingAvailable() ? ['group' => 'action', 'id' => 'book-call', 'title' => __('Book a call'), 'icon' => 'fas fa-calendar-check', 'url' => lroute('book.index'), 'keywords' => 'meeting schedule call termin'] : null,
      $cvUrl ? ['group' => 'action', 'id' => 'download-cv', 'title' => __('Download CV'), 'icon' => 'fas fa-file-arrow-down', 'url' => $cvUrl, 'keywords' => 'resume lebenslauf'] : null,
      ['group' => 'action', 'id' => 'linkedin', 'title' => 'LinkedIn', 'icon' => 'fab fa-linkedin', 'url' => config('portfolio.linkedin'), 'external' => true, 'keywords' => 'social connect'],
      ['group' => 'action', 'id' => 'github', 'title' => 'GitHub', 'icon' => 'fab fa-github', 'url' => config('portfolio.github'), 'external' => true, 'keywords' => 'code repositories'],
      ['group' => 'action', 'id' => 'rss', 'title' => __('RSS feed'), 'icon' => 'fas fa-rss', 'url' => route('feed'), 'keywords' => 'subscribe feed'],
    ])))
    ->values();
@endphp

<dialog
  id="command-palette"
  aria-label="{{ __('Search and commands') }}"
  class="m-0 h-full max-h-none w-full max-w-none border-0 bg-transparent p-4 pt-[12vh] backdrop:bg-gray-950/60 backdrop:backdrop-blur-sm sm:p-6 sm:pt-[14vh]"
  data-search-url="{{ lroute('search') }}"
>
  <div class="mx-auto max-w-xl overflow-hidden rounded-2xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-2xl">
    <div class="flex items-center gap-3 border-b border-gray-200 dark:border-white/10 px-4">
      <i class="fas fa-magnifying-glass text-gray-400"></i>
      <input
        type="text"
        data-palette-input
        role="combobox"
        aria-expanded="true"
        aria-controls="command-palette-results"
        aria-autocomplete="list"
        autocomplete="off"
        spellcheck="false"
        placeholder="{{ __('Search pages, articles and case studies…') }}"
        class="h-14 flex-1 bg-transparent text-base placeholder-gray-400 focus:outline-none"
      />
      <kbd class="hidden rounded border border-gray-200 dark:border-white/10 px-1.5 py-0.5 font-mono text-[10px] text-gray-500 sm:block">Esc</kbd>
    </div>

    <ul
      id="command-palette-results"
      role="listbox"
      data-palette-results
      aria-label="{{ __('Results') }}"
      class="max-h-[55vh] overflow-y-auto p-2"
    ></ul>

    <div class="flex items-center justify-between gap-4 border-t border-gray-200 dark:border-white/10 px-4 py-2.5 text-xs text-gray-500">
      <span class="flex items-center gap-3">
        <span><kbd class="font-mono">↑↓</kbd> {{ __('navigate') }}</span>
        <span><kbd class="font-mono">↵</kbd> {{ __('open') }}</span>
      </span>
      <span data-palette-status role="status" aria-live="polite"></span>
    </div>
  </div>
  <script type="application/json" data-palette-commands>@json($paletteCommands)</script>
</dialog>
