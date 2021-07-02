@extends('layouts.admin')

@section('content')
    <div class="container">

        <button type="button" class="btn btn-secondary btn-user" data-tbody="organizer">organizer</button>
        <button type="button" class="btn btn-secondary btn-user" data-tbody="attendee">attendee</button>

        <br>
        <br>

        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">First</th>
                    <th scope="col">Last</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody id="organizer">
                @foreach($users['organizer'] as $user)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $user->firstname }}</td>
                        <td>{{ $user->lastname }}</td>
                        <td>{{ $user->email }}</td>
                        <td>No Actions Yet</td>
                    </tr>
                @endforeach
            </tbody>

            <tbody id="attendee" style="display:none">
                @foreach($users['attendee'] as $user)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $user->firstname }}</td>
                        <td>{{ $user->lastname }}</td>
                        <td>{{ $user->email }}</td>
                        <td>No Actions Yet</td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function() {
            $('.btn-user').on('click', function() {
                $(this).button('toggle');
                $('table tbody').hide();

                $(`#${$(this).data('tbody')}`).show();
            });
        })
    </script>
@endpush
