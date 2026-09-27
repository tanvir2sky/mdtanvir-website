@extends('admin.layouts.app')

@section('title', 'Availability | Admin')

@php
  $input = 'rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:outline-none';
  $weekdays = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
@endphp

@section('content')
  <div class="mb-6">
    <a href="{{ route('admin.bookings.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400"><i class="fas fa-arrow-left mr-2"></i>Bookings</a>
    <h1 class="mt-3 text-3xl font-black">Availability</h1>
    <p class="text-gray-600 dark:text-gray-400">When visitors can book a {{ config('booking.slot_minutes') }}-minute call. Calls need {{ config('booking.min_notice_hours') }}h notice and can be booked up to {{ config('booking.window_days') }} days ahead.</p>
  </div>

  <div class="grid gap-6 xl:grid-cols-3">
    <div class="space-y-6 xl:col-span-2">
      {{-- Settings --}}
      <section class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
        <h2 class="mb-4 text-lg font-bold">Booking settings</h2>
        <form method="POST" action="{{ route('admin.availability.settings') }}" class="space-y-4">
          @csrf @method('PUT')
          <label class="flex items-center gap-2 font-semibold">
            <input type="checkbox" name="booking_enabled" value="1" @checked(old('booking_enabled', $settings->booking_enabled)) />
            Booking page is live
            <span class="font-normal text-sm text-gray-500">(shows "Book a call" on the site once you have weekly hours)</span>
          </label>
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label for="booking_timezone" class="mb-2 block text-sm font-medium">Your time zone</label>
              <select id="booking_timezone" name="booking_timezone" class="{{ $input }} w-full">
                @foreach ($timezones as $zone)
                  <option value="{{ $zone }}" @selected(old('booking_timezone', $settings->bookingTimezone()) === $zone)>{{ $zone }}</option>
                @endforeach
              </select>
              @error('booking_timezone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
              <label for="booking_meeting_url" class="mb-2 block text-sm font-medium">Meeting link</label>
              <input id="booking_meeting_url" name="booking_meeting_url" type="url" value="{{ old('booking_meeting_url', $settings->booking_meeting_url) }}" placeholder="https://meet.google.com/…" class="{{ $input }} w-full" />
              <p class="mt-1 text-xs text-gray-500">Your personal Zoom, Meet or Teams link, included in confirmations.</p>
              @error('booking_meeting_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
          </div>
          <button class="px-5 py-2.5 rounded-xl bg-primary-600 text-white font-semibold hover:bg-primary-700">Save settings</button>
        </form>
      </section>

      {{-- Weekly hours --}}
      <section class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
        <h2 class="mb-4 text-lg font-bold">Weekly hours <span class="text-sm font-normal text-gray-500">({{ $settings->bookingTimezone() }})</span></h2>

        <div class="mb-6 divide-y divide-gray-200 dark:divide-gray-800">
          @foreach ($weekdays as $number => $label)
            <div class="flex flex-wrap items-center gap-3 py-3">
              <span class="w-28 font-semibold">{{ $label }}</span>
              @forelse ($rules->get($number, []) as $rule)
                <form method="POST" action="{{ route('admin.availability.rules.destroy', $rule) }}" class="inline-flex">@csrf @method('DELETE')
                  <span class="inline-flex items-center gap-2 rounded-full bg-primary-50 dark:bg-primary-900/30 px-3 py-1 text-sm font-mono">
                    {{ $rule->startLabel() }}–{{ $rule->endLabel() }}
                    <button aria-label="Remove" class="text-gray-500 hover:text-red-600"><i class="fas fa-xmark"></i></button>
                  </span>
                </form>
              @empty
                <span class="text-sm text-gray-400">Unavailable</span>
              @endforelse
            </div>
          @endforeach
        </div>

        <form method="POST" action="{{ route('admin.availability.rules.store') }}" class="space-y-3 rounded-xl bg-gray-50 dark:bg-gray-800/50 p-4">
          @csrf
          <p class="text-sm font-semibold">Add hours</p>
          <div class="flex flex-wrap gap-2">
            @foreach ($weekdays as $number => $label)
              <label class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-700 px-2.5 py-1.5 text-sm has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50 dark:has-[:checked]:bg-primary-900/30">
                <input type="checkbox" name="weekdays[]" value="{{ $number }}" @checked(in_array($number, old('weekdays', []))) /> {{ substr($label, 0, 3) }}
              </label>
            @endforeach
          </div>
          <div class="flex flex-wrap items-center gap-3">
            <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" step="900" class="{{ $input }}" required />
            <span>to</span>
            <input type="time" name="end_time" value="{{ old('end_time', '17:00') }}" step="900" class="{{ $input }}" required />
            <button class="px-4 py-2.5 rounded-xl bg-gray-900 dark:bg-white dark:text-gray-900 text-white font-semibold">Add</button>
          </div>
          @foreach (['weekdays', 'start_time', 'end_time'] as $field)
            @error($field) <p class="text-sm text-red-600">{{ $message }}</p> @enderror
          @endforeach
        </form>
      </section>

      {{-- Blocked dates --}}
      <section class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
        <h2 class="mb-4 text-lg font-bold">Days off</h2>
        <form method="POST" action="{{ route('admin.availability.blocked.store') }}" class="mb-4 flex flex-wrap items-center gap-3">
          @csrf
          <input type="date" name="date" min="{{ today()->toDateString() }}" required class="{{ $input }}" />
          <input type="text" name="reason" placeholder="Reason (private)" maxlength="255" class="{{ $input }} flex-1" />
          <button class="px-4 py-2.5 rounded-xl bg-gray-900 dark:bg-white dark:text-gray-900 text-white font-semibold">Block day</button>
        </form>
        @error('date') <p class="mb-3 text-sm text-red-600">{{ $message }}</p> @enderror
        <div class="flex flex-wrap gap-2">
          @forelse ($blockedDates as $blocked)
            <form method="POST" action="{{ route('admin.availability.blocked.destroy', $blocked) }}">@csrf @method('DELETE')
              <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 dark:bg-gray-800 px-3 py-1 text-sm">
                {{ $blocked->date->format('D, M j, Y') }} @if ($blocked->reason) <span class="text-gray-500">· {{ $blocked->reason }}</span> @endif
                <button aria-label="Unblock" class="text-gray-500 hover:text-red-600"><i class="fas fa-xmark"></i></button>
              </span>
            </form>
          @empty
            <p class="text-sm text-gray-500">No upcoming days off.</p>
          @endforelse
        </div>
      </section>
    </div>

    {{-- Preview --}}
    <aside class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 h-fit">
      <h2 class="mb-1 text-lg font-bold">Next 7 days</h2>
      <p class="mb-4 text-sm text-gray-500">Open slots visitors can book now.</p>
      @forelse ($preview as $date => $daySlots)
        <div class="mb-4">
          <p class="mb-2 text-sm font-semibold">{{ \Illuminate\Support\Carbon::parse($date)->format('l, M j') }}</p>
          <div class="flex flex-wrap gap-1.5">
            @foreach ($daySlots as $slot)
              <span class="rounded-md bg-gray-100 dark:bg-gray-800 px-2 py-1 font-mono text-xs">{{ $slot->setTimezone($settings->bookingTimezone())->format('H:i') }}</span>
            @endforeach
          </div>
        </div>
      @empty
        <p class="text-sm text-gray-500">No open slots in the next 7 days. Add weekly hours to get started.</p>
      @endforelse
    </aside>
  </div>
@endsection
