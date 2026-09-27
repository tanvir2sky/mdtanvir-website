{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>MD Tanvir Hossain | Blog</title>
    <link>{{ route('blog.index') }}</link>
    <description>Insights on Laravel, PHP, Shopify, AI and modern web engineering by MD Tanvir Hossain.</description>
    <language>en</language>
    <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
    @if ($posts->isNotEmpty())
      <lastBuildDate>{{ $posts->first()->published_at->toRssString() }}</lastBuildDate>
    @endif
    @foreach ($posts as $post)
      <item>
        <title>{{ $post->title }}</title>
        <link>{{ route('blog.show', $post->slug) }}</link>
        <guid isPermaLink="true">{{ route('blog.show', $post->slug) }}</guid>
        <pubDate>{{ $post->published_at->toRssString() }}</pubDate>
        <description>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content), 300) }}</description>
      </item>
    @endforeach
  </channel>
</rss>
