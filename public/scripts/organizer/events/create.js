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

    window.ClassicEditor
    .create(document.querySelector('#description'), {
        //plugins: window.Editor.Plugins,
        removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed'],
    })
    .then( editor => {
        console.log( editor );
    } )
    .catch( error => {
        console.error( error );
    } );

    $('#location').on('change', function() {
        let location = $(this).val();
        let additional_field = $('.location-additional-field');
        additional_field.find('.form-group').each((i, div) => $(div).addClass('d-none'));
        additional_field.find(`.location-${location}`).removeClass('d-none');
    });

    //run in the end of the script
    $('#location').trigger('change');
})