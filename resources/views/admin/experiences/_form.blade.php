@php
  $experience ??= null;
  $input = 'w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none';
  $focusValue = collect($experience?->focus ?? [])
    ->map(fn ($items) => collect($items)->map(fn ($item) => "{$item['icon']} | {$item['title']} | {$item['text']}")->all())
    ->all();
@endphp

<div class="space-y-6">
  <div class="grid sm:grid-cols-3 gap-5">
    <div>
      <label for="company" class="block mb-2 text-sm font-medium">Company</label>
      <input id="company" name="company" type="text" required value="{{ old('company', $experience?->company) }}" class="{{ $input }}" />
      @error('company') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
      <label for="url" class="block mb-2 text-sm font-medium">Company website</label>
      <input id="url" name="url" type="url" value="{{ old('url', $experience?->url) }}" placeholder="https://" class="{{ $input }}" />
      @error('url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
      <label for="period" class="block mb-2 text-sm font-medium">Period</label>
      <input id="period" name="period" type="text" required value="{{ old('period', $experience?->period) }}" placeholder="2020 - 2025" class="{{ $input }}" />
      <p class="mt-1 text-xs text-gray-500">Use "YYYY - YYYY" to show the length automatically.</p>
      @error('period') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
  </div>

  <label class="flex items-center gap-2">
    <input type="checkbox" name="is_current" value="1" @checked(old('is_current', $experience?->is_current)) />
    <span>Current role <span class="text-xs text-gray-500">(shown as the large featured card)</span></span>
  </label>

  @include('admin.partials.translatable-field', ['name' => 'role', 'label' => 'Role / job title', 'value' => $experience?->role, 'required' => true])

  @include('admin.partials.translatable-field', [
    'name' => 'highlights', 'label' => 'Highlights', 'type' => 'lines', 'rows' => 6,
    'value' => $experience?->highlights, 'help' => 'One bullet point per line.',
  ])

  @include('admin.partials.translatable-field', [
    'name' => 'focus', 'label' => 'Focus areas (current role only)', 'type' => 'lines', 'rows' => 4,
    'value' => $focusValue,
    'placeholder' => 'fas fa-brain | AI features | LLM-powered features in production.',
    'help' => 'One per line as "icon | title | short text". Icons are Font Awesome classes.',
  ])

  <div>
    <label for="tags" class="block mb-2 text-sm font-medium">Tech used</label>
    <input id="tags" name="tags" type="text" value="{{ old('tags', implode(', ', $experience?->tags ?? [])) }}" placeholder="Laravel, PHP, Shopify" class="{{ $input }}" />
    <p class="mt-1 text-xs text-gray-500">Comma-separated.</p>
  </div>

  <div class="flex items-center gap-3 pt-3">
    <button type="submit" class="px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold">{{ $submitLabel }}</button>
    <a href="{{ route('admin.experiences.index') }}" class="px-5 py-3 rounded-xl border border-gray-300 dark:border-gray-700 font-semibold">Cancel</a>
  </div>
</div>
