$(function() {

    let intervals = [];

    const modals = { date: $('#date-modal') }

    const moment_format = 'YYYY-MM-DD';

    //const events = Object.values(config.events).map(event => Object.values(event)).flat();
    const events = config.events;
    console.log(events)

    const calendar = new window.Fullcalendar.Calendar($('#calendar').get(0), {
        plugins: window.Fullcalendar.Plugins,
        selectable: true,
        height: 790,
        initialView: 'dayGridMonth',
        events: events,
        eventBorderColor: 'white',
        displayEventTime: false,
        eventClick: info => {
            let event = info.event.extendedProps
            window.location.href = config.routes.show.replace('resource_id', event.code)
        },
        dateClick: info => { //single date

            //show the select multi-day event button
            let start = info.dateStr;//moment(info.dateStr).startOf('day').format(moment_format); //full calendar adds 1 day to info.endStr so we need to add 1 day to start to compensate
            let end = info.dateStr;//moment(info.dateStr).endOf('day').format(moment_format);

            $('.add-event-button')
            .attr('href', config.routes.create + `/?start=${start}&end=${end}`)

            /*
            let eventsOfTheDay = Object.keys(config.events).map(function(key) {
                let keyRange = key.split(',')
                if (
                    info.dateStr > keyRange[0] && info.dateStr < keyRange[1] ||
                    (info.dateStr == keyRange[0])
                    ) {
                    return config.events[key]
                }
            })
            .filter(Boolean)
            .flat();
            */

            fetchScheduleEvents({
                start: start,
                end: end
            }, (eventsOfTheDay) => {

                let events = Object.keys(eventsOfTheDay).length
                ? constructEventList(eventsOfTheDay[info.dateStr], info.dateStr)
                : '<h3>No event scheduled for this date!</h3>';

                dateModal(info, events, moment(info.dateStr).isAfter()); //moment(info.dateStr).isAfter() compares the moment object if its after NOW date

            })

        },
        select: info => { //multi date

            //show the select multi-day event button
            let start = moment(info.startStr).format(moment_format); //full calendar adds 1 day to info.endStr so we need to add 1 day to start to compensate
            let end = moment(info.endStr).subtract(1, 'days').format(moment_format);

            if(start == end) {
                return;
            }

            $('.add-event-button')
            .attr('href', config.routes.create + `/?start=${start}&end=${end}`)
            .show()

            fetchScheduleEvents({
                start: start,
                end: end
            }, eventsOfTheDay => {

                if (Object.keys(eventsOfTheDay).length) {

                    let events = $.map(eventsOfTheDay, function(events, index) {
                       return `<h3>${moment(index).format('MMMM D YYYY')}</h3> <div class="col-md-12"> ${constructEventList(events, index)}</div><hr>`
                    })

                    dateModal(info, events, moment(info.dateStr).isAfter()); //moment(info.dateStr).isAfter() compares the moment object if its after NOW date

                } else {

                   let events =  '<h3>No event scheduled for this date!</h3>'
                   dateModal(info, events, moment(info.dateStr).isAfter()); //moment(info.dateStr).isAfter() compares the moment object if its after NOW date
                }
            })
        }
    });

    function dateModal(info, events, add_buton_bool = true) {
        let readableFormat = date => moment(date).format('MMMM Do YYYY');
        let event_for = info.dateStr
                    ? readableFormat(info.dateStr)
                    : `${readableFormat(info.startStr)} - ${readableFormat(moment(info.endStr).subtract(1, 'days'))}`;

        modals.date.modal('show');
        modals.date.find('.date-title').text(`Events for ${ event_for }`);
        modals.date.find('.date-events').html(events);


        modals.date.find('.event-status').each((i, status_div) => {
            let status_data = $(status_div).data();

            let schedule_start = moment(status_data.start)
            let schedule_end = moment(status_data.end)

            $(status_div).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

            intervals[i] = setInterval(_ => {
                let status_html = ''
                switch (status_data.status) {
                    case 'CONCLUDED':
                        status_html = '<b class="text-muted">CONCLUDED</b >'
                        break;

                    case 'ONGOING':
                        status_html = '<b class="text-success">ONGOING</b >'
                        break;

                    case 'PENDING':
                        status_html = '<b class="text-muted">PENDING</b >'
                        break;

                    default: //*SOON
                        let timeDiff = moment.duration(moment(schedule_start).diff(moment()));
                        let countdown = timeDiff.format("hh : mm : ss")
                        let secondsLeft = parseFloat(timeDiff.format("s").replace(/,/g, ''))

                        if(secondsLeft > 0) {
                            status_html = `<small>starts in</small> <h4 title="Event Countdown" ><b class="event-status-timer-disabled text-warning">${countdown}</b></h4>`;
                        } else {
                            //let timeDiff = moment.duration(moment(schedule_end).diff(moment())); //! for ongoing to concluded
                            status_html = '<b class="text-success">ONGOING</b >'
                            clearInterval(i);
                        }
                        break;
                }

                $(status_div).html(status_html);
            }, 1000);
        });
    }

    function constructEventList(schedules, day) {
        let event_row = schedule => {
            let date_formater = (date) => ({time: moment(date).format('hh:mm A'), day: moment(date).format('MMMM D YYYY')});

            /*
                check if the the event is scheduled today or was in the past
                *TRUE - do not render the edit button
                !FALSE - render the edit button redirecting to the edit page
            */
            let edit_button = moment(schedule.schedule_start).isBefore() ? '' : `<a class=" btn btn-link" href="${config.routes.edit.replace('resource_id', schedule.event.code)}">Edit</a>`;

            return `
                <div class="event row">
                    <div class="col-md-7 col-sm-12">
                        <br>
                        <h2 class="lead">${schedule.event.name}</h2>
                        <p>
                            Location: <b>${schedule.event.location}</b>
                            <br>
                            Category: <b>${schedule.event.category.name}</b>
                            <br>
                            Type: <b>${schedule.event.type.name}</b>
                        </p>
                    </div>
                    <div class="col-md-5 col-sm-12">
                        <br>

                        <h4 class="scheduled_time">
                            <strong>${date_formater(schedule.schedule_start).time}</strong> - <strong>${date_formater(schedule.schedule_end).time}</strong>
                        </h4>

                        <div class="event-status" data-status="${schedule.status}" data-day="${day}" data-start="${ schedule.schedule_start }" data-end="${ schedule.schedule_end }"></div>

                        <a class=" btn btn-link btn-light" href="${config.routes.show.replace('resource_id', schedule.event.code)}">Preview</a>

                        <a class=" btn btn-link btn-light" href="${config.routes.invitations.replace('resource_id', schedule.event.code)}">Attendees</a>

                        ${edit_button}
                    </div>
                </div>`;

        };

        return schedules.map(schedule => event_row(schedule)).join('<br>');
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

    function fetchScheduleEvents(date, callback) {
        axios.get(config.routes.fetchScheduleEvents, { params: date })
        .then(function (response) {
            callback(response.data)
        })
        .catch(function (error) {
          console.warn(error);
        });
    }

    modals.date.on('hidden.bs.modal', function (e) {
        intervals.forEach(i => clearInterval(i));
    });

    calendar.render();
});