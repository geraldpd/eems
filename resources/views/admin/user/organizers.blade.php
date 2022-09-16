@extends('layouts.admin')

@section('content')
    <div class="container">
        @if(session()->has('message'))
            <div class="alert alert-info">
                {{ session()->get('message') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <h1>User Management</h1>
            </div>
            <div class="col-md-6">
                <div class="float-right mt-2">
                    <a href="{{ route('admin.users.attendees') }}" class="btn btn-link" data-user="attendee">
                        <i class="fas fa-user"></i> Attendees
                    </a>
                    <button class="btn btn-primary active" data-user="organizer">
                        <i class="fas fa-user"></i> Organizers
                    </button>
                </div>
            </div>
        </div>

        <br>

        <table id="table" class="table table-striped table-bordered"  width="100%">
            <thead class="thead-dark">
                <tr>
                    <th rowspan="2" class="text-center ">#</th>
                    <th rowspan="2" class="text-center align-middle">Organization</th>
                    <th rowspan="2" class="text-center align-middle">Department</th>
                    <th colspan="2" class="text-center">Organizer</th>
                    <th rowspan="2" class="text-center">Action</th>
                </tr>
                <tr>
                    <th class="text-center">Name</th>
                    <th class="text-center">Email</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($organizers as $organizer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $organizer->organization->name }}</td>
                        <td>{{ $organizer->organization->department }}</td>
                        <td>{{ $organizer->fullname }}</td>
                        <td>{{ $organizer->email }}</td>
                        <td>
                            <a class="btn btn-link" href="{{ route('admin.events.index', ['organizer' => $organizer->email]) }}">events</a>
                        </td>
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

