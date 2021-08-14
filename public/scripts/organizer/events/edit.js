$(function() {
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
    })
    .trigger('change');
})