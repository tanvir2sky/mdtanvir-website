@extends('admin.layouts.app')

@section('title', 'Add Project | Admin')

@include('admin.partials.editor-assets')

@section('content')
  <div class="mb-6">
    <a href="{{ route('admin.projects.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400"><i class="fas fa-arrow-left mr-2"></i>Back</a>
    <h1 class="mt-3 text-3xl font-black">Add Project</h1>
  </div>

  <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
    @csrf
    @include('admin.projects._form', ['submitLabel' => 'Save'])
  </form>
@endsection
