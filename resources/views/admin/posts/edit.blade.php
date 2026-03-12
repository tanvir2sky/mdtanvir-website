@extends('admin.layouts.app')

@section('title', 'Edit Post | Admin')

@push('styles')
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet" />
@endpush

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (window.jQuery && $('.js-summernote').length) {
        $('.js-summernote').summernote({
          height: 360,
          placeholder: 'Write your article content...',
          toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
          ]
        });
      }
    });
  </script>
@endpush

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
