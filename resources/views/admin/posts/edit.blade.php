@extends('admin.layouts.app')

@section('title', 'Edit Post | Admin')

@include('admin.partials.editor-assets')

@section('content')
  <div class="mb-6">
    <h1 class="text-3xl font-black">Edit Post</h1>
    <p class="text-gray-600 dark:text-gray-400">Update content and publication status.</p>
  </div>

  <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data" class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
    @csrf
    @method('PUT')
    @include('admin.posts._form', ['submitLabel' => 'Update Post', 'post' => $post])
  </form>
@endsection
