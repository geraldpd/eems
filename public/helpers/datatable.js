

    $('.destroy-button').on('click', function() {
        let that = this;

        axios.delete($(this).data('destroy_route'))
            .then(function (response) {
                // handle success
                if(!response.data.result === 'success') {
                    alert('category cannot be deleted, an event is using this category');
                }

                alert('category deleted');
                DataTable.row( $(that).parents('tr') ).remove().draw(false);
            })
            .catch(function (error) {
                // handle error
                console.warn(error);
            })
    });

    const DataTable = $('#table').DataTable();
