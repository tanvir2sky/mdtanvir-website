{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
  @php
    // Pages that exist in every language, listed once per language with hreflang alternates.
    $localized = [
      ['home', [], 'monthly', '1.0', null],
      ['blog.index', [], 'weekly', '0.8', null],
    ];
    foreach ($projects as $project) {
      $localized[] = ['projects.show', $project->slug, 'monthly', '0.7', $project->updated_at];
    }
  @endphp

  @foreach ($localized as [$name, $params, $changefreq, $priority, $updated])
    @foreach (\App\Support\Locale::supported() as $locale)
      <url>
        <loc>{{ lroute($name, $params, $locale) }}</loc>
        @foreach (\App\Support\Locale::supported() as $alternate)
          <xhtml:link rel="alternate" hreflang="{{ $alternate }}" href="{{ lroute($name, $params, $alternate) }}" />
        @endforeach
        @if ($updated)
          <lastmod>{{ $updated->toAtomString() }}</lastmod>
        @endif
        <changefreq>{{ $changefreq }}</changefreq>
        <priority>{{ $priority }}</priority>
      </url>
    @endforeach
  @endforeach

  {{-- Articles are English-only. --}}
  @foreach ($posts as $post)
    <url>
      <loc>{{ route('blog.show', $post->slug) }}</loc>
      <lastmod>{{ $post->updated_at?->toAtomString() }}</lastmod>
      <priority>0.6</priority>
    </url>
  @endforeach
</urlset>
