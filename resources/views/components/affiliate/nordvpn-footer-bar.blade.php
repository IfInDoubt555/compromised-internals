@php
  // Copy + tracking (feel free to tweak)
  $headline = 'Watch rallies anywhere — bypass region blocks with NordVPN.';
  $ctaText  = 'Unblock coverage — Save up to 77% + 3 months free';
  $subid    = 'vpn-footer-stream';
@endphp

<div class="w-full bg-stone-100/95 dark:bg-stone-800/95 border-t border-stone-300 dark:border-stone-700 relative z-40">
  <div class="max-w-7xl mx-auto flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-4 py-3 text-sm">

    {{-- Left: value props (same clean layout as the regular footer) --}}
    <div class="text-center sm:text-left leading-tight">
      <p class="font-medium text-stone-900 dark:text-stone-100">
        {{ $headline }}
      </p>
      <p class="mt-0.5 text-stone-700 dark:text-stone-300">
        <span class="whitespace-nowrap">Fast servers for HD streams</span>
        <span aria-hidden="true" class="mx-2">•</span>
        <span class="whitespace-nowrap">Servers in 60+ countries</span>
        <span aria-hidden="true" class="mx-2">•</span>
        <span class="whitespace-nowrap">30-day money-back</span>
      </p>
    </div>

    {{-- Right: CTA --}}
    <div class="flex items-center justify-center">
      <x-affiliate-link
        brand="nordvpn"
        href="https://nordvpn.tpx.lt/wqCztrC5"
        subid="{{ $subid }}"
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-sky-600 text-white font-medium
               hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-400">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M12 2l7 3v6c0 5-3.5 9.3-7 11-3.5-1.7-7-6-7-11V5l7-3z"/>
        </svg>
        <span>{{ $ctaText }}</span>
      </x-affiliate-link>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-4 pb-2">
    <p class="text-[11px] text-stone-500 dark:text-stone-400 text-center sm:text-left">
      Sponsored link. Using our partner links helps support Compromised Internals at no extra cost to you.
    </p>
  </div>
</div>