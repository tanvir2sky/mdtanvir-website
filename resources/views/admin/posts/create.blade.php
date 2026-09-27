@extends('admin.layouts.app')

@section('title', 'Create Post | Admin')

@include('admin.partials.editor-assets')

@section('content')
  <div class="mb-6">
    <h1 class="text-3xl font-black">Create New Post</h1>
    <p class="text-gray-600 dark:text-gray-400">Write your blog content and set SEO details.</p>
  </div>

  <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
    @csrf
    @include('admin.posts._form', ['submitLabel' => 'Create Post'])
  </form>
@endsection
