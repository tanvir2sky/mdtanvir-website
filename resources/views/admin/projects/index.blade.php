@extends('admin.layouts.app')

@section('title', 'Projects | Admin')

@section('content')
  <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
      <h1 class="text-3xl font-black">Projects</h1>
      <p class="text-gray-600 dark:text-gray-400">Cards in the home page "Work highlights" grid, and their case-study pages.</p>
    </div>
    <a href="{{ route('admin.projects.create') }}" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition">
      <i class="fas fa-plus mr-2"></i>Add Project
    </a>
  </div>

  <div class="space-y-3">
    @forelse ($projects as $project)
      @php $accent = $project->accentStyle(); @endphp
      <div class="flex items-center gap-4 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
        @include('admin.partials.move-buttons', ['route' => 'admin.projects.move', 'model' => $project, 'first' => $loop->first, 'last' => $loop->last])
        <span class="grid h-11 w-11 place-items-center rounded-xl ring-1 {{ $accent['tile'] }}"><i class="{{ $project->icon }}"></i></span>
        <div class="flex-1 min-w-0">
          <p class="font-bold">
            {{ $project->t('title', 'en') }}
            @if ($project->is_featured)
              <i class="fas fa-star ml-1 text-amber-500" title="Featured (wide card)"></i>
            @endif
          </p>
          <p class="text-sm text-gray-500">
            {{ $project->t('category', 'en') ?? 'No category' }} ·
            @if (! $project->is_visible)
              <span class="font-semibold text-gray-600 dark:text-gray-300">Hidden</span> ·
            @endif
            Case study:
            @if ($project->case_study_published)
              <a href="{{ route('projects.show', $project->slug) }}" target="_blank" class="font-semibold text-green-600 hover:underline">published</a>
            @else
              <span class="font-semibold text-amber-600">draft</span>
            @endif
          </p>
        </div>
        <a href="{{ route('admin.projects.edit', $project) }}" class="px-3 py-1.5 rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">Edit</a>
        <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" onsubmit="return confirm('Delete this project?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Delete</button>
        </form>
      </div>
    @empty
      <p class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center text-gray-500">No projects yet.</p>
    @endforelse
  </div>
@endsection
