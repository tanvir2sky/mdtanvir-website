@php
  $skillGroup ??= null;
@endphp

<div class="space-y-6">
  <div>
    <label for="icon" class="block mb-2 text-sm font-medium">Icon</label>
    <input id="icon" name="icon" type="text" required value="{{ old('icon', $skillGroup?->icon ?? 'fas fa-code') }}"
      class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 font-mono text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none" />
    <p class="mt-1 text-xs text-gray-500">A Font Awesome class, e.g. <code>fas fa-server</code>, <code>fab fa-laravel</code>, <code>fas fa-robot</code>.</p>
    @error('icon') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
  </div>

  @include('admin.partials.translatable-field', ['name' => 'name', 'label' => 'Group name', 'value' => $skillGroup?->name, 'required' => true])

  @include('admin.partials.translatable-field', [
    'name' => 'items', 'label' => 'Skills', 'type' => 'lines', 'rows' => 6, 'required' => true,
    'value' => $skillGroup?->items, 'help' => 'One skill per line.',
  ])

  <div class="flex items-center gap-3 pt-3">
    <button type="submit" class="px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold">{{ $submitLabel }}</button>
    <a href="{{ route('admin.skills.index') }}" class="px-5 py-3 rounded-xl border border-gray-300 dark:border-gray-700 font-semibold">Cancel</a>
  </div>
</div>
