<footer class="relative z-10 py-10 px-4 sm:px-6 lg:px-8 border-t border-gray-200 dark:border-gray-800 bg-white/70 dark:bg-gray-950/70">
  <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
    <p class="text-gray-600 dark:text-gray-400 text-sm">
      © {{ now()->year }} MD Tanvir Hossain. {{ __('All rights reserved.') }}
    </p>
    <div class="flex flex-wrap items-center justify-center gap-5 text-sm">
      <a href="{{ lroute('home') }}#home" class="hover:text-primary-600 dark:hover:text-primary-400">{{ __('Home') }}</a>
      <a href="{{ lroute('blog.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ __('Blog') }}</a>
      <a href="{{ lroute('tools.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ __('Tools') }}</a>
      <a href="{{ lroute('guestbook.index') }}" class="hover:text-primary-600 dark:hover:text-primary-400">{{ __('Guestbook') }}</a>
      <a href="{{ lroute('home') }}#contact" class="hover:text-primary-600 dark:hover:text-primary-400">{{ __('Contact') }}</a>
      <a href="{{ route('feed') }}" class="hover:text-primary-600 dark:hover:text-primary-400" aria-label="{{ __('RSS feed') }}"><i class="fas fa-rss"></i></a>
    </div>
  </div>
</footer>
