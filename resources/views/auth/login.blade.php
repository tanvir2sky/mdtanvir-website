@extends('layouts.app')

@section('title', 'Admin Login | MD Tanvir Hossain')

@section('content')
  <main class="min-h-screen flex items-center justify-center px-4 py-24">
    <div class="w-full max-w-md bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl p-8">
      <p class="text-sm font-semibold uppercase tracking-wider text-primary-700 dark:text-primary-300 mb-2">Secure Area</p>
      <h1 class="text-3xl font-black mb-6">Admin Login</h1>

      <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf
        <div>
          <label for="email" class="block mb-2 text-sm font-medium">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
          @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="password" class="block mb-2 text-sm font-medium">Password</label>
          <input id="password" name="password" type="password" required
            class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
          @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex items-center gap-2">
          <input id="remember" name="remember" type="checkbox" class="rounded border-gray-300" />
          <label for="remember" class="text-sm">Remember me</label>
        </div>

        <button type="submit" class="w-full py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
          Sign in
        </button>
      </form>
    </div>
  </main>
@endsection
