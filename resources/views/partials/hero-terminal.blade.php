@php
  $terminalData = \App\Support\Portfolio::terminalData();
@endphp
<div
  id="hero-terminal"
  class="relative mt-6 rounded-2xl overflow-hidden border border-gray-800 bg-gray-950 shadow-2xl text-left"
>
  <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-900 border-b border-gray-800">
    <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
    <span class="w-3 h-3 rounded-full bg-yellow-500/80"></span>
    <span class="w-3 h-3 rounded-full bg-green-500/80"></span>
    <span class="ml-2 text-xs font-mono text-gray-400">tanvir@portfolio: ~</span>
  </div>
  <div
    data-terminal-output
    class="h-56 overflow-y-auto px-4 py-3 font-mono text-[13px] leading-relaxed text-gray-200"
    aria-live="polite"
  ></div>
  <form data-terminal-form class="flex items-center gap-2 px-4 py-2.5 border-t border-gray-800 font-mono text-[13px]">
    <label for="terminal-input" class="text-cyan-400 shrink-0">$ php artisan</label>
    <input
      id="terminal-input"
      data-terminal-input
      type="text"
      autocomplete="off"
      autocapitalize="off"
      spellcheck="false"
      placeholder="help"
      aria-label="{{ __('Terminal command') }}"
      class="flex-1 min-w-0 bg-transparent text-gray-100 placeholder-gray-600 focus:outline-none"
    />
  </form>
  <script type="application/json" data-terminal-data>@json($terminalData)</script>
</div>
