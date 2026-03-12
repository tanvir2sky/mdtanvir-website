@extends('admin.layouts.app')

@section('title', 'View Contact Message | Admin')

@section('content')
  <div class="mb-6">
    <a href="{{ route('admin.contacts.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400">
      <i class="fas fa-arrow-left mr-2"></i>Back to messages
    </a>
  </div>

  <article class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 sm:p-8">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
      <h1 class="text-3xl font-black">{{ $contact->subject }}</h1>
      <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $contact->is_read ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
        {{ $contact->is_read ? 'Read' : 'Unread' }}
      </span>
    </div>

    <div class="grid sm:grid-cols-2 gap-4 mb-6 text-sm">
      <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
        <p class="text-gray-500 mb-1">Name</p>
        <p class="font-semibold">{{ $contact->name }}</p>
      </div>
      <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
        <p class="text-gray-500 mb-1">Email</p>
        <a href="mailto:{{ $contact->email }}" class="font-semibold text-primary-600 dark:text-primary-400 hover:underline">{{ $contact->email }}</a>
      </div>
      <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50 sm:col-span-2">
        <p class="text-gray-500 mb-1">Received At</p>
        <p class="font-semibold">{{ $contact->created_at->format('M d, Y H:i:s') }}</p>
      </div>
    </div>

    <div class="prose max-w-none dark:prose-invert">
      <h2>Message</h2>
      <p class="whitespace-pre-line text-gray-800 dark:text-gray-200">{{ $contact->message }}</p>
    </div>
  </article>
@endsection
