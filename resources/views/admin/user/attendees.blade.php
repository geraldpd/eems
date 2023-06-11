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
                    <th scope="col">Status</th>
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
                                $verification = $attendee->email_verified_at ? ['badge' => 'success', 'status' => 'Email Verified'] : ['badge' => 'secondary', 'status' => 'Email Unverified'];
                                $approval = $attendee->is_approved ? ['badge' => 'primary', 'status' => 'Account Approved'] : ['badge' => 'secondary', 'status' => 'Pending Approval'];
                            @endphp
                            <span class="badge badge-{{ $verification['badge']}}">{{ $verification['status'] }}</span>
                            <br>
                            <span class="badge badge-{{ $approval['badge']}}">{{ $approval['status'] }}</span>
                        </td>
                        <td>
                            <a class="btn btn-link btn-sm" href="{{ route('admin.users.show', ['user' => $attendee->id]) }}">View Profile</a>
                        </td>
                    </tr>
                @empty

                @endforelse
            </tbody>

        </table>
    </div>

    <form action="{{ route("admin.users.approve", ['user_id']) }}" method="POST" id="approve-form">
        @csrf
    </form>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#table').DataTable();

            $('.approve-user').on('click', function() {
                let user_id = $(this).data('user_id');
                let user_type = $(this).data('user_type');

                window.Swal.fire({
                    title: `Approve ${user_type}?`,
                    text: 'TAre you sure you want to approve this attendee?',
                    icon: 'question',
                    confirmButtonText: 'Approve',
                    confirmButtonColor: '#007bff',
                    showCancelButton: true
                })
                .then((result) => {
                    if (!result.isConfirmed) return;

                    let approveForm = $('#approve-form')
                    let approveActionRoute = approveForm.prop('action').replace('user_id', user_id)

                    approveForm.prop('action', approveActionRoute).trigger('submit')
                });
            })
        })
    </script>
@endpush

