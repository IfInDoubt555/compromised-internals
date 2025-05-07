@if ($paginator->hasPages())
    <div class="flex flex-col items-center justify-center mt-4 gap-2 text-sm text-gray-800">
        {{-- Result Count --}}
        <div>
            @if ($paginator->firstItem())
                Showing <span class="font-semibold text-red-600">{{ $paginator->firstItem() }}</span>
                to <span class="font-semibold text-red-600">{{ $paginator->lastItem() }}</span>
                of <span class="font-semibold text-red-600">{{ $paginator->total() }}</span> results
            @else
                Showing <span class="font-semibold text-white">{{ $paginator->count() }}</span> results
            @endif
        </div>

        {{-- Pagination Controls --}}
        <nav class="flex items-center space-x-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 bg-gray-700 text-gray-400 rounded-md cursor-not-allowed">«</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md">«</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2 bg-gray-700 text-gray-400 rounded-md">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-2 bg-red-600 text-white font-semibold rounded-md">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md">»</a>
            @else
                <span class="px-3 py-2 bg-gray-700 text-gray-400 rounded-md cursor-not-allowed">»</span>
            @endif
        </nav>
    </div>
@endif
