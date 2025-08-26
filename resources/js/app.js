import './bootstrap';
import Alpine from 'alpinejs';
import './stages';

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
                
            // ⬇️ Add these
            displayEventTime: false,   // hides "12a" or time text
            timeZone: 'local',         // optional, safe when sending YYYY-MM-DD only
                
            events: function (info, successCallback, failureCallback) {
                fetch('/api/events?start=' + info.startStr + '&end=' + info.endStr)
                    .then(response => response.json())
                    .then(data => {
                        console.log('Events loaded from API:', data);
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
                    allDay: true
                };
            }
        });
        calendar.render();
    }
});