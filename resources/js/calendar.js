import '@fullcalendar/daygrid/index.css';
import '@fullcalendar/list/index.css';

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

const mq = window.matchMedia('(max-width: 768px)');
const isMobile = () => mq.matches;

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

// ---- Helpers for the ICS subscribe/download controls ----
function deriveTplFromUrl(urlStr) {
  try {
    const u = new URL(urlStr, window.location.origin);
    const replaced = u.pathname.replace(/(?:^|\/)(\d{4})(?=\.ics|\/)/, (m, y) => m.replace(y, '{year}'));
    if (replaced !== u.pathname) {
      u.pathname = replaced;
      u.search = '';
      return u.pathname + (u.search || '');
    }
  } catch {}
  return null;
}

function buildFeedUrl(year, champ) {
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

  if (!tpl) {
    const fallback = urlInput?.value || (appleBtn?.href ? appleBtn.href.replace(/^webcal:\/\//, 'https://') : null);
    if (!fallback) return null;
    try {
      const u = new URL(fallback, window.location.origin);
      if (champ) u.searchParams.set('champ', champ);
      else u.searchParams.delete('champ');
      return u.toString();
    } catch {
      return null;
    }
  }

  const feedUrl = new URL(tpl.replace('{year}', year), window.location.origin);
  if (champ) feedUrl.searchParams.set('champ', champ);
  else feedUrl.searchParams.delete('champ');
  return feedUrl.toString();
}

function buildDownloadUrl(year, champ) {
  const dlTplAttr = document.body?.dataset?.downloadTpl || null;
  const dlBtn     = document.getElementById('ics-download-btn');

  let tpl = dlTplAttr;
  if (!tpl && dlBtn?.href) tpl = deriveTplFromUrl(dlBtn.href);
  if (!tpl) return dlBtn?.href || null;

  const dlUrl = new URL(tpl.replace('{year}', year), window.location.origin);
  if (champ) dlUrl.searchParams.set('champ', champ);
  else dlUrl.searchParams.delete('champ');
  return dlUrl.toString();
}

function updateIcsLinks(calendar, state) {
  const year  = calendar.view.currentStart.getFullYear();
  const champ = state?.champ ? String(state.champ).toUpperCase() : null;

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

/**
 * Initialize FullCalendar on the given element id (default: 'calendar').
 * Returns the calendar instance, or null if the element isn't found.
 */
export default function initCalendar(containerId = 'calendar') {
  const el = document.getElementById(containerId);
  if (!el) return null;

  // Single, explicit endpoint (can be overridden via data attribute on <body>)
  const endpoint = document.body?.dataset?.eventsEndpoint || '/api/events';

  const urlChamp = new URLSearchParams(location.search).get('champ');
  const state = { champ: urlChamp ? urlChamp.toUpperCase() : null };

  const calendar = new Calendar(el, {
    plugins: [dayGridPlugin, interactionPlugin, listPlugin],
    initialView: initialViewFor(),
    headerToolbar: headerFor(),
    contentHeight: 'auto',
    expandRows: true,
    dayMaxEvents: true,
    displayEventTime: false,
    timeZone: 'local',
    views: { listMonth: { noEventsContent: 'No rallies this month.' } },

    windowResize() {
      const nextView = initialViewFor();
      if (calendar.view.type !== nextView) calendar.changeView(nextView);
      calendar.setOption('headerToolbar', headerFor());
    },

    eventClassNames(arg) {
      const c = arg.event.extendedProps?.championship;
      const cls = CHAMP_CLASS(c);
      return cls ? [cls] : [];
    },

    // ---- Single-source fetch with strong logging ----
    events(info, success, failure) {
      const params = new URLSearchParams({
        start: info.startStr,
        end:   info.endStr,
      });
      if (state.champ) params.set('champ', state.champ);

      const url = `${endpoint}?${params.toString()}`;

      fetch(url)
        .then(res => {
          if (!res.ok) throw new Error(`HTTP ${res.status}`);
          return res.json();
        })
        .then(data => {
          console.log('Events loaded:', { url, count: data?.length, sample: data?.[0] });
          success(data);
        })
        .catch(err => {
          console.error('Failed to load events:', { url, err });
          failure(err);
        });
    },

    eventDataTransform(eventData) {
      return { ...eventData, allDay: true };
    },

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

    eventClick(info) {
      if (info.event.url) return; // keep default navigation if a URL exists
    },
  });

  calendar.render();

  // Keep ICS links in sync with the visible month + filter
  calendar.on('datesSet', () => updateIcsLinks(calendar, state));
  updateIcsLinks(calendar, state);

  // Filter-chip wiring (WRC / ERC / ARA / ALL)
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
      updateIcsLinks(calendar, state);
    });
  });

  return calendar;
}

// Optional: expose for debugging
window.initRallyCalendar = initCalendar;