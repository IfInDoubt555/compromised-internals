@props([
    /**
     * Collection|array of items to show.
     * Each item can be:
     *  - \App\Models\Post (expects route-model binding for posts.show)
     *  - array: ['title' => string, 'url' => string, 'meta' => string|null]
     */
    'items'   => collect(),
    'limit'   => 3,
    'heading' => 'Hot Right Now',
])

@php
    $collection = collect($items)->take($limit);

    $toUrl = function ($it) {
        // Post model -> posts.show
        if (is_object($it) && isset($it->id)) {
            try { return route('posts.show', $it); } catch (\Throwable $e) {}
        }
        return data_get($it, 'url', '#');
    };

    $toTitle = fn ($it) => is_object($it) ? ($it->title ?? '(untitled)') : (data_get($it, 'title', '(untitled)'));

    $toMeta = function ($it) {
        if (is_object($it)) {
            $author = $it->author->name ?? $it->user->name ?? null;
            $date   = optional($it->published_at ?? $it->created_at)->format('M j, Y');
            return trim(implode(' • ', array_filter([$author, $date])));
        }
        return data_get($it, 'meta');
    };
@endphp

<div class="ci-card p-4 ring-1 ring-black/5 dark:ring-white/10 bg-white dark:bg-stone-900 text-stone-900 dark:text-stone-100">
    <div class="flex items-center justify-between mb-3">
        <h3 class="ci-title-sm flex items-center gap-2">
            {{-- flame icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12.001 2c.89 2.784-.44 4.93-1.99 6.48-1.1 1.1-2.01 2.009-2.01 3.52A4 4 0 0 0 12 16a4 4 0 0 0 4-4c0-2.237-1.3-3.763-2.51-5.15C12.82 5.36 11.87 4.27 12 2c3.5 2.3 6 6.06 6 10a6 6 0 1 1-12 0c0-3.03 1.7-4.73 3.01-6.04C10.04 4.93 10.77 3.86 12 2z"/>
            </svg>
            <span>{{ $heading }}</span>
        </h3>

        <a href="{{ route('posts.index', ['sort' => 'hot']) }}"
           class="text-xs ci-link underline-offset-2 hover:underline">
            View all
        </a>
    </div>

    @if($collection->isNotEmpty())
        <ul class="space-y-2">
            @foreach($collection as $it)
                <li>
                    <a href="{{ $toUrl($it) }}"
                       class="block rounded-xl ring-1 ring-black/5 dark:ring-white/10
                              bg-stone-50 hover:bg-stone-100
                              dark:bg-stone-800/60 dark:hover:bg-stone-800
                              transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-stone-900">
                        <div class="px-3 py-2">
                            <p class="text-sm font-medium leading-snug line-clamp-2">
                                {{ $toTitle($it) }}
                            </p>
                            @php $meta = $toMeta($it); @endphp
                            @if($meta)
                                <p class="mt-1 text-xs ci-muted">{{ $meta }}</p>
                            @endif
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-sm ci-muted">Nothing trending right now.</p>
    @endif
</div>