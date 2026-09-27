{{-- ↑/↓ reorder buttons. Params: $route (e.g. 'admin.projects.move'), $model, $first, $last --}}
<div class="flex flex-col gap-1">
  @foreach (['up' => 'fa-chevron-up', 'down' => 'fa-chevron-down'] as $direction => $icon)
    <form method="POST" action="{{ route($route, [$model, $direction]) }}">
      @csrf
      @method('PATCH')
      <button
        type="submit"
        aria-label="Move {{ $direction }}"
        @disabled(($direction === 'up' && $first) || ($direction === 'down' && $last))
        class="grid h-7 w-7 place-items-center rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-30 disabled:cursor-not-allowed"
      ><i class="fas {{ $icon }} text-xs"></i></button>
    </form>
  @endforeach
</div>
