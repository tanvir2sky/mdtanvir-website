<x-mail::message>
# {{ __('Confirm your subscription') }}

{{ __('Thanks for signing up to my newsletter! Please confirm your email address so I can send you new articles on Laravel, Shopify and AI engineering.') }}

<x-mail::button :url="$confirmUrl">
{{ __('Confirm subscription') }}
</x-mail::button>

{{ __('This link is valid for 7 days. If you did not sign up, just ignore this email and you will not hear from me again.') }}

{{ __('Thanks,') }}<br>
MD Tanvir Hossain
</x-mail::message>
