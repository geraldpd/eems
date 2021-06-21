$(function() {

    let schedule_start = $('#schedule_start');
    let schedule_end = $('#schedule_end');
    let date = moment($('input[name="date"]').val());

    schedule_start.on('change', function() {
        schedule_end.prop('min', $(this).val());
        let time = $(this).val().split(':');

        max_schedule_end = date
            .set({
                'hour': time[0],
                'minute': time[1],
            })
            .add('minute', 30)
            .format('HH:mm');

        schedule_end.val(max_schedule_end)
        console.log(max_schedule_end);

        //console.log( moment(date+' '+$(this).val()).add('30', 'minutes').format('h:i'))
    });

    schedule_end.on('change', function() {
        schedule_start.prop('max', $(this).val());
    });
})