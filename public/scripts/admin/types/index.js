$(function() {
    $('#table').DataTable({
        "autoWidth": true
    });

    $('#table').on('click', '.delete-row', function() {
        let type_id = $(this).data('id');

        window.Swal.fire({
            title: `Delete Event Type?`,
            icon: 'question',
            confirmButtonText: 'Yes',
            confirmButtonColor: '#007bff',
            showCancelButton: true
        })
        .then((result) => {
            if (!result.isConfirmed) return;

            let delete_form = $('#delete-resource-form')
            let delete_route = delete_form.prop('action').replace('type_id', type_id)

            delete_form.prop('action', delete_route).trigger('submit')
        });
    });
})