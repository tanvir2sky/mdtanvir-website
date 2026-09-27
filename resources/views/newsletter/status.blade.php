@extends('layouts.app')

@section('title', $title.' | MD Tanvir Hossain')

@section('robots', 'noindex, follow')

@section('content')
  @include('partials.site-header')

  <main id="main-content" class="relative z-10 flex min-h-[80vh] items-center justify-center px-4 pb-24 pt-28">
    <div class="w-full max-w-lg rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/80 dark:bg-white/[0.03] p-10 text-center backdrop-blur-sm shadow-xl">
      <span class="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-cyan-400 via-primary-500 to-violet-500 text-2xl text-white shadow-lg shadow-primary-500/30">
        <i class="{{ $icon }}"></i>
      </span>
      <h1 class="mb-3 text-3xl font-black tracking-tight text-gray-900 dark:text-white">{{ $title }}</h1>
      <p class="mb-8 text-gray-600 dark:text-gray-400">{{ $message }}</p>
      <a href="{{ $actionUrl ?? lroute('blog.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-3 font-semibold text-white hover:bg-primary-700 transition">
        {{ $actionLabel ?? __('Browse articles') }} <i class="fas fa-arrow-right text-xs"></i>
      </a>
    </div>
  </main>
  @include('partials.site-footer')
@endsection
