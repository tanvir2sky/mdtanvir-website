@extends('admin.layouts.app')

@section('title', 'Subscribers | Admin')

@section('content')
  <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
      <h1 class="text-3xl font-black">Newsletter Subscribers</h1>
      <p class="text-gray-600 dark:text-gray-400">People who signed up for new-article emails. Only confirmed subscribers receive them.</p>
    </div>
    <a href="{{ route('admin.subscribers.export', ['status' => $status ?? 'confirmed']) }}" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition">
      <i class="fas fa-file-csv mr-2"></i>Export CSV
    </a>
  </div>

  <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
    <nav class="flex flex-wrap gap-2 text-sm">
      @foreach (['all' => null, 'confirmed' => 'confirmed', 'pending' => 'pending', 'unsubscribed' => 'unsubscribed'] as $label => $value)
        <a
          href="{{ route('admin.subscribers.index', array_filter(['status' => $value])) }}"
          class="px-4 py-2 rounded-xl font-semibold {{ $status === $value ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800' }}"
        >
          {{ ucfirst($label) }} <span class="ml-1 opacity-60">{{ $counts[$label] }}</span>
        </a>
      @endforeach
    </nav>
    <form method="GET" class="flex gap-2">
      @if ($status)
        <input type="hidden" name="status" value="{{ $status }}" />
      @endif
      <input type="search" name="q" value="{{ request('q') }}" placeholder="Search email…" class="rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-sm" />
      <button class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-gray-800 text-sm font-semibold">Search</button>
    </form>
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 dark:bg-gray-800/50 text-left">
        <tr>
          <th class="px-5 py-3 font-semibold">Email</th>
          <th class="px-5 py-3 font-semibold">Status</th>
          <th class="px-5 py-3 font-semibold">Language</th>
          <th class="px-5 py-3 font-semibold">Signed up</th>
          <th class="px-5 py-3 font-semibold">Source</th>
          <th class="px-5 py-3 font-semibold"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($subscribers as $subscriber)
          @php
            $badge = [
              'confirmed' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
              'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
              'unsubscribed' => 'bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
            ][$subscriber->status()];
          @endphp
          <tr class="border-t border-gray-200 dark:border-gray-800">
            <td class="px-5 py-4 font-semibold">{{ $subscriber->email }}</td>
            <td class="px-5 py-4"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">{{ ucfirst($subscriber->status()) }}</span></td>
            <td class="px-5 py-4 uppercase">{{ $subscriber->locale }}</td>
            <td class="px-5 py-4">{{ $subscriber->created_at->format('M d, Y') }}</td>
            <td class="px-5 py-4 text-gray-500">{{ $subscriber->source ?? '-' }}</td>
            <td class="px-5 py-4 text-right">
              <form method="POST" action="{{ route('admin.subscribers.destroy', $subscriber) }}" onsubmit="return confirm('Delete this subscriber permanently?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-5 py-10 text-center text-gray-500">No subscribers yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $subscribers->links() }}
  </div>
@endsection
