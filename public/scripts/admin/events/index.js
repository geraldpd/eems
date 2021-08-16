$(function() {

    const modals = {
        date: $('#date-modal')
    }

    const moment_format = 'YYYY-MM-DD';

    const events = Object.values(config.events).map(event => Object.values(event)).flat();

    var calendar = new window.Fullcalendar.Calendar($('#calendar').get(0), {
        plugins: window.Fullcalendar.Plugins,
        selectable: true,
        height: 790,
        initialView: 'dayGridMonth',
        events: events,
        eventBorderColor: 'white',
        eventClick: info => {
            let event = info.event.extendedProps.event;
            window.location.href = config.routes.show.replace('resource_id', event.code)
        },
        dateClick: info => { //single date
            if(!config.events[info.dateStr] && window.moment(info.dateStr).isBefore()) {
                return;
            }

            let events = config.events[info.dateStr] ?? false
                ? constructEventList(config.events[info.dateStr])
                : '<h3>No event scheduled for this date!</h3>';

            dateModal(info, events, window.moment(info.dateStr).isAfter()); //window.moment(info.dateStr).isAfter() compares the moment object if its after NOW date
        },
        select: info => { //multi date
            let start = window.moment(info.startStr).add(1, 'days').format(moment_format); //full calendar adds 1 day to info.endStr so we need to add 1 day to start to compensate
            let end = window.moment(info.endStr).format(moment_format);

            if(start == end) {
                return;
            }

            let selected_dates = constructListOfIntervals(info.startStr, info.endStr, 'days');

            let events = $.map(selected_dates, date => {
                if(!config.events[date]) {
                    return;
                }

                return `<h3>${window.moment(date).format('MMMM D YYYY')}</h3> <div class="col-md-12"> ${constructEventList(config.events[date])}</div>`;
            }).join('<hr>');

            if(!events) {
                return;
            }

            dateModal(info, events, false);
        }
    });

    function dateModal(info, events, add_buton_bool = true) {
        let readableFormat = date => window.moment(date).format('MMMM Do YYYY');
        let event_for = info.dateStr ? readableFormat(info.dateStr) : `${readableFormat(info.startStr)} - ${readableFormat(info.endStr)}`;

        modals.date.modal('show');
        modals.date.find('.date-title').text(`Events for ${ event_for }`);
        modals.date.find('.add-event-button').attr('href', `${config.routes.create}?date=${info.dateStr}`).toggle(add_buton_bool);
        modals.date.find('.date-events').html(events);

        modals.date.find('.event-countdown').each((i, countdown_div) => {
            let countdown = $(countdown_div).data();

            i = setInterval(_ => {
                if(window.moment(countdown.start).isBefore()) {
                    clearInterval(i);
                    return $(countdown_div).html('<p>Event has concluded</p>').prev().addClass('text-secondary');
                }

                let diff = moment(countdown.end).diff(moment())
                let duration = window.moment.duration(diff).format("hh:mm:ss");

                $(countdown_div).html(`<h3 title="Event Countdown" ><b class="event-countdown-timer">${duration}</b></h3>`);
            }, 1000);
        });
    }

    function constructEventList(events) {
        let event_row = event => {
            let date_formater = (date) => ({time: window.moment(date).format('hh:mm A'), day: window.moment(date).format('MMMM D YYYY')});

            /*
                check if the the event is scheduled today or was in the past
                *TRUE - do not render the edit button
                !FALSE - render the edit button redirecting to the edit page
            */
            let edit_button = window.moment(event.schedule_start).isBefore() ? '' : `<a class=" btn btn-link" href="${config.routes.edit.replace('resource_id', event.code)}">Edit</a>`;

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
                        <h4 class="scheduled_time"><strong>${date_formater(event.schedule_start).time}</strong> - <strong>${date_formater(event.schedule_end).time}</strong></h4>
                        <div class="event-countdown" data-start="${event.schedule_start}" data-end="${event.schedule_end}"></div>
                        <a class=" btn btn-link" href="${config.routes.show.replace('resource_id', event.code)}">Preview</a>
                        ${edit_button}
                    </div>
                </div>`;

        };

        return events.map(data => event_row(data.event)).join('<br>');
    }

    function constructListOfIntervals(start, end, interval) {
        const intervals = {};

        while (window.moment(end).diff(start, interval) > 0) {
          const current_end = window.moment(window.moment(start).add(1, interval)).format(moment_format);

          Object.assign(intervals, { [start]: current_end });

          start = current_end;
        }

        //return intervals;
        return Object.keys(intervals);
    }

    calendar.render();
});