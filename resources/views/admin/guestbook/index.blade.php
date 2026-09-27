@extends('admin.layouts.app')

@section('title', 'Guestbook | Admin')

@section('content')
  <div class="mb-6">
    <h1 class="text-3xl font-black">Guestbook</h1>
    <p class="text-gray-600 dark:text-gray-400">Notes appear on the public guestbook only after you approve them.</p>
  </div>

  <nav class="mb-5 flex flex-wrap gap-2 text-sm">
    @foreach (['all' => null, 'pending' => 'pending', 'approved' => 'approved'] as $label => $value)
      <a href="{{ route('admin.guestbook.index', array_filter(['status' => $value])) }}"
        class="px-4 py-2 rounded-xl font-semibold {{ $status === $value ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800' }}">
        {{ ucfirst($label) }} <span class="ml-1 opacity-60">{{ $counts[$label] }}</span>
      </a>
    @endforeach
  </nav>

  <div class="space-y-3">
    @forelse ($entries as $entry)
      <article class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div class="min-w-0 flex-1">
            <p class="font-bold">
              {{ $entry->name }}
              @if ($entry->approved_at)
                <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">Approved</span>
              @else
                <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">Pending</span>
              @endif
            </p>
            <p class="text-xs text-gray-500">{{ $entry->created_at->format('M d, Y H:i') }} · {{ strtoupper($entry->locale) }} @if ($entry->website) · {{ $entry->website }} @endif</p>
            <p class="mt-3 whitespace-pre-line break-words text-gray-700 dark:text-gray-300">{{ $entry->message }}</p>
          </div>
          <div class="flex gap-2">
            @if ($entry->approved_at)
              <form method="POST" action="{{ route('admin.guestbook.unapprove', $entry) }}">@csrf @method('PATCH')
                <button class="px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800">Hide</button>
              </form>
            @else
              <form method="POST" action="{{ route('admin.guestbook.approve', $entry) }}">@csrf @method('PATCH')
                <button class="px-3 py-1.5 rounded-lg bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 font-semibold">Approve</button>
              </form>
            @endif
            <form method="POST" action="{{ route('admin.guestbook.destroy', $entry) }}" onsubmit="return confirm('Delete this note?');">@csrf @method('DELETE')
              <button class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Delete</button>
            </form>
          </div>
        </div>
      </article>
    @empty
      <p class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center text-gray-500">No notes here.</p>
    @endforelse
  </div>

  <div class="mt-6">{{ $entries->links() }}</div>
@endsection
