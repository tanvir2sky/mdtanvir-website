<x-mail::message>
# {{ __('Thanks for your request, :name!', ['name' => $booking->name]) }}

{{ __('I received your request for a :minutes-minute call on:', ['minutes' => config('booking.slot_minutes', 30)]) }}

**{{ $when->locale(app()->getLocale())->translatedFormat('l, j F Y · H:i') }}** ({{ $booking->visitor_timezone }})

{{ __('I will confirm it by email shortly, with a calendar invite and the meeting link.') }}

<x-mail::panel>
{{ $booking->topic }}
</x-mail::panel>

{{ __('Plans changed?') }} [{{ __('Cancel this request') }}]({{ $booking->cancelUrl() }})

{{ __('Thanks,') }}<br>
MD Tanvir Hossain
</x-mail::message>
