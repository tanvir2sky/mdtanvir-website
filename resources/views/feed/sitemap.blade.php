{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>{{ route('home') }}</loc>
    <changefreq>monthly</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>{{ route('blog.index') }}</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
  @foreach ($posts as $post)
    <url>
      <loc>{{ route('blog.show', $post->slug) }}</loc>
      <lastmod>{{ $post->updated_at?->toAtomString() }}</lastmod>
      <priority>0.6</priority>
    </url>
  @endforeach
</urlset>
