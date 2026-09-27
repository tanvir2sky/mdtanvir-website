@extends('admin.layouts.app')

@section('title', 'Skills | Admin')

@section('content')
  <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
      <h1 class="text-3xl font-black">Skills</h1>
      <p class="text-gray-600 dark:text-gray-400">The skill cards on the home page, in display order.</p>
    </div>
    <a href="{{ route('admin.skills.create') }}" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition">
      <i class="fas fa-plus mr-2"></i>Add Skill Group
    </a>
  </div>

  <div class="space-y-3">
    @forelse ($skillGroups as $group)
      <div class="flex items-center gap-4 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
        @include('admin.partials.move-buttons', ['route' => 'admin.skills.move', 'model' => $group, 'first' => $loop->first, 'last' => $loop->last])
        <span class="grid h-11 w-11 place-items-center rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-300"><i class="{{ $group->icon }}"></i></span>
        <div class="flex-1 min-w-0">
          <p class="font-bold">{{ $group->t('name', 'en') }}</p>
          <p class="truncate text-sm text-gray-500">{{ implode(', ', $group->items['en'] ?? []) }}</p>
        </div>
        <a href="{{ route('admin.skills.edit', $group) }}" class="px-3 py-1.5 rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">Edit</a>
        <form method="POST" action="{{ route('admin.skills.destroy', $group) }}" onsubmit="return confirm('Delete this skill group?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Delete</button>
        </form>
      </div>
    @empty
      <p class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center text-gray-500">No skill groups yet.</p>
    @endforelse
  </div>
@endsection
