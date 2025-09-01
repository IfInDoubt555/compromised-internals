@extends('layouts.app')

@section('content')

@php
    /** @var \App\Models\User $user */
    $p = $user->profile; // may be null

    // Safe helpers (work even if you haven't added show_* columns yet)
    $show = function($flag, $default = true) use ($p) {
        return $p ? (($p->{$flag} ?? $default) ? true : false) : false;
    };

    // Compute age & fan years without needing model accessors
    $age = null;
    if ($p && filled($p->birthday)) {
        try { $age = \Carbon\Carbon::parse($p->birthday)->age; } catch (\Throwable $e) {}
    }

    $fanYears = null;
    if ($p && is_numeric($p->rally_fan_since ?? null)) {
        $y = (int) $p->rally_fan_since;
        if ($y >= 1900 && $y <= (int) now()->year) {
            $fanYears = (int) now()->year - $y;
        }
    }

    // Socials array (render only non-empty)
    $socials = [];
    if ($p) {
        $norm = function($v) {
            if (!filled($v)) return null;
            $v = trim($v);
            if (\Illuminate\Support\Str::startsWith($v, ['http://','https://'])) return $v;
            return 'https://' . ltrim($v, '/');
        };
        $socials = array_filter([
            'Website'   => $norm($p->website ?? null),
            'Instagram' => $norm($p->instagram ?? null),
            'YouTube'   => $norm($p->youtube ?? null),
            'Twitter/X' => $norm($p->twitter ?? null),
            'Twitch'    => $norm($p->twitch ?? null),
        ]);
    }
@endphp

@if (auth()->check() && auth()->id() === $user->id)
  <div class="text-center mb-4">
      <a href="{{ route('profile.edit') }}"
         class="inline-block px-4 py-2 mt-6 bg-yellow-400 text-black text-sm font-semibold rounded-full shadow hover:bg-yellow-500 transition
                dark:bg-amber-400/90 dark:text-stone-900 dark:hover:bg-amber-400 dark:ring-1 dark:ring-white/10">
          ✏️ Edit Your Profile
      </a>
  </div>
@endif

{{-- Optional banner --}}
@if($p && filled($p->banner_image))
  <div class="max-w-4xl mx-auto px-4">
    <div class="h-40 sm:h-48 rounded-2xl overflow-hidden ring-1 ring-black/5 dark:ring-white/10">
      <img src="{{ asset('storage/'.$p->banner_image) }}" alt="" class="w-full h-full object-cover">
    </div>
  </div>
@endif

