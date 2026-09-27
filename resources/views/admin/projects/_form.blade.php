@php
  $project ??= null;
  $input = 'w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none';
@endphp

<div class="space-y-8">
  {{-- Card --}}
  <section class="space-y-6">
    <h2 class="text-lg font-bold border-b border-gray-200 dark:border-gray-800 pb-2">Card</h2>

    @include('admin.partials.translatable-field', ['name' => 'title', 'label' => 'Title', 'value' => $project?->title, 'required' => true])
    @include('admin.partials.translatable-field', ['name' => 'category', 'label' => 'Category label', 'value' => $project?->category, 'placeholder' => 'e.g. E-commerce'])
    @include('admin.partials.translatable-field', ['name' => 'summary', 'label' => 'Short description', 'type' => 'textarea', 'rows' => 3, 'value' => $project?->summary, 'required' => true])

    <div class="grid sm:grid-cols-3 gap-5">
      <div>
        <label for="icon" class="block mb-2 text-sm font-medium">Icon</label>
        <input id="icon" name="icon" type="text" required value="{{ old('icon', $project?->icon ?? 'fas fa-cube') }}" class="{{ $input }} font-mono text-sm" />
        <p class="mt-1 text-xs text-gray-500">Font Awesome class.</p>
      </div>
      <div>
        <label for="accent" class="block mb-2 text-sm font-medium">Accent colour</label>
        <select id="accent" name="accent" class="{{ $input }}">
          @foreach (array_keys(\App\Models\Project::ACCENTS) as $accent)
            <option value="{{ $accent }}" @selected(old('accent', $project?->accent ?? 'sky') === $accent)>{{ ucfirst($accent) }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label for="slug" class="block mb-2 text-sm font-medium">URL slug</label>
        <input id="slug" name="slug" type="text" value="{{ old('slug', $project?->slug) }}" placeholder="generated from title" class="{{ $input }} font-mono text-sm" />
        @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    </div>

    <div>
      <label for="tags" class="block mb-2 text-sm font-medium">Tech stack</label>
      <input id="tags" name="tags" type="text" value="{{ old('tags', implode(', ', $project?->tags ?? [])) }}" placeholder="Laravel, Shopify API, MySQL" class="{{ $input }}" />
      <p class="mt-1 text-xs text-gray-500">Comma-separated.</p>
    </div>

    <div class="flex flex-wrap gap-6">
      <label class="flex items-center gap-2">
        <input type="checkbox" name="is_visible" value="1" @checked(old('is_visible', $project?->is_visible ?? true)) />
        <span>Show on home page</span>
      </label>
      <label class="flex items-center gap-2">
        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $project?->is_featured)) />
        <span>Featured <span class="text-xs text-gray-500">(wide card)</span></span>
      </label>
    </div>
  </section>

  {{-- Case study --}}
  <section class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 dark:border-gray-800 pb-2">
      <h2 class="text-lg font-bold">Case study page</h2>
      <label class="flex items-center gap-2 font-semibold">
        <input type="checkbox" name="case_study_published" value="1" @checked(old('case_study_published', $project?->case_study_published)) />
        <span>Published <span class="text-xs font-normal text-gray-500">(the card links to it once published)</span></span>
      </label>
    </div>

    @include('admin.partials.translatable-field', ['name' => 'role', 'label' => 'My role', 'value' => $project?->role, 'placeholder' => 'e.g. Lead backend engineer'])

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <div>
        <label for="year" class="block mb-2 text-sm font-medium">Year</label>
        <input id="year" name="year" type="text" value="{{ old('year', $project?->year) }}" placeholder="2024" class="{{ $input }}" />
      </div>
      <div>
        <label for="duration" class="block mb-2 text-sm font-medium">Duration</label>
        <input id="duration" name="duration" type="text" value="{{ old('duration', $project?->duration) }}" placeholder="6 months" class="{{ $input }}" />
      </div>
      <div>
        <label for="live_url" class="block mb-2 text-sm font-medium">Live URL</label>
        <input id="live_url" name="live_url" type="url" value="{{ old('live_url', $project?->live_url) }}" placeholder="https://" class="{{ $input }}" />
        @error('live_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
      <div>
        <label for="repo_url" class="block mb-2 text-sm font-medium">Repository URL</label>
        <input id="repo_url" name="repo_url" type="url" value="{{ old('repo_url', $project?->repo_url) }}" placeholder="https://github.com/…" class="{{ $input }}" />
        @error('repo_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>
    </div>

    <div>
      <label for="cover_image" class="block mb-2 text-sm font-medium">Cover image</label>
      <input id="cover_image" name="cover_image" type="file" accept="image/*" class="{{ $input }}" />
      @error('cover_image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      @if ($project?->cover_image)
        <div class="mt-3 flex items-center gap-4">
          <img src="{{ $project->coverUrl() }}" alt="" class="h-24 w-auto rounded-lg border border-gray-200 dark:border-gray-700" />
          <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remove_cover_image" value="1" /> Remove image</label>
        </div>
      @else
        <p class="mt-1 text-xs text-gray-500">Without an image, a cover is generated from the accent colour and icon.</p>
      @endif
    </div>

    @include('admin.partials.translatable-field', ['name' => 'challenge', 'label' => 'The challenge', 'type' => 'textarea', 'rows' => 4, 'value' => $project?->challenge])
    @include('admin.partials.translatable-field', ['name' => 'approach', 'label' => 'My approach', 'type' => 'textarea', 'rows' => 4, 'value' => $project?->approach])
    @include('admin.partials.translatable-field', ['name' => 'outcome', 'label' => 'The outcome', 'type' => 'textarea', 'rows' => 4, 'value' => $project?->outcome])
    @include('admin.partials.translatable-field', [
      'name' => 'body', 'label' => 'Full write-up', 'type' => 'editor', 'value' => $project?->body,
      'help' => 'Use headings (H2/H3) to build the table of contents. Add screenshots with the picture button.',
    ])
  </section>

  <div class="flex items-center gap-3 pt-3">
    <button type="submit" class="px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold">{{ $submitLabel }}</button>
    <a href="{{ route('admin.projects.index') }}" class="px-5 py-3 rounded-xl border border-gray-300 dark:border-gray-700 font-semibold">Cancel</a>
  </div>
</div>
