$(function() {
    $('#location').on('change', function() {
        let location = $(this).val();
        let additional_field = $('.location-additional-field');
        additional_field.find('.form-group').each((i, div) => $(div).addClass('d-none'));
        additional_field.find(`.location-${location}`).removeClass('d-none');
    });

    window.ckeditor
    .create($('#description').get(0), {
        removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed'],
    })
    .then( editor => {
        console.log( editor );
    } )
    .catch( error => {
        console.error( error );
    } );


    $('#location').trigger('change');
});