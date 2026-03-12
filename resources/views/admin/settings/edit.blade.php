@extends('admin.layouts.app')

@section('title', 'Tracking Settings | Admin')

@section('content')
  <div class="mb-6">
    <h1 class="text-3xl font-black">Tracking Settings</h1>
    <p class="text-gray-600 dark:text-gray-400">Control Google Tag Manager, Google Analytics, and Microsoft Clarity from admin.</p>
  </div>

  <form method="POST" action="{{ route('admin.settings.update') }}" class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 space-y-8">
    @csrf
    @method('PUT')

    <section class="space-y-4">
      <div class="flex items-center gap-3">
        <input id="enable_gtm" name="enable_gtm" type="checkbox" value="1" {{ old('enable_gtm', $settings->enable_gtm) ? 'checked' : '' }} />
        <label for="enable_gtm" class="font-semibold text-lg">Enable Google Tag Manager</label>
      </div>
      <div>
        <label for="gtm_id" class="block mb-2 text-sm font-medium">GTM ID (example: GTM-XXXXXXX)</label>
        <input id="gtm_id" name="gtm_id" type="text" value="{{ old('gtm_id', $settings->gtm_id) }}"
          class="w-full max-w-lg rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
      </div>
    </section>

    <section class="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-800">
      <div class="flex items-center gap-3">
        <input id="enable_ga" name="enable_ga" type="checkbox" value="1" {{ old('enable_ga', $settings->enable_ga) ? 'checked' : '' }} />
        <label for="enable_ga" class="font-semibold text-lg">Enable Google Analytics (GA4)</label>
      </div>
      <div>
        <label for="ga_measurement_id" class="block mb-2 text-sm font-medium">Measurement ID (example: G-XXXXXXXXXX)</label>
        <input id="ga_measurement_id" name="ga_measurement_id" type="text" value="{{ old('ga_measurement_id', $settings->ga_measurement_id) }}"
          class="w-full max-w-lg rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
      </div>
    </section>

    <section class="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-800">
      <div class="flex items-center gap-3">
        <input id="enable_clarity" name="enable_clarity" type="checkbox" value="1" {{ old('enable_clarity', $settings->enable_clarity) ? 'checked' : '' }} />
        <label for="enable_clarity" class="font-semibold text-lg">Enable Microsoft Clarity</label>
      </div>
      <div>
        <label for="clarity_project_id" class="block mb-2 text-sm font-medium">Clarity Project ID</label>
        <input id="clarity_project_id" name="clarity_project_id" type="text" value="{{ old('clarity_project_id', $settings->clarity_project_id) }}"
          class="w-full max-w-lg rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
      </div>
    </section>

    <div class="pt-4 border-t border-gray-200 dark:border-gray-800">
      <button type="submit" class="px-6 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold transition">
        Save Settings
      </button>
    </div>
  </form>
@endsection
