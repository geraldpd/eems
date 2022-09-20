$(function() {
    $('#table').DataTable({
        "autoWidth": true
    });

    $('#table_filter').append(`
        <div class="float-right">
            <label><input type="date" id="filter-from" class="form-control form-control-sm" placeholder="from" aria-controls="table" value="${config.from}"> </label>
            <label><input type="date" id="filter-to" class="form-control form-control-sm" placeholder="to" aria-controls="table" min="${config.from}" value="${config.to}"> </label>
        </div>
    `)

    $('#table_filter')
    .find('#filter-from')
    .on('change', function() {
        $('#table_filter').find('#filter-to').prop('min', $(this).val()).val($(this).val())
        filterDateRange()
    })

    $('#table_filter')
    .find('#filter-to')
    .on('change', function() {
        filterDateRange()
    });

    function filterDateRange() {
        let from = $('#filter-from').val()
        let to = $('#filter-to').val()

        if(!from || !to) {
            return;
        }

        let index_url = window.location.href
        index_url = updateQueryStringParameter(index_url,'from', from)
        index_url = updateQueryStringParameter(index_url,'to', to)

        window.location.replace(index_url)
    }

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
          return uri.replace(re, '$1' + key + "=" + value + '$2');
        }
        else {
          return uri + separator + key + "=" + value;
        }
      }
})