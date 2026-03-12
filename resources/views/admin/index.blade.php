@extends('layouts.app')

@section('content')
  <main class="min-h-screen flex items-center justify-center px-4 py-24">
    <div class="max-w-2xl w-full rounded-2xl border border-gray-200 dark:border-gray-800 bg-white/90 dark:bg-gray-900/90 p-8 shadow-xl text-center">
      <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-3">
        Admin Panel
      </p>
      <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white mb-4">
        Coming Soon
      </h1>
      <p class="text-gray-700 dark:text-gray-300 leading-relaxed mb-8">
        This area is reserved for the future admin panel where blog management and additional portfolio content features will be implemented.
      </p>
      <a
        href="{{ url('/') }}"
        class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition-colors"
      >
        Back to Portfolio
      </a>
    </div>
  </main>
@endsection
