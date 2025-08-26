import './bootstrap';
import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';   // ← add this
import './stages';

Alpine.plugin(intersect);                      // ← and this
window.Alpine = Alpine;
Alpine.start();

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

window.FullCalendar = {
  Calendar,
  dayGridPlugin,
  interactionPlugin,
  listPlugin,
};

const mq = window.matchMedia('(max-width: 768px)');
const isMobile = () => mq.matches;

document.addEventListener('DOMContentLoaded', () => {
  const calendarEl = document.getElementById('calendar');
  if (!calendarEl) return;

  // --- championship filter state ---
  const urlChamp = new URLSearchParams(location.search).get('champ');
  const state = { champ: urlChamp ? urlChamp.toUpperCase() : null };

  const CHAMP_CLASS = (c) => {
    const key = String(c || '').toUpperCase();
    if (key === 'ARC') return 'champ-ara'; // alias ARC -> ARA
    if (key === 'WRC') return 'champ-wrc';
    if (key === 'ERC') return 'champ-erc';
    if (key === 'ARA') return 'champ-ara';
    return '';
  };

  const headerFor = () =>
    isMobile()
      ? { left: 'title', right: 'prev,next' }
      : { left: 'prev,next today', center: 'title', right: '' };

  const initialViewFor = () => (isMobile() ? 'listMonth' : 'dayGridMonth');

  // Build GCal URL for all-day events (used in list view “Add to Google”)
  const gcalAllDayUrl = (ev) => {
    const pad = (n) => String(n).padStart(2, '0');
    const ymd = (d) => `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}`;
    const start = ev.start ? ymd(ev.start) : '';
    const end = ev.end ? ymd(ev.end) : start;
    const params = new URLSearchParams({
      action: 'TEMPLATE',
      text: ev.title || 'Rally',
      dates: `${start}/${end}`,
      details: ev.extendedProps?.description || '',
      location: ev.extendedProps?.location || '',
    });
    return `https://calendar.google.com/calendar/render?${params.toString()}`;
  };

  // Prefer /api/events, but fall back to /calendar/events if needed
  const EVENTS_ENDPOINTS = ['/api/events', '/calendar/events'];

  const calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, interactionPlugin, listPlugin],
    initialView: initialViewFor(),
    headerToolbar: headerFor(),
    contentHeight: 'auto',
    expandRows: true,
    dayMaxEvents: true,

    displayEventTime: false,
    timeZone: 'local',

    views: {
      listMonth: { noEventsContent: 'No rallies this month.' },
    },

    windowResize() {
      const nextView = initialViewFor();
      if (calendar.view.type !== nextView) calendar.changeView(nextView);
      calendar.setOption('headerToolbar', headerFor());
    },

    // Color classes per championship
    eventClassNames(arg) {
      const c = arg.event.extendedProps?.championship;
      const cls = CHAMP_CLASS(c);
      return cls ? [cls] : [];
    },

    // Fetch events with optional championship filter (+ endpoint fallback)
    events(info, success, failure) {
      const params = new URLSearchParams({ start: info.startStr, end: info.endStr });
      if (state.champ) params.set('champ', state.champ);

      let i = 0;
      const tryFetch = () =>
        fetch(`${EVENTS_ENDPOINTS[i]}?${params.toString()}`)
          .then((r) => {
            if (!r.ok) throw new Error(`HTTP ${r.status}`);
            return r.json();
          })
          .then((data) => {
            console.log('Events loaded:', data);
            success(data);
          })
          .catch((err) => {
            if (++i < EVENTS_ENDPOINTS.length) return tryFetch();
            console.error('Error loading events:', err);
            failure(err);
          });

      tryFetch();
    },

    eventDataTransform(eventData) {
      return { ...eventData, allDay: true };
    },

    // Keep default link behavior; add "Add to Google" only in list view
    eventDidMount(arg) {
      if (!arg.view.type.startsWith('list')) return;
      const titleEl = arg.el.querySelector('.fc-list-event-title');
      if (!titleEl || titleEl.querySelector('a[data-gcal]')) return;
      const a = document.createElement('a');
      a.href = gcalAllDayUrl(arg.event);
      a.target = '_blank';
      a.rel = 'noopener';
      a.textContent = 'Add to Google';
      a.className = 'ml-2 text-xs underline text-blue-700';
      a.dataset.gcal = '1';
      titleEl.appendChild(a);
    },

    // Guard: if you ever add custom logic, don’t hijack valid anchor URLs
    eventClick(info) {
      if (info.event.url) return; // let the browser follow the slug link
    },
  });

  calendar.render();

  // ------------------------------
  // Keep ICS drawer links in sync
  // ------------------------------

  // Helpers to derive/templating URLs when templates aren’t provided.
  const deriveTplFromUrl = (urlStr) => {
    try {
      const u = new URL(urlStr, window.location.origin);
      // Replace a year-like segment in the pathname with {year}
      const replaced = u.pathname.replace(/(?:^|\/)(\d{4})(?=\.ics|\/)/, (m, y) => m.replace(y, '{year}'));
      if (replaced !== u.pathname) {
        u.pathname = replaced;
        u.search = ''; // clear query; we re-add champ each time
        return u.pathname + (u.search || '');
      }
    } catch {}
    return null;
  };

  const buildFeedUrl = (year, champ) => {
    const feedTplAttr = document.body?.dataset?.feedTpl || null;
    const urlInput = document.getElementById('icsFeedUrl');
    const appleBtn  = document.getElementById('ics-apple-btn');
    const gcalBtn   = document.getElementById('ics-gcal-btn');

    let tpl = feedTplAttr;
    if (!tpl && urlInput?.value) tpl = deriveTplFromUrl(urlInput.value);
    if (!tpl && gcalBtn?.href) {
      try {
        const cid = new URL(gcalBtn.href).searchParams.get('cid');
        if (cid) tpl = deriveTplFromUrl(cid);
      } catch {}
    }
    if (!tpl && appleBtn?.href) {
      const https = appleBtn.href.replace(/^webcal:\/\//, 'https://');
      tpl = deriveTplFromUrl(https);
    }

    // If we still don’t have a template, just return the current value (no year switching).
    if (!tpl) {
      const fallback = urlInput?.value || (appleBtn?.href ? appleBtn.href.replace(/^webcal:\/\//, 'https://') : null);
      if (!fallback) return null;
      try {
        const u = new URL(fallback, window.location.origin);
        if (champ) {
          u.searchParams.set('champ', champ);
        } else {
          u.searchParams.delete('champ');
        }
        return u.toString();
      } catch {
        return null;
      }
    }

    // Build from template
    const feedUrl = new URL(tpl.replace('{year}', year), window.location.origin);
    if (champ) {
      feedUrl.searchParams.set('champ', champ);
    } else {
      feedUrl.searchParams.delete('champ');
    }
    return feedUrl.toString();
  };

  const buildDownloadUrl = (year, champ) => {
    const dlTplAttr = document.body?.dataset?.downloadTpl || null;
    const dlBtn     = document.getElementById('ics-download-btn');

    let tpl = dlTplAttr;
    if (!tpl && dlBtn?.href) tpl = deriveTplFromUrl(dlBtn.href);

    if (!tpl) return dlBtn?.href || null;

    const dlUrl = new URL(tpl.replace('{year}', year), window.location.origin);
    if (champ) {
      dlUrl.searchParams.set('champ', champ);
    } else {
      dlUrl.searchParams.delete('champ');
    }
    return dlUrl.toString();
  };

  function updateIcsLinks() {
    const year  = calendar.view.currentStart.getFullYear();
    const champ = state?.champ ? String(state.champ).toUpperCase() : null;

    // Elements in the drawer
    const gcalBtn    = document.getElementById('ics-gcal-btn');
    const appleBtn   = document.getElementById('ics-apple-btn');
    const outlookBtn = document.getElementById('ics-outlook-btn');
    const dlBtn      = document.getElementById('ics-download-btn');
    const urlInput   = document.getElementById('icsFeedUrl');

    const httpsFeed = buildFeedUrl(year, champ);
    if (httpsFeed) {
      const webcalHref = httpsFeed.replace(/^https?:\/\//, 'webcal://');
      const gcalHref   = 'https://calendar.google.com/calendar/r?cid=' + encodeURIComponent(httpsFeed);

      if (gcalBtn)    gcalBtn.href = gcalHref;
      if (appleBtn)   appleBtn.href = webcalHref;
      if (outlookBtn) outlookBtn.href = webcalHref;
      if (urlInput)   urlInput.value = httpsFeed;
    }

    const dlHref = buildDownloadUrl(year, champ);
    if (dlBtn && dlHref) dlBtn.href = dlHref;
  }

  // run on start and whenever the view changes
  calendar.on('datesSet', updateIcsLinks);
  updateIcsLinks();

  // ------------------------------
  // Filter-chip wiring
  // ------------------------------
  const chipBtns = document.querySelectorAll('#cal-controls [data-champ]');
  chipBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      state.champ = btn.dataset.champ ? btn.dataset.champ.toUpperCase() : null;

      chipBtns.forEach((b) => {
        const active = b === btn;
        b.classList.toggle('bg-gray-700', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('bg-gray-200', !active);
        b.classList.toggle('text-gray-900', !active);
      });

      calendar.refetchEvents();
      updateIcsLinks(); // keep drawer links in sync with current filter
    });
  });
});