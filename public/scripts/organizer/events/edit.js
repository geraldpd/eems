$(function() {
    $('#location').on('change', function() {
        let location = $(this).val();
        let additional_field = $('.location-additional-field');
        additional_field.find('.form-group').each((i, div) => $(div).addClass('d-none'));
        additional_field.find(`.location-${location}`).removeClass('d-none');
    });

    window.ClassicEditor
    .create($('#description').get(0), {
        removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed'],
    })
    .then( editor => {
        //console.log( editor );
    } )
    .catch( error => {
        //console.error( error );
    } );


    const uppy = new window.Uppy({ debug: true, autoProceed: true })
    uppy.use(window.FileInput, {
      target: '.documents',
    })
    uppy.use(window.ProgressBar, {
      target: '.documents-progressbar',
      hideAfterFinish: true,
    })
    uppy.use(window.XHRUpload, {
      endpoint: config.tempdocs.store,
      formData: true,
      fieldName: 'documents[]',
      headers: {'X-CSRF-TOKEN': config.csrf}
    });

    // And display uploaded files
    uppy.on('upload-success', (file, response) => {
        const url = response.uploadURL
        const file_name = file.name
        let document =`
            <tr>
                <td><a href="${url}" target="_blank">${file_name}</a></td>
                <td class="text-center"> <button type="button" data-name="${file_name}" class="btn btn-sm btn-secondary remove-document">remove</button> </td>
            </tr>
        `;
        $('.uploaded-documents').removeClass('d-none').find('tbody').append(document);
    });

    $('.uploaded-documents').on('click', '.remove-document', function() {
        let document = $(this);

        document.attr('disabled', true).html('<i class="fas fa-spin fa-spinner"></i>');
        let uppy_doc = uppy.getFiles().filter(doc => doc.name === document.data('name'))[0];

        axios.post(config.tempdocs.destroy, {
            _method: 'DELETE',
            name: document.data('name'),
            code: config.event.code
        })
        .then(_ =>  {
            document.closest('tr').remove();
            if(!$('.uploaded-documents').find('tbody tr').length) {
                $('.uploaded-documents').addClass('d-none')
            }
            uppy.removeFile(uppy_doc.id)
        })
        .catch(_ => {
          console.warn(_);
        });
    });

    if(!  _.keys(config.event.uploaded_documents).length) {
        $('.uploaded-documents').addClass('d-none')
    }

    $('#location').trigger('change');
});