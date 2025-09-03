{{-- resources/views/security/policy.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 pt-6 pb-12">

  {{-- Page title chip: light = soft white card, dark = hero surface --}}
  <div
    class="rounded-2xl px-6 py-5 mb-6 text-center font-orbitron text-2xl sm:text-3xl font-extrabold tracking-tight
           bg-white/85 ring-1 ring-stone-900/10 shadow-xl
           dark:bg-stone-900/70 dark:ring-white/10">
    Responsible Disclosure Policy
  </div>

  {{-- Subheader blurb --}}
  <p class="text-center mb-8 text-stone-700 dark:text-stone-300">
    We take security seriously. If you discover a vulnerability in our site or services,
    please report it responsibly so we can fix it quickly and keep users safe.
  </p>

  {{-- Main card --}}
  <div class="rounded-2xl bg-white/90 ring-1 ring-stone-900/10 shadow-sm
              dark:bg-stone-900/70 dark:ring-white/10">

    <div class="px-6 py-5 border-b border-stone-200/70 dark:border-white/10">
      <h2 class="font-orbitron text-xl font-semibold">Reporting a Vulnerability</h2>
      <p class="mt-3 text-stone-700 dark:text-stone-300">
        Email our security team at
        <a class="ci-link" href="mailto:security@compromisedinternals.com">security@compromisedinternals.com</a>
        with as much detail as possible:
      </p>
      <ul class="mt-3 list-disc list-inside space-y-1 text-stone-700 dark:text-stone-300">
        <li>A clear description of the vulnerability</li>
        <li>Steps to reproduce (URLs, payloads, account state, etc.)</li>
        <li>Observed and potential impact</li>
        <li>Any logs, screenshots, or PoC that helps us verify quickly</li>
      </ul>
      <p class="mt-2 text-[11px] text-stone-500 dark:text-stone-400">
        Please avoid including sensitive personal data in reports.
      </p>
    </div>

    <div class="px-6 py-5 border-b border-stone-200/70 dark:border-white/10">
      <h2 class="font-orbitron text-xl font-semibold">Please Don’t</h2>
      <ul class="mt-3 list-disc list-inside space-y-1 text-stone-700 dark:text-stone-300">
        <li>Perform denial-of-service or resource-exhaustion attacks</li>
        <li>Access, modify, or exfiltrate data that isn’t yours</li>
        <li>Impact availability of the service or other users</li>
        <li>Publicly disclose details before we’ve remediated</li>
      </ul>
    </div>

    <div class="px-6 py-5">
      <h2 class="font-orbitron text-xl font-semibold">Acknowledgments</h2>
      <p class="mt-3 text-stone-700 dark:text-stone-300">
        If you report a valid issue and follow this policy, we’re happy to credit you on our
        <a class="ci-link" href="{{ route('security.hof') }}">Security Hall of Fame</a> (if you’d like).
      </p>

      <p class="mt-4 text-[12px] text-stone-500 dark:text-stone-400">
        Last updated: {{ now()->format('F j, Y') }}
      </p>
    </div>

  </div>
</div>
@endsection