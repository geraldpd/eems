$(function() {

    var calendar = new FullCalendar.Calendar($('#calendar').get(0), {
        selectable: true,
        initialView: 'dayGridMonth',
        events: config.events,
        eventClick: function(info) {
            console.log(info.event.extendedProps)
            //console.log('Event: ' + info.event.title);
            //console.log('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
            //console.log('View: ' + info.view.type);
            //console.log(info.el)
        },
        dateClick: function(info) {
            console.log('dateClick', info);
            window.location.href = `${config.routes.create}?date=${info.dateStr}`;

        },
        select: function(info) {
            //this is also applicable to multiple day selections
            return //!console.log('select', arguments)
        }
    });

    calendar.render();
})