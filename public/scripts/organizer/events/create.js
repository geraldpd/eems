
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
        removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed'],
    })
    .then( editor => {
        //console.log( editor );
    } )
    .catch( error => {
        //console.error( error );
    } );

    $('#location').on('change', function() {
        let location = $(this).val();
        let additional_field = $('.location-additional-field');
        additional_field.find('.form-group').each((i, div) => $(div).addClass('d-none'));
        additional_field.find(`.location-${location}`).removeClass('d-none');
    });

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

        let uppy_doc = uppy.getFiles().filter(doc => {
            return doc.name === document.data('name')
        })[0];

        axios.post(config.tempdocs.destroy, {
            _method: 'DELETE',
            name: document.data('name')
        })
        .then(_ =>  {
            uppy.removeFile(uppy_doc.id)
            document.closest('tr').remove();
            if(!$('.uploaded-documents').find('tbody tr').length) {
                $('.uploaded-documents').addClass('d-none')
            }
        })
        .catch(_ => {
          console.warn(_);
        });
    })

    if(!config.tempdocs.count) {
        $('.uploaded-documents').addClass('d-none')
    }

    //run in the end of the script
    $('#location').trigger('change');
})