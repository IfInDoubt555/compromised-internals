{{-- Reusable blog carousel --}}
@props([
  /** @var \Illuminate\Support\Collection|\App\Models\Post[] $posts */
  'posts',
  // Card variant from your blog-post-card partial
  'variant' => 'featured',
  // Autoplay seconds (0 = off)
  'interval' => 6,
])

@php
  $items = $posts->values(); // ensure 0-based indexes
@endphp

@if ($items->count() > 0)
<div
  x-data="carousel({
    interval: {{ (int) $interval }},
    count: {{ $items->count() }},
  })"
  x-init="init()"
  @keydown.right.prevent="next()"
  @keydown.left.prevent="prev()"
  class="relative"
>
  {{-- Track --}}
<div class="overflow-hidden rounded-2xl ring-1 ring-black/5 dark:ring-white/10"
     x-ref="viewport"
     :style="viewportStyle">
  <div
    x-ref="track"
    class="flex transition-transform duration-500 ease-in-out will-change-transform"
    :style="`transform: translateX(-${index * 100}%)`"
    @transitionend="onTransitionEnd()"
    @mouseenter="pause()" @mouseleave="play()"
    @touchstart.passive="touchStart($event)" @touchmove.passive="touchMove($event)" @touchend.passive="touchEnd()"
  >
      {{-- Clone: last --}}
      <div class="w-full shrink-0">
        @include('partials.blog-post-card', ['post' => $items->last(), 'variant' => $variant])
      </div>

      {{-- Slides --}}
      @foreach ($items as $post)
        <div class="w-full shrink-0">
          @include('partials.blog-post-card', ['post' => $post, 'variant' => $variant])
        </div>
      @endforeach

      {{-- Clone: first --}}
      <div class="w-full shrink-0">
        @include('partials.blog-post-card', ['post' => $items->first(), 'variant' => $variant])
      </div>
    </div>
  </div>

  {{-- Arrows --}}
  <button
    type="button"
    @click="prev()"
    class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-black/40 hover:bg-black/60 text-white p-2 backdrop-blur focus:outline-none focus:ring-2 focus:ring-white/70"
    aria-label="Previous"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M15.53 4.47 7.999 12l7.53 7.53-1.06 1.06L5.88 12l8.59-8.59 1.06 1.06Z"/></svg>
  </button>
  <button
    type="button"
    @click="next()"
    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-black/40 hover:bg-black/60 text-white p-2 backdrop-blur focus:outline-none focus:ring-2 focus:ring-white/70"
    aria-label="Next"
  >
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M8.47 4.47 16 12l-7.53 7.53 1.06 1.06L18.12 12 9.53 3.41 8.47 4.47Z"/></svg>
  </button>

  {{-- Dots --}}
  <div class="mt-3 flex items-center justify-center gap-2">
    @for ($i = 0; $i < $items->count(); $i++)
      <button
        type="button"
        :class="dotClass({{ $i }})"
        @click="goTo({{ $i }})"
        class="h-2.5 w-2.5 rounded-full transition-all"
        aria-label="Go to slide {{ $i + 1 }}"
      ></button>
    @endfor
  </div>
</div>
@endif

{{-- Alpine helper --}}
<script>
  function carousel({ interval = 6, count = 1 }) {
    return {
      index: 1,                 // start at first real slide (after the cloned last)
      count,
      playing: interval > 0,
      timer: null,
      startX: 0,
      deltaX: 0,
      viewportStyle: '',

      init() {
        this.play();
        this.$nextTick(() => this.updateHeight());
        window.addEventListener('resize', () => this.updateHeight(), { passive: true });
      },
      play() {
        if (!this.playing || this.count <= 1) return;
        this.stop();
        this.timer = setInterval(() => this.next(), interval * 1000);
      },
      pause() { this.stop(); },
      stop() { if (this.timer) clearInterval(this.timer); this.timer = null; },

      next() { this.index++; this.$nextTick(() => this.updateHeight()); },
      prev() { this.index--; this.$nextTick(() => this.updateHeight()); },

      onTransitionEnd() {
        // Jump without animation at the edges to keep it infinite
        const track = this.$refs.track;
        track.classList.remove('transition-transform');
        if (this.index === this.count + 1) this.index = 1;     // from cloned first -> real first
        if (this.index === 0) this.index = this.count;         // from cloned last -> real last
        // Force style update then restore transition for subsequent moves
        requestAnimationFrame(() => requestAnimationFrame(() => {
               track.classList.add('transition-transform');
               this.updateHeight();
             }));
      },

      goTo(i) { this.index = i + 1; },

      dotClass(i) { return (this.realIndex() === i) ? 'bg-white w-6' : 'bg-white/40'; },
      realIndex() {
        if (this.index === 0) return this.count - 1;
        if (this.index === this.count + 1) return 0;
        return this.index - 1;
      },

      // Touch support
      touchStart(e) { this.pause(); this.startX = e.touches[0].clientX; this.deltaX = 0; },
      touchMove(e) { this.deltaX = e.touches[0].clientX - this.startX; },
      touchEnd() {
        if (Math.abs(this.deltaX) > 50) this.deltaX < 0 ? this.next() : this.prev();
        this.play();
      },

      updateHeight() {
        // Current slide element (account for the leading clone)
        const slideIdx = this.index; // 0 = last clone, 1..count = real, count+1 = first clone
        const track = this.$refs.track;
        if (!track) return;
        const slides = track.children;
        const el = slides?.[slideIdx];
        if (!el) return;
        const h = el.offsetHeight;
        if (h > 0) this.viewportStyle = `height:${h}px`;
      }
    }
  }
</script>