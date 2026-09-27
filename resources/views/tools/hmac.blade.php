@extends('tools.layout')

@section('title', __('Shopify webhook HMAC verifier').' | MD Tanvir Hossain')
@section('meta_description', __('Verify a Shopify webhook HMAC signature against the raw request body and your app secret, right in your browser.'))
@section('meta_url', lroute('tools.hmac'))

@section('tool_title', __('Shopify webhook HMAC verifier'))
@section('tool_icon', 'fas fa-shield-halved')
@section('tool_accent', \App\Models\Project::ACCENTS['violet']['tile'])
@section('tool_intro', __('Paste the raw webhook body, the X-Shopify-Hmac-Sha256 header and your app secret to check whether the signature is valid.'))

@php
  $input = 'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-950/70 px-4 py-3 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-primary-500';
@endphp

@section('tool_body')
  <div id="hmac-tool" class="grid gap-6 lg:grid-cols-5">
    <form class="space-y-5 rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6 backdrop-blur-sm lg:col-span-3" data-hmac-form>
      <div>
        <label for="hmac-secret" class="mb-2 block text-sm font-medium">{{ __('App client secret') }}</label>
        <div class="relative">
          <input id="hmac-secret" type="password" autocomplete="off" spellcheck="false" class="{{ $input }} pr-12" data-hmac-secret placeholder="shpss_…" />
          <button type="button" data-hmac-reveal aria-label="{{ __('Show secret') }}" class="absolute right-2 top-1/2 grid h-8 w-8 -translate-y-1/2 place-items-center rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-white/5">
            <i class="fas fa-eye"></i>
          </button>
        </div>
      </div>

      <div>
        <label for="hmac-header" class="mb-2 block text-sm font-medium">{{ __('X-Shopify-Hmac-Sha256 header') }}</label>
        <input id="hmac-header" type="text" autocomplete="off" spellcheck="false" class="{{ $input }}" data-hmac-header placeholder="XWmrwMey6OsLMeiZKwP4FppHH3cmAiiJJAweH5Jo4bM=" />
      </div>

      <div>
        <label for="hmac-body" class="mb-2 block text-sm font-medium">{{ __('Raw request body') }}</label>
        <textarea id="hmac-body" rows="9" spellcheck="false" class="{{ $input }}" data-hmac-body placeholder='{"id":820982911946154508,"email":"jon@example.com"}'></textarea>
        <p class="mt-1 text-xs text-gray-500">{{ __('Use the exact bytes Shopify sent. Re-formatted JSON produces a different signature.') }}</p>
      </div>

      <button type="submit" class="w-full rounded-xl bg-primary-600 px-5 py-3 font-semibold text-white hover:bg-primary-700 transition">
        {{ __('Verify signature') }}
      </button>
      <p class="flex items-center gap-2 text-xs text-gray-500">
        <i class="fas fa-lock"></i>{{ __('Everything runs in your browser. Nothing you enter is sent to a server.') }}
      </p>
    </form>

    <div class="space-y-5 lg:col-span-2">
      <div data-hmac-result aria-live="polite" class="rounded-3xl border border-dashed border-gray-300 dark:border-white/15 p-6 text-center text-gray-500">
        <i class="fas fa-fingerprint mb-3 text-3xl"></i>
        <p>{{ __('The result appears here.') }}</p>
      </div>

      <div class="rounded-3xl border border-gray-200/80 dark:border-white/10 bg-white/70 dark:bg-white/[0.03] p-6">
        <div class="mb-3 flex items-center justify-between">
          <h2 class="font-bold text-gray-900 dark:text-white">{{ __('Laravel verification snippet') }}</h2>
          <button type="button" data-copy-snippet class="rounded-lg border border-gray-200 dark:border-white/10 px-2.5 py-1 text-xs font-semibold hover:border-primary-500">{{ __('Copy') }}</button>
        </div>
        <pre class="overflow-x-auto rounded-xl bg-[#0b1120] p-4 font-mono text-xs leading-relaxed text-gray-200" data-snippet>$expected = base64_encode(hash_hmac(
    'sha256',
    $request->getContent(),
    config('services.shopify.secret'),
    true
));

abort_unless(hash_equals(
    $expected,
    (string) $request->header('X-Shopify-Hmac-Sha256')
), 401);</pre>
      </div>
    </div>
  </div>
@endsection

@section('tool_about')
  <p>{{ __('Shopify signs every webhook with your app\'s client secret. It computes an HMAC-SHA256 of the raw request body and sends it, base64-encoded, in the X-Shopify-Hmac-Sha256 header. Your app should compute the same value and compare the two in constant time before trusting the payload.') }}</p>
  <p>{{ __('The most common reason for a mismatch is hashing parsed and re-encoded JSON instead of the raw body. Whitespace, key order and escaping all change the signature.') }}</p>
  <p><a href="{{ lroute('blog.show', 'shopify-webhooks-in-laravel') }}">{{ __('Read the full guide to handling Shopify webhooks in Laravel') }}</a></p>
@endsection
