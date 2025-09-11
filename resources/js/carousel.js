// Global factory for x-data="carousel({ interval: 6, count: N })"
window.carousel = (opts = {}) => {
  const cfg = { interval: 6, count: 1, start: 0, ...opts }
  return {
    index: cfg.start,
    count: cfg.count,
    timer: null,
    init() {
      if (this.count > 1 && cfg.interval) {
        this.timer = setInterval(() => this.next(), cfg.interval * 1000)
      }
    },
    destroy() { if (this.timer) clearInterval(this.timer) },
    next()    { this.index = (this.index + 1) % this.count },
    prev()    { this.index = (this.index - 1 + this.count) % this.count },
    go(i)     { this.index = i % this.count },
    dotClass(i)   { return this.index === i ? 'bg-white ring-2 ring-amber-400' : 'bg-zinc-500/60' },
    slideClass(i) { return this.index === i ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-full' },
    // Optional: viewport style helper if your markup uses :style="viewportStyle"
    get viewportStyle() { return '' }
  }
}