@extends('tools.layout')

@section('title', __('Cron expression explainer').' | MD Tanvir Hossain')
@section('meta_description', __('Explain any cron expression in plain language, preview the next run times and get the matching Laravel scheduler method.'))
@section('meta_url', lroute('tools.cron'))

@section('tool_title', __('Cron expression explainer'))
@section('tool_icon', 'fas fa-clock')
@section('tool_accent', \App\Models\Project::ACCENTS['sky']['tile'])
@section('tool_intro', __('Type a cron expression to see what it means, when it runs next, and how to write it with the Laravel scheduler.'))

@section('tool_body')
  <div id="cron-tool" class="space-y-6">
    <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm">
      <label for="cron-input" class="mb-2 block text-sm font-medium">{{ __('Cron expression') }}</label>
      <input
        id="cron-input"
        type="text"
        value="*/15 9-17 * * 1-5"
        autocomplete="off"
        spellcheck="false"
        data-cron-input
        class="w-full rounded-2xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950/70 px-5 py-4 text-center font-mono text-2xl tracking-wider focus:outline-none focus:ring-2 focus:ring-primary-500 sm:text-3xl"
      />
      <div class="mt-3 grid grid-cols-5 gap-2 text-center font-mono text-[11px] uppercase tracking-wider text-gray-500" aria-hidden="true">
        <span>{{ __('minute') }}</span><span>{{ __('hour') }}</span><span>{{ __('day (month)') }}</span><span>{{ __('month') }}</span><span>{{ __('day (week)') }}</span>
      </div>

      <div class="mt-5 flex flex-wrap gap-2" aria-label="{{ __('Examples') }}">
        @foreach (['* * * * *', '*/5 * * * *', '0 * * * *', '30 2 * * *', '0 9 * * 1-5', '0 0 1 * *', '*/15 9-17 * * 1-5', '0 8 * * 6,0'] as $preset)
          <button type="button" data-cron-preset="{{ $preset }}" class="rounded-lg border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 px-2.5 py-1 font-mono text-xs text-gray-700 dark:text-gray-300 hover:border-primary-500 transition">{{ $preset }}</button>
        @endforeach
      </div>
    </div>

    <div aria-live="polite" class="grid gap-6 lg:grid-cols-5">
      <section class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 lg:col-span-3">
        <h2 class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('In plain language') }}</h2>
        <p data-cron-description class="text-2xl font-bold leading-snug text-gray-900 dark:text-white"></p>
        <p data-cron-error class="hidden rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-800 dark:text-rose-200"></p>

        <h2 class="mb-3 mt-8 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('Laravel scheduler') }}</h2>
        <div class="relative">
          <pre class="overflow-x-auto rounded-xl bg-[#0b1120] p-4 pr-20 font-mono text-sm text-gray-200"><code data-cron-laravel></code></pre>
          <button type="button" data-cron-copy class="absolute right-3 top-3 rounded-lg bg-white/10 px-2.5 py-1 text-xs font-semibold text-white hover:bg-white/20">{{ __('Copy') }}</button>
        </div>
      </section>

      <section class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 lg:col-span-2">
        <h2 class="mb-1 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">{{ __('Next runs') }}</h2>
        <p class="mb-4 text-xs text-gray-500" data-cron-timezone></p>
        <ol data-cron-runs class="space-y-2 font-mono text-sm"></ol>
      </section>
    </div>
  </div>
@endsection

@section('tool_about')
  <p>{{ __('A cron expression has five fields: minute, hour, day of the month, month and day of the week. Each field accepts a value, a list (1,15), a range (9-17), a step (*/15) or * for every value. Month and weekday names like JAN or MON work too.') }}</p>
  <p>{{ __('When both day fields are restricted, cron runs when either one matches. Next run times are shown in your browser\'s time zone; on a server they follow the server or the schedule\'s configured time zone.') }}</p>
@endsection
