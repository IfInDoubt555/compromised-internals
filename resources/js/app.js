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
      return { ...eventData, allDay: true };
    },
  });

  calendar.render();
});