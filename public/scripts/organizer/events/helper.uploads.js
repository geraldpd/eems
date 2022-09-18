$(function() {

    const uppy = new window.Uppy({ debug: true, autoProceed: true })
    uppy.use(window.FileInput, {
      target: '.documents',
      pretty: true
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
        const url = config.tempdocs.download.replace('document_path', response.body.document_path)
        const file_name = file.name
        let document =`
            <tr title="This document is not yet attached to this event, press ${config.save_button} button to save it to this events document folder">
                <td><a href="${url}" target="_blank" class="text-warning">${file_name}</a></td>
                <td class="text-center">
                  <button type="button" data-name="${file_name}" data-_method="DELETE" class="btn btn-sm btn-secondary remove-document">remove</button>
                </td>
            </tr>
        `;
        $('.uploaded-documents').removeClass('d-none').find('tbody').append(document);
    });

    $('.uploaded-documents').on('click', '.remove-document', function() {
        let document = $(this);

        document.attr('disabled', true).html('<i class="fas fa-spin fa-spinner"></i>');
        let uppy_doc = uppy.getFiles().filter(doc => doc.name === document.data('name'))[0];

        axios.delete(config.tempdocs.destroy, {
          data: document.data()
        })
        .then(_ =>  {
            document.closest('tr').remove();
            if(!$('.uploaded-documents').find('tbody tr').length) {
                $('.uploaded-documents').addClass('d-none')
            }
            uppy.removeFile(uppy_doc.id);
        })
        .catch(_ => {
          console.warn(_);
        });

    });
})