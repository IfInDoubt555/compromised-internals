export default function initScrollControls() {
  const topBtn = document.getElementById('back-to-top');
  const midBtn = document.getElementById('scroll-middle');
  const botBtn = document.getElementById('scroll-bottom');

  function toggle() {
    const visible = window.scrollY > 200;
    [topBtn, midBtn, botBtn].forEach(btn => {
      if (!btn) return;
      btn.classList.toggle('hidden', !visible);
    });
  }

  window.addEventListener('scroll', toggle);
  window.addEventListener('load', toggle);

  topBtn?.addEventListener('click', e => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  midBtn?.addEventListener('click', e => {
    e.preventDefault();
    window.scrollTo({ top: document.body.scrollHeight / 2, behavior: 'smooth' });
  });

  botBtn?.addEventListener('click', e => {
    e.preventDefault();
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
  });
}