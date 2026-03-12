@php
  $route = request()->route()?->getName();
@endphp
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Admin Dashboard')</title>
    @vite(["resources/css/app.css", "resources/js/app.js"])
    @stack('styles')
  </head>
  <body class="bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen">
    <div class="min-h-screen flex">
      <aside class="w-72 hidden lg:flex flex-col border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="px-6 py-6 border-b border-gray-200 dark:border-gray-800">
          <p class="text-sm uppercase tracking-wider text-primary-600 dark:text-primary-400 font-semibold">Admin Panel</p>
          <h1 class="text-2xl font-black mt-1">MD Tanvir</h1>
        </div>

        <nav class="p-4 space-y-2">
          <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-xl {{ $route === 'admin.dashboard' ? 'bg-primary-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <i class="fas fa-chart-line mr-2"></i> Dashboard
          </a>
          <a href="{{ route('admin.posts.index') }}" class="block px-4 py-3 rounded-xl {{ str_starts_with((string) $route, 'admin.posts.') ? 'bg-primary-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <i class="fas fa-newspaper mr-2"></i> Blog Posts
          </a>
          <a href="{{ route('admin.contacts.index') }}" class="block px-4 py-3 rounded-xl {{ str_starts_with((string) $route, 'admin.contacts.') ? 'bg-primary-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <i class="fas fa-envelope-open-text mr-2"></i> Contacts
          </a>
          <a href="{{ route('admin.settings.edit') }}" class="block px-4 py-3 rounded-xl {{ str_starts_with((string) $route, 'admin.settings.') ? 'bg-primary-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <i class="fas fa-gear mr-2"></i> Settings
          </a>
          <a href="{{ route('home') }}" class="block px-4 py-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800">
            <i class="fas fa-globe mr-2"></i> View Website
          </a>
        </nav>

        <div class="mt-auto p-4 border-t border-gray-200 dark:border-gray-800">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-4 py-3 rounded-xl bg-gray-900 dark:bg-gray-700 text-white font-semibold hover:opacity-90 transition">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </button>
          </form>
        </div>
      </aside>

      <div class="flex-1 flex flex-col">
        <header class="border-b border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur px-4 sm:px-6 py-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">Administrator</p>
              <p class="font-semibold">{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700 transition">
              Back to site
            </a>
          </div>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
          @if (session('status'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 text-green-800 px-4 py-3">
              {{ session('status') }}
            </div>
          @endif

          @yield('content')
        </main>
      </div>
    </div>
    @stack('scripts')
  </body>
</html>
