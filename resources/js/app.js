import './bootstrap';
import Alpine from 'alpinejs';
import './stages';

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
  const state = { champ: null };

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

  // Build GCal URL for all-day events
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
      // If you later include a slug in extendedProps, you could do:
      // window.location.href = `/calendar/${info.event.extendedProps.slug}`;
    },
  });

  calendar.render();

  // Filter-chip wiring
  document.querySelectorAll('#cal-controls [data-champ]').forEach((btn) => {
    btn.addEventListener('click', () => {
      state.champ = btn.dataset.champ || null;
      document.querySelectorAll('#cal-controls [data-champ]').forEach((b) => {
        const active = b === btn;
        b.classList.toggle('bg-gray-700', active);
        b.classList.toggle('text-white', active);
        b.classList.toggle('bg-gray-200', !active);
        b.classList.toggle('text-gray-900', !active);
      });
      calendar.refetchEvents();
    });
  });
});