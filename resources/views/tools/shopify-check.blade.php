@extends('tools.layout')

@section('title', ($report ? __('Store check: :host', ['host' => $report['host']]) : __('Shopify store health check')).' | MD Tanvir Hossain')
@section('meta_description', __('Free Shopify store health check: speed, SEO basics, images and catalogue size, with practical suggestions.'))
@section('meta_url', lroute('tools.shopify-check'))
@if ($report || $error)
  @section('robots', 'noindex, follow')
@endif

@section('tool_title', __('Shopify store health check'))
@section('tool_icon', 'fab fa-shopify')
@section('tool_accent', \App\Models\Project::ACCENTS['emerald']['tile'])
@section('tool_intro', __('Enter a Shopify store address to get a quick report on speed, SEO basics, images and catalogue size, with practical suggestions.'))

@php
  $errorMessages = [
    'invalid_url' => __('Enter a valid store address, like mystore.com or mystore.myshopify.com.'),
    'private_address' => __('That address cannot be checked.'),
    'unreachable' => __('The store could not be reached. Check the address and try again.'),
    'too_large' => __('The store page is too large to analyse.'),
    'too_many_redirects' => __('The store redirects too many times to be checked.'),
    'redirect_blocked' => __('The store redirects to a different website, so it cannot be checked.'),
    'rate_limited' => __('You have run a lot of checks. Please try again later.'),
  ];

  $statusStyles = [
    'pass' => ['fa-circle-check', 'text-emerald-600 dark:text-emerald-400'],
    'warn' => ['fa-triangle-exclamation', 'text-amber-500'],
    'fail' => ['fa-circle-xmark', 'text-rose-600 dark:text-rose-400'],
  ];

  $describe = function (array $check, array $facts) {
    $v = $check['value'];
    $total = $facts['images'] ?? 0;

    return match ($check['id']) {
      'response_time' => [__('Server response time'), __(':ms ms to load the homepage', ['ms' => number_format($v)]), __('Remove unused apps and heavy theme scripts, and compress large hero images.')],
      'page_weight' => [__('Homepage HTML size'), __(':kb KB of HTML', ['kb' => $v]), __('Trim inline scripts and app snippets injected into the theme.')],
      'viewport' => [__('Mobile viewport'), $check['status'] === 'pass' ? __('Viewport meta tag found.') : __('No viewport meta tag.'), __('Add a viewport meta tag so the store renders correctly on phones.')],
      'title' => [__('Page title'), $v ? __(':count characters', ['count' => $v]) : __('No page title found.'), __('Aim for a 30–60 character title with your brand and main product type.')],
      'meta_description' => [__('Meta description'), $v ? __(':count characters', ['count' => $v]) : __('No meta description found.'), __('Write a 70–160 character description that sells the store in search results.')],
      'h1' => [__('Main heading (H1)'), __(':count H1 headings', ['count' => $v]), $v === 0 ? __('Add one H1 that describes the page.') : __('Use a single H1 per page.')],
      'image_alt' => [__('Image alt text'), __(':missing of :total images have no alt text', ['missing' => $v, 'total' => $total]), __('Add descriptive alt text for accessibility and image search.')],
      'lazy_loading' => [__('Lazy-loaded images'), __(':lazy of :total images load lazily', ['lazy' => $v, 'total' => $total]), __('Lazy-load images below the fold to speed up the first paint.')],
      'open_graph' => [__('Social sharing tags'), $check['status'] === 'pass' ? __('Open Graph title and image found.') : __('Open Graph tags are incomplete.'), __('Add og:title and og:image so shared links look good.')],
      'canonical' => [__('Canonical URL'), $check['status'] === 'pass' ? __('Canonical link found.') : __('No canonical link.'), __('Add a canonical link to avoid duplicate-content issues.')],
      'lang' => [__('Language attribute'), $v ? __('Language: :lang', ['lang' => $v]) : __('No language attribute.'), __('Set the lang attribute on the html element.')],
      'catalogue' => [__('Public catalogue'), $v === null ? __('Product feed not public.') : ($v >= 250 ? __('250+ products') : trans_choice(':count product|:count products', $v)), __('No products are publicly listed yet.')],
      default => [$check['id'], '', ''],
    };
  };
@endphp

