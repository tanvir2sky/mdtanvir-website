<x-mail::message>
@if ($forOwner)
# Call confirmed

**{{ $booking->name }}** ({{ $booking->email }})

**{{ $when->format('l, j F Y · H:i') }}** ({{ $when->tzName }}) · their time zone: {{ $booking->visitor_timezone }}

<x-mail::panel>
{{ $booking->topic }}
</x-mail::panel>

The calendar invite is attached.
@else
# {{ __('Your call is confirmed') }}

{{ __('Looking forward to talking, :name! Here are the details:', ['name' => $booking->name]) }}

**{{ $when->locale(app()->getLocale())->translatedFormat('l, j F Y · H:i') }}** ({{ $booking->visitor_timezone }})

@if ($meetingUrl)
<x-mail::button :url="$meetingUrl">
{{ __('Join the call') }}
</x-mail::button>
@else
{{ __('I will send you the meeting link before the call.') }}
@endif

{{ __('A calendar invite is attached, so you can add the call to your calendar.') }}

{{ __('Need to cancel?') }} [{{ __('Cancel the call') }}]({{ $cancelUrl }})

{{ __('Thanks,') }}<br>
MD Tanvir Hossain
@endif
</x-mail::message>
