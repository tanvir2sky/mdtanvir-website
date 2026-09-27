@php
  $post = $post ?? null;
@endphp

<div class="space-y-5">
  <div>
    <label for="title" class="block mb-2 text-sm font-medium">Title</label>
    <input id="title" name="title" type="text" required value="{{ old('title', $post?->title) }}"
      class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
    @error('title')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div class="grid sm:grid-cols-2 gap-5">
    <div>
      <label for="category" class="block mb-2 text-sm font-medium">Category</label>
      <input id="category" name="category" type="text" list="category-options" maxlength="60"
        value="{{ old('category', $post?->category) }}" placeholder="e.g. Laravel"
        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
      <datalist id="category-options">
        @foreach (array_keys(config('blog.categories')) as $categoryOption)
          <option value="{{ $categoryOption }}"></option>
        @endforeach
      </datalist>
      <p class="mt-1 text-xs text-gray-500">Pick a suggestion to get its colour and icon, or type a new one.</p>
      @error('category')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>
    <div>
      <label for="tags" class="block mb-2 text-sm font-medium">Tags</label>
      <input id="tags" name="tags" type="text"
        value="{{ old('tags', implode(', ', $post?->tags ?? [])) }}" placeholder="queues, redis, horizon"
        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
      <p class="mt-1 text-xs text-gray-500">Comma-separated.</p>
      @error('tags')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>
  </div>

  <div>
    <label for="excerpt" class="block mb-2 text-sm font-medium">Excerpt</label>
    <textarea id="excerpt" name="excerpt" rows="3"
      class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">{{ old('excerpt', $post?->excerpt) }}</textarea>
    @error('excerpt')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div>
    <label for="content" class="block mb-2 text-sm font-medium">Content</label>
    <textarea id="content" name="content" rows="14" required
      class="js-summernote w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">{{ old('content', $post?->content) }}</textarea>
    @error('content')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div class="grid sm:grid-cols-2 gap-5">
    <div>
      <label for="meta_title" class="block mb-2 text-sm font-medium">Meta Title</label>
      <input id="meta_title" name="meta_title" type="text" value="{{ old('meta_title', $post?->meta_title) }}"
        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
      @error('meta_title')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>
    <div>
      <label for="published_at" class="block mb-2 text-sm font-medium">Publish Date/Time</label>
      <input id="published_at" name="published_at" type="datetime-local"
        value="{{ old('published_at', $post?->published_at?->format('Y-m-d\\TH:i')) }}"
        class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
      @error('published_at')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
      @enderror
    </div>
  </div>

  <div>
    <label for="meta_description" class="block mb-2 text-sm font-medium">Meta Description</label>
    <textarea id="meta_description" name="meta_description" rows="3"
      class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none">{{ old('meta_description', $post?->meta_description) }}</textarea>
    @error('meta_description')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
  </div>

  <div>
    <label for="featured_image" class="block mb-2 text-sm font-medium">Featured Image</label>
    <input id="featured_image" name="featured_image" type="file" accept="image/*"
      class="w-full rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:outline-none" />
    @error('featured_image')
      <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror

    @if ($post?->featured_image)
      <div class="mt-3">
        <p class="text-xs text-gray-500 mb-2">Current image</p>
        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="h-28 w-auto rounded-lg border border-gray-200 dark:border-gray-700" />
      </div>
    @endif
  </div>

  <div class="flex flex-wrap items-center gap-6">
    <label class="flex items-center gap-2">
      <input type="checkbox" name="is_published" value="1" {{ old('is_published', $post?->is_published) ? 'checked' : '' }} />
      <span>Published</span>
    </label>
    <label class="flex items-center gap-2">
      <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $post?->is_featured) ? 'checked' : '' }} />
      <span>Featured <span class="text-xs text-gray-500">(the latest featured post is shown large at the top of the blog)</span></span>
    </label>
    @if ($post?->newsletter_sent_at)
      <span class="flex items-center gap-2 text-sm text-gray-500">
        <i class="fas fa-paper-plane text-green-600"></i>
        Emailed to subscribers on {{ $post->newsletter_sent_at->format('M d, Y H:i') }}
      </span>
    @else
      <label class="flex items-center gap-2">
        <input type="checkbox" name="notify_subscribers" value="1" {{ old('notify_subscribers', $post?->notify_subscribers ?? true) ? 'checked' : '' }} />
        <span>Email subscribers when published <span class="text-xs text-gray-500">(sent once, as soon as the post is live)</span></span>
      </label>
    @endif
  </div>

  <div class="flex items-center gap-3 pt-3">
    <button type="submit" class="px-5 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold">
      {{ $submitLabel }}
    </button>
    <a href="{{ route('admin.posts.index') }}" class="px-5 py-3 rounded-xl border border-gray-300 dark:border-gray-700 font-semibold">
      Cancel
    </a>
  </div>
</div>
