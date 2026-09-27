@php
  $siteUrl = rtrim(config('app.url'), '/');
  $defaultTitle = 'MD Tanvir Hossain | '.__('Software Engineer');
  $defaultDescription = __('MD Tanvir Hossain - Software Engineer specializing in Laravel, PHP, Shopify, AI/LLM integration, and modern web development');
  $currentLocale = \App\Support\Locale::current();
  // Blog articles are English-only, so they get no language alternates (their canonical is the English URL).
  $alternates = request()->routeIs('blog.show', 'de.blog.show') ? [] : \App\Support\Locale::alternates();
  $defaultImage = asset('img/profile.jpg');

  $seoTitle = trim($__env->yieldContent('title')) ?: $defaultTitle;
  $seoDescription = trim($__env->yieldContent('meta_description')) ?: $defaultDescription;
  $seoImage = trim($__env->yieldContent('meta_image')) ?: $defaultImage;
  $seoType = trim($__env->yieldContent('meta_type')) ?: 'website';
  $seoUrl = trim($__env->yieldContent('meta_url')) ?: url()->current();

  $seoImage = str_starts_with($seoImage, 'http') ? $seoImage : url($seoImage);
  $personId = $siteUrl . '/#person';
  $websiteId = $siteUrl . '/#website';

  $structuredData = [
    '@context' => 'https://schema.org',
    '@graph' => [
      [
        '@type' => 'Person',
        '@id' => $personId,
        'name' => 'MD Tanvir Hossain',
        'url' => $siteUrl,
        'image' => url(asset('img/profile.jpg')),
        'jobTitle' => 'Software Engineer',
        'worksFor' => ['@type' => 'Organization', 'name' => 'Altruan GmbH', 'url' => 'https://altruan.de'],
        'description' => $defaultDescription,
        'email' => 'contact@mdtanvir.com',
        'sameAs' => [
          'https://www.linkedin.com/in/tanvir-cs',
          'https://github.com/tanvir-cs',
        ],
        'knowsAbout' => [
          'Laravel',
          'PHP',
          'Shopify',
          'Large Language Models',
          'AI Integration',
          'Web Development',
          'Software Architecture',
        ],
      ],
      [
        '@type' => 'WebSite',
        '@id' => $websiteId,
        'url' => $siteUrl,
        'name' => 'MD Tanvir Hossain',
        'description' => $defaultDescription,
        'publisher' => ['@id' => $personId],
        'inLanguage' => ['en', 'de'],
      ],
    ],
  ];

  if (request()->routeIs('home', 'de.home')) {
    $structuredData['@graph'][] = [
      '@type' => 'ProfilePage',
      '@id' => $siteUrl . '/#profilepage',
      'url' => $siteUrl,
      'name' => $seoTitle,
      'description' => $seoDescription,
      'isPartOf' => ['@id' => $websiteId],
      'mainEntity' => ['@id' => $personId],
    ];
  }
@endphp

<link rel="canonical" href="{{ $seoUrl }}" />
@foreach ($alternates as $code => $alternateUrl)
  <link rel="alternate" hreflang="{{ $code }}" href="{{ $alternateUrl }}" />
@endforeach
@if (isset($alternates['en']))
  <link rel="alternate" hreflang="x-default" href="{{ $alternates['en'] }}" />
@endif
<meta name="robots" content="@yield('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1')" />

<meta property="og:locale" content="{{ \App\Support\Locale::ogLocale() }}" />
@foreach (\App\Support\Locale::supported() as $code)
  @if ($code !== $currentLocale)
    <meta property="og:locale:alternate" content="{{ \App\Support\Locale::ogLocale($code) }}" />
  @endif
@endforeach
<meta property="og:type" content="{{ $seoType }}" />
<meta property="og:site_name" content="MD Tanvir Hossain" />
<meta property="og:title" content="{{ $seoTitle }}" />
<meta property="og:description" content="{{ $seoDescription }}" />
<meta property="og:url" content="{{ $seoUrl }}" />
<meta property="og:image" content="{{ $seoImage }}" />
<meta property="og:image:alt" content="MD Tanvir Hossain - {{ __('Software Engineer') }}" />
@if ($seoType === 'article' && ($articlePublished = trim($__env->yieldContent('article_published_time'))))
  <meta property="article:published_time" content="{{ $articlePublished }}" />
  <meta property="article:author" content="MD Tanvir Hossain" />
@endif

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seoTitle }}" />
<meta name="twitter:description" content="{{ $seoDescription }}" />
<meta name="twitter:image" content="{{ $seoImage }}" />
<meta name="twitter:image:alt" content="MD Tanvir Hossain - {{ __('Software Engineer') }}" />

<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) !!}
</script>

@stack('structured_data')
