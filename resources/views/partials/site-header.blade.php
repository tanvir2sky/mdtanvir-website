<nav
  class="fixed top-0 w-full z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-xl border-b border-gray-200/70 dark:border-gray-800/70 transition-colors duration-300"
>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      <a
        href="{{ route('home') }}#home"
        class="text-lg font-extrabold tracking-tight text-primary-700 dark:text-primary-300"
      >
        MD Tanvir
      </a>
      <div class="hidden md:flex items-center space-x-7">
        <a href="{{ route('home') }}#home" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Home</a>
        <a href="{{ route('home') }}#about" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">About</a>
        <a href="{{ route('home') }}#skills" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Skills</a>
        <a href="{{ route('home') }}#experience" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Experience</a>
        <a href="{{ route('home') }}#projects" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Projects</a>
        <a href="{{ route('blog.index') }}" class="text-sm font-semibold text-primary-700 dark:text-primary-300">Blog</a>
        <a href="{{ route('home') }}#contact" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">Contact</a>
      </div>

      <div class="flex items-center gap-2">
        <button
          id="theme-toggle"
          aria-label="Toggle theme"
          class="p-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors"
        >
          <i class="fas fa-moon dark:hidden"></i>
          <i class="fas fa-sun hidden dark:inline"></i>
        </button>
        <button
          id="mobile-menu-toggle"
          aria-label="Toggle mobile menu"
          class="md:hidden p-2 rounded-lg bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700 transition-colors"
        >
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>
  </div>

  <div
    id="mobile-menu"
    class="hidden md:hidden border-t border-gray-200 dark:border-gray-800 bg-white/95 dark:bg-gray-950/95"
  >
    <div class="px-4 py-4 space-y-3">
      <a href="{{ route('home') }}#home" class="block text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Home</a>
      <a href="{{ route('home') }}#about" class="block text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">About</a>
      <a href="{{ route('home') }}#skills" class="block text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Skills</a>
      <a href="{{ route('home') }}#experience" class="block text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Experience</a>
      <a href="{{ route('home') }}#projects" class="block text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Projects</a>
      <a href="{{ route('blog.index') }}" class="block font-semibold text-primary-700 dark:text-primary-300">Blog</a>
      <a href="{{ route('home') }}#contact" class="block text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400">Contact</a>
    </div>
  </div>
</nav>

<div id="particles-js" class="fixed inset-0 z-0 pointer-events-none"></div>
