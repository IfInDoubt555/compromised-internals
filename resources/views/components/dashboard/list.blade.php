@props(['items'=>collect(), 'title'=>'Items', 'showRoute'=>null, 'editRoute'=>null, 'deleteRoute'=>null])

<div class="bg-white shadow rounded-2xl p-4">
  <h3 class="font-bold mb-2">{{ $title }}</h3>

  @if($items->isEmpty())
    <p class="text-sm text-gray-500">Nothing here yet.</p>
  @else
    <ul class="space-y-2">
      @foreach($items as $item)
        <li class="flex items-center justify-between">
          <a href="{{ route($showRoute, $item) }}" class="truncate hover:underline">
            {{ $item->title }}
          </a>
          <div class="flex items-center gap-3 text-sm">
            @if($editRoute)
              <a href="{{ route($editRoute, $item) }}" class="underline">Edit</a>
            @endif
            @if($deleteRoute)
              <form method="POST" action="{{ route($deleteRoute, $item) }}"
                    onsubmit="return confirm('Delete this item?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 underline">Delete</button>
              </form>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  @endif
</div>