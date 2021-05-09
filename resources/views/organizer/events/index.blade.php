@extends('layouts.organizer')

@section('content')
    <div class="container">

        <button type="button" class="btn btn-user" data-tbody="organizer">organizer</button>
        <button type="button" class="btn btn-user" data-tbody="attendee">attendee</button>

        <br>
        <br>

        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Description</th>
                    <th scope="col">Location</th>
                    <th scope="col">Documents</th>
                </tr>
            </thead>

            <tbody id="organizer">
                @foreach($events as $event)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
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
                $('table tbody').hide();

                $(`#${$(this).data('tbody')}`).show();
            });
        })
    </script>
@endpush
