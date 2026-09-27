@php
  // On the home page, section links are in-page anchors; elsewhere they point back to home.
  $onHome = request()->routeIs('home', 'de.home');
  $homeUrl = $onHome ? '' : lroute('home');
  $isBlog = request()->routeIs('blog.*', 'de.blog.*');
  $links = [
    ['#home', __('Home')],
    ['#about', __('About')],
    ['#skills', __('Skills')],
    ['#experience', __('Experience')],
    ['#projects', __('Projects')],
  ];
  $currentLocale = app()->getLocale();
  $linkClass = 'text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors';
@endphp

<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-primary-600 focus:px-4 focus:py-2 focus:text-white">
  {{ __('Skip to content') }}
</a>

<nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-xl border-b border-gray-200/70 dark:border-gray-800/70 transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16 gap-4">
      <a href="{{ $homeUrl }}#home" class="text-lg font-extrabold tracking-tight text-primary-700 dark:text-primary-300">
        MD Tanvir
      </a>

      <div class="hidden lg:flex items-center gap-6">
        @foreach ($links as [$anchor, $label])
          <a href="{{ $homeUrl }}{{ $anchor }}" class="{{ $onHome ? 'nav-link' : '' }} {{ $linkClass }}">{{ $label }}</a>
        @endforeach
        <a href="{{ lroute('blog.index') }}" class="{{ $isBlog ? 'text-sm font-semibold text-primary-700 dark:text-primary-300' : $linkClass }}">{{ __('Blog') }}</a>
        <a href="{{ $homeUrl }}#contact" class="{{ $onHome ? 'nav-link' : '' }} {{ $linkClass }}">{{ __('Contact') }}</a>
      </div>

      <div class="flex items-center gap-2">
        <button
          type="button"
          data-command-palette-open
          class="hidden sm:inline-flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-900/70 px-3 py-1.5 text-sm text-gray-500 hover:border-gray-300 dark:hover:border-gray-600 transition"
        >
          <i class="fas fa-magnifying-glass text-xs"></i>
          <span>{{ __('Search') }}</span>
          <kbd class="rounded border border-gray-200 dark:border-gray-700 px-1.5 font-mono text-[10px]" data-shortcut-label>Ctrl K</kbd>
        </button>
        <button
          type="button"
          data-command-palette-open
          aria-label="{{ __('Search') }}"
          class="sm:hidden p-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors"
        >
          <i class="fas fa-magnifying-glass"></i>
        </button>

        <div class="flex items-center rounded-lg bg-gray-200 dark:bg-gray-800 p-0.5 text-xs font-bold" role="group" aria-label="{{ __('Language') }}">
          @foreach (\App\Support\Locale::NAMES as $code => $name)
            <a
              href="{{ locale_switch_url($code) }}"
              hreflang="{{ $code }}"
              lang="{{ $code }}"
              title="{{ $name }}"
              @if ($code === $currentLocale) aria-current="true" @endif
              class="rounded-md px-2 py-1.5 uppercase transition {{ $code === $currentLocale ? 'bg-white dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-900 dark:hover:text-white' }}"
            >{{ $code }}</a>
          @endforeach
        </div>

        <button
          id="theme-toggle"
          type="button"
          aria-label="{{ __('Toggle theme') }}"
          class="p-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors"
        >
          <i class="fas fa-moon dark:hidden"></i>
          <i class="fas fa-sun hidden dark:inline"></i>
        </button>
        <button
          id="mobile-menu-toggle"
          type="button"
          aria-label="{{ __('Toggle mobile menu') }}"
          class="lg:hidden p-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors"
        >
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
  </div>

  <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-200 dark:border-gray-800 bg-white/95 dark:bg-gray-950/95">
    <div class="px-4 py-4 space-y-3">
      @foreach ($links as [$anchor, $label])
        <a href="{{ $homeUrl }}{{ $anchor }}" class="block {{ $onHome ? 'nav-link' : '' }} text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">{{ $label }}</a>
      @endforeach
      <a href="{{ lroute('blog.index') }}" class="block {{ $isBlog ? 'font-semibold text-primary-700 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400' }}">{{ __('Blog') }}</a>
      <a href="{{ $homeUrl }}#contact" class="block {{ $onHome ? 'nav-link' : '' }} text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">{{ __('Contact') }}</a>
    </div>
  </div>
</nav>

<div id="particles-js" class="fixed inset-0 z-0 pointer-events-none"></div>
