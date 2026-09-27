@extends('admin.layouts.app')

@section('title', 'Experience | Admin')

@section('content')
  <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
      <h1 class="text-3xl font-black">Experience</h1>
      <p class="text-gray-600 dark:text-gray-400">The career timeline on the home page. The role marked "current" is shown as the featured card.</p>
    </div>
    <a href="{{ route('admin.experiences.create') }}" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition">
      <i class="fas fa-plus mr-2"></i>Add Experience
    </a>
  </div>

  <div class="space-y-3">
    @forelse ($experiences as $experience)
      <div class="flex items-center gap-4 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
        @include('admin.partials.move-buttons', ['route' => 'admin.experiences.move', 'model' => $experience, 'first' => $loop->first, 'last' => $loop->last])
        <div class="flex-1 min-w-0">
          <p class="font-bold">
            {{ $experience->t('role', 'en') }} <span class="font-normal text-gray-500">at</span> {{ $experience->company }}
            @if ($experience->is_current)
              <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Current</span>
            @endif
          </p>
          <p class="text-sm text-gray-500">
            {{ $experience->period }} ·
            {{ count($experience->highlights['en'] ?? []) }} highlights ·
            German: {{ $experience->hasTranslation('role', 'de') ? 'yes' : 'no' }}
          </p>
        </div>
        <a href="{{ route('admin.experiences.edit', $experience) }}" class="px-3 py-1.5 rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300">Edit</a>
        <form method="POST" action="{{ route('admin.experiences.destroy', $experience) }}" onsubmit="return confirm('Delete this experience?');">
          @csrf
          @method('DELETE')
          <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Delete</button>
        </form>
      </div>
    @empty
      <p class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center text-gray-500">No experience yet.</p>
    @endforelse
  </div>
@endsection
