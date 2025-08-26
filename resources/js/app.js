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

  const headerFor = () =>
    isMobile()
      ? { left: 'title', right: 'prev,next' }
      : { left: 'prev,next today', center: 'title', right: '' };

  const initialViewFor = () => (isMobile() ? 'listMonth' : 'dayGridMonth');

  // Build a Google Calendar "Add" URL for all-day events
  const gcalAllDayUrl = (ev) => {
    const pad = (n) => String(n).padStart(2, '0');
    const ymd = (d) => `${d.getFullYear()}${pad(d.getMonth() + 1)}${pad(d.getDate())}`;

    // FullCalendar sets all-day end to next day (exclusive). That’s fine for GCal.
    const start = ev.start ? ymd(ev.start) : '';
    const end   = ev.end ? ymd(ev.end) : start;

    const params = new URLSearchParams({
      action: 'TEMPLATE',
      text: ev.title || 'Rally',
      dates: `${start}/${end}`,
      details: ev.extendedProps?.description || '',
      location: ev.extendedProps?.location || '',
    });

    return `https://calendar.google.com/calendar/render?${params.toString()}`;
  };

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
      listMonth: {
        noEventsContent: 'No rallies this month.',
      },
    },

    windowResize() {
      const nextView = initialViewFor();
      if (calendar.view.type !== nextView) {
        calendar.changeView(nextView);
      }
      calendar.setOption('headerToolbar', headerFor());
    },

    events(info, success, failure) {
      fetch(`/api/events?start=${info.startStr}&end=${info.endStr}`)
        .then((r) => r.json())
        .then((data) => {
          console.log('Events loaded from API:', data);
          success(data);
        })
        .catch((err) => {
          console.error('Error loading events:', err);
          failure(err);
        });
    },

    eventDataTransform(eventData) {
      // Your events are all-day on the site
      return { ...eventData, allDay: true };
    },

    // Add “Add to Google” to list view rows
    eventDidMount(arg) {
      if (!arg.view.type.startsWith('list')) return;

      const titleEl = arg.el.querySelector('.fc-list-event-title');
      if (!titleEl || titleEl.querySelector('a[data-gcal]')) return; // avoid duplicates

      const a = document.createElement('a');
      a.href = gcalAllDayUrl(arg.event);
      a.target = '_blank';
      a.rel = 'noopener';
      a.textContent = 'Add to Google';
      a.className = 'ml-2 text-xs underline text-blue-700';
      a.dataset.gcal = '1';
      titleEl.appendChild(a);
    },
  });

  calendar.render();
});