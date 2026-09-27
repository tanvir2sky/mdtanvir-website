{{--
  English + German inputs for one translatable field.
  Params: $name, $label, $value (array keyed by locale), $type (text|textarea|lines|editor),
          $required (English required), $rows, $help, $placeholder
--}}
@php
  $type = $type ?? 'text';
  $rows = $rows ?? 3;
  $required = $required ?? false;
  $value = $value ?? [];
  $locales = ['en' => 'English', 'de' => 'Deutsch'];
  $inputClass = 'w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none';
@endphp

<fieldset>
  <legend class="block mb-2 text-sm font-medium">{{ $label }}</legend>
  <div class="grid gap-3 {{ $type === 'editor' ? '' : 'lg:grid-cols-2' }}">
    @foreach ($locales as $code => $localeName)
      @php
        $current = $value[$code] ?? null;
        $current = is_array($current) ? implode("\n", $current) : $current;
        $fieldValue = old("{$name}.{$code}", $current);
        $id = str_replace(['[', ']'], ['-', ''], $name)."-{$code}";
      @endphp
      <div>
        <label for="{{ $id }}" class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-gray-500">
          <span class="rounded bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 font-mono">{{ strtoupper($code) }}</span>
          {{ $localeName }}
          @if ($code !== 'en')
            <span class="normal-case tracking-normal font-normal">(optional, falls back to English)</span>
          @endif
        </label>

        @if ($type === 'text')
          <input id="{{ $id }}" name="{{ $name }}[{{ $code }}]" type="text" value="{{ $fieldValue }}"
            placeholder="{{ $placeholder ?? '' }}" @required($required && $code === 'en') class="{{ $inputClass }}" />
        @else
          <textarea id="{{ $id }}" name="{{ $name }}[{{ $code }}]" rows="{{ $rows }}"
            placeholder="{{ $placeholder ?? '' }}" @required($required && $code === 'en' && $type !== 'editor')
            class="{{ $inputClass }} {{ $type === 'editor' ? 'js-summernote' : '' }} {{ $type === 'lines' ? 'font-mono text-sm' : '' }}">{{ $fieldValue }}</textarea>
        @endif

        @error("{$name}.{$code}")
          <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
      </div>
    @endforeach
  </div>
  @if (! empty($help))
    <p class="mt-1 text-xs text-gray-500">{{ $help }}</p>
  @endif
</fieldset>
