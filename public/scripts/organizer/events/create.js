$(function() {

    let schedule_start = $('#schedule_start');
    let schedule_end = $('#schedule_end');

    schedule_start.on('change', function() {
        schedule_end.prop('min', $(this).val());
    });

    schedule_end.on('change', function() {
        schedule_start.prop('max', $(this).val());
    });
})