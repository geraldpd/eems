$(function() {

    const modals = {
        date: $('#date-modal')
    }

    const dates = {
        now: moment()
    }

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
            dateModal(events, info, info.dateStr);
        },
        select: function(info) { //multi date
            return;
            selected_date = `${info.startStr} to ${info.endStr}`;
            dateModal(events, info, selected_date);
        }
    });

    function dateModal(data, info, selected_date)
    {
        modals.date.modal('show');
        modals.date.find('.date-title').text(`Events for ${selected_date}`);
        modals.date.find('.add-event-button').attr('href', `${config.routes.create}?date=${selected_date}`);

        let events = config.events[info.dateStr] ?? false;

        if(!events) {
            modals.date.find('.date-events').html('<h3>No event scheduled for this date!</h3>');

            return;
        };

        let event_row = event => {
            let date_formater = (date) => ({time: moment(date).format('hh:mm A'), day: moment(date).format('MMMM D YYYY')});

            /*
                check if the start of the event is in the past
                *TRUE - do not render the edit button
                !FALSE - render the edit button redirecting to the edit page
            */
            let edit_button = moment().diff(event.schedule_start, 'days') > 0 ? '' : `<a href="${config.routes.edit.replace('resource_id', event.id)}" class="btn btn-primary">view</a>`;

            /*
                check if the the scheduled event is set to the current date
            */
            if(edit_button && moment(event.schedule_start).format('MM-DD-Y') == moment().format('MM-DD-Y')) {

                /*
                    add 1 hour to the current time and compare it to the start of the event
                    *TRUE - when the start of the event is not yet close to the current hour, render the button
                    !FALSE - do not allow editing of the event
                */
                edit_button = moment().add(1, 'hours').diff(event.schedule_start, 'hours') < 0 ? edit_button : '';

            }

            //? SHOULD THE EDIT BUTTON JUST BE HIDDEN WHEN THE EVENT DATE IS THE CURRENT DATE?

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
                        ${edit_button}
                    </div>
                </div>`;

        };

        let date_events = events.map(data => event_row(data.event)).join('<hr>');

        modals.date.find('.date-events').html(date_events);
    }

    calendar.render();

})