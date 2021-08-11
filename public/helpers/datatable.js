let DataTableInstances = {};

let intervalLimit = 0;
const dataTableInitiator = setInterval(() => {

    if(intervalLimit == 120) {//30 seconds
        //assume that there is no window.dataTable in the current page
        clearInterval(dataTableInitiator);
    }

    if (typeof window.dataTable === 'function') {
        clearInterval(dataTableInitiator);
        window.dataTableConstructor(window.dataTable());
    }
    intervalLimit++;
}, 250);

$(function() {

    $.noConflict();

    window.dataTableConstructor = function(options = {}){

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

        const DataTable = $('#table').DataTable(options);
    }
});

