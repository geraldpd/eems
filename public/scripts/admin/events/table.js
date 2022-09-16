$(function() {
    $('#table').DataTable({
        "autoWidth": true
    });

    $('#table_filter').append(`
        <div class="float-right">
            <label><input type="date" id="filter-from" class="form-control form-control-sm" placeholder="from" aria-controls="table"> </label>
            <label><input type="date" id="filter-to" class="form-control form-control-sm" placeholder="to" aria-controls="table"> </label>
        </div>
    `)

    $('#table_filter').find('#filter-from').on('change', function() {
        $('#table_filter').find('#filter-to').prop('min', $(this).val())
    });
})