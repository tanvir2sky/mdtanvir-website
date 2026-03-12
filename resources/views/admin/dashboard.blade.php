@extends('admin.layouts.app')

@section('title', 'Dashboard | Admin')

@section('content')
  <div class="mb-8">
    <h1 class="text-3xl font-black">Dashboard Overview</h1>
    <p class="text-gray-600 dark:text-gray-400">Manage blog posts and contact messages from one place.</p>
  </div>

  <div class="grid sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-8">
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
      <p class="text-xs uppercase tracking-wider text-gray-500">Posts</p>
      <p class="text-3xl font-black mt-2">{{ $stats['posts_total'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
      <p class="text-xs uppercase tracking-wider text-gray-500">Published</p>
      <p class="text-3xl font-black mt-2">{{ $stats['posts_published'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
      <p class="text-xs uppercase tracking-wider text-gray-500">Messages</p>
      <p class="text-3xl font-black mt-2">{{ $stats['messages_total'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
      <p class="text-xs uppercase tracking-wider text-gray-500">Unread</p>
      <p class="text-3xl font-black mt-2">{{ $stats['messages_unread'] }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5">
      <p class="text-xs uppercase tracking-wider text-gray-500">Admin Users</p>
      <p class="text-3xl font-black mt-2">{{ $stats['admins_total'] }}</p>
    </div>
  </div>

  <div class="grid xl:grid-cols-2 gap-6">
    <section class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Recent Contact Messages</h2>
        <a href="{{ route('admin.contacts.index') }}" class="text-sm text-primary-600 dark:text-primary-400 font-semibold">View all</a>
      </div>
      <div class="space-y-3">
        @forelse ($recentMessages as $msg)
          <a href="{{ route('admin.contacts.show', $msg) }}" class="block p-4 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-primary-400 transition">
            <p class="font-semibold">{{ $msg->subject }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $msg->name }} • {{ $msg->email }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $msg->created_at->diffForHumans() }}</p>
          </a>
        @empty
          <p class="text-gray-500">No contact messages yet.</p>
        @endforelse
      </div>
    </section>

    <section class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Recent Blog Posts</h2>
        <a href="{{ route('admin.posts.index') }}" class="text-sm text-primary-600 dark:text-primary-400 font-semibold">Manage posts</a>
      </div>
      <div class="space-y-3">
        @forelse ($recentPosts as $post)
          <a href="{{ route('admin.posts.edit', $post) }}" class="block p-4 rounded-xl border border-gray-200 dark:border-gray-800 hover:border-primary-400 transition">
            <p class="font-semibold">{{ $post->title }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ $post->is_published ? 'Published' : 'Draft' }}
              @if ($post->published_at)
                • {{ $post->published_at->format('M d, Y') }}
              @endif
            </p>
          </a>
        @empty
          <p class="text-gray-500">No blog posts yet.</p>
        @endforelse
      </div>
    </section>
  </div>
@endsection
