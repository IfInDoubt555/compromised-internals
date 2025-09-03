import EmblaCarousel from 'embla-carousel';

let booted = false;

function initStageCarousel(section) {
  if (!section || section.dataset.emblaInit === '1') return;
  const viewport = section.querySelector('[data-embla-viewport]');
  if (!viewport) return;

  const prev = section.querySelector('[data-embla-prev]');
  const next = section.querySelector('[data-embla-next]');
  const embla = EmblaCarousel(viewport, { loop: true, align: 'center' });

  prev?.addEventListener('click', () => embla.scrollPrev());
  next?.addEventListener('click', () => embla.scrollNext());

  section.dataset.emblaInit = '1';
}

function initPublicStages() {
  // Scope to the page hook so we don’t touch other pages
  const root = document.querySelector('[data-stages]');
  if (!root) return false;

  // Support either:
  //  - <section data-stage-carousel data-stages>
  //  - <div data-stages> ... <section data-stage-carousel> ... </div>
  const sections = root.matches('[data-stage-carousel]')
    ? [root]
    : Array.from(root.querySelectorAll('[data-stage-carousel]'));

  sections.forEach(initStageCarousel);
  return sections.length > 0;
}

/** Admin form helpers (runs only on admin stages pages) */
function initStageForm() {
  // Detect form; bail if not present
  const typeSel    = document.getElementById('stage_type');
  const ssInput    = document.querySelector('input[name="ss_number"]');
  const ssWrap     = document.getElementById('ss_number_wrap');
  const daySelect  = document.getElementById('day_select');
  const startInput = document.getElementById('start_time_local');
  const secondDay  = document.getElementById('second_day_select');
  const secondTime = document.getElementById('second_pass_time_local');

  const present = typeSel || daySelect || startInput || secondDay || secondTime;
  if (!present) return false;

  const toggleSS = () => {
    const isSD = typeSel?.value === 'SD';
    if (ssInput) {
      if (isSD) {
        ssInput.value = '';
        ssInput.disabled = true;
        ssInput.removeAttribute('required');
      } else {
        ssInput.disabled = false;
        ssInput.setAttribute('required', 'required');
      }
    }
    if (ssWrap) ssWrap.style.opacity = isSD ? '0.6' : '1';
  };
  typeSel?.addEventListener('change', toggleSS);
  toggleSS();

  // Auto-select Day from Start timestamp (YYYY-MM-DDTHH:mm)
  startInput?.addEventListener('change', () => {
    const v = startInput.value;
    if (!v || !daySelect) return;
    const d = v.split('T')[0];
    const opt = [...daySelect.options].find(o => o.dataset.date === d);
    if (opt) daySelect.value = opt.value;
  });

  // Seed sensible defaults when Day chosen
  daySelect?.addEventListener('change', () => {
    const opt = daySelect.options[daySelect.selectedIndex];
    const base = opt?.dataset?.date;
    if (!base) return;
    if (startInput && !startInput.value) startInput.value = `${base}T08:00`;
    if (secondTime && !secondTime.value) secondTime.value = `${base}T13:00`;
  });

  // Auto-select Second Day from second pass time
  secondTime?.addEventListener('change', () => {
    const v = secondTime.value;
    if (!v || !secondDay) return;
    const d = v.split('T')[0];
    const opt = [...secondDay.options].find(o => o.dataset.date === d);
    if (opt) secondDay.value = opt.value;
  });

  return true;
}

/** Public boot: safe to call multiple times */
export default function bootStages() {
  // Idempotent: allow re-run on PJAX but avoid duplicate listeners
  const didPublic = initPublicStages();
  const didAdmin  = initStageForm();

  // Mark that we've attempted boot at least once
  booted = booted || didPublic || didAdmin;
}

// Auto-boot on direct include (back-compat)
if (document.readyState !== 'loading') {
  bootStages();
} else {
  document.addEventListener('DOMContentLoaded', () => bootStages(), { once: true });
}

// Optional: expose for manual reboots after partial updates/Ajax
window.bootStageCarousels = bootStages;