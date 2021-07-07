@extends('layouts.admin')

@section('content')
    <div class="container">

        <button type="button" class="btn btn-secondary btn-user" data-user="organizer">organizer</button>
        <button type="button" class="btn btn-secondary btn-user" data-user="attendee">attendee</button>

        <br>
        <br>

        <table id="table" class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">First</th>
                    <th scope="col">Last</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody></tbody>

        </table>
    </div>
@endsection

@push('scripts')
    {!! tableScript('categories') !!}
    <script type="text/javascript">

        $(function() {

            const user_data = {
                attendee: constructTableData(@json($users['attendee'])),
                organizer: constructTableData(@json($users['organizer']))
            };

            $('.btn-user').on('click', function() {
                $(this).button('toggle');

                DataTable.clear();
                DataTable.rows.add(user_data[$(this).data('user')]);
                DataTable.draw();
            })
            .trigger('click')

            function constructTableData(users){
                return users.map((user, iteration) => [
                    `<strong>${iteration + 1}</strong>`, //#
                    user.firstname, //First name
                    user.lastname, //Last Name
                    user.email, //Email
                    `
                        <a href="#" class="btn btn-primary">Edit</a>
                        <a href="#" class="btn btn-secondary">Delete</a>
                    `
                ])
            }
        })
    </script>
@endpush