<div class="max-w-4xl mx-auto mt-6 p-8 bg-white shadow-xl rounded-2xl ring-1 ring-black/5
            dark:bg-stone-900/70 dark:ring-white/10 dark:text-stone-200">

  <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
      {{-- Avatar --}}
      <div class="w-40 h-40 rounded-full overflow-hidden bg-gray-100 border border-gray-200 shadow
                  dark:bg-stone-800/60 dark:border-white/10 dark:ring-1 dark:ring-white/10">
        <x-user-avatar
          :path="$user->profile_picture"   {{-- e.g. 'profiles/foo.png' on disk=public --}}
          alt="{{ $user->name }}'s avatar"
          :size="160"                      {{-- matches w-40/h-40 (160px) --}}
          class="object-cover w-full h-full"
        />
      </div>

      {{-- Header / Summary --}}
      <div class="flex-1 min-w-0">
          <h1 class="text-3xl font-bold text-center md:text-left mb-2 flex flex-wrap items-center gap-2"
              @if($p && filled($p->profile_color)) style="color: {{ $p->profile_color }};" @endif>
              {{ $p?->display_name ?: $user->name }}

              @if ($p && $p->rally_role)
                <span class="text-sm font-semibold px-3 py-1 rounded-full shadow
                             bg-blue-100 text-blue-800
                             dark:bg-blue-900/30 dark:text-blue-300">
                    {{ $p->rally_role }}
                </span>
              @endif
          </h1>

          {{-- Quick facts --}}
          @if($p)
            <dl class="mt-1 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2 text-sm">
              @if($show('show_location', true) && filled($p->location))
                <div><dt class="text-stone-500">Location</dt><dd>{{ $p->location }}</dd></div>
              @endif

              @if(filled($p->rally_fan_since) || $fanYears)
                <div>
                  <dt class="text-stone-500">Rally Fan</dt>
                  <dd>
                    @if(filled($p->rally_fan_since)) Since {{ $p->rally_fan_since }} @endif
                    @if($fanYears) ({{ $fanYears }} yrs) @endif
                  </dd>
                </div>
              @endif

              @if($show('show_birthday', false) && filled($p->birthday))
                <div>
                  <dt class="text-stone-500">Birthday</dt>
                  <dd>
                    {{ \Carbon\Carbon::parse($p->birthday)->toFormattedDateString() }}
                    @if(!is_null($age)) ({{ $age }}) @endif
                  </dd>
                </div>
              @endif
            </dl>
          @endif

          {{-- Badges --}}
          @if($p)
            @php
              $badges = [];
              if ($fanYears && $fanYears >= 10) $badges[] = $fanYears.'y Fan';
              if ($p->rally_role) $badges[] = $p->rally_role;
            @endphp
            @if(!empty($badges))
              <div class="mt-2 flex flex-wrap gap-1">
                @foreach($badges as $b)
                  <span class="px-2 py-0.5 text-xs rounded-full ring-1 ring-black/5 dark:ring-white/10">{{ $b }}</span>
                @endforeach
              </div>
            @endif
          @endif

          {{-- Bio --}}
          @if ($p && filled($p->bio))
            <div class="mt-6">
              <h2 class="text-lg font-bold mb-1 dark:text-stone-100">🧾 About Me</h2>
              <p class="text-gray-700 leading-relaxed dark:text-stone-300">{{ $p->bio }}</p>
            </div>
          @endif

          {{-- Favorites --}}
          @if ($p && (($p->favorite_driver ?? null) || ($p->favorite_car ?? null) || ($p->favorite_event ?? null) || ($p->favorite_game ?? null)))
            @if($show('show_favorites', true))
              <div class="mt-6">
                <h2 class="text-lg font-bold mb-1 dark:text-stone-100">⭐ Favorites</h2>
                <ul class="mt-1 grid sm:grid-cols-2 gap-y-1 text-sm">
                  @if($p->favorite_driver)<li><span class="text-stone-500">Driver:</span> {{ $p->favorite_driver }}</li>@endif
                  @if($p->favorite_car)<li><span class="text-stone-500">Car:</span> {{ $p->favorite_car }}</li>@endif
                  @if($p->favorite_event)<li><span class="text-stone-500">Event:</span> {{ $p->favorite_event }}</li>@endif
                  @if($p->favorite_game)<li><span class="text-stone-500">Game:</span> {{ $p->favorite_game }}</li>@endif
                </ul>
              </div>
            @endif
          @endif

          {{-- Car Setup Notes --}}
          @if ($p && filled($p->car_setup_notes) && $show('show_car_setup_notes', false))
            <div class="mt-6">
              <h2 class="text-lg font-bold mb-1 dark:text-stone-100">🔧 Car Setup Notes</h2>
              <pre class="mt-1 text-sm bg-stone-50 dark:bg-stone-950 p-3 rounded-lg overflow-x-auto">{{ $p->car_setup_notes }}</pre>
            </div>
          @endif

          {{-- Social Links --}}
          @if($p && !empty($socials) && $show('show_socials', true))
            <div class="mt-6">
              <h2 class="text-lg font-bold mb-1 dark:text-stone-100">🔗 Links</h2>
              <div class="mt-2 flex flex-wrap gap-2">
                @foreach($socials as $label => $href)
                  <a href="{{ $href }}" target="_blank" rel="noopener"
                     class="px-3 py-1.5 rounded-lg ring-1 ring-black/5 dark:ring-white/10 text-sm hover:bg-stone-100 dark:hover:bg-stone-800">
                    {{ $label }}
                  </a>
                @endforeach
              </div>
            </div>
          @endif

          {{-- Empty state --}}
          @if(!$p)
            <p class="text-sm text-gray-500 mt-4 dark:text-stone-400">This user hasn't filled out their profile yet.</p>
          @endif
      </div>
  </div>
</div>

@endsection