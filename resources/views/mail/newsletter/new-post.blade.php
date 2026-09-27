<x-mail::message>
@if ($post->category)
**{{ $post->category }}** · {{ __(':minutes min read', ['minutes' => $post->readingTime()]) }}
@endif

# {{ $post->title }}

{{ $post->summary(300) }}

<x-mail::button :url="$postUrl">
{{ __('Read the article') }}
</x-mail::button>

{{ __('Thanks for reading,') }}<br>
MD Tanvir Hossain

<x-mail::subcopy>
{{ __('You are receiving this because you subscribed to new articles on my blog.') }} [{{ __('Unsubscribe') }}]({{ $unsubscribeUrl }})
</x-mail::subcopy>
</x-mail::message>
