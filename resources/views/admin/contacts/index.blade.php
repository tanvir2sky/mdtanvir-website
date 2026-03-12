@extends('admin.layouts.app')

@section('title', 'Contact Messages | Admin')

@section('content')
  <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <div>
      <h1 class="text-3xl font-black">Contact Messages</h1>
      <p class="text-gray-600 dark:text-gray-400">All inquiries submitted from your portfolio website.</p>
    </div>
  </div>

  <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 dark:bg-gray-800/50 text-left">
        <tr>
          <th class="px-5 py-3 font-semibold">Subject</th>
          <th class="px-5 py-3 font-semibold">Name</th>
          <th class="px-5 py-3 font-semibold">Email</th>
          <th class="px-5 py-3 font-semibold">Date</th>
          <th class="px-5 py-3 font-semibold">Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($messages as $message)
          <tr class="border-t border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/30">
            <td class="px-5 py-4">
              <a href="{{ route('admin.contacts.show', $message) }}" class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">
                {{ $message->subject }}
              </a>
            </td>
            <td class="px-5 py-4">{{ $message->name }}</td>
            <td class="px-5 py-4">{{ $message->email }}</td>
            <td class="px-5 py-4">{{ $message->created_at->format('M d, Y H:i') }}</td>
            <td class="px-5 py-4">
              <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $message->is_read ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                {{ $message->is_read ? 'Read' : 'Unread' }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-5 py-10 text-center text-gray-500">No messages found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-6">
    {{ $messages->links() }}
  </div>
@endsection
