@extends('layouts.admin')

@section('content')
    <div class="container">

        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <a href="{{ route('admin.users.attendees') }}"  class="btn btn-secondary btn-user" data-user="attendee">attendees</a>
        <button type="button" class="btn btn-secondary btn-user active" data-user="organizer">organizers</button>

        <br>
        <br>

        <table id="table" class="table table-striped table-bordered"  width="100%">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">First</th>
                    <th scope="col">Last</th>
                    <th scope="col">Email</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($organizers as $organizer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $organizer->firstname }}</td>
                        <td>{{ $organizer->lastname }}</td>
                        <td>{{ $organizer->email }}</td>
                        <td>actions</td>
                    </tr>
                @empty

                @endforelse
            </tbody>

        </table>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#table').DataTable();
        })
    </script>
@endpush

