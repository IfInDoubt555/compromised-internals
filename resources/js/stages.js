import EmblaCarousel from 'embla-carousel'

// ---------- Public carousel ----------
function initStageCarousel(root) {
  const viewport = root.querySelector('[data-embla-viewport]')
  if (!viewport) return
  const prev = root.querySelector('[data-embla-prev]')
  const next = root.querySelector('[data-embla-next]')
  const embla = EmblaCarousel(viewport, { loop: true, align: 'center' })
  prev?.addEventListener('click', () => embla.scrollPrev())
  next?.addEventListener('click', () => embla.scrollNext())
}

// ---------- Admin form helpers ----------
function initStageForm() {
  // Bail if we’re not on the admin stages form page
  const typeSel    = document.getElementById('stage_type')
  const ssInput    = document.querySelector('input[name="ss_number"]')
  const daySelect  = document.getElementById('day_select')
  const startInput = document.getElementById('start_time_local')

  const secondDay  = document.getElementById('second_day_select')
  const secondTime = document.getElementById('second_pass_time_local')

  if (!typeSel && !daySelect && !startInput && !secondDay && !secondTime) return

  // Toggle SS # when Type = SD (Shakedown)
  const toggleSS = () => {
    const isSD = typeSel?.value === 'SD'
    if (ssInput) {
      ssInput.disabled = !!isSD
      // dim the SS # field visually when disabled
      const wrapper = ssInput.closest('div')
      if (wrapper) wrapper.style.opacity = isSD ? '0.6' : '1'
    }
  }
  typeSel?.addEventListener('change', toggleSS)
  toggleSS()

  // Auto-select Day from Start timestamp (YYYY-MM-DDTHH:mm)
  startInput?.addEventListener('change', () => {
    const v = startInput.value
    if (!v || !daySelect) return
    const d = v.split('T')[0]
    const opt = [...daySelect.options].find(o => o.dataset.date === d)
    if (opt) daySelect.value = opt.value
  })

  // When a Day is chosen and Start is empty, seed sensible default times
  daySelect?.addEventListener('change', () => {
    const opt = daySelect.options[daySelect.selectedIndex]
    const base = opt?.dataset?.date
    if (!base) return
    if (startInput && !startInput.value) startInput.value = `${base}T08:00`
    if (secondTime && !secondTime.value) secondTime.value = `${base}T13:00`
  })

  // Auto-select Second Day from Second Pass timestamp
  secondTime?.addEventListener('change', () => {
    const v = secondTime.value
    if (!v || !secondDay) return
    const d = v.split('T')[0]
    const opt = [...secondDay.options].find(o => o.dataset.date === d)
    if (opt) secondDay.value = opt.value
  })
}

// ---------- Boot both (safe to run on any page) ----------
function boot() {
  document.querySelectorAll('[data-stage-carousel]').forEach(initStageCarousel)
  initStageForm()
}

document.addEventListener('DOMContentLoaded', boot)
// if you do partial page updates/Ajax later:
window.bootStageCarousels = boot