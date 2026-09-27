@extends('admin.layouts.app')

@section('title', 'Store Checks | Admin')

@section('content')
  <div class="mb-6">
    <h1 class="text-3xl font-black">Shopify Store Checks</h1>
    <p class="text-gray-600 dark:text-gray-400">Stores visitors ran through the health check. Frequent or low-scoring stores can be good leads.</p>
  </div>

  @if ($topHosts->isNotEmpty())
    <section class="mb-6 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
      <h2 class="mb-3 font-bold">Most checked (30 days)</h2>
      <div class="flex flex-wrap gap-2">
        @foreach ($topHosts as $top)
          <span class="rounded-full bg-gray-100 dark:bg-gray-800 px-3 py-1 text-sm">{{ $top->host }} <span class="opacity-60">×{{ $top->total }}</span></span>
        @endforeach
      </div>
    </section>
  @endif

  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 dark:bg-gray-800/50 text-left">
        <tr>
          <th class="px-5 py-3 font-semibold">Store</th>
          <th class="px-5 py-3 font-semibold">Shopify</th>
          <th class="px-5 py-3 font-semibold">Score</th>
          <th class="px-5 py-3 font-semibold">Language</th>
          <th class="px-5 py-3 font-semibold">Checked</th>
          <th class="px-5 py-3 font-semibold"></th>
        </tr>
      </thead>
      <tbody>
        @forelse ($checks as $check)
          <tr class="border-t border-gray-200 dark:border-gray-800">
            <td class="px-5 py-4 font-semibold">{{ $check->host }}</td>
            <td class="px-5 py-4">{{ $check->is_shopify ? 'Yes' : 'No' }}</td>
            <td class="px-5 py-4">
              @if ($check->score !== null)
                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $check->score >= 80 ? 'bg-green-100 text-green-800' : ($check->score >= 50 ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800') }}">{{ $check->score }}</span>
              @else
                -
              @endif
            </td>
            <td class="px-5 py-4 uppercase">{{ $check->locale }}</td>
            <td class="px-5 py-4">{{ $check->created_at->format('M d, Y H:i') }}</td>
            <td class="px-5 py-4 text-right">
              <a href="{{ route('tools.shopify-check', ['store' => $check->host]) }}" target="_blank" class="text-primary-600 dark:text-primary-400 font-semibold">View report</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">No store checks yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">{{ $checks->links() }}</div>
@endsection
