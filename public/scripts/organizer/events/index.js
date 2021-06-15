$(function() {

    const modals = {
        date: $('#date-modal')
    }

    const events = Object.values(config.events).map(event => Object.values(event)).flat();

    console.log(events)
    console.log(config.events)
    var calendar = new FullCalendar.Calendar($('#calendar').get(0), {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        selectable: true,
        initialView: 'dayGridMonth',
        events: events,
        eventClick: function(info) {
            //console.log(info
            console.log(info.event.extendedProps)
            //console.log('Event: ' + info.event.title);
            //console.log('Coordinates: ' + info.jsEvent.pageX + ',' + info.jsEvent.pageY);
            //console.log('View: ' + info.view.type);
            //console.log(info.el)
        },
        dateClick: function(info) {
            modals.date.modal('show');
            modals.date.find('.date-title').text(`Events for ${info.dateStr}`);
            modals.date.find('.add-event-button').attr('href', `${config.routes.create}?date=${info.dateStr}`);

            let events = config.events[info.dateStr] ?? false;

            if(!events) {
                modals.date.find('.date-events').html('<h3>No event scheduled for this date!</h3>');
                return
            };

            dateModal(events, info)
        },
        select: function(info) {
            //console.log(arguments)
            //this is also applicable to multiple day selections
            return //!console.log('select', arguments)
        }
    });

    function dateModal(data, info)
    {
        let event_row = event => {
            let date_formater = (date) => ({time: moment(date).format('hh:mm A'), day: moment(date).format('MMMM D YYYY')});

            return `
            <div class="event row">
                <div class="col-md-9">
                    <small>${event.location}</small>
                    <h2 style="padding:0px;">${event.name}</h2>
                    <p>
                        <strong>${date_formater(event.schedule_start).time}</strong> ${date_formater(event.schedule_start).day}
                        </br>
                        <strong>${date_formater(event.schedule_end).time}</strong> ${date_formater(event.schedule_end).day}
                    </p>
                </div>
                <div class="col-md-3">
                    <p>Category: <b>${event.category.name}</b></p>
                    <p>Type: <b>${event.type}</b></p>
                    <a href="${config.routes.edit.replace('resource_id', event.id)}" class="btn btn-primary">view</a>
                </div>
            </div>`;

        };

        let date_events = data.map(data => event_row(data.event)).join('<hr>');

        modals.date.find('.date-events').html(date_events);
    }

    calendar.render();

})