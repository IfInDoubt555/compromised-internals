@if ($paginator->hasPages())
  <div class="ci-pagination flex flex-col items-center justify-center gap-2 text-sm">
    {{-- Result Count --}}
    <div class="ci-muted">
      @if ($paginator->firstItem())
        Showing
        <span class="font-semibold">{{ $paginator->firstItem() }}</span>
        to
        <span class="font-semibold">{{ $paginator->lastItem() }}</span>
        of
        <span class="font-semibold">{{ $paginator->total() }}</span>
        results
      @else
        Showing <span class="font-semibold">{{ $paginator->count() }}</span> results
      @endif
    </div>

    {{-- Pagination Controls --}}
    <nav role="navigation" aria-label="Pagination" class="flex items-center">
      {{-- Previous Page Link --}}
      @if ($paginator->onFirstPage())
        <span aria-disabled="true" aria-label="@lang('pagination.previous')">
          <span aria-hidden="true">«</span>
        </span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">«</a>
      @endif

      {{-- Pagination Elements --}}
      @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
          <span aria-disabled="true"><span>{{ $element }}</span></span>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="active" aria-current="page"><span>{{ $page }}</span></span>
            @else
              <a href="{{ $url }}">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      {{-- Next Page Link --}}
      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">»</a>
      @else
        <span aria-disabled="true" aria-label="@lang('pagination.next')">
          <span aria-hidden="true">»</span>
        </span>
      @endif
    </nav>
  </div>
@endif