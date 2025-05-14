import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

window.FullCalendar = {
    Calendar,
    dayGridPlugin,
    interactionPlugin
};

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    if (calendarEl) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            events: function (info, successCallback, failureCallback) {
                fetch('/api/events?start=' + info.startStr + '&end=' + info.endStr)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Events loaded from API:', data); // ✅ DEBUG HERE
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Error loading events:', error);
                        failureCallback(error);
                    });
            },
                        eventDataTransform: function (eventData) {
                return {
                    ...eventData,
                    allDay: true // ⬅️ Ensure full-day rendering
                };
            }
        });

        calendar.render();
    }
});