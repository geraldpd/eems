$(function() {
    $('#table').DataTable({
        "autoWidth": true
    });

    $('#table').on('click', '.delete-row', function() {
        let category_id = $(this).data('id');

        window.Swal.fire({
            title: `Delete Category?`,
            icon: 'question',
            confirmButtonText: 'Yes',
            confirmButtonColor: '#007bff',
            showCancelButton: true
        })
        .then((result) => {
            if (!result.isConfirmed) return;

            let delete_form = $('#delete-resource-form')
            let delete_route = delete_form.prop('action').replace('category_id', category_id)

            delete_form.prop('action', delete_route).trigger('submit')
        });
    });
})