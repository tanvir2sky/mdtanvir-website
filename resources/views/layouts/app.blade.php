<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
  @php
    $trackingSettings = \Illuminate\Support\Facades\Schema::hasTable('site_settings')
      ? \App\Models\SiteSetting::query()->first()
      : null;
    $gtmEnabled = $trackingSettings?->enable_gtm && filled($trackingSettings?->gtm_id);
    $gaEnabled = $trackingSettings?->enable_ga && filled($trackingSettings?->ga_measurement_id);
    $clarityEnabled = $trackingSettings?->enable_clarity && filled($trackingSettings?->clarity_project_id);
  @endphp
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta
      name="description"
      content="@yield('meta_description', 'MD Tanvir Hossain - Software Engineer specializing in Laravel, PHP, Shopify, and modern web development')"
    />
    <meta
      name="keywords"
      content="@yield('meta_keywords', 'MD Tanvir Hossain, Software Engineer, Laravel, PHP, Shopify, Web Developer')"
    />
    <meta name="author" content="MD Tanvir Hossain" />
    <title>@yield('title', 'MD Tanvir Hossain | Software Engineer')</title>

    @if ($gtmEnabled)
      <!-- Google Tag Manager -->
      <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $trackingSettings->gtm_id }}');
      </script>
    @endif

    @if ($gaEnabled)
      <!-- Google Analytics -->
      <script async src="https://www.googletagmanager.com/gtag/js?id={{ $trackingSettings->ga_measurement_id }}"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $trackingSettings->ga_measurement_id }}');
      </script>
    @endif

    @if ($clarityEnabled)
      <!-- Microsoft Clarity -->
      <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "{{ $trackingSettings->clarity_project_id }}");
      </script>
    @endif

    @vite(["resources/css/app.css", "resources/js/app.js"])
  </head>
  <body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300 antialiased">
    @if ($gtmEnabled)
      <!-- Google Tag Manager (noscript) -->
      <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ $trackingSettings->gtm_id }}"
          height="0" width="0" style="display:none;visibility:hidden"></iframe>
      </noscript>
    @endif

    @yield('content')
  </body>
</html>
