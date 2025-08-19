import EmblaCarousel from 'embla-carousel'

function initStageCarousel(root) {
  const viewport = root.querySelector('[data-embla-viewport]')
  if (!viewport) return
  const prev = root.querySelector('[data-embla-prev]')
  const next = root.querySelector('[data-embla-next]')
  const embla = EmblaCarousel(viewport, { loop: true, align: 'center' })
  prev?.addEventListener('click', () => embla.scrollPrev())
  next?.addEventListener('click', () => embla.scrollNext())
}

function boot() {
  document.querySelectorAll('[data-stage-carousel]').forEach(initStageCarousel)
}

document.addEventListener('DOMContentLoaded', boot)
// if you do partial page updates/Ajax later, you can also call window.bootStageCarousels()
window.bootStageCarousels = boot
