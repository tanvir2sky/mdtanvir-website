{{-- Back-to-top button with a ring that fills as the page scrolls. --}}
<button
  id="scroll-top"
  type="button"
  aria-label="{{ __('Scroll to top') }}"
  data-visible="false"
  class="scroll-top group fixed bottom-5 right-5 z-40 grid h-12 w-12 place-items-center rounded-full border border-gray-200/80 bg-white/80 text-gray-700 shadow-lg shadow-gray-900/10 backdrop-blur-md transition hover:-translate-y-0.5 hover:text-primary-600 hover:shadow-xl focus:outline-none focus-visible:ring-4 focus-visible:ring-primary-500/30 dark:border-white/10 dark:bg-gray-900/80 dark:text-gray-200 dark:hover:text-primary-300 sm:bottom-8 sm:right-8"
>
  <svg viewBox="0 0 48 48" class="absolute inset-0 h-full w-full -rotate-90" aria-hidden="true">
    <defs>
      <linearGradient id="scroll-top-gradient" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" stop-color="#22d3ee" />
        <stop offset="55%" stop-color="#0ea5e9" />
        <stop offset="100%" stop-color="#8b5cf6" />
      </linearGradient>
    </defs>
    <circle cx="24" cy="24" r="22" fill="none" stroke="url(#scroll-top-gradient)" stroke-width="2.5" stroke-linecap="round"
      pathLength="100" stroke-dasharray="100" stroke-dashoffset="100" data-scroll-progress />
  </svg>
  <i class="fas fa-arrow-up relative text-sm transition-transform duration-300 group-hover:-translate-y-0.5"></i>
</button>
