// resources/js/scrollControls.js
export default function initScrollControls() {
    const topBtn = document.getElementById('back-to-top');
    const midBtn = document.getElementById('scroll-middle');
    const botBtn = document.getElementById('scroll-bottom');
    const tip    = document.getElementById('back-to-top-tooltip');
    let shown = false;
  
    function toggle() {
      const visible = window.scrollY > 200;
      [topBtn, midBtn, botBtn].forEach(btn => {
        if (!btn) return;
        btn.classList.toggle('hidden', !visible);
      });
      if (visible && !shown && tip) {
        shown = true;
        tip.classList.remove('hidden');
        tip.classList.add('fade-in');
        setTimeout(() => tip.classList.add('fade-out'), 3000);
      }
    }
  
    window.addEventListener('scroll', toggle);
    window.addEventListener('load',  toggle);
  
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
  