@section('tool_body')
  <form method="GET" action="{{ lroute('tools.shopify-check') }}" class="mb-8 rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm">
    <label for="store" class="mb-2 block text-sm font-medium">{{ __('Store address') }}</label>
    <div class="flex flex-col gap-3 sm:flex-row">
      <div class="relative flex-1">
        <i class="fas fa-store pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input id="store" name="store" type="text" inputmode="url" autocomplete="off" required value="{{ $store }}" placeholder="mystore.com"
          class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950/70 py-3 pl-11 pr-4 focus:outline-none focus:ring-2 focus:ring-primary-500" />
      </div>
      <div class="hidden" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off" /></div>
      <button type="submit" class="rounded-xl bg-primary-600 px-6 py-3 font-semibold text-white hover:bg-primary-700 transition" data-loading-label="{{ __('Checking…') }}">
        {{ __('Check store') }}
      </button>
    </div>
    <p class="mt-2 text-xs text-gray-500">{{ __('Only public pages are read, the same as any visitor would see. Results are cached for an hour.') }}</p>
  </form>

  @if ($error)
    <div role="alert" class="flex items-start gap-3 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-5 py-4 text-rose-800 dark:text-rose-200">
      <i class="fas fa-circle-exclamation mt-1"></i>
      <span>{{ $errorMessages[$error] ?? $errorMessages['unreachable'] }}</span>
    </div>
  @endif

  @if ($report && ! $report['is_shopify'])
    <div role="status" class="rounded-3xl border border-amber-500/40 bg-amber-500/10 p-6 text-amber-900 dark:text-amber-100">
      <p class="flex items-center gap-2 text-lg font-bold"><i class="fas fa-circle-info"></i>{{ __(':host does not look like a Shopify store.', ['host' => $report['host']]) }}</p>
      <p class="mt-2">{{ __('This check only works for stores running on Shopify. If it is a Shopify store behind a proxy, try its .myshopify.com address.') }}</p>
    </div>
  @endif

  @if ($report && $report['is_shopify'])
    @php
      $score = $report['score'];
      $ring = $score >= 80 ? 'text-emerald-500' : ($score >= 50 ? 'text-amber-500' : 'text-rose-500');
      $facts = $report['facts'];
      $circumference = 2 * M_PI * 52;
    @endphp

    <section aria-live="polite" class="space-y-6">
      <div class="grid gap-6 lg:grid-cols-3">
        <div class="flex flex-col items-center justify-center rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 text-center">
          <div class="relative mb-4 h-40 w-40">
            <svg viewBox="0 0 120 120" class="h-full w-full -rotate-90" role="img" aria-label="{{ __('Score: :score out of 100', ['score' => $score]) }}">
              <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="10" class="text-gray-200 dark:text-white/10" />
              <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round" class="{{ $ring }}"
                stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $circumference * (1 - $score / 100) }}" />
            </svg>
            <span aria-hidden="true" class="absolute inset-0 grid place-items-center text-4xl font-black text-gray-900 dark:text-white">{{ $score }}</span>
          </div>
          <p class="font-bold text-gray-900 dark:text-white">{{ $facts['name'] ?? $report['host'] }}</p>
          <a href="https://{{ $report['host'] }}" target="_blank" rel="noopener noreferrer nofollow" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">{{ $report['host'] }}</a>
          <p class="mt-2 text-xs text-gray-500">{{ __('Checked :time', ['time' => \Illuminate\Support\Carbon::parse($report['checked_at'])->diffForHumans()]) }}</p>
        </div>

        <dl class="grid grid-cols-2 gap-3 lg:col-span-2 sm:grid-cols-3">
          @foreach ([
            [__('Products'), $facts['products'] === null ? '–' : ($facts['products'] >= 250 ? '250+' : $facts['products'])],
            [__('Collections'), $facts['collections'] === null ? '–' : ($facts['collections'] >= 250 ? '250+' : $facts['collections'])],
            [__('Currency'), $facts['currency'] ?? '–'],
            [__('Response time'), number_format($facts['response_ms']).' ms'],
            [__('HTML size'), $facts['html_kb'].' KB'],
            [__('Images'), $facts['images']],
          ] as [$label, $value])
            <div class="rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-4">
              <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $label }}</dt>
              <dd class="mt-1 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $value }}</dd>
            </div>
          @endforeach
        </dl>
      </div>

      <div class="grid gap-3 md:grid-cols-2">
        @foreach (collect($report['checks'])->sortBy(fn ($c) => ['fail' => 0, 'warn' => 1, 'pass' => 2][$c['status']]) as $check)
          @php [$title, $detail, $suggestion] = $describe($check, $facts); [$icon, $color] = $statusStyles[$check['status']]; @endphp
          <article class="flex gap-4 rounded-2xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-5">
            <i class="fas {{ $icon }} mt-0.5 text-lg {{ $color }}" aria-label="{{ ['pass' => __('Passed'), 'warn' => __('Could be better'), 'fail' => __('Needs attention')][$check['status']] }}"></i>
            <div>
              <h3 class="font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ $detail }}</p>
              @if ($check['status'] !== 'pass')
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200"><i class="fas fa-lightbulb mr-1.5 text-amber-500"></i>{{ $suggestion }}</p>
              @endif
            </div>
          </article>
        @endforeach
      </div>

      <div class="relative overflow-hidden rounded-3xl p-px bg-gradient-to-br from-emerald-400 via-teal-500 to-cyan-600">
        <div class="flex flex-col items-start gap-4 rounded-[calc(1.5rem-1px)] bg-white dark:bg-gray-950 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
          <div>
            <h2 class="text-xl font-black text-gray-900 dark:text-white">{{ __('Want help fixing these?') }}</h2>
            <p class="text-gray-600 dark:text-gray-400">{{ __('I build and optimise Shopify stores and apps. Send me the report and I will suggest next steps.') }}</p>
          </div>
          <a href="{{ lroute('home', ['subject' => __('Store health check: :host', ['host' => $report['host']])]) }}#contact" class="shrink-0 rounded-xl bg-primary-600 px-5 py-3 font-semibold text-white hover:bg-primary-700 transition">
            {{ __('Get in touch') }}
          </a>
        </div>
      </div>

      <p class="text-xs text-gray-500">{{ __('This is a quick automated check of the homepage, not a full audit. Scores are indicative.') }}</p>
    </section>
  @endif
@endsection

@section('tool_about')
  <p>{{ __('The check loads the store homepage and Shopify\'s public meta.json, products.json and collections.json endpoints, the same data any visitor or search engine can see. It then looks at response time, page size, mobile readiness, titles, descriptions, headings, image alt text, lazy loading and social tags.') }}</p>
  <p>{{ __('Each check is weighted and combined into a score out of 100. Nothing is stored except the store address and score, which helps me see which tools are useful.') }}</p>
@endsection
