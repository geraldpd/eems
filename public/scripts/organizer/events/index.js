$(function() {

    const modals = {
        date: $('#date-modal')
    }

    const moment_format = 'YYYY-MM-DD';

    const events = Object.values(config.events).map(event => Object.values(event)).flat();

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
        dateClick: function(info) { //single date

            let events = config.events[info.dateStr] ?? false
                ? constructEventList(config.events[info.dateStr])
                : '<h3>No event scheduled for this date!</h3>';

            dateModal(info, events);
        },
        select: function(info) { //multi date
            let start = moment(info.startStr).add(1, 'days').format(moment_format); //full calendar adds 1 day to info.endStr so we need to add 1 day to start to compensate
            let end = moment(info.endStr).format(moment_format);

            if(start == end) {
                return;
            }

            let selected_dates = constructListOfIntervals(info.startStr, info.endStr, 'days');

            let events = $.map(selected_dates, date => {
                if(!config.events[date]) {
                    return;
                }

                return `<h3>${date}</h3> <div class="col-md-12"> ${constructEventList(config.events[date])}</div> `
            }).join('<br>');

            dateModal(info, events)
        }
    });

    function dateModal(info, events)
    {
        modals.date.modal('show');
        modals.date.find('.date-title').text(`Events for ${info.dateStr}`);
        modals.date.find('.add-event-button').attr('href', `${config.routes.create}?date=${info.dateStr}`);
        modals.date.find('.date-events').html(events);
    }

    function constructEventList(events) {
        let event_row = event => {
            let date_formater = (date) => ({time: moment(date).format('hh:mm A'), day: moment(date).format('MMMM D YYYY')});

            /*
                check if the the event is scheduled today or was in the past
                *TRUE - do not render the edit button
                !FALSE - render the edit button redirecting to the edit page
            */
            let edit_button = moment().diff(event.schedule_start, 'days') >= 0 ? '' : `<a href="${config.routes.edit.replace('resource_id', event.id)}" class="btn btn-primary">view</a>`;

            return `
                <div class="event row">
                    <div class="col-md-9">
                        <small>${event.location}</small>
                        <strong class="lead">${event.name}</strong>
                        <p>
                            <strong>${date_formater(event.schedule_start).time}</strong> ${date_formater(event.schedule_start).day}
                            </br>
                            <strong>${date_formater(event.schedule_end).time}</strong> ${date_formater(event.schedule_end).day}
                        </p>
                    </div>
                    <div class="col-md-3">
                        <p>Category: <b>${event.category.name}</b></p>
                        <p>Type: <b>${event.type}</b></p>
                        ${edit_button}
                    </div>
                </div>`;

        };

        return events.map(data => event_row(data.event)).join('<hr>');
    }

    function constructListOfIntervals(start, end, interval) {
        const intervals = {};

        while (moment(end).diff(start, interval) > 0) {
          const current_end = moment(moment(start).add(1, interval)).format(moment_format);

          Object.assign(intervals, { [start]: current_end });

          start = current_end;
        }

        //return intervals;
        return Object.keys(intervals);
    }

    calendar.render();

})