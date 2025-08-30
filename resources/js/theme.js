// resources/js/theme.js
const KEY = 'ci-theme'; // same key your <head> boot script uses

export function setTheme(mode) {
  // mode: 'dark' | 'light' | 'system'
  try { localStorage.setItem(KEY, mode); } catch {}
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const isDark = mode === 'dark' || (mode === 'system' && prefersDark);
  document.documentElement.classList.toggle('dark', isDark);
}

export function initThemeToggle() {
  const btn = document.querySelector('[data-theme-toggle]');
  if (!btn) return;

  btn.addEventListener('click', () => {
    const current = (localStorage.getItem(KEY) || 'system');
    const next = current === 'dark' ? 'light' : current === 'light' ? 'system' : 'dark';
    setTheme(next);
    btn.setAttribute('data-theme-state', next); // optional
  });

  // apply on load
  setTheme(localStorage.getItem(KEY) || 'system');

  // sync if system changes
  const mq = window.matchMedia('(prefers-color-scheme: dark)');
  mq.addEventListener?.('change', () => {
    if ((localStorage.getItem(KEY) || 'system') === 'system') setTheme('system');
  });
}