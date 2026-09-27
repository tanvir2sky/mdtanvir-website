@extends('admin.layouts.app')

@section('title', 'Dashboard | Admin')

@section('content')
  <div class="mb-8">
    <h1 class="text-3xl font-black">Dashboard Overview</h1>
    <p class="text-gray-600 dark:text-gray-400">Manage your content, messages and newsletter from one place.</p>
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
      <p class="text-xs uppercase tracking-wider text-gray-500">Subscribers</p>
      <p class="text-3xl font-black mt-2">{{ $stats['subscribers_active'] }}</p>
      <p class="mt-1 text-xs text-gray-500">{{ $stats['subscribers_pending'] }} awaiting confirmation</p>
    </div>
  </div>

  <div class="grid sm:grid-cols-3 gap-4 mb-8">
    @foreach ([
      ['admin.bookings.index', 'fa-calendar-check', 'Pending bookings', $stats['bookings_pending']],
      ['admin.guestbook.index', 'fa-book-open', 'Guestbook to review', $stats['guestbook_pending']],
      ['admin.store-checks.index', 'fa-store', 'Store checks (7 days)', $stats['store_checks_week']],
    ] as [$target, $icon, $label, $value])
      <a href="{{ route($target) }}" class="flex items-center gap-4 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-5 hover:border-primary-400 transition">
        <span class="grid h-11 w-11 place-items-center rounded-xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-300"><i class="fas {{ $icon }}"></i></span>
        <span>
          <span class="block text-xs uppercase tracking-wider text-gray-500">{{ $label }}</span>
          <span class="block text-2xl font-black">{{ $value }}</span>
        </span>
      </a>
    @endforeach
  </div>

  @if ($topPosts->isNotEmpty() && $topPosts->first()->views_count > 0)
    <section class="mb-8 rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
      <h2 class="mb-4 text-xl font-bold">Top Posts</h2>
      <div class="space-y-2">
        @foreach ($topPosts as $top)
          <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 dark:border-gray-800 px-4 py-3">
            <span class="font-semibold">{{ $top->title }}</span>
            <span class="shrink-0 font-mono text-sm text-gray-500"><i class="far fa-eye mr-1"></i>{{ number_format($top->views_count) }} · <i class="far fa-heart mr-1"></i>{{ $top->reactions_count }}</span>
          </div>
        @endforeach
      </div>
    </section>
  @endif

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
