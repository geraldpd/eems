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

            fetchScheduleEvents({
                start: start,
                end: end
            }, eventsOfTheDay => {

                let events = ''

                if (Object.keys(eventsOfTheDay).length) {
                    events = $.map(eventsOfTheDay, function(events, index) {
                        return `<h3>${moment(index).format('MMMM D YYYY')}</h3> <div class="col-md-12"> ${constructEventList(events, index)}</div><hr>`
                    })
                } else {
                    events =  '<h3>No event scheduled for this date!</h3>'
                }

                dateModal(info, events, moment(info.startStr).isAfter()); //moment(info.dateStr).isAfter() compares the moment object if its after NOW date
            })
        }
    });

    function dateModal(info, events, add_buton_bool = true) {
        let eventCount = 0;
        let readableFormat = date => moment(date).format('MMMM Do YYYY');
        let event_for = info.dateStr
        ? readableFormat(info.dateStr)
        : `${readableFormat(info.startStr)} - ${readableFormat(moment(info.endStr).subtract(1, 'days'))}`;

        modals.date.modal('show');
        modals.date.find('.date-title').text(`Events for ${ event_for }`);
        modals.date.find('.date-events').html(events);

        modals.date.find('.toggle-concluded-events').off().on('click', _ => {
            modals.date.find('.event').each((i, event_div) => {

                let concludedEvent = $(event_div).data('status') == 'CONCLUDED'

                if(concludedEvent) {
                    $(event_div).toggle(!$(event_div).is(':visible'))
                }
            })
        })

        modals.date.find('.event-status').each((i, status_div) => {
            eventCount += 1;
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
                    let timeDiffEnd = moment.duration(moment(schedule_end).diff(moment())); //! for ongoing to concluded
                    let secondsLeftEnd = parseFloat(timeDiffEnd.format("s").replace(/,/g, ''))

                    if(secondsLeftEnd < 0) {
                        status_html = '<b class="text-muted">CONCLUDED</b >'
                        clearInterval(intervals[i])
                    } else {
                        status_html = '<b class="text-success">ONGOING</b >'
                    }
                    break;

                    case 'PENDING':
                    status_html = '<b class="text-muted">PENDING</b >'
                    break;

                    default: //*SOON
                    let timeDiff = moment.duration(moment(schedule_start).diff(moment()));
                    let countdown = timeDiff.format("hh : mm : ss")
                    let secondsLeft = parseFloat(timeDiff.format("s").replace(/,/g, ''))

                    if(secondsLeft > 0) {
                        status_html = `<sub>starts in</sub> <br> <strong title="Event Countdown" ><b class="event-status-timer-disabled text-warning">${countdown}</b></strong>`;
                    } else {
                        let timeDiffEnd = moment.duration(moment(schedule_end).diff(moment())); //! for ongoing to concluded
                        let secondsLeftEnd = parseFloat(timeDiffEnd.format("s").replace(/,/g, ''))

                        if(secondsLeftEnd > 0) {
                            status_html = '<b class="text-success">ONGOING</b >'
                        } else {
                            status_html = '<b class="text-muted">CONCLUDED</b >'
                            clearInterval(intervals[i])
                        }
                    }
                    break;
                }

                $(status_div).html(status_html);
            }, 1000);
        });

        eventCount ? modals.date.find('.toggle-concluded-events').show() : modals.date.find('.toggle-concluded-events').hide()

        $('.add-event-button').toggle(add_buton_bool)
    }

    function constructEventList(schedules, day) {
        let event_row = schedule => {
            let date_formater = (date) => ({time: moment(date).format('hh:mm A'), day: moment(date).format('MMMM D YYYY')});

            /*
            check if the the event is scheduled today or was in the past
            *TRUE - do not render the edit button
            !FALSE - render the edit button redirecting to the edit page
            */
            let edit_button = moment(schedule.schedule_start).isBefore() ? '' : `<a class=" btn btn-link btn-light" href="${config.routes.edit.replace('resource_id', schedule.event.code)}">Edit</a>`;

            return `
            <div class="event row" data-status="${schedule.status}">
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

            <div class="scheduled_time">
                <strong>${date_formater(schedule.schedule_start).time}</strong> - <strong>${date_formater(schedule.schedule_end).time}</strong>
            </div>

            <div class="event-status" data-status="${schedule.status}" data-day="${day}" data-start="${ schedule.schedule_start }" data-end="${ schedule.schedule_end }"></div>

            <a class=" btn btn-link btn-light" href="${config.routes.show.replace('resource_id', schedule.event.code)}">Preview</a>

            <a class=" btn btn-link btn-light" href="${config.routes.invitations.replace('resource_id', schedule.event.code)}">Attendees</a>

            ${edit_button}
            </div>
            </div>`;

        };

        return schedules.map(schedule => event_row(schedule)).join('');
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