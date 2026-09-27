<x-mail::message>
# {{ __('About your call request') }}

{{ __('Hi :name, unfortunately I cannot make the call on :when.', ['name' => $booking->name, 'when' => $when->locale(app()->getLocale())->translatedFormat('l, j F Y · H:i')]) }}

@if ($booking->decline_reason)
<x-mail::panel>
{{ $booking->decline_reason }}
</x-mail::panel>
@endif

{{ __('Feel free to pick another time or send me a message instead.') }}

<x-mail::button :url="$bookUrl">
{{ __('Find another time') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
MD Tanvir Hossain
</x-mail::message>
