$(function() {

    let schedule_start = $('#schedule_start');
    let schedule_end = $('#schedule_end');
    let date = window.moment($('input[name="date"]').val());

    schedule_start.on('change', function() {
        schedule_end.prop('min', $(this).val());
        let time = $(this).val().split(':');

        let max_schedule_end = date
            .set({
                'hour': time[0],
                'minute': time[1],
            })
            .add('minute', 30)
            .format('HH:mm');

        schedule_end.val(max_schedule_end);
    });

    schedule_end.on('change', function() {
        schedule_start.prop('max', $(this).val());
    });

    $('#location').on('change', function() {
        let location = $(this).val();
        let additional_field = $('.location-additionl-field');
        additional_field.find('.form-group').each((i, div) => $(div).addClass('d-none'));

        switch (location) {
            case 'venue':
                additional_field.find('.location-venue').removeClass('d-none');
                break;

            default: //online
                additional_field.find('.location-online').removeClass('d-none');
                break;
        }
    });
})