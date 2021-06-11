$(function() {

    const modals = {
        date: $('#date-modal')
    }

    const calendar_events = Object.values(config.events).map(event => Object.values(event)).flat();

    var calendar = new FullCalendar.Calendar($('#calendar').get(0), {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        selectable: true,
        initialView: 'dayGridMonth',
        events: calendar_events,
        eventClick: function(info) {
            //console.log(info
            console.log(info.event.extendedProps)
            //console.log('Event: ' + info.event.title);
            //console.log('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
            //console.log('View: ' + info.view.type);
            //console.log(info.el)
        },
        dateClick: function(info) {

            let events = config.events[info.dateStr] ?? false;

            if(!events) return;

            console.log(events)

            modals.date.modal('show');
            modals.date.find('.date-title').text(`Events for ${info.dateStr}`);
            modals.date.find('.date-events').html(events);
            //window.location.href = `${config.routes.create}?date=${info.dateStr}`;
        },
        select: function(info) {
            //console.log(arguments)
            //this is also applicable to multiple day selections
            return //!console.log('select', arguments)
        }
    });

    calendar.render();

})