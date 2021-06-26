$(function() {

    const modals = {
        date: $('#date-modal')
    }

    const moment_format = 'YYYY-MM-DD';

    const events = Object.values(config.events).map(event => Object.values(event)).flat();

    var calendar = new FullCalendar.Calendar($('#calendar').get(0), {
        height: "auto",
        selectable: true,
        initialView: 'dayGridMonth',
        events: events,
        eventBorderColor: 'white',
        eventClick: info => {
            let event = info.event.extendedProps.event;
            window.location.href = config.routes.show.replace('resource_id', event.code)
        },
        dateClick: info => { //single date
            if(!config.events[info.dateStr] && moment(info.dateStr).isBefore()) {
                return;
            }

            let events = config.events[info.dateStr] ?? false
                ? constructEventList(config.events[info.dateStr])
                : '<h3>No event scheduled for this date!</h3>';

            dateModal(info, events, moment(info.dateStr).isAfter()); //moment(info.dateStr).isAfter() compares the moment object if its after NOW date
        },
        select: info => { //multi date
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

                return `<h3>${date}</h3> <div class="col-md-12"> ${constructEventList(config.events[date])}</div>`;
            }).join('<hr>');

            if(!events) {
                return;
            }

            dateModal(info, events, false);
        }
    });

    function dateModal(info, events, add_buton_bool = true) {
        modals.date.modal('show');
        modals.date.find('.date-title').text(`Events for ${info.dateStr}`);
        modals.date.find('.add-event-button').attr('href', `${config.routes.create}?date=${info.dateStr}`).toggle(add_buton_bool);
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
            let edit_button = moment(event.schedule_start).isBefore() ? '' : `<a class=" btn btn-link" href="${config.routes.edit.replace('resource_id', event.code)}">Edit</a>`;

            return `
                <div class="event row">
                    <div class="col-md-7 col-sm-12">
                        <br>
                        <h2 class="lead">${event.name}</h2>
                        <p>
                            Location: <b>${event.location}</b>
                            <br>
                            Category: <b>${event.category.name}</b>
                            <br>
                            Type: <b>${event.type}</b>
                        </p>
                    </div>
                    <div class="col-md-5 col-sm-12">
                        <br>
                        <p>
                            <h4 class=""><strong>${date_formater(event.schedule_start).time}</strong> - <strong>${date_formater(event.schedule_end).time}</strong></h4>
                            <a class=" btn btn-link" href="${config.routes.show.replace('resource_id', event.code)}">Preview</a>
                            <a class=" btn btn-link" href="${config.routes.attendees.replace('resource_id', event.code)}">Attendees</a>
                            ${edit_button}
                        </p>
                    </div>
                </div>`;

        };

        return events.map(data => event_row(data.event)).join('<br>');
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
});