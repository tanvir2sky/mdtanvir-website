@extends('admin.layouts.app')

@section('title', 'Bookings | Admin')

@php
  $badges = [
    'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    'confirmed' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
    'declined' => 'bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
  ];
@endphp

@section('content')
  <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
      <h1 class="text-3xl font-black">Bookings</h1>
      <p class="text-gray-600 dark:text-gray-400">Call requests from the booking page. Times are shown in {{ $timezone }}.</p>
    </div>
    <a href="{{ route('admin.availability.edit') }}" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700 transition">
      <i class="fas fa-calendar-week mr-2"></i>Availability
    </a>
  </div>

  <h2 class="mb-3 text-xl font-bold">Upcoming</h2>
  <div class="mb-10 space-y-3">
    @forelse ($upcoming as $booking)
      @php $when = $booking->startsIn($timezone); @endphp
      <article class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div class="flex gap-4">
            <div class="w-16 shrink-0 rounded-xl bg-primary-50 dark:bg-primary-900/30 py-2 text-center">
              <p class="text-xs font-semibold uppercase text-primary-600 dark:text-primary-300">{{ $when->format('M') }}</p>
              <p class="text-2xl font-black">{{ $when->format('j') }}</p>
              <p class="text-xs text-gray-500">{{ $when->format('D') }}</p>
            </div>
            <div class="min-w-0">
              <p class="font-bold">
                {{ $when->format('H:i') }}–{{ $booking->ends_at->copy()->setTimezone($timezone)->format('H:i') }} · {{ $booking->name }}
                <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $badges[$booking->status] }}">{{ ucfirst($booking->status) }}</span>
              </p>
              <p class="text-sm text-gray-500"><a href="mailto:{{ $booking->email }}" class="hover:underline">{{ $booking->email }}</a> · their zone: {{ $booking->visitor_timezone }} · {{ strtoupper($booking->locale) }}</p>
              <p class="mt-2 whitespace-pre-line text-gray-700 dark:text-gray-300">{{ $booking->topic }}</p>
            </div>
          </div>
          <div class="flex flex-col items-end gap-2">
            @if ($booking->status === 'pending')
              <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}">@csrf @method('PATCH')
                <button class="px-4 py-2 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700"><i class="fas fa-check mr-1"></i>Approve & send invite</button>
              </form>
            @endif
            <details class="text-right">
              <summary class="cursor-pointer text-sm text-red-600 dark:text-red-400">Decline…</summary>
              <form method="POST" action="{{ route('admin.bookings.decline', $booking) }}" class="mt-2 flex flex-col items-end gap-2">@csrf @method('PATCH')
                <textarea name="reason" rows="2" maxlength="500" placeholder="Optional message to {{ $booking->name }}" class="w-64 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-3 py-2 text-sm"></textarea>
                <button class="px-3 py-1.5 rounded-lg bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 text-sm font-semibold">Decline & notify</button>
              </form>
            </details>
          </div>
        </div>
      </article>
    @empty
      <p class="rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 p-10 text-center text-gray-500">No upcoming calls.</p>
    @endforelse
  </div>

  <h2 class="mb-3 text-xl font-bold">Past & closed</h2>
  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 dark:bg-gray-800/50 text-left">
        <tr>
          <th class="px-5 py-3 font-semibold">When</th>
          <th class="px-5 py-3 font-semibold">Name</th>
          <th class="px-5 py-3 font-semibold">Status</th>
          <th class="px-5 py-3 font-semibold">Topic</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($past as $booking)
          <tr class="border-t border-gray-200 dark:border-gray-800">
            <td class="px-5 py-4 whitespace-nowrap">{{ $booking->startsIn($timezone)->format('M d, Y H:i') }}</td>
            <td class="px-5 py-4">{{ $booking->name }}<br><span class="text-xs text-gray-500">{{ $booking->email }}</span></td>
            <td class="px-5 py-4"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $badges[$booking->status] }}">{{ ucfirst($booking->status) }}</span></td>
            <td class="px-5 py-4 max-w-md truncate">{{ $booking->topic }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-5 py-10 text-center text-gray-500">Nothing here yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-6">{{ $past->links() }}</div>
@endsection
