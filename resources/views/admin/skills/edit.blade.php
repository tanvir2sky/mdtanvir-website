@extends('admin.layouts.app')

@section('title', 'Edit Skill Group | Admin')

@section('content')
  <div class="mb-6">
    <a href="{{ route('admin.skills.index') }}" class="text-sm font-semibold text-primary-600 dark:text-primary-400"><i class="fas fa-arrow-left mr-2"></i>Back</a>
    <h1 class="mt-3 text-3xl font-black">Edit Skill Group</h1>
  </div>

  <form method="POST" action="{{ route('admin.skills.update', $skillGroup) }}" class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6">
    @csrf
    @method('PUT')
    @include('admin.skills._form', ['submitLabel' => 'Save changes'])
  </form>
@endsection
