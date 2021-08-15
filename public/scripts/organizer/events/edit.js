$(function() {
    $('#location').on('change', function() {
        let location = $(this).val();
        let additional_field = $('.location-additionl-field');
        additional_field.find('.form-group').each((i, div) => $(div).addClass('d-none'));
        additional_field.find(`.location-${location}`).removeClass('d-none');
    });

    $('#location').trigger('change');
});