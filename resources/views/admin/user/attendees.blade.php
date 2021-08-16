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
                    <button type="button" class="btn btn-primary btn-user active" data-user="attendee">
                        <i class="fas fa-user"></i> Attendees
                    </button>
                    <a href="{{ route('admin.users.organizers') }}" class="btn btn-link btn-user" data-user="organizer">
                        <i class="fas fa-user"></i> Organizers
                    </a>
                </div>
            </div>
        </div>

        <br>

        <table id="table" class="table table-striped table-bordered"  width="100%">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">First</th>
                    <th scope="col">Last</th>
                    <th scope="col">Email</th>
                    <th scope="col">Verification</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($attendees as $attendee)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $attendee->firstname }}</td>
                        <td>{{ $attendee->lastname }}</td>
                        <td>{{ $attendee->email }}</td>
                        <td class="text-center">
                            @php
                                $verification = $attendee->email_verified_at ? ['badge' => 'success', 'status' => 'verified'] : ['badge' => 'secondary', 'status' => 'unverified'];
                            @endphp
                            <span class="badge badge-{{ $verification['badge']}}">{{ $verification['status'] }}</span>
                        </td>
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

