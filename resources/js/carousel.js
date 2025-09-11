// Global Alpine factory for x-data="carousel({ interval: 6, count: N })"
window.carousel = (opts = {}) => {
  const cfg = { interval: 6, count: 1, start: 0, autoplay: true, ...opts }

  return {
    // state
    index: cfg.start,
    count: cfg.count,

    // timers & touch
    timer: null,
    startX: 0,
    dx: 0,

    // lifecycle
    init() {
      // apply initial transform and keep it in sync when index changes
      this.applyTransform()
      this.$watch('index', () => this.applyTransform())

      // autoplay
      if (cfg.autoplay && this.count > 1 && cfg.interval) this.play()
    },
    destroy() {
      this.pause()
    },

    // autoplay controls (needed by @mouseenter="pause()" @mouseleave="play()")
    play() {
      if (!this.timer && cfg.interval) {
        this.timer = setInterval(() => this.next(), cfg.interval * 1000)
      }
    },
    pause() {
      if (this.timer) {
        clearInterval(this.timer)
        this.timer = null
      }
    },

    // navigation (used by next/prev buttons & dots)
    next() { this.index = (this.index + 1) % this.count },
    prev() { this.index = (this.index - 1 + this.count) % this.count },
    go(i)  { this.index = ((i % this.count) + this.count) % this.count },

    // classes used in your markup
    dotClass(i)   { return this.index === i ? 'bg-white ring-2 ring-amber-400' : 'bg-zinc-500/60' },
    slideClass(i) { return this.index === i ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-full' },

    // style helpers (safe alternatives to complex :style expressions)
    get trackStyle() { return { transform: `translateX(-${this.index * 100}%)` } }, // if you bind :style="trackStyle"
    get viewportStyle() { return '' }, // satisfies any :style="viewportStyle" you have

    // direct DOM application so you don't need :style="…"
    applyTransform() {
      if (this.$refs && this.$refs.track) {
        this.$refs.track.style.transform = `translateX(-${this.index * 100}%)`
      }
    },

    // event hooks referenced in markup
    onTransitionEnd() { /* no-op hook to satisfy @transitionend */ },

    // touch support (used by @touchstart/@touchmove/@touchend)
    touchStart(e) {
      this.pause()
      this.startX = (e.touches ? e.touches[0] : e).clientX
      this.dx = 0
    },
    touchMove(e) {
      const x = (e.touches ? e.touches[0] : e).clientX
      this.dx = x - this.startX
    },
    touchEnd() {
      if (Math.abs(this.dx) > 40) {
        this.dx < 0 ? this.next() : this.prev()
      }
      this.play()
    },
  }
